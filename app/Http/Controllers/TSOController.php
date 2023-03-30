<?php

namespace App\Http\Controllers;

use App\Utils\Util;
use App\Models\Business;
use App\Models\ActivityLog;
use App\Models\AttendanceLb;
use App\Utils\ResponseUtil;
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
    private $util;

    public function __construct(ApiServices $service, Util $util, ResponseUtil $buildRes)
    {
        $this->service = $service;
        $this->buildRes = $buildRes;
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
        if (!auth()->user()->can('TSO.view')) {
            abort(403, 'Unauthorized action.');
        }

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

                $validator = Validator::make($request->all(), []);
                if ($validator->fails()) {
                    return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
                } else {
                    $reqdata = $request->only(['deparment_code', 'start_date', 'end_date']);
                    $start_date = null;
                    $end_date = null;

                    $business_id = Session::get('business_id');
                    $business = Business::where('id', $business_id)->select('id', 'pending_day')->first();

                    $start_date = Carbon::createFromFormat('d-m-Y', $request['start_date']);
                    $end_date = Carbon::createFromFormat('d-m-Y', $request['end_date']);

                    $start_date_work_day = Carbon::createFromFormat('d-m-Y', $request['start_date']);
                    $end_date_work_day = Carbon::createFromFormat('d-m-Y', $request['end_date']);
                    $start_date_overtime = Carbon::createFromFormat('d-m-Y', $request['start_date'])->subDays($business->pending_day);
                    $end_date_overtime = Carbon::createFromFormat('d-m-Y', $request['end_date'])->subDays($business->pending_day);

                    $datas = app(PrintReportContoller::class)->getPayrollAttendanceReport(
                        $start_date_work_day,
                        $end_date_work_day,
                        $start_date_overtime,
                        $end_date_overtime,
                        $request['deparment_code'],
                    );

                    $attendance_tsos = collect([]);
                    foreach ($datas as $data) {
                        foreach ($data['attendance_reports'] as $report) {
                            foreach ($report['attendances'] as $attendance) {
                                if ($attendance['operational_status'] == 'invalid' || $attendance['attendance_lb_status'] == 'accept') {
                                    $attendance_tsos->push([
                                        'employee' => $report['employee'],
                                        'timetable' => $attendance['timetable'],
                                        "date" => $attendance['date'],
                                        "first_punch" => $attendance['first_punch'],
                                        "last_punch" => $attendance['last_punch'],
                                        'operational_id' => $attendance['operational_id'],
                                        'operational_has_timetable_id' => $attendance['operational_has_timetable_id'],
                                        'operational_plusm_value' => $attendance['operational_plusm_value'],
                                        'operational_status' => $attendance['operational_status'],
                                        'operational_note' => $attendance['operational_note'],

                                        'attendance_tso_id' => $attendance['attendance_tso_id'],

                                        'attendance_lb_id' => $attendance['attendance_lb_id'],
                                        'attendance_lb_status' => $attendance['attendance_lb_status'],
                                        'is_holiday' => $attendance['is_holiday'],
                                    ]);
                                }
                            }
                        }
                    }

                    return $this->buildRes->RESPONSE_REQ('success', $attendance_tsos, null);
                }
            }

            $department_bios = collect($this->service->get_departments(['page_size' => 999])['data']);
            return view('task.tso.index', compact('department_bios'));
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    public function tso_store(Request $request)
    {
        // if (!auth()->user()->can('attendance-tso.approved') || !request()->ajax()) {
        //     abort(403, 'Unauthorized action.');
        // }

        try {
            $validator = Validator::make($request->all(), [
                'tso.*.tso_date' => 'required',
                'tso.*.emp_id' => 'required',
                'tso.*.dept_id' => 'required',
                'tso.*.first_punch' => 'required',
                'tso.*.last_punch' => 'required',
            ]);
            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                foreach ($request['tso'] as $item) {
                    $employee = $this->service->read_employee( $item['emp_id']);
                    $department = $this->service->read_department( $item['dept_id']);
                    $attendance_tso = new AttendanceTso([
                        'tso_date' => Carbon::parse($item['tso_date'])->format('Y-m-d'),
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
                    $employee = $this->service->read_employee( $item['emp_id']);
                    $department = $this->service->read_department( $item['dept_id']);
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
