<?php

namespace App\Http\Controllers;

use App\Utils\Util;
use App\Models\Shift;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Department;
use App\Models\RequestTask;
use App\Models\Transaction;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\RequestTaskHasEmp;
use App\Services\Api\ApiServices;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class TSOController extends Controller
{
    private $apiService;
    private $buildRes;
    private $util;

    public function __construct(ApiServices $apiService, Util $util, ResponseUtil $buildRes)
    {
        $this->apiService = $apiService;
        $this->buildRes = $buildRes;
        $this->util = $util;
    }

    public function find_TSO()
    {
        $business_id = Session::get('business_id');
        $slug_week = ['mgg', 'sen', 'sel', 'rab', 'kam', 'jum', 'sab'];
        $start_time = Carbon::parse("2022-10-16 23:59:59");
        $end_time = Carbon::parse("2022-10-29 23:59:59");
        $filter['start_time'] = $start_time->hour(0)->minute(0)->second(0)->format('Y-m-d H:i:s');
        $filter['end_time'] = $end_time->addHours(1)->hour(23)->minute(59)->second(59)->format('Y-m-d H:i:s');
        $dates = $this->util->generateDateRange($start_time, $end_time);
        foreach ($dates as $date) {
            $code_day = Carbon::parse($date)->dayOfWeek;
            $th_dates[] = ['slug' => $slug_week[$code_day], 'date' => $date];
        }

        $attendance_manuals = Transaction::whereBetween('punch_time', [$filter['start_time'], $filter['end_time']])->get();
        $attendance_device_count = $this->apiService->get_transactions($filter)['count'];
        $attendance_devices = collect($this->apiService->get_transactions(array_merge(['page_size' => $attendance_device_count], $filter))['data']);
        foreach ($attendance_manuals as $key => $itemAttendanceM) {
            $itemAttendanceM['id'] = $itemAttendanceM['emp'];
            $attendance_devices[] = $itemAttendanceM->toArray();
        }

        $q = 'Erwan';
        $department_bios = collect($this->apiService->get_departments(["page_size" => 999])['data']);
        $employee_bios = $this->apiService->get_employees(array_merge(["employee_icontains" => $q, "page_size" => 12]))['data'];
        foreach (($employee_bios ?? []) as $itemEmployee) {
            $employee_id = $itemEmployee['id'];
            $employee_dept_parent = $department_bios->where('id', $itemEmployee['department']['id']);
            $department = Department::where('dept_id', $itemEmployee['department']['id'])->first();
            $employee = Employee::where('business_id', $business_id)->where('emp_id', $itemEmployee['id'])->first();
            $shift = Shift::where('business_id', $business_id)->where('dept_id', $employee_dept_parent[''])->with(['shiftday.shiftday_has_timetable'])->get();
            $attens_groupings = $this->_group_by_date($attendance_devices->filter(function ($itemAttendance) use ($employee_id) {
                return $itemAttendance['emp'] === $employee_id;
            }));

            foreach ($dates as $date_key => $date) {
                if (count($dates) - 1 != $date_key) {

                }
            }
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // if (!auth()->user()->can('TSO.view')) {
        //     abort(403, 'Unauthorized action.');
        // }

        try {
            if (request()->ajax()) {
                $business_id = Session::get('business_id');
                $TSOs = [];
                $order = null;
                $render =  view('Task.TSO.table', compact('TSOs', 'order'))->render();
                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            }

            return  view('Task.TSO.index');
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if (!auth()->user()->can('TSO-approved.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $render = view('Task.TSO.create')->render();
            return $this->buildRes->RESPONSE_REQ('success', $render, null);
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('request-task.create')  || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
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

    /**
     * Rules validation group.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'date' => 'required',
            'emps' => 'required',
            'position' => 'required',
        ];
    }
}
