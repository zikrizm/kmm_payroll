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
use App\Models\SalaryArchiveTdEmpShift;
use Illuminate\Support\Facades\DB;

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
                $render = view('report.payroll_report.invalid_calculation', compact('salary_archive'))->render();
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
            $result = collect([]);

            $business_id = Session::get('business_id');
            $business = Business::where('id', $business_id)->select('id', 'pending_day')->first();
            if ($request->has('start_date') && $request->has('end_date')) {
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

                DB::beginTransaction();

                

                foreach ($result['departments'] as $department) {
                    $latest_td_id = SalaryArchiveTh::whereDate('start_date_work_day', $result['start_date_work_day'])
                        ->whereDate('end_date_work_day', $result['end_date_work_day'])
                        ->where('dept_id', $department['department']['id'])
                        ->join('salary_archive_tds as td', 'td.salary_archive_th_id', '=', 'salary_archive_ths.id')
                        ->orderByDesc('td.created_at')
                        ->value('td.id');
                        
                    // SALARY
                    $salary_archive_th = SalaryArchiveTh::whereDate('start_date_work_day', $result['start_date_work_day'])
                        ->whereDate('end_date_work_day', $result['end_date_work_day'])
                        ->where('dept_id', $department['department']['id'])
                        ->first();

                    if (empty($salary_archive_th)) {
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

                        $salary_archive_th->save();
                    }

                    $salary_archive_td = new SalaryArchiveTd([
                        'salary_archive_th_id' => $salary_archive_th->id,
                        'total_HK_value' => $department['total_hk'],
                        'total_JL_value' => $department['total_jl'],
                        'total_kasbon_pay_value' => $department['total_loan_paid'],
                        'total_salary_pay_value' => $department['total_salary'],
                        'total_overtime_pay_value' => $department['total_overtime'],
                        'total_tbhn_u_position_pay_value' => $department['total_job_bonus'],
                        'total_tbhn_u_libur_pay_value' => $department['total_tbhn_plus_u_libur'],
                        'grand_total_pay_value' => $department['final_total'],
                        'created_user' => auth()->user()->id,
                        'updated_user' => auth()->user()->id,
                    ]);

                    $salary_archive_td->save();

                    // FOOD
                    $food_archive_th = FoodArchiveTh::whereDate('start_date', $result['start_date'])->whereDate('end_date', $result['end_date'])->where('dept_id', $department['department']['id'])->first();
                    if (empty($food_archive_th)) {
                        $food_archive_th = new FoodArchiveTh([
                            'start_date' => $result['start_date'],
                            'end_date' => $result['end_date'],
                            'dept_id' => $department['department']['id'],
                            'dept_code' => $department['department']['dept_code'],
                            'dept_name' =>  $department['department']['dept_name'],
                        ]);
                        
                        $food_archive_th->save();
                    }

                    $food_archive_td = new FoodArchiveTd([
                        'food_archive_th_id' => $food_archive_th->id,
                        'total' => $department['total_food'],
                        'created_user' => auth()->user()->id,
                        'updated_user' => auth()->user()->id,
                    ]);
                    $food_archive_td->save();

                    foreach ($department['employees'] as $employee) {
                        $kasbon_payment_id = null;
                        if($employee['employee_debt_id']) {
                            $lastest_kasbon_payment = SalaryArchiveTdEmp::where('emp_id', $employee['employee']['id'])
                                ->where('salary_archive_td_id', $latest_td_id)
                                ->first();

                            // Log::info($latest_td_id);
                            // Log::info($result['start_date_work_day']);
                            // Log::info($result['end_date_work_day']);
                            // Log::info( $department['department']['id']);
                            // Log::info($lastest_kasbon_payment);
                            // Log::info($employee['loan_installment_count']);

                            if ($lastest_kasbon_payment && $lastest_kasbon_payment->kasbon_payment_id) {
                                EmployeeDebtPay::where('id', $lastest_kasbon_payment->kasbon_payment_id)
                                    ->update([
                                        'payment' => $employee['total_loan_paid'],
                                        'debt_payment_date' => now(),
                                        'updated_user' => auth()->id(),
                                    ]);

                                $kasbon_payment_id =  $lastest_kasbon_payment->kasbon_payment_id;
                            } else {
                                $kasbon_payment_id = EmployeeDebtPay::insertGetId([
                                    'employee_debt_id' => $employee['employee_debt_id'],
                                    'payment' => $employee['total_loan_paid'],
                                    'debt_payment_date' => now(),
                                    'created_user' => auth()->id(),
                                    'updated_user' => auth()->id(),
                                ]);
                            }
                        } 

                        // Log::info($kasbon_payment_id);
                        
                        // SALARY
                        $salary_archive_td_emp = new SalaryArchiveTdEmp([
                            'salary_archive_td_id' => $salary_archive_td->id,
                            'HK_value' => $employee['total_hk'],
                            'JL_value' => $employee['total_jl'],
                            'emp_id' => $employee['employee']['id'],
                            'emp_code' => $employee['employee']['emp_code'],
                            'first_name' =>  $employee['employee']['first_name'],
                            'last_name' =>  $employee['employee']['last_name'],
                            'photo' =>  $employee['employee']['photo'],
                            'kasbon_pay_value' => $employee['total_loan_paid'],
                            'remaining_kasbon_pay_value' => $employee['total_loan_balance'],
                            'salary_pay_value' => $employee['total_salary'],
                            'overtime_pay_value' => $employee['total_overtime'],
                            'tbhn_u_position_pay_value' => $employee['total_job_bonus'],
                            'tbhn_u_libur_pay_value' => $employee['total_tbhn_plus_u_libur'],
                            'total_pay_value' => $employee['final_total'],
                            'kasbon_payment_id' => $kasbon_payment_id,
                        ]);
                        $salary_archive_td_emp->save();

                        // FOOD
                        $food_archive_td_emp = new FoodArchiveTdEmp([
                            'food_archive_td_id' => $food_archive_td->id,
                            'emp_id' => $employee['employee']['id'],
                            'emp_code' => $employee['employee']['emp_code'],
                            'first_name' =>  $employee['employee']['first_name'],
                            'last_name' =>  $employee['employee']['last_name'],
                            'photo' =>  $employee['employee']['photo'],
                            'total' => $employee['total_food'],
                        ]);
                        $food_archive_td_emp->save();

                        foreach ($employee['attendances'] as $attendance) {
                            // SALARY
                            $salary_archive_td_emp_attendance = new SalaryArchiveTdEmpAttendance([
                                'salary_td_emp_id' => $salary_archive_td_emp->id,
                                'attendance_date' => $attendance['date'],
                                'value_string' => $attendance['text_value'],
                                'JL' => $attendance['total_jl'],
                                'HK' => $attendance['total_hk'],
                                'be_one_shift' => $attendance['total_shifted_overtime'],
                                'HK_pay_value' => $attendance['total_day_salary'],
                                'JL_pay_value' => $attendance['total_overtime'],
                                'tbhn_u_libur_pay_value' => $attendance['total_tbhn_plus_u_libur'],
                                'is_holiday' => $attendance['is_holiday'],
                                'is_counting_salary' => $attendance['salary_included'],
                                'is_counting_overtime' => $attendance['overtime_included'],
                            ]);
                            $salary_archive_td_emp_attendance->save();

                            foreach ($attendance['shifts'] as $shift) {
                                $salary_archive_td_emp_shift = new SalaryArchiveTdEmpShift([
                                    'salary_td_emp_attendance_id' => $salary_archive_td_emp_attendance->id,
                                    
                                    'timetable_id' =>!empty($shift['timetable']) ? $shift['timetable']['id'] : null,
                                    'total_jl' => $shift['total_jl'],
                                    'total_hk' => $shift['total_hk'],
                                    'working_start_punch' => $shift['working']['start_punch'],
                                    'working_end_punch' => $shift['working']['end_punch'],
                                    'break_time_start_punch' => $shift['break_time']['start_punch'],
                                    'break_time_end_punch' => $shift['break_time']['end_punch'],
                                    'total_shifted_overtime' => $shift['total_shifted_overtime'],
                                    'total_hk_pay' => $shift['total_hk_pay'],
                                    'total_hk_bonus' => $shift['total_hk_bonus'],
                                    'total_overtime' => $shift['total_overtime'],
                                ]);

                                $salary_archive_td_emp_shift->save();
                            }

                            // FOOD
                            $food_archive_td_emp_attendance = new FoodArchiveTdEmpAttendance([
                                'food_archive_td_emp_id' => $food_archive_td_emp->id,
                                'food_date' => $attendance['date'],
                                'total' => $attendance['total_food'],
                            ]);
                            $food_archive_td_emp_attendance->save();
                        }
                    }
                }

                DB::commit();
            } else {
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }
}
