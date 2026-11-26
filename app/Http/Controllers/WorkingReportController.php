<?php

namespace App\Http\Controllers;

use App\Services\Api\ApiServices;
use App\Utils\Util;
use App\Utils\ResponseUtil;
use App\Utils\AttendanceUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class WorkingReportController extends Controller
{
    private $service;
    private $buildRes;
    private $attendanceUtil;
    private $util;

    /** Hanya yang sudah absen dan melewati batas disiplin. */
    private const SHOWN_STATUSES = ['telat'];

    public function __construct(ApiServices $service, Util $util, AttendanceUtil $attendanceUtil, ResponseUtil $buildRes)
    {
        $this->service = $service;
        $this->buildRes = $buildRes;
        $this->attendanceUtil = $attendanceUtil;
        $this->util = $util;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!auth()->user()->can('report.working.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $department_code = $request['department_code'];
            $date = $request['date'];
            $sort = $request->input('sort') === 'desc' ? 'desc' : 'asc';

            if (request()->ajax()) {
                $rows = collect();

                if ($request->filled('date')) {
                    $date_carbon = Carbon::createFromFormat('d-m-Y', $request['date'])->startOfDay();
                    $direction = $sort === 'desc' ? -1 : 1;

                    $rows = $this->attendanceUtil
                        ->getLateCheckInReport($date_carbon, $department_code ?: null)
                        ->filter(fn ($row) => in_array($row['status'], self::SHOWN_STATUSES, true) && !$row['within_tolerance'])
                        ->sortBy(fn ($row) => [
                            $direction * (int) $row['late_minutes'],
                            $row['employee']['first_name'] ?? '',
                        ])
                        ->values();
                }

                $render = view('Report.working_report.table', compact('rows', 'date'))->render();
                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            }

            $department_bios = collect($this->service->get_departments(['page_size' => 999])['data']);
            return view('Report.working_report.index', compact('department_bios', 'department_code', 'date', 'sort'));
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }
}
