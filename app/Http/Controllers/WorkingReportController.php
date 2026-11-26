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

    /** Status yang ditampilkan di laporan: telat masuk dan belum masuk. */
    private const SHOWN_STATUSES = ['telat', 'belum_masuk'];

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

            if (request()->ajax()) {
                $departments = collect();

                if ($request->filled('date')) {
                    $date_carbon = Carbon::createFromFormat('d-m-Y', $request['date'])->startOfDay();

                    $departments = $this->attendanceUtil
                        ->getLateCheckInReport($date_carbon, $department_code)
                        ->filter(fn ($row) => in_array($row['status'], self::SHOWN_STATUSES, true))
                        ->groupBy(fn ($row) => $row['department']['id'])
                        ->map(fn ($rows) => [
                            'department' => $rows->first()['department'],
                            'rows' => $rows
                                ->sortBy(fn ($row) => $row['employee']['first_name'] ?? '')
                                ->values(),
                        ])
                        ->values();
                }

                $render = view('Report.working_report.table', compact('departments', 'date'))->render();
                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            }

            $department_bios = collect($this->service->get_departments(['page_size' => 999])['data']);
            return view('Report.working_report.index', compact('department_bios', 'department_code', 'date'));
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }
}
