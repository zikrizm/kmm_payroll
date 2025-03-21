<?php

namespace App\Http\Controllers;

use App\Utils\Util;
use App\Models\Business;
use App\Utils\ResponseUtil;
use App\Models\EmployeeDebt;
use Illuminate\Http\Request;
use App\Models\FoodArchiveTd;
use App\Models\FoodArchiveTh;
use App\Utils\AttendanceUtil;
use Illuminate\Support\Carbon;
use App\Models\EmployeeDebtPay;
use App\Models\SalaryArchiveTd;
use App\Models\SalaryArchiveTh;
use App\Models\FoodArchiveTdEmp;
use App\Services\Api\ApiServices;
use App\Models\SalaryArchiveTdEmp;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Models\FoodArchiveTdEmpAttendance;
use App\Models\SalaryArchiveTdEmpAttendance;

class PayrollReportController extends Controller
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

        if (!auth()->user()->can('payroll-report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = Session::get('business_id');
        $department_code = $request['department_code'];
        $start_date = $request['start_date'];
        $end_date = $request['end_date'];

        try {
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

                    $result = $this->attendanceUtil->getAttendance(
                        $start_date_work_day,
                        $end_date_work_day,
                        $start_date_overtime,
                        $end_date_overtime,
                        $request['department_code'],
                    );

                    $render = view('report.payroll_report.table', compact('result', 'start_date_work_day', 'end_date_work_day', 'start_date_overtime', 'end_date_overtime'))->render();
                    return $this->buildRes->RESPONSE_REQ('success', $render, null);
                } else {
                    $render = view('report.payroll_report.table', compact('result'))->render();
                    return $this->buildRes->RESPONSE_REQ('success', $render, null);
                }
            }

            $department_bios = collect($this->service->get_departments(['page_size' => 999])['data']);
            return view('report.payroll_report.index', compact('department_bios', 'department_code', 'start_date', 'end_date'));
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
            $start_date_format = Carbon::createFromFormat('d-m-Y', $request->start_date);
            $end_date_format = Carbon::createFromFormat('d-m-Y', $request->end_date);
            $department_code = $request->department_code;
            $dates = $this->util->generateDateRange($start_date, $end_date);
            $departments = $this->service->get_departments(['page_size' => 999, 'dept_code' => $department_code])['data'];

            $salary_archive = SalaryArchiveTh::whereDate('start_date_work_day', $start_date_format->format('Y-m-d'))->whereDate('end_date_work_day', $end_date_format->format('Y-m-d'))->where('dept_code', $request->department_code)->first();
            if (empty($salary_archive)) {
                $render = view('report.payroll_report.calculation', compact('dates', 'start_date', 'end_date',  'departments', 'department_code'))->render();
                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            } else {
                $render = view('report.payroll_report.invalid_calculation')->render();
                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            }
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
            // $datas = collect([]);
            $result = collect([]);

            $business_id = Session::get('business_id');
            $business = Business::where('id', $business_id)->select('id', 'pending_day')->first();
            if ($request->has('start_date') && $request->has('end_date')) {
                $start_date_work_day = Carbon::createFromFormat('d-m-Y', $request['start_date']);
                $end_date_work_day = Carbon::createFromFormat('d-m-Y', $request['end_date']);
                $start_date_overtime = Carbon::createFromFormat('d-m-Y', $request['start_date'])->subDays($business->pending_day);
                $end_date_overtime = Carbon::createFromFormat('d-m-Y', $request['end_date'])->subDays($business->pending_day);

                // $datas = app(PrintReportContoller::class)->getPayrollAttendanceReport(
                //     $start_date_work_day,
                //     $end_date_work_day,
                //     $start_date_overtime,
                //     $end_date_overtime,
                //     $request['department_code'],
                // );

                $result = $this->attendanceUtil->getAttendance(
                    $start_date_work_day,
                    $end_date_work_day,
                    $start_date_overtime,
                    $end_date_overtime,
                    $request['department_code'],
                );

                foreach ($result['departments'] as $department) {
                    // SALARY
                    $is_recalculate = false;
                    $salary_archive_th = SalaryArchiveTh::whereDate('start_date_work_day', $data['start_date_work_day'],)->whereDate('end_date_work_day', $data['end_date_work_day'])->where('dept_id', $data['department']['id'])->first();
                    if (empty($salary_archive_th)) {
                        $is_recalculate = false;
                        $salary_archive_th = new SalaryArchiveTh([
                            'business_id' => $business_id,
                            'start_date' => $result['start_date'],
                            'end_date' => $result['end_date'],
                            'start_date_work_day' => $result['start_date_work_day'],
                            'end_date_work_day' =>  $result['end_date_work_day'],
                            'start_date_overtime' => $result['start_date_overtime'],
                            'end_date_overtime' => $result['end_date_overtime'],
                            'dept_id' => $department['department']['id'],
                            'dept_code' => $department['department']['dept_code'],
                            'dept_name' =>  $department['department']['dept_name'],
                        ]);

                        // $salary_archive_th->save();
                    } else {
                        $is_recalculate = true;
                    }

                    // $salary_archive_td = new SalaryArchiveTd([
                    //     'salary_archive_th_id' => $salary_archive_th->id,
                    //     'total_HK_value' => $department['total_hk'],
                    //     'total_JL_value' => $department['total_jl'],
                    //     'total_kasbon_pay_value' => $department['total_loan_paid'],
                    //     'total_salary_pay_value' => $department['total_salary'],
                    //     'total_overtime_pay_value' => $department['total_overtime'],
                    //     'total_tbhn_u_position_pay_value' => $department['total_job_bonus'],
                    //     'total_tbhn_u_libur_pay_value' => $department['total_tbhn_plus_u_libur'],
                    //     'grand_total_pay_value' => $department['grand_total_pay_value'],
                    //     'created_user' => auth()->user()->id,
                    //     'updated_user' => auth()->user()->id,
                    // ]);
                    // $salary_archive_td->save();

                    // // FOOD
                    // $food_archive_th = FoodArchiveTh::whereDate('start_date', $result['start_date'])->whereDate('end_date', $result['end_date'])->where('dept_id', $data['department']['id'])->first();
                    // if (empty($food_archive_th)) {
                    //     $food_archive_th = new FoodArchiveTh([
                    //         'start_date' => $result['start_date'],
                    //         'end_date' => $result['end_date'],
                    //         'dept_id' => $department['department']['id'],
                    //         'dept_code' => $department['department']['dept_code'],
                    //         'dept_name' =>  $department['department']['dept_name'],
                    //     ]);
                        
                    //     $food_archive_th->save();
                    // }

                    // $food_archive_td = new FoodArchiveTd([
                    //     'food_archive_th_id' => $food_archive_th->id,
                    //     'total' => $department['total_food'],
                    //     'created_user' => auth()->user()->id,
                    //     'updated_user' => auth()->user()->id,
                    // ]);
                    // $food_archive_td->save();

                    // foreach ($department['employees'] as $employee) {

                    //     // SALARY
                    //     $salary_archive_td_emp = new SalaryArchiveTdEmp([
                    //         'salary_archive_td_id' => $salary_archive_td->id,
                    //         'HK_value' => $employee['total_hk'],
                    //         'JL_value' => $employee['total_jl'],
                    //         'emp_id' => $employee['employee']['id'],
                    //         'emp_code' => $employee['employee']['emp_code'],
                    //         'first_name' =>  $employee['employee']['first_name'],
                    //         'last_name' =>  $employee['employee']['last_name'],
                    //         'photo' =>  $employee['employee']['photo'],
                    //         'kasbon_pay_value' => $employee['total_loan_paid'],
                    //         'remaining_kasbon_pay_value' => $employee['total_loan_balance'],
                    //         'salary_pay_value' => $employee['total_salary'],
                    //         'overtime_pay_value' => $employee['total_overtime'],
                    //         'tbhn_u_position_pay_value' => $employee['total_job_bonus'],
                    //         'tbhn_u_libur_pay_value' => $employee['total_tbhn_plus_u_libur'],
                    //         'total_pay_value' => $employee['final_total'],
                    //     ]);
                    //     $salary_archive_td_emp->save();

                    //     // if (!$is_recalculate) {
                    //     //     $kasbons = EmployeeDebt::where('business_id', $business_id)
                    //     //         ->where('paid', 0)
                    //     //         ->where('emp_id', $employee['employee']['id'])
                    //     //         ->whereDate('date', '>= ', $result['start_date'])
                    //     //         ->whereDate('date', '<= ',  $result['end_date'])->get();

                    //     //     if($kasbons->count()) {
                    //     //         $pay = $report['kasbon_pay_value'] / $kasbons->count();
                    //     //         foreach ($kasbons as $kasbon) {
                    //     //             $kasbon_pay = new EmployeeDebtPay([
                    //     //                 'employee_debt_id' => $kasbon->id,
                    //     //                 'debt_payment_date' => new Carbon(),
                    //     //                 'payment' => $pay,
                    //     //                 'created_user' => auth()->user()->id,
                    //     //                 'updated_user' => auth()->user()->id,
                    //     //             ]);

                    //     //             $kasbon_pay->save();
                    //     //         }
                    //     //     }
                    //     // }
                        
                    //     // FOOD
                    //     $food_archive_td_emp = new FoodArchiveTdEmp([
                    //         'food_archive_td_id' => $food_archive_td->id,
                    //         'emp_id' => $employee['employee']['id'],
                    //         'emp_code' => $employee['employee']['emp_code'],
                    //         'first_name' =>  $employee['employee']['first_name'],
                    //         'last_name' =>  $employee['employee']['last_name'],
                    //         'photo' =>  $employee['employee']['photo'],
                    //         'total' => $employee['total_food'],
                    //     ]);
                    //     $food_archive_td_emp->save();

                    //     foreach ($employee['attendances'] as $attendance) {

                    //         // SALARY
                    //         $salary_archive_td_emp_attendance = new SalaryArchiveTdEmpAttendance([
                    //             'salary_td_emp_id' => $salary_archive_td_emp->id,
                                
                    //             'attendance_date' => $attendance['date'],
                    //             'value_string' => $attendance['value_string'],
                    //             'JL' => $attendance['total_jl'],
                    //             'HK' => $attendance['total_hk'],
                    //             'be_one_shift' => $attendance['total_shifted_overtime'],
                    //             'HK_pay_value' => $attendance['total_day_salary'],
                    //             'JL_pay_value' => $attendance['total_overtime'],
                    //             'tbhn_u_libur_pay_value' => $attendance['total_tbhn_plus_u_libur'],
                    //             'is_holiday' => $attendance['is_holiday'],
                    //             'is_counting_salary' => $attendance['salary_included'],
                    //             'is_counting_overtime' => $attendance['overtime_included'],
                    //         ]);
                    //         $salary_archive_td_emp_attendance->save();

                    //         foreach ($attendance['shifts'] as $shift) {
                    //             $salary_archive_td_emp_shift = new SalaryArchiveTdEmpShift([
                    //                 'salary_td_emp_attendance_id' => $salary_archive_td_emp_attendance->id,
                                    
                    //                 'timetable_id' => $shift['timetable']['id'],
                    //                 'working_start_punch' => $shift['working']['start_punch'],
                    //                 'working_end_punch' => $shift['working']['end_punch'],
                    //                 'break_time_start_punch' => $shift['break_time']['start_punch'],
                    //                 'break_time_end_punch' => $shift['break_time']['end_punch'],
                    //                 'total_shifted_overtime' => $shift['total_shifted_overtime'],
                    //                 'total_hk_pay' => $shift['total_hk_pay'],
                    //                 'total_hk_bonus' => $shift['total_hk_bonus'],
                    //                 'total_overtime' => $shift['total_overtime'],
                    //             ]);

                    //             $salary_archive_td_emp_shift->save();
                    //         }

                    //         // FOOD
                    //         $food_archive_td_emp_attendance = new FoodArchiveTdEmpAttendance([
                    //             'food_archive_td_emp_id' => $food_archive_td_emp->id,
                    //             'food_date' => $attendance['date'],
                    //             'total' => $attendance['total_food'],
                    //         ]);
                    //         $food_archive_td_emp_attendance->save();
                    //     }
                    // }
                }

                // $start_date_work_day = Carbon::createFromFormat('d-m-Y', $request['start_date']);
                // $end_date_work_day = Carbon::createFromFormat('d-m-Y', $request['end_date']);
                // $start_date_overtime = Carbon::createFromFormat('d-m-Y', $request['start_date'])->subDays($business->pending_day);
                // $end_date_overtime = Carbon::createFromFormat('d-m-Y', $request['end_date'])->subDays($business->pending_day);

                // $datas = app(PrintReportContoller::class)->getPayrollAttendanceReport(
                //     $start_date_work_day,
                //     $end_date_work_day,
                //     $start_date_overtime,
                //     $end_date_overtime,
                //     $request['department_code'],
                // );

                // if (count($datas)) {
                //     foreach ($datas as $data) {

                //         // SALARY
                //         $is_recalculate = false;
                //         $salary_archive_th = SalaryArchiveTh::whereDate('start_date_work_day', $data['start_date_work_day'],)->whereDate('end_date_work_day', $data['end_date_work_day'])->where('dept_id', $data['department']['id'])->first();
                //         if (empty($salary_archive_th)) {
                //             $is_recalculate = false;
                //             $salary_archive_th = new SalaryArchiveTh([
                //                 'business_id' => $business_id,
                //                 'start_date' => $data['start_date'],
                //                 'end_date' => $data['end_date'],
                //                 'start_date_work_day' => $data['start_date_work_day'],
                //                 'end_date_work_day' =>  $data['end_date_work_day'],
                //                 'start_date_overtime' => $data['start_date_overtime'],
                //                 'end_date_overtime' => $data['end_date_overtime'],
                //                 'dept_id' => $data['department']['id'],
                //                 'dept_code' => $data['department']['dept_code'],
                //                 'dept_name' =>  $data['department']['dept_name'],
                //             ]);
                //             $salary_archive_th->save();
                //         } else {
                //             $is_recalculate = true;
                //         }

                //         $salary_archive_td = new SalaryArchiveTd([
                //             'salary_archive_th_id' => $salary_archive_th->id,
                //             'total_HK_value' => $data['total_HK_value'],
                //             'total_JL_value' => $data['total_JL_value'],
                //             'total_kasbon_pay_value' => $data['total_kasbon_pay_value'],
                //             'total_salary_pay_value' => $data['total_salary_pay_value'],
                //             'total_overtime_pay_value' => $data['total_overtime_pay_value'],
                //             'total_tbhn_u_position_pay_value' => $data['total_tbhn_u_position_pay_value'],
                //             'total_tbhn_u_libur_pay_value' => $data['total_tbhn_u_libur_pay_value'],
                //             'grand_total_pay_value' => $data['grand_total_pay_value'],
                //             'created_user' => auth()->user()->id,
                //             'updated_user' => auth()->user()->id,
                //         ]);
                //         $salary_archive_td->save();

                //         // FOOD
                //         $food_archive_th = FoodArchiveTh::whereDate('start_date', $data['start_date'],)->whereDate('end_date', $data['end_date'])->where('dept_id', $data['department']['id'])->first();
                //         if (empty($food_archive_th)) {
                //             $food_archive_th = new FoodArchiveTh([
                //                 'start_date' => $data['start_date'],
                //                 'end_date' => $data['end_date'],
                //                 'dept_id' => $data['department']['id'],
                //                 'dept_code' => $data['department']['dept_code'],
                //                 'dept_name' =>  $data['department']['dept_name'],
                //             ]);
                //             $food_archive_th->save();
                //         }

                //         $food_archive_td = new FoodArchiveTd([
                //             'food_archive_th_id' => $food_archive_th->id,
                //             'total' => $data['total_food_value'],
                //             'created_user' => auth()->user()->id,
                //             'updated_user' => auth()->user()->id,
                //         ]);
                //         $food_archive_td->save();

                //         foreach ($data['attendance_reports'] as $report) {

                //             // SALARY
                //             $salary_archive_td_emp = new SalaryArchiveTdEmp([
                //                 'salary_archive_td_id' => $salary_archive_td->id,
                //                 'HK_value' => $report['HK_value'],
                //                 'JL_value' => $report['JL_value'],
                //                 'emp_id' => $report['employee']['id'],
                //                 'emp_code' => $report['employee']['emp_code'],
                //                 'first_name' =>  $report['employee']['first_name'],
                //                 'last_name' =>  $report['employee']['last_name'],
                //                 'photo' =>  $report['employee']['photo'],
                //                 'kasbon_pay_value' => $report['kasbon_pay_value'],
                //                 'remaining_kasbon_pay_value' => $report['remaining_kasbon_pay_value'],
                //                 'salary_pay_value' => $report['salary_pay_value'],
                //                 'overtime_pay_value' => $report['overtime_pay_value'],
                //                 'tbhn_u_position_pay_value' => $report['tbhn_u_position_pay_value'],
                //                 'tbhn_u_libur_pay_value' => $report['tbhn_u_libur_pay_value'],
                //                 'total_pay_value' => $report['total_pay_value'],
                //             ]);
                //             $salary_archive_td_emp->save();

                //             if (!$is_recalculate) {
                //                 $kasbons = EmployeeDebt::where('business_id', $business_id)
                //                     ->where('paid', 0)
                //                     ->where('emp_id', $report['employee']['id'])
                //                     ->whereDate('date', '>= ', $data['start_date'])
                //                     ->whereDate('date', '<= ',  $data['end_date'])->get();

                //                     Log::info($kasbons);
                //                 if($kasbons->count()) {
                //                     $pay = $report['kasbon_pay_value'] / $kasbons->count();
                //                     foreach ($kasbons as $kasbon) {
                //                         $kasbon_pay = new EmployeeDebtPay([
                //                             'employee_debt_id' => $kasbon->id,
                //                             'debt_payment_date' => new Carbon(),
                //                             'payment' => $pay,
                //                             'created_user' => auth()->user()->id,
                //                             'updated_user' => auth()->user()->id,
                //                         ]);
    
                //                         $kasbon_pay->save();
                //                     }
                //                 }
                                
                //             }
                            
                //             // FOOD
                //             $food_archive_td_emp = new FoodArchiveTdEmp([
                //                 'food_archive_td_id' => $food_archive_td->id,
                //                 'emp_id' => $report['employee']['id'],
                //                 'emp_code' => $report['employee']['emp_code'],
                //                 'first_name' =>  $report['employee']['first_name'],
                //                 'last_name' =>  $report['employee']['last_name'],
                //                 'photo' =>  $report['employee']['photo'],
                //                 'total' => $report['food_value'],
                //             ]);
                //             $food_archive_td_emp->save();

                //             foreach ($report['attendances'] as $attendance) {

                //                 // SALARY
                //                 $salary_archive_td_emp_attendance = new SalaryArchiveTdEmpAttendance([
                //                     'salary_td_emp_id' => $salary_archive_td_emp->id,
                //                     'timetable_id' => (!empty($attendance['timetable'])) ? $attendance['timetable']['id'] : null,
                //                     'operational_id' => $attendance['operational_id'],
                //                     'operational_has_timetable_id' => $attendance['operational_has_timetable_id'],
                //                     'operational_plusm_value' => $attendance['operational_plusm_value'],
                //                     'operational_status' => $attendance['operational_status'],
                //                     'operational_note' => $attendance['operational_note'],
                //                     'attendance_tso_id' => $attendance['attendance_tso_id'],
                //                     'attendance_lb_id' => $attendance['attendance_lb_id'],
                //                     'attendance_lb_status' => $attendance['attendance_lb_status'],

                //                     'attendance_date' => $attendance['date'],
                //                     'value_string' => $attendance['value_string'],
                //                     'first_punch' => $attendance['first_punch'],
                //                     'last_punch' => $attendance['last_punch'],
                //                     'JL' => $attendance['JL'],
                //                     'HK' => $attendance['HK'],
                //                     'be_one_shift' => $attendance['be_one_shift'],
                //                     'HK_pay_value' => $attendance['HK_pay_value'],
                //                     'JL_pay_value' => $attendance['JL_pay_value'],
                //                     'tbhn_u_libur_pay_value' => $attendance['tbhn_u_libur_pay_value'],
                //                     'be_one_shift' => $attendance['be_one_shift'],
                //                     'is_holiday' => $attendance['is_holiday'],
                //                     'is_addition_date' => $attendance['is_addition_date'],
                //                     'is_counting_salary' => $attendance['is_counting_salary'],
                //                     'is_counting_overtime' => $attendance['is_counting_overtime'],
                //                 ]);
                //                 $salary_archive_td_emp_attendance->save();

                //                 // FOOD
                //                 $food_archive_td_emp_attendance = new FoodArchiveTdEmpAttendance([
                //                     'food_archive_td_emp_id' => $food_archive_td_emp->id,
                //                     'food_date' => $attendance['date'],
                //                     'total' => $attendance['food'],
                //                 ]);
                //                 $food_archive_td_emp_attendance->save();
                //             }
                //         }
                //     }
                // } else {
                // }
            } else {
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }
}
