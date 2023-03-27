<?php

namespace App\Http\Controllers;

use App\Utils\Util;
use App\Models\Business;
use App\Models\FoodArchive;
use App\Models\FoodArchiveEmp;
use App\Models\FoodArchiveEmpAttendance;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;
use App\Models\SalaryArchive;
use Illuminate\Support\Carbon;
use App\Models\SalaryArchiveEmp;
use App\Services\Api\ApiServices;
use App\Models\SalaryArchiveEmpAttendance;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class PayrollReportController extends Controller
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
        Log::info('[' . request()->route()->getName() . ']::GET');

        if (!auth()->user()->can('payroll-report.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            if (request()->ajax()) {
                $datas = collect([]);
                $start_date = null;
                $end_date = null;

                $business_id = Session::get('business_id');
                $business = Business::where('id', $business_id)->select('id', 'pending_day')->first();

                if ($request->has('start_date') && $request->has('end_date')) {
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
                        $request['department_code'],
                    );

                    $render = view('report.payroll_report.table', compact('datas', 'start_date_work_day', 'end_date_work_day', 'start_date_overtime', 'end_date_overtime'))->render();
                    return $this->buildRes->RESPONSE_REQ('success', $render, null);
                } else {
                    $render = view('report.payroll_report.table', compact('datas', 'start_date_work_day', 'end_date_work_day', 'start_date_overtime', 'end_date_overtime'))->render();
                    return $this->buildRes->RESPONSE_REQ('success', $render, null);
                }
            }

            $department_bios = collect($this->service->get_departments(['page_size' => 999])['data']);
            return  view('report.payroll_report.index', compact('department_bios'));
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
        Log::info('[' . request()->route()->getName() . ']::GET');

        if (!auth()->user()->can('payroll-report.calculate')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $start_date = Carbon::parse($request->start_date);
            $end_date = Carbon::parse($request->end_date);
            $department_code = $request->department_code;
            $dates = $this->util->generateDateRange($start_date, $end_date);
            $departments = $this->service->get_departments(['page_size' => 999, 'dept_code' => $department_code])['data'];

            $render = view('report.payroll_report.calculation', compact('dates', 'start_date', 'end_date',  'departments', 'department_code'))->render();
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
        Log::info('[' . request()->route()->getName() . ']::POST');

        if (!auth()->user()->can('payroll-report.calculate')  || !request()->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $datas = collect([]);

            $business_id = Session::get('business_id');
            $business = Business::where('id', $business_id)->select('id', 'pending_day')->first();
            if ($request->has('start_date') && $request->has('end_date')) {
                $start_date_work_day = Carbon::createFromFormat('d-m-Y', $request['start_date']);
                $end_date_work_day = Carbon::createFromFormat('d-m-Y', $request['end_date']);
                $start_date_overtime = Carbon::createFromFormat('d-m-Y', $request['start_date'])->subDays($business->pending_day);
                $end_date_overtime = Carbon::createFromFormat('d-m-Y', $request['end_date'])->subDays($business->pending_day);

                $datas = app(PrintReportContoller::class)->getPayrollAttendanceReport(
                    $start_date_work_day,
                    $end_date_work_day,
                    $start_date_overtime,
                    $end_date_overtime,
                    $request['department_code'],
                );

                if (count($datas)) {
                    foreach ($datas as $data) {
                        // SALARY
                        $salary_archive = new SalaryArchive([
                            'business_id' => $business_id,
                            'start_date' => $data['start_date'],
                            'end_date' => $data['end_date'],
                            'start_date_work_day' => $data['start_date_work_day'],
                            'end_date_work_day' =>  $data['end_date_work_day'],
                            'start_date_overtime' => $data['start_date_overtime'],
                            'end_date_overtime' => $data['start_date_overtime'],
                            'dept_id' => $data['department']['id'],
                            'dept_code' => $data['department']['dept_code'],
                            'dept_name' =>  $data['department']['dept_name'],
                            'total_HK_value' => $data['total_HK_value'],
                            'total_JL_value' => $data['total_JL_value'],
                            'total_kasbon_pay_value' => $data['total_kasbon_pay_value'],
                            'total_salary_pay_value' => $data['total_salary_pay_value'],
                            'total_overtime_pay_value' => $data['total_overtime_pay_value'],
                            'total_tbhn_u_position_pay_value' => $data['total_tbhn_u_position_pay_value'],
                            'total_tbhn_u_libur_pay_value' => $data['total_tbhn_u_libur_pay_value'],
                            'grand_total_pay_value' => $data['grand_total_pay_value'],
                            'created_user' => auth()->user()->id,
                            'updated_user' => auth()->user()->id,
                        ]);
                        $salary_archive->save();
                        // FOOD
                        $food_archive = new FoodArchive([
                            'start_date' => $data['start_date'],
                            'end_date' => $data['end_date'],
                            'dept_id' => $data['department']['id'],
                            'dept_code' => $data['department']['dept_code'],
                            'dept_name' =>  $data['department']['dept_name'],
                            'total' => $data['total_food_value'],
                        ]);
                        $food_archive->save();

                        foreach ($data['attendance_reports'] as $report) {
                            $salary_archive_emp = new SalaryArchiveEmp([
                                'salary_archive_id' => $salary_archive->id,
                                'HK_value' => $report['HK_value'],
                                'JL_value' => $report['JL_value'],
                                'emp_id' => $report['employee']['id'],
                                'emp_code' => $report['employee']['emp_code'],
                                'first_name' =>  $report['employee']['first_name'],
                                'last_name' =>  $report['employee']['last_name'],
                                'photo' =>  $report['employee']['photo'],
                                'kasbon_pay_value' => $report['kasbon_pay_value'],
                                'remaining_kasbon_pay_value' => $report['remaining_kasbon_pay_value'],
                                'salary_pay_value' => $report['salary_pay_value'],
                                'overtime_pay_value' => $report['overtime_pay_value'],
                                'tbhn_u_position_pay_value' => $report['tbhn_u_position_pay_value'],
                                'tbhn_u_libur_pay_value' => $report['tbhn_u_libur_pay_value'],
                                'total_pay_value' => $report['total_pay_value'],
                            ]);

                            $salary_archive_emp->save();

                            // FOOD
                            $food_archive_emp = new FoodArchiveEmp([
                                'food_archive_id' => $food_archive->id,
                                'emp_id' => $report['employee']['id'],
                                'emp_code' => $report['employee']['emp_code'],
                                'first_name' =>  $report['employee']['first_name'],
                                'last_name' =>  $report['employee']['last_name'],
                                'photo' =>  $report['employee']['photo'],
                                'total' => $report['food_value'],
                            ]);
                            $food_archive_emp->save();
                            foreach ($report['attendances'] as $attendance) {
                                $salary_archive_emp_attendance = new SalaryArchiveEmpAttendance([
                                    'salary_archive_emp_id' => $salary_archive_emp->id,
                                    'timetable_id' => (!empty($attendance['timetable'])) ? $attendance['timetable']['id'] : null,
                                    'operational_id' => $attendance['operational_id'],
                                    'operational_has_timetable_id' => $attendance['operational_has_timetable_id'],
                                    'operational_plusm_value' => $attendance['operational_plusm_value'],
                                    'operational_status' => $attendance['operational_status'],
                                    'operational_note' => $attendance['operational_note'],
                                    'attendance_tso_id' => $attendance['attendance_tso_id'],
                                    'attendance_lb_id' => $attendance['attendance_lb_id'],
                                    'attendance_lb_status' => $attendance['attendance_lb_status'],

                                    'attendance_date' => $attendance['date'],
                                    'value_string' => $attendance['value_string'],
                                    'first_punch' => $attendance['first_punch'],
                                    'last_punch' => $attendance['last_punch'],
                                    'JL' => $attendance['JL'],
                                    'HK' => $attendance['HK'],
                                    'be_one_shift' => $attendance['be_one_shift'],
                                    'HK_pay_value' => $attendance['HK_pay_value'],
                                    'JL_pay_value' => $attendance['JL_pay_value'],
                                    'tbhn_u_libur_pay_value' => $attendance['tbhn_u_libur_pay_value'],
                                    'be_one_shift' => $attendance['be_one_shift'],
                                    'is_holiday' => $attendance['is_holiday'],
                                    'is_addition_date' => $attendance['is_addition_date'],
                                    'is_counting_salary' => $attendance['is_counting_salary'],
                                    'is_counting_overtime' => $attendance['is_counting_overtime'],
                                ]);
                                $salary_archive_emp_attendance->save();

                                // FOOD
                                $food_archive_emp_attendance = new FoodArchiveEmpAttendance([
                                    'food_archive_emp_id' => $food_archive_emp->id,
                                    'food_date' => $attendance['date'],
                                    'total' => $attendance['food'],
                                ]);
                                $food_archive_emp_attendance->save();
                            }
                        }
                    }
                } else {
                }
            } else {
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }
}
