<?php

namespace App\Http\Controllers;


use App\Models\Business;
use App\Utils\Util;
use App\Models\Shift;
use App\Models\Holiday;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Department;
use App\Models\Operational;
use App\Models\RequestTask;
use App\Models\Transaction;
use App\Utils\ResponseUtil;
use App\Models\EmployeeDebt;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Services\Api\ApiServices;
use App\Models\EmployeeHasPosition;
use App\Models\EmployeeStatusLb;
use App\Models\EmployeeTso;
use App\Models\OtRicebill;
use App\Models\OtRicebillPerday;
use App\Models\SalaryArchive;
use App\Models\SalaryArchivePerday;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class PrintV2Controller extends Controller
{
    private $service;
    private $buildRes;
    private $util;

    public $slug_week = ['Mgg', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

    public function __construct(ApiServices $service, Util $util, ResponseUtil $buildRes)
    {
        $this->service = $service;
        $this->buildRes = $buildRes;
        $this->util = $util;
    }

    public function getMergeAttendance(Carbon $start_date, Carbon $end_date)
    {
        // MERGE DATA ATTENDANCE
        $attendancelocal = Transaction::whereBetween('punch_time', [$start_date, $end_date])->get();
        $attendancebios_count = $this->service->get_transactions([
            "start_time" =>  $start_date->hour(0)->minute(0)->second(0)->format('Y-m-d H:i:s'),
            "end_time" =>  $end_date->addDays(1)->hour(23)->minute(59)->second(59)->format('Y-m-d H:i:s'),
        ])['count'];
        $attendances = collect($this->service->get_transactions(array_merge(['page_size' => $attendancebios_count], [
            "start_time" =>  $start_date->hour(0)->minute(0)->second(0)->format('Y-m-d H:i:s'),
            "end_time" =>  $end_date->addDays(1)->hour(23)->minute(59)->second(59)->format('Y-m-d H:i:s'),
        ]))['data']);

        foreach ($attendancelocal as $key => $item) {
            // $item['id'] = $item['emp'];
            $attendances->push($item->toArray());
        }

        return $attendances;
    }

    public function getEmployee(int $department)
    {
        $empbios = [];
        if (!empty($department)) {
            $empbios_count = $this->service->get_employees(["departments" => $department])["count"];
            $empbios = $this->service->get_employees(["page_size" => $empbios_count, "departments" => $department])['data'];
        } else {
            $empbios_count = $this->service->get_employees([])["count"];
            $empbios = $this->service->get_employees(["page_size" => $empbios_count])['data'];
        }

        return $empbios;
    }

    public function getDiffPaymentEmp(string $payment_period, Carbon $start_date, Carbon $end_date)
    {
        switch ($payment_period) {
            case 'mounthly':
                return $start_date->diffInMonths($end_date);
                break;
            case 'weekly':
                return $start_date->diffInWeeks($end_date);
                break;
            case 'daily':
                return $start_date->diffInDays($end_date);
                break;
            default:
                return 0;
                break;
        }
    }

    public function getEmployeeKasbonPaid(Employee $emp, Carbon $start_date, Carbon $end_date)
    {
        $value = 0;
        $business_id = Session::get('business_id');
        $payment_period_diff = $this->getDiffPaymentEmp($emp->payment_period, $start_date, $end_date);

        $kasbons = EmployeeDebt::where('business_id', $business_id)
            ->where('paid', 0)
            ->where('emp_id', $emp->emp_id)
            ->whereDate('date', '>= ', $start_date)
            ->whereDate('date', '<= ', $end_date)
            ->with(['instalments' => fn ($query) => $query->select('id', 'employee_debt_id', 'date', 'instalment_debt')])->get();

        $total_cicilan_kasbon = $kasbons->sum('instalment');
        $total_kasbon = $kasbons->sum('debt');
        foreach ($kasbons as $item) {
            $value += ($item->instalments->isNotEmpty()) ? $item->instalments->sum('instalment_debt') : 0;
        }

        $remaining_kasbon = $total_kasbon - $total_cicilan_kasbon;
        $value = ($value > $total_cicilan_kasbon) ? $remaining_kasbon : $value * $payment_period_diff;

        return $value;
    }


    public function getEmployeeExtraPayPosition(Employee $emp,  Carbon $start_date, Carbon $end_date)
    {
        $position = Position::where('permanently', '!=', 0)->whereHas('employee_has_position.employee', function ($e) use ($emp) {
            $e->where('emp_id', $emp->emp_id);
        })->get();

        $payment_period_diff = $this->getDiffPaymentEmp($emp->payment_period, $start_date, $end_date);
        return $position->sum('extra_pay') * $payment_period_diff;
    }

    public function getRangeDate(Carbon $start_date, Carbon $end_date)
    {
        $business_id = Session::get('business_id');
        $holidays = Holiday::select('id', 'business_id', 'start_date', 'end_date')->where('business_id', $business_id)
            ->whereDate('start_date', '<=', $start_date)
            ->whereDate('end_date', '>=', $end_date)->get();

        $dates = [];
        for ($date = $start_date->copy(); $date->lte($end_date); $date->addDay()) {
            $data = [];
            $dayOfWeek = $date->dayOfWeek;
            $holiday = $holidays->whereDate('start_date', '<=', $date)
                ->whereDate('end_date', '>=', $date)->first();

            if ($date->isSunday() || !empty($holiday)) {
                $data['is_holiday'] = true;
            }
            $data['d'] = $date->format('d');
            $data['date'] = $date->format('Y-m-d');
            $data['slug'] = $this->slug_week[$dayOfWeek];

            $dates[] = $data;
        }

        return $dates;
    }

    public function index(Request $request)
    {
        $start_date = new Carbon();
        $end_date = new Carbon();
        $department = 1;
        $business_id = Session::get('business_id');
        $business = Business::where('id', $business_id)->first();

        $range_dates = $this->getRangeDate($start_date, $end_date);

        $attendances = $this->getMergeAttendance($start_date, $end_date);
        $attendances_grouping = $attendances->groupBy([
            fn ($item) => $item['emp'],
            fn ($item) => Carbon::parse($item['punch_time'])->format('Y-m-d'),
        ]);
        $empbios = $this->getEmployee($department);
        $empbios_grouping_by_dept = $empbios->groupBy([fn ($item) => $item['department']['id']]);

        // $departmentbios = collect($this->service->get_departments(["page_size" => 999])['data']);
        $departmentlocal = Department::select('id', 'dept_id', 'sitting_money', 'still_paid')->get();


        $operationals = Operational::where('business_id', $business_id)
            ->whereBetween('date', [$start_date, $end_date])->get();

        if (!empty($department)) {
            $shifts = Shift::where('business_id', $business_id)->where('dept_id', $department)->get();
        } else {
            $shifts = Shift::where('business_id', $business_id)->get();
        }

        foreach ($empbios_grouping_by_dept as $key_dept_id => $empdepts) {
            $empdept = $departmentlocal->where('dept_id', $key_dept_id)->first();
            $empshift = $shifts->where('dept_id', $key_dept_id)->first();
            $empoperationals = $operationals->where('dept_id', $key_dept_id)->get();

            foreach ($empdepts as $emp) {
                $attendance_employee = $attendances_grouping[$emp['id']];
                if ($attendance_employee && count($attendance_employee)) {
                    $emplocal = Employee::where('business_id', $business_id)
                        ->where('emp_id', $emp['id'])
                        ->select('business_id', 'emp_id', 'emp_code', 'daily_salary', 'payment_period')
                        ->first();

                    if ($emplocal->isNotEmpty()) {
                        $attendances = [];
                        $kasbon = $this->getEmployeeKasbonPaid($emplocal, $start_date, $end_date);
                        $positionExtraPay = $this->getEmployeeExtraPayPosition($emplocal, $start_date, $end_date);

                        $range_date_count = count($range_dates);
                        foreach ($range_dates as $key => $item) {
                            if ($range_date_count - 1 != $key) {
                                $date = Carbon::parse($item['date']);
                                $attendance_data = [
                                    'date' => $date,
                                    'JL_value' => 0,
                                    'HK_value' => 0,
                                    'value' => 0,
                                    'value_string' => 0,
                                    'be_one_shift' => 0,
                                ];

                                $attendance_employee_date = collect($attendance_employee[$date] ?? []);
                                if ($attendance_employee_date->isNotEmpty()) {
                                    // OPERATIONAL
                                    $operational_dates = $empoperationals->get($date);

                                    // ATTENDANCE
                                    $first = $attendance_employee_date->first();
                                    $last = $attendance_employee_date->last();
                                    $first_punch = Carbon::parse($first['punch_time']);
                                    $last_punch = Carbon::parse($last['punch_time']);

                                    $shiftdays = $empshift->shiftdays->where('code_day', $date->dayOfWeek)->first();
                                    if ($shiftdays->isNotEmpty()) {
                                        foreach ($shiftdays->shiftday_has_timetables as $key => $shiftday_has_timetable) {
                                            $timetable = $shiftday_has_timetable->timetable;
                                            $check_in = Carbon::parse($date . $timetable->check_in);
                                            $check_out = Carbon::parse($date . $timetable->check_out);

                                            $time_check_in_add_plusmn = $check_in->copy()->subMinutes($timetable->check_in_plusmn);
                                            $time_check_in_sub_plusmn = $check_in->copy()->addMinutes($timetable->check_in_plusmn);
                                            $time_check_out_add_plusmn = $check_in->copy()->subMinutes($timetable->check_out_plusmn);
                                            $time_check_out_sub_plusmn = $check_in->copy()->addMinutes($timetable->check_out_plusmn);

                                            $time_check_out_plus_ot =  $check_out->copy()->addHours($timetable->duration_ot_limit);
                                            $time_check_out_cross =  $check_out->copy()->addDays($timetable->cross_day ?? 0);

                                            if ($first_punch->between($time_check_in_sub_plusmn, $time_check_in_add_plusmn)) {
                                                $attendance_date_time_cross = collect();
                                                $attendance_date_time_uncross = collect();

                                                $next_date_key = $range_dates[$key + 1];
                                                if (!empty($attendance_employee_date->get($next_date_key))) {
                                                    foreach ($attendance_employee_date->get($next_date_key) as $attendance_emp_next_date) {
                                                        $check_in_next_date = Carbon::parse($attendance_emp_next_date['punch_time']);
                                                        if ($check_in_next_date->lte($time_check_out_plus_ot)) {
                                                            $attendance_date_time_cross[] = collect($attendance_emp_next_date);
                                                        } else {
                                                            $attendance_date_time_uncross[] = collect($attendance_emp_next_date);
                                                        }
                                                    }

                                                    if (!empty($attendance_date_time_cross)) {
                                                        $attendance_employee_date[$date]->push(...$attendance_date_time_cross);
                                                        $attendance_employee_date[$next_date_key] = collect($attendance_date_time_uncross);
                                                        // dirubah karena timetable nya ad CROSS-nya
                                                        // biar perhitungan jam keluarnya berubah
                                                        $last_punch = Carbon::parse($attendance_date_time_cross->last()['last_punch']);
                                                    }
                                                }

                                                if ($first_punch->lt($check_in)) {
                                                    $diff_time_in = $first_punch->diffInSeconds($check_in);
                                                    $minute = intval(gmdate('i', $diff_time_in));
                                                    $attendance_data['JL_value'] += intval(gmdate('G', $diff_time_in));
                                                    if ($timetable->ot_roundone_hr) {
                                                        if ($minute >= $timetable->ot_roundhalf_hr && $minute < $timetable->ot_roundone_hr) {
                                                            $attendance_data['JL_value'] += 0.5;
                                                        } else if ($minute >= $timetable->ot_roundone_hr) {
                                                            $attendance_data['JL_value']++;
                                                        }
                                                    }
                                                }

                                                if ($attendance_employee_date->count() > 1) {
                                                    if ($last_punch->gte($time_check_out_plus_ot)) {
                                                        $diff_time_out = $time_check_out_plus_ot->diffInSeconds($last_punch);
                                                        $minute = intval(gmdate('i', $diff_time_out));

                                                        $attendance_data['JL_value'] += intval(gmdate('G', $diff_time_out));
                                                        if ($timetable->ot_roundone_hr) {
                                                            if ($minute >= $timetable->ot_roundhalf_hr && $minute < $timetable->ot_roundone_hr) {
                                                                $attendance_data['JL_value'] += 0.5;
                                                            } else if ($minute >= $timetable->ot_roundone_hr) {
                                                                $attendance_data['JL_value']++;
                                                            }
                                                        }

                                                        if ($timetable->duration_count_one_shift) {
                                                            if ($timetable->duration_count_one_shift <= $attendance_data['JL_value']) {
                                                                $attendance_data['be_one_shift'] += floor($attendance_data['JL_value'] / ($timetable->duration_count_one_shift));
                                                                $attendance_data['JL_value'] = $attendance_data['JL_value'] - ($timetable->duration_count_one_shift * $timetable['be_one_shift']);
                                                            }
                                                        }
                                                    }

                                                    $break_times = $timetable->timetable_has_break_time;
                                                    $break_time_first = (!empty($break_times) && count($break_times)) ? $break_times->first() : null;
                                                    if ($break_time_first) {
                                                        $break_time_start = Carbon::parse($date . $break_time_first->break_time->start_time)->subMinutes($timetable->check_out_plusmn);
                                                        $break_time_end = Carbon::parse($date . $break_time_first->break_time->end_time);

                                                        $is_half_day = $last_punch->between($break_time_start, $break_time_end);
                                                        if ($last_punch->gt($break_time_start)) {
                                                            if ($is_half_day) {
                                                                if ($timetable->is_without_break) {
                                                                    $attendance_data['HK_value'] += 1;
                                                                } else {
                                                                    $attendance_data['HK_value'] += 0.5;
                                                                }
                                                            } else {
                                                                $attendance_data['HK_value'] += 1;
                                                            }
                                                        }
                                                    }
                                                }
                                                break;
                                            } else {
                                                //
                                            }
                                        }
                                    } else {
                                        //
                                    }

                                    // if($attendance_data['HK_value'])
                                } else {
                                    $operational_dates = $empoperationals->get($date);
                                    if ($operational_dates->isNotEmpty()) {
                                    } else {
                                        //
                                    }
                                }
                            }
                        }
                    } else {
                    }
                } else {
                }
            }
        }
    }
}
