<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Shift;
use App\Utils\ResponseUtil;
use App\Utils\AttendanceUtil;
use App\Models\EmployeeDebt;
use App\Models\Holiday;
use App\Models\Operational;
use App\Models\Position;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Services\Api\ApiServices;
use App\Utils\Util;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AttendanceReportCardController extends Controller
{
    private $service;
    private $buildRes;
    private $attendanceUtil;
    private $util;

    public function __construct(ApiServices $service, Util $util, AttendanceUtil $attendanceUtil, ResponseUtil $buildRes)
    {
        $this->service = $service;
        $this->buildRes = $buildRes;
        $this->attendanceUtil = $attendanceUtil;
        $this->util = $util;
    }

    private function departmentCodesFromRequest(Request $request): string|array|null
    {
        $codes = $request->input('department_codes');
        if (is_array($codes)) {
            $filtered = array_values(array_filter($codes));
            return empty($filtered) ? null : $filtered;
        }

        $code = $request->input('department_code');
        if (empty($code)) {
            return null;
        }

        return is_array($code) ? array_values(array_filter($code)) : $code;
    }

    private function emptyAttendanceCardResult(
        Carbon $start_date_work_day,
        Carbon $end_date_work_day,
        Carbon $start_date_overtime,
        Carbon $end_date_overtime
    ): \Illuminate\Support\Collection {
        return collect([
            'departments' => collect(),
            'range_dates' => collect(),
            'start_date' => min($start_date_work_day, $start_date_overtime),
            'end_date' => max($end_date_work_day, $end_date_overtime),
            'start_date_work_day' => $start_date_work_day,
            'end_date_work_day' => $end_date_work_day,
            'start_date_overtime' => $start_date_overtime,
            'end_date_overtime' => $end_date_overtime,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!auth()->user()->can('attendance-card.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = Session::get('business_id');
            $department_codes = $this->departmentCodesFromRequest($request);
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];

            if (request()->ajax()) {
                $result = collect([]);

                $business = Business::where('id', $business_id)->select('id', 'pending_day')->first();
                if ($request->has('start_date') && $request->has('end_date')) {
                    $start_date = Carbon::createFromFormat('d-m-Y', $request['start_date']);
                    $end_date = Carbon::createFromFormat('d-m-Y', $request['end_date']);

                    $start_date_work_day = Carbon::createFromFormat('d-m-Y', $request['start_date']);
                    $end_date_work_day = Carbon::createFromFormat('d-m-Y', $request['end_date']);
                    $start_date_overtime = Carbon::createFromFormat('d-m-Y', $request['start_date'])->subDays($business->pending_day);
                    $end_date_overtime = Carbon::createFromFormat('d-m-Y', $request['end_date'])->subDays($business->pending_day);

                    $filter_department_codes = $this->departmentCodesFromRequest($request);
                    if ($filter_department_codes === null) {
                        $result = $this->emptyAttendanceCardResult(
                            $start_date_work_day,
                            $end_date_work_day,
                            $start_date_overtime,
                            $end_date_overtime,
                        );
                    } else {
                        $result = $this->attendanceUtil->getAttendance(
                            $start_date_work_day,
                            $end_date_work_day,
                            $start_date_overtime,
                            $end_date_overtime,
                            $filter_department_codes,
                        );
                    }

                    $render = view('Report.attendance_card.table', compact('result', 'start_date_work_day', 'end_date_work_day', 'start_date_overtime', 'end_date_overtime'))->render();
                    return $this->buildRes->RESPONSE_REQ('success', $render, null);
                } else {
                    $render = view('Report.attendance_card.table', compact('result'))->render();
                    return $this->buildRes->RESPONSE_REQ('success', $render, null);
                }
            }

            $department_bios = collect($this->attendanceUtil->getDepartment());
            if (!is_array($department_codes)) {
                $department_codes = $department_codes ? [$department_codes] : [];
            }
            return view('Report.attendance_card.index', compact('department_bios', 'department_codes', 'start_date', 'end_date'));
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }


    function _group_by_date($array)
    {
        $return = array();
        foreach ($array as $val) {
            $date = Carbon::parse($val['punch_time'])->format('Y-m-d');
            if (!empty($date)) {
                $return[$date][] = $val;
                usort($return[$date], function ($a, $b) {
                    return strtotime($a['punch_time']) - strtotime($b['punch_time']);
                });
            }
        }
        return $return;
    }

    public function is_holiday($holidays, $date)
    {
        $holiday_datas = [];
        $date_atten = Carbon::parse($date);
        foreach ($holidays as $item) {
            if ($date_atten->between(Carbon::parse($item->start_date), Carbon::parse($item->end_date))) {
                $holiday_datas[] = $item;
            }
        }

        return $holiday_datas;
    }
}
