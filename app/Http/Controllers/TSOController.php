<?php

namespace App\Http\Controllers;

use App\Utils\Util;
use App\Models\Business;
use App\Models\ActivityLog;
use App\Models\AttendanceLb;
use App\Utils\ResponseUtil;
use App\Utils\AttendanceUtil;
use Illuminate\Http\Request;
use App\Models\AttendanceTso;
use Illuminate\Support\Carbon;
use App\Services\Api\ApiServices;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class TSOController extends Controller
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

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        Log::info('[' . request()->route()->getName() . ']::GET');
        // if (!auth()->user()->can('TSO.view')) {
        //     abort(403, 'Unauthorized action.');
        // }

        try {
            if (request()->ajax()) {
                $page_size = 10;
                $page = 1;
                if (!empty($request->input('page'))) {
                    $page = (int)$request->page;
                }

                if ($request->has('page_size')) {
                    $page_size = $request->page_size;
                }

                $business_id = Session::get('business_id');
                $department_code = $request['department_code'];
                $start_date = $request['start_date'];
                $end_date = $request['end_date'];

                $validator = Validator::make($request->all(), []);
                if ($validator->fails()) {
                    return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
                } else {
                    $business = Business::where('id', $business_id)->select('id', 'pending_day')->first();
                    $start_date = Carbon::createFromFormat('d-m-Y', $request['start_date']);
                    $end_date = Carbon::createFromFormat('d-m-Y', $request['end_date']);

                    $start_date_work_day = Carbon::createFromFormat('d-m-Y', $request['start_date']);
                    $end_date_work_day = Carbon::createFromFormat('d-m-Y', $request['end_date']);
                    $start_date_overtime = Carbon::createFromFormat('d-m-Y', $request['start_date'])->subDays($business->pending_day);
                    $end_date_overtime = Carbon::createFromFormat('d-m-Y', $request['end_date'])->subDays($business->pending_day);

                    $result = $this->attendanceUtil->getAttendance(
                        $start_date_work_day,
                        $end_date_work_day,
                        $start_date_overtime,
                        $end_date_overtime,
                        $request['department_code'],
                    );

                    $attendance_tsos = collect([]);
                    foreach ($result['departments'] as $department) {
                        foreach ($department['employees'] as $employee) {
                            foreach ($employee['attendances'] as $attendance) {
                                foreach ($employee['shifts'] as $shift) {
                                    if ($shift['operational']['status']) {
                                        $attendance_tsos->push([
                                            'employee' => $employee['employee'],
                                            'timetable' => $shift['timetable'],
                                            "date" => $attendance['date'],
                                            "first_punch" => $shift['first_punch'],
                                            "last_punch" => $shift['last_punch'],
                                            'operational' => $shift['operational'],
                                            'is_holiday' => $attendance['is_holiday'],
                                        ]);
                                    }
                                }
                            }
                        }
                    }

                    return $this->buildRes->RESPONSE_REQ('success', $attendance_tsos, null);
                }
            }

            $department_bios = collect($this->attendanceUtil->getDepartment());
            return view('task.tso.index', compact('department_bios', 'department_code', 'start_date', 'end_date'));
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    public function store_approve_tso(Request $request)
    {
        // if (!auth()->user()->can('attendance-tso.approved') || !request()->ajax()) {
        //     abort(403, 'Unauthorized action.');
        // }

        try {
            $validator = Validator::make($request->all(), [
                'tso.*.tso_date' => 'required|date_format:Y-m-d',
                'tso.*.emp_id' => 'required',
                'tso.*.dept_id' => 'required',
                'tso.*.first_punch' => 'nullable|date_format:Y-m-d H:i:s',
                'tso.*.last_punch' => 'nullable|date_format:Y-m-d H:i:s',
            ]);
            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                foreach ($request['tso'] as $item) {
                    $employee = $this->service->read_employee($item['emp_id']);
                    $department = $this->service->read_department($item['dept_id']);
                    $attendance_tso = new AttendanceTso([
                        'tso_date' => Carbon::parse($item['tso_date'])->format('Y-m-d'),
                        'emp_id' => $employee['id'],
                        'emp_code' => $employee['emp_code'],
                        'first_name' => $employee['first_name'],
                        'last_name' => $employee['last_name'],
                        'photo' => $employee['photo'],
                        'dept_id' => $item['dept_id'],
                        'dept_code' => $department['dept_code'],
                        'dept_name' => $department['dept_name'],
                        'first_punch' => $item['first_punch'],
                        'last_punch' => $item['last_punch'],
                        'note' => $item['note'],
                        'operational_id' => $item['operational_id'],
                        'timetable_id' => $item['timetable_id'],
                    ]);
                    $attendance_tso->save();
                }

                // ** create activity log user
                ActivityLog::created_activity('Approved attendance', 'User ' . auth()->user()->username . ' approved TSO (tidak sesuai operasional)');
                return $this->buildRes->RESPONSE_REQ('success', null,  ['success' => ['Approved TSO succesfully']]);
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }
    public function destroy_approve_tso($id, Request $request)
    {
        // if (!auth()->user()->can('attendance-tso.approved') || !request()->ajax()) {
        //     abort(403, 'Unauthorized action.');
        // }

        try {
            $attendance_tso = AttendanceTso::where('id', $id)->first();
            if (!empty($attendance_tso)) {
                $attendance_tso->delete();

                // ** create activity log user
                ActivityLog::created_activity('Approved attendance', 'User ' . auth()->user()->username . ' approved TSO (tidak sesuai operasional) di batalkan');
                return $this->buildRes->RESPONSE_REQ('success', null,  ['success' => ['Cancel approved TSO succesfully']]);
            } else {
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    public function update_status_lb(Request $request)
    {
        // if (!auth()->user()->can('attendance-lb.set-status') || !request()->ajax()) {
        //     abort(403, 'Unauthorized action.');
        // }

        try {
            $validator = Validator::make($request->all(), [
                'lb.*.lb_date' => 'required',
                'lb.*.lb_status' => 'required|in:accept,cancel',
                'lb.*.emp_id' => 'required',
                'lb.*.dept_id' => 'required',
                'lb.*.first_punch' => 'required',
                'lb.*.last_punch' => 'required',
            ]);
            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                foreach ($request['lb'] as $item) {
                    $employee = $this->service->read_employee($item['emp_id']);
                    $department = $this->service->read_department($item['dept_id']);
                    $attendance_lb = new AttendanceLb([
                        'lb_date' => Carbon::parse($item['lb_date'])->format('Y-m-d'),
                        'lb_status' => $item['lb_status'],
                        'emp_id' => $item['emp_id'],
                        'emp_code' => $item['emp_code'],
                        'first_name' => $employee['first_name'],
                        'last_name' => $employee['last_name'],
                        'photo' => $employee['photo'],
                        'dept_id' => $item['dept_id'],
                        'dept_code' => $department['dept_code'],
                        'dept_name' => $department['dept_name'],
                        'first_punch' => $item['first_punch'],
                        'last_punch' => $item['last_punch'],
                        'operational_id' => $item['operational_id'],
                        'timetable_id' => $item['timetable_id'],
                    ]);
                    $attendance_lb->save();
                }

                // ** create activity log user
                ActivityLog::created_activity('Approved attendance', 'User ' . auth()->user()->username . ' approved TSO (tidak sesuai operasional)');
                return $this->buildRes->RESPONSE_REQ('success', null,  ['success' => ['Approved TSO succesfully']]);
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
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
