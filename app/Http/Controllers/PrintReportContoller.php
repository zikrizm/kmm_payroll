<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLb;
use App\Models\AttendanceTso;
use App\Models\Shift;
use App\Models\Holiday;
use App\Models\Business;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Timetable;
use App\Models\Department;
use App\Models\EmployeeTso;
use App\Models\Operational;
use App\Models\Transaction;
use App\Models\EmployeeDebt;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\EmployeeStatusLb;
use App\Models\SalaryArchiveTh;
use App\Services\Api\ApiServices;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class PrintReportContoller extends Controller
{
    private $service;

    public $slug_week = ['Mgg', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

    public function __construct(ApiServices $service)
    {
        $this->service = $service;
    }

    public function getMergeAttendance(Carbon $start_date, Carbon $end_date)
    {
        // MERGE DATA ATTENDANCE
        $attendancelocal = Transaction::whereBetween('punch_time', [$start_date, $end_date])->orderBy('punch_time', 'ASC')->get();
        $attendancebios_count = $this->service->get_transactions([
            "start_time" =>  $start_date->copy()->startOfDay()->format('Y-m-d H:i:s'),
            "end_time" =>  $end_date->addDays(1)->endOfDay()->format('Y-m-d H:i:s'),
        ])['count'];
        $attendances = collect($this->service->get_transactions(array_merge(['page_size' => $attendancebios_count], [
            "start_time" =>  $start_date->copy()->startOfDay()->format('Y-m-d H:i:s'),
            "end_time" =>  $end_date->addDays(1)->endOfDay()->format('Y-m-d H:i:s'),
        ]))['data']);

        foreach ($attendancelocal as $key => $item) {
            // $item['id'] = $item['emp'];
            $attendances->push($item);
        }

        return $attendances->sortBy('punch_time');
    }

    public function getEmployee(array|null $department)
    {
        $empbios = [];
        if (!empty($department)) {
            $empbios_count = $this->service->get_employees(["department" => $department['id']])["count"];
            $empbios = collect($this->service->get_employees(["page_size" => $empbios_count, "department" => $department['id']])['data']);
        } else {
            $empbios_count = $this->service->get_employees([])["count"];
            $empbios = collect($this->service->get_employees(["page_size" => $empbios_count])['data']);
        }

        return collect($empbios);
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
            ->with(['employee_debt_pays' => fn ($query) => $query->select('id', 'employee_debt_id', 'debt_payment_date', 'payment')])->get();

        // TOTAL CICILAN YANG AKAN DI BAYAR
        $total_cicilan_kasbon = $kasbons->sum('instalment');
        $total_kasbon = $kasbons->sum('debt');
        foreach ($kasbons as $item) {
            $value += ($item->employee_debt_pays->isNotEmpty()) ? $item->employee_debt_pays->sum('payment') : 0;
        }

        $remaining_kasbon = $total_kasbon - $value;
        $kasbon_pay = ((($total_cicilan_kasbon * $payment_period_diff) + $value) > $total_kasbon) ? $remaining_kasbon : $total_cicilan_kasbon * $payment_period_diff;

        return ['remaining_kasbon' => $remaining_kasbon, 'kasbon_pay' => $kasbon_pay];
    }


    public function getEmployeeExtraPayPosition(Employee $emp,  Carbon $start_date, Carbon $end_date)
    {
        $position = Position::where('permanently', '!=', 0)->whereHas('employee_has_position.employee', function ($e) use ($emp) {
            $e->where('emp_id', $emp->emp_id);
        })->get();

        $payment_period_diff = $this->getDiffPaymentEmp($emp->payment_period, $start_date, $end_date);
        return $position->sum('extra_pay') * $payment_period_diff;
    }

    public function getRangeDate(
        Business $business,
        Carbon $start_date_work_day,
        Carbon $end_date_work_day,
        Carbon $start_date_overtime,
        Carbon $end_date_overtime
    ) {
        if ($start_date_work_day->lte($start_date_overtime)) {
            $start_date = $start_date_work_day->subDay()->copy();
        } else {
            $start_date = $start_date_overtime->subDay()->copy();
        }

        if ($end_date_work_day->gte($end_date_overtime)) {
            $end_date = $end_date_work_day->addDay()->copy();
        } else {
            $end_date = $end_date_overtime->addDay()->copy();
        }

        $dates = collect();

        for ($date = $start_date->copy(); $date->lte($end_date); $date->addDay()) {
            $data = collect();
            $dayOfWeek = $date->dayOfWeek;

            $holidays = Holiday::select('id', 'business_id', 'start_date', 'end_date')->where('business_id', $business->id)
                ->whereDate('start_date', '<=', $date)->whereDate('end_date', '>=', $date)->get();
            if ($date->isSunday() || $holidays->isNotEmpty()) {
                $data['is_holiday'] = true;
            } else {
                $data['is_holiday'] = false;
            }

            if ($date->isSameDay($start_date) || $date->isSameDay($end_date)) {
                $data['is_addition_date'] = true;
                $data['is_counting_salary'] = false;
                $data['is_counting_overtime'] = false;
            } else {
                $data['is_addition_date'] = false;

                if ($date->between($start_date_work_day, $end_date_work_day)) {
                    $data['is_counting_salary'] = true;
                } else {
                    $data['is_counting_salary'] = false;
                }

                if ($date->between($start_date_overtime, $end_date_overtime)) {
                    $data['is_counting_overtime'] = true;
                } else {
                    $data['is_counting_overtime'] = false;
                }
            }

            $data['d'] = $date->format('d');
            $data['date'] = $date->format('Y-m-d');
            $data['key'] = $this->slug_week[$dayOfWeek];
            $dates->push($data);
        }
        return $dates;
    }


    public function countingOvertime(
        Collection $range_date,
        Carbon $first_punch,
        Carbon $last_punch,
        Timetable $timetable,
        int $length_attendance_emp,
    ) {
        $overtime_data = ['JL_pay_value' => 0, 'JL' => 0, 'be_one_shift' => 0];
        $date = Carbon::parse($range_date['date']);

        $check_in = Carbon::parse($date->format('Y-m-d') . $timetable->check_in);
        $check_out = Carbon::parse($date->format('Y-m-d') . $timetable->check_out);
        $check_out_plus_cross_day =  $check_out->copy()->addDays($timetable->cross_day ?? 0);

        if ($first_punch->lt($check_in)) {
            $diffInSeconds = $first_punch->diffInSeconds($check_in);
            $minute = intval(gmdate('i', $diffInSeconds));
            $overtime_data['JL'] += intval(gmdate('G', $diffInSeconds));
            if ($timetable->ot_roundone_hr) {
                if ($minute >= $timetable->ot_roundhalf_hr && $minute < $timetable->ot_roundone_hr) {
                    $overtime_data['JL'] += 0.5;
                } else if ($minute >= $timetable->ot_roundone_hr) {
                    $overtime_data['JL']++;
                }
            }
        }

        if ($length_attendance_emp > 1 && $last_punch->gte($check_out_plus_cross_day)) {


            $diffInSeconds = $last_punch->diffInSeconds($check_out_plus_cross_day);
            $minute = intval(gmdate('i', $diffInSeconds));
            $overtime_data['JL'] += intval(gmdate('G', $diffInSeconds));
            if ($timetable->ot_roundone_hr) {
                if ($minute >= $timetable->ot_roundhalf_hr && $minute < $timetable->ot_roundone_hr) {
                    $overtime_data['JL'] += 0.5;
                } else if ($minute >= $timetable->ot_roundone_hr) {
                    $overtime_data['JL']++;
                }
            }

            if ($timetable->duration_count_one_shift) {
                if ($timetable->duration_count_one_shift <= $overtime_data['JL']) {
                    $overtime_data['be_one_shift'] += floor($overtime_data['JL'] / ($timetable->duration_count_one_shift));
                    $overtime_data['JL'] = $overtime_data['JL'] - ($timetable->duration_count_one_shift * $overtime_data['be_one_shift']);
                }
            }

            if ($overtime_data['be_one_shift'] > 0) {
                $lembur = $overtime_data['JL'] * ($overtime_data['be_one_shift'] + 1);
            } else {
                $lembur = $overtime_data['JL'];
            }

            if (!empty($timetable->duration_rice_shift) && $lembur > $timetable->duration_rice_shift) {
                $timetable['food'] += floor($lembur / $timetable->duration_rice_shift);
            }
        }

        if ($timetable->ot_period && $timetable->ot_pay) {
            $overtime_data['JL_pay_value'] += ((($overtime_data['JL'] ?? 0) * 60) / $timetable->ot_period) * $timetable->ot_pay;
        } else {
            $overtime_data['JL_pay_value'] += $overtime_data['JL'];
        }

        return collect($overtime_data);
    }

    public function countingSalary(
        Department $department_local,
        Collection $range_date,
        Carbon $first_punch,
        Carbon $last_punch,
        Timetable $timetable,
    ) {
        $salary_data = ['HK' => 0];
        $date = Carbon::parse($range_date['date']);

        $check_in = Carbon::parse($date->format('Y-m-d') . $timetable->check_in);
        $check_out = Carbon::parse($date->format('Y-m-d') . $timetable->check_out);
        $check_out_plusmn =  $check_out->copy()->addHours($timetable->check_out_plusmn);
        if ($range_date['is_holiday'] && $department_local->still_paid) {
            $salary_data['HK']++;
        }

        if ($last_punch->gte($check_out) || ($timetable->check_out_plusmn && $last_punch->gte($check_out_plusmn))) {
            $salary_data['HK']++;
        } else {
            $break_times = $timetable->timetable_has_break_time;
            $break_time_first = (!empty($break_times) && $break_times->count()) ? $break_times->first() : null;
            if (!empty($break_time_first)) {
                $break_time_start = Carbon::parse($date->format('Y-m-d') . $break_time_first->break_time->start_time)->subMinutes($timetable->check_out_plusmn);
                $break_time_end = Carbon::parse($date->format('Y-m-d') . $break_time_first->break_time->end_time);

                $is_half_day = $last_punch->between($break_time_start, $break_time_end);
                if ($last_punch->gt($break_time_start)) {
                    if ($is_half_day) {
                        if ($timetable->is_without_break) {
                            $salary_data['HK']++;
                        } else {
                            $salary_data['HK'] += 0.5;
                        }
                    } else {
                        $salary_data['HK']++;
                    }
                }
            }
        }

        return collect($salary_data);
    }

    public function checkAttendaceOperational(Carbon $date, Operational $operational_date, array $attendance_data)
    {
        $attendance_data['operational_id'] = $operational_date->id;
        foreach ($operational_date->operational_has_timetables as $timetable_operational) {
            if (!empty($attendance_data['timetable']) && $attendance_data['timetable']['id'] == $timetable_operational->timetable_id) {
                $attendance_data['operational_has_timetable_id'] = $timetable_operational->id;

                if ($timetable_operational->status == 'active') {
                    $last_punch = Carbon::parse($attendance_data['last_punch']);

                    $checkout_add_plusmn = Carbon::parse($date->format('Y-m-d') . $attendance_data['timetable']['check_out'])->subMinutes($attendance_data['timetable']['check_out_plusmn']);
                    // $diffInHours = $checkout_plus_ot_limit->diffInHours($last_punch, false);
                    if ($attendance_data['be_one_shift'] > 0) {
                        $lembur = $attendance_data['JL'] * ($attendance_data['be_one_shift'] + 1);
                    } else {
                        $lembur = $attendance_data['JL'];
                    }

                    if ($last_punch->gte($checkout_add_plusmn)) {
                        if ($lembur == $timetable_operational->ot_limit) {
                            // OPERATIONAL SUDAH SEASUAI
                            $attendance_data['operational_status'] = 'valid';
                            $attendance_data['operational_note'] = 'Sudah sesuai, karena (KARYAWAN SESUAI DENGAN SHIFT DAN TIDAK ± DARI BATAS WAKTU LEMBUR OPERATIONAL)';
                        } else if ($lembur < $timetable_operational->ot_limit) {
                            // LEMBUR LEBIH KECIL,DARI WAKTU LEMBUR OPERATIONAL
                            $attendance_data['operational_status'] = 'invalid';
                            $attendance_data['operational_plusm_value'] = $lembur - $timetable_operational->ot_limit;
                            $attendance_data['operational_note'] = 'Operasional dan kehadiran karyawan tidak sesuai, karena (LEMBUR LEBIH KECIL, DARI WAKTU LEMBUR OPERATIONAL)';
                        } else if ($lembur > $timetable_operational->ot_limit) {

                            // LEMBUR LEBIH BESAR,DARI WAKTU LEMBUR OPERATIONAL
                            $attendance_data['operational_status'] = 'invalid';
                            $attendance_data['operational_plusm_value'] = $lembur - $timetable_operational->ot_limit;
                            $attendance_data['operational_note'] = 'Operasional dan kehadiran karyawan tidak sesuai, karena (LEMBUR LEBIH BESAR, DARI WAKTU LEMBUR OPERATIONAL)';
                        }
                    } else {
                        // OPERATIONAL HADIR, KARYAWAN HADIR TETAPI TIDAK SESUAI DENGAN SHIFT
                        $attendance_data['operational_status'] = 'invalid';
                        $attendance_data['operational_plusm_value'] = $lembur - $timetable_operational->ot_limit;
                        $attendance_data['operational_note'] = 'Operasional dan kehadiran karyawan tidak sesuai, karena (TIDAK SESUAI DENGAN SHIFT)';
                    }
                } else {
                    // OPERATIONAL DILIBURKAN, TETAPI KARYAWAN HADIR
                    $attendance_data['operational_status'] = 'invalid';
                    $attendance_data['operational_note'] = 'Operasional diliburkan, tetapi karyawan masuk';
                }

                break;
            } else {
                // $attendance_data['operational']['operational_has_timetable']['status'] = $timetable_operational->status;

                if ($timetable_operational->status == 'active') {
                    if (empty($attendance_data['first_punch'])) {
                        // OPERATIONAL HADIR, KARYAWAN TIDAK HADIR
                        $attendance_data['operational_status'] = 'invalid';
                        $attendance_data['operational_note'] = 'Operasional hadir, tetapi karyawan tidak masuk';
                    } else {
                        // OPERATIONAL HADIR, KARYAWAN HADIR TETAPI TIDAK SESUAI DENGAN SHIFT
                        $attendance_data['operational_status'] = 'invalid';
                        $attendance_data['operational_note'] = 'Operasional dan kehadiran karyawan tidak sesuai, karena (TIDAK SESUAI DENGAN SHIFT)';
                    }
                } else {
                    if (empty($attendance_data['first_punch'])) {
                        // OPERATIONAL SUDAH SESUAI
                        $attendance_data['operational_status'] = 'valid';
                        $attendance_data['attendance_lb_status'] = 'accept';
                        $attendance_data['operational_note'] = 'Sudah sesuai, karena (OPERATIONAL DILIBURKAN DAN KARYAWAN TIDAK MASUK)';
                        break;
                    } else {
                        // OPERATIONAL TIDAK SESUAI OPERASIONAL LIBUR TAPI KARYAWAN MASUK
                        $attendance_data['operational_status'] = 'invalid';
                        $attendance_data['operational_status'] = false;
                        $attendance_data['operational_note'] = 'Operasional diliburkan, tetapi karyawan masuk';
                    }
                }
            }
        }

        return $attendance_data;
    }

    public function checkAttendancLbStatus(Carbon $date, int $emp_id, array $attendance_data)
    {
        $attendance_lb = AttendanceLb::where('lb_date', $date->format('Y-m-d'))->where('emp_id', $emp_id)->first();
        if (!empty($attendance_lb)) {
            $attendance_data['attendance_lb_id'] = $attendance_lb->id;
            $attendance_data['attendance_lb_status'] = $attendance_lb->lb_status;
        } else {
            if ($attendance_data['attendance_lb_status'] == 'accept') {
                $attendance_data['attendance_lb_status'] = 'accept';
            } else {
                $attendance_data['attendance_lb_status'] = null;
            }
        }

        return $attendance_data;
    }


    public function buildValueHtml($attendance)
    {
        if ($attendance['JL'] != 0 || $attendance['HK'] != 0) {
            if ($attendance['is_counting_salary'] && $attendance['is_counting_overtime']) {
                if ($attendance['HK'] == 0.5) {
                    if ($attendance['JL'] > 0) {
                        $attendance['value_string'] =  '1/2 (' . $attendance['JL'] . ')';
                    } else {
                        $attendance['value_string'] = '1/2';
                    }

                    if ($attendance['attendance_lb_status'] == 'accept') {
                        $attendance['value_string'] += ' LB';
                    }
                } else if ($attendance['HK'] > 0.5) {
                    if ($attendance['is_holiday'] && $attendance['HK'] == 1) {
                        $attendance['value_string'] = '';
                    } else {
                        $attendance['value_string'] = (string)($attendance['JL']);
                    }
                    if ($attendance['attendance_lb_status'] == 'accept') {
                        $attendance['value_string'] += ' LB';
                    }
                } else {
                    $attendance['value_string'] = 'X';
                }
            } else if ($attendance['is_counting_salary']) {
                if ($attendance['HK'] == 0.5) {
                    if ($attendance['JL'] > 0) {
                        $attendance['value_string'] = '1/2';
                    } else {
                        $attendance['value_string'] =  '1/2 (' . $attendance['JL'] . ')';
                    }

                    if ($attendance['attendance_lb_status'] == 'accept') {
                        $attendance['value_string'] += ' LB';
                    }
                } else if ($attendance['HK'] > 0.5) {
                    $attendance['value_string'] = '';
                } else {
                    $attendance['value_string'] = 'X';
                }
            } else if ($attendance['is_counting_overtime']) {
                if ($attendance['HK'] > 0) {
                    if ($attendance['is_holiday'] && $attendance['HK'] == 1) {
                        $attendance['value_string'] = '';
                    } else {
                        $attendance['value_string'] = (string)($attendance['JL']);
                    }
                    if ($attendance['attendance_lb_status'] == 'accept') {
                        $attendance['value_string'] += ' LB';
                    }
                } else {
                    $attendance['value_string'] = '-';
                }
            }
        } else if ($attendance['JL'] == 0 && $attendance['HK'] == 0 && $attendance['attendance_lb_status'] == 'given') {
            $attendance['value_string'] = 'LB';
        } else if ($attendance['JL'] == 0 && $attendance['HK'] == 0 && $attendance['is_holiday']) {
            $attendance['value_string'] = '';
        } else if ($attendance['JL'] == 0 && $attendance['HK'] == 0 && !$attendance['is_holiday']) {
            if ($attendance['is_counting_salary'] && $attendance['is_counting_overtime']) {
                $attendance['value_string'] = 'X';
            } else if ($attendance['is_counting_salary']) {
                $attendance['value_string'] = 'X';
            } else if ($attendance['is_counting_overtime']) {
                $attendance['value_string'] = '-';
            }
        }

        return $attendance;
    }


    public function  getPayrollAttendanceReport(
        Carbon $start_date_work_day,
        Carbon $end_date_work_day,
        Carbon $start_date_overtime,
        Carbon $end_date_overtime,
        string|null $department_code,
    ) {
        try {
            $datas = collect();
            $department_query = null;

            $start_date = null;
            $end_date = null;

            if ($start_date_work_day && $end_date_work_day && $start_date_overtime && $end_date_overtime) {
                if ($start_date_work_day->lte($start_date_overtime)) {
                    $start_date = $start_date_work_day->copy();
                } else {
                    $start_date = $start_date_overtime->copy();
                }

                if ($end_date_work_day->gte($end_date_overtime)) {
                    $end_date = $end_date_work_day->copy();
                } else {
                    $end_date = $end_date_overtime->copy();
                }

                $business_id = Session::get('business_id');
                $business = Business::where('id', $business_id)->select('id', 'pending_day')->first();
                $range_dates = $this->getRangeDate(
                    $business,
                    $start_date_work_day->copy(),
                    $end_date_work_day->copy(),
                    $start_date_overtime->copy(),
                    $end_date_overtime->copy()
                );

                if ($department_code) {
                    $department_query = collect($this->service->get_departments(["dept_code" => $department_code])['data'])->first();
                    $departmentbios = collect($this->service->get_departments(["page_size" => 999])['data']);
                    $department_code = $department_code;
                } else {
                    $departmentbios = collect($this->service->get_departments(["page_size" => 999])['data']);
                }

                $attendances = $this->getMergeAttendance($start_date->copy(), $end_date->copy());
                $attendances_grouping = $attendances->groupBy([
                    fn ($item) => $item['emp'],
                    fn ($item) => Carbon::parse($item['punch_time'])->format('Y-m-d'),
                ]);

                $empbios = $this->getEmployee($department_query);
                $empbios_grouping_by_dept = $empbios->groupBy([fn ($item) => $item['department']['id']]);

                // GET DEPARTMENT LOCAL DB
                $departmentlocal = Department::select('id', 'dept_id', 'sitting_money', 'still_paid')->get();

                // GET OPERATIONAL
                $operationals = Operational::where('business_id', $business_id)
                    ->whereBetween('date', [$start_date->copy()->subDays($business->pending_day), $end_date])->get();

                // GET SHIFTS
                $shifts = Shift::where('business_id', $business_id);
                if (!empty($department_code)) {
                    $shifts = $shifts->where('dept_id', $department_query['id']);
                }
                $shifts  = $shifts->with([
                    'shiftdays' => fn ($query) => $query->select('id', 'shift_id', 'code_day'),
                    'shiftdays.shiftday_has_timetables'  => fn ($query) => $query->select('id', 'shift_day_id', 'timetable_id'),
                    'shiftdays.shiftday_has_timetables.timetable' => fn ($query) => $query->select('id', 'name', 'check_in', 'check_out', 'check_in_plusmn', 'check_out_plusmn', 'cross_day', 'work_time', 'is_without_break', 'ot_roundone_hr', 'ot_roundhalf_hr', 'ot_period', 'ot_pay', 'duration_count_one_shift', 'duration_ot_limit', 'duration_rice_shift'),
                ])->get();

                if ($attendances_grouping->isNotEmpty()) {
                    foreach ($empbios_grouping_by_dept as $key_dept_id => $empdepts) {
                        $employee_department_local = $departmentlocal->where('dept_id', $key_dept_id)->first();

                        $empdeptbios = $departmentbios->where('id', $key_dept_id)->first();

                        $empshift = $shifts->where('dept_id', $key_dept_id)->first();
                        $empoperationals = $operationals->where('dept_id', $key_dept_id)->values();

                        $attendance_reports = collect();
                        foreach ($empdepts as $emp) {
                            $attendances = collect();
                            $report = collect([
                                'employee' => $emp,
                                'attendances' => collect(),
                                'HK_value' => 0,
                                'JL_value' => 0,
                                'food_value' => 0,
                                'kasbon_pay_value' => 0,
                                'remaining_kasbon_pay_value' => 0,
                                'salary_pay_value' => 0,
                                'overtime_pay_value' => 0,
                                'tbhn_u_libur_pay_value' => 0,
                                'tbhn_u_position_pay_value' => 0,
                                'total_pay_value' => 0,
                            ]);

                            $attendance_employee = $attendances_grouping->get($emp['id']);
                            $emplocal = Employee::where('business_id', $business_id)->where('emp_id', $emp['id'])
                                ->select('id', 'business_id', 'emp_id', 'emp_code', 'daily_salary', 'payment_period')
                                ->first();

                            if (!empty($emplocal)) {
                                $kasbon = $this->getEmployeeKasbonPaid($emplocal, $start_date, $end_date);
                                $report['kasbon_pay_value'] += $kasbon['kasbon_pay'];
                                $report['remaining_kasbon_pay_value'] += $kasbon['remaining_kasbon'];
                                $report['tbhn_u_position_pay_value'] += $this->getEmployeeExtraPayPosition($emplocal, $start_date, $end_date);

                                $range_date_count = count($range_dates);
                                foreach ($range_dates as $iDate => $range_date) {
                                    if ($range_date_count - 1 != $iDate) {
                                        $date = Carbon::parse($range_date['date']);
                                        $next_date = $date->copy()->addDay();

                                        $attendance_data = [
                                            'date' => $date->format('Y-m-d'),
                                            'first_punch' => null,
                                            'last_punch' => null,
                                            'JL' => 0,
                                            'HK' => 0,

                                            'value_string' => '',
                                            'be_one_shift' => 0,

                                            'food' => 0,
                                            'HK_pay_value' => 0,
                                            'JL_pay_value' => 0,
                                            'tbhn_u_libur_pay_value' => 0,
                                            'timetable' => null,

                                            'operational_id' => null,
                                            'operational_has_timetable_id' => null,
                                            'operational_plusm_value' => null,
                                            'operational_status' => null,
                                            'operational_note' => null,

                                            'attendance_tso_id' => null,

                                            'attendance_lb_id' => null,
                                            'attendance_lb_status' => null,

                                            'is_holiday' => $range_date['is_holiday'],
                                            'is_addition_date' => $range_date['is_addition_date'],
                                            'is_counting_salary' => $range_date['is_counting_salary'],
                                            'is_counting_overtime' => $range_date['is_counting_overtime'],
                                        ];

                                        $attendance_employee_in_dates = collect($attendance_employee[$date->format('Y-m-d')] ?? []);
                                        if ($attendance_employee_in_dates->isNotEmpty() && count($attendance_employee_in_dates) >= 2) {
                                            $first = $attendance_employee_in_dates->first();
                                            $last = $attendance_employee_in_dates->last();
                                            $first_punch = Carbon::parse($first['punch_time']);
                                            $last_punch = Carbon::parse($last['punch_time']);
                                            $attendance_data['first_punch'] = $first['punch_time'];
                                            $attendance_data['last_punch'] =count($attendance_employee_in_dates) >=2? $last['punch_time']: null;

                                            if (!empty($empshift)) {
                                                $shiftday = $empshift->shiftdays->where('code_day', $date->dayOfWeek)->first();
                                                if (!empty($shiftday)) {
                                                    foreach ($shiftday->shiftday_has_timetables as $shiftday_has_timetable) {
                                                        $timetable = $shiftday_has_timetable->timetable;
                                                        $check_in = Carbon::parse($date->format('Y-m-d') . $timetable->check_in);
                                                        $check_out = Carbon::parse($date->format('Y-m-d') . $timetable->check_out);

                                                        $check_in_add_plusmn = $check_in->copy()->subMinutes($timetable->check_in_plusmn);
                                                        $check_in_sub_plusmn = $check_in->copy()->addMinutes($timetable->check_in_plusmn);


                                                        if ($first_punch->between($check_in_sub_plusmn, $check_in_add_plusmn)) {
                                                            $attendance_data['timetable'] = $timetable->toArray();
                                                            $check_out_plus_duration_ot_limit =  $check_out->copy()->addHours($timetable->duration_ot_limit);
                                                            // if($date->format('Y-m-d') == '2023-03-22') {

                                                            //     Log::info($check_out_plus_duration_ot_limit);
                                                            // }
                                                            $attendance_date_time_cross = collect();
                                                            $attendance_date_time_uncross = collect();
                                                            if (!empty($attendance_employee->get($next_date->format('Y-m-d')))) {
                                                                foreach ($attendance_employee->get($next_date->format('Y-m-d')) ?? [] as $attendance_emp_next_date) {
                                                                    $check_in_next_date = Carbon::parse($attendance_emp_next_date['punch_time']);
                                                                    if ($check_in_next_date->lte($check_out_plus_duration_ot_limit)) {
                                                                        $attendance_date_time_cross[] = collect($attendance_emp_next_date);
                                                                    } else {
                                                                        $attendance_date_time_uncross[] = collect($attendance_emp_next_date);
                                                                    }
                                                                }

                                                                if (!empty($attendance_date_time_cross) && $attendance_date_time_cross->isNotEmpty()) {
                                                                    $attendance_employee[$date->format('Y-m-d')]->push(...$attendance_date_time_cross);
                                                                    $attendance_employee[$next_date->format('Y-m-d')] = collect($attendance_date_time_uncross);
                                                                    $last_punch = Carbon::parse($attendance_date_time_cross->last()['punch_time']);
                                                                    $attendance_data['last_punch'] = $attendance_date_time_cross->last()['punch_time'];
                                                                }
                                                            }

                                                            if ($range_date['is_counting_overtime']) {
                                                                $countingOvertime = $this->countingOvertime($range_date, $first_punch, $last_punch, $timetable, $attendance_employee_in_dates->count());
                                                                $attendance_data['JL'] += $countingOvertime['JL'];
                                                                $attendance_data['JL_pay_value'] += $countingOvertime['JL_pay_value'];
                                                                $attendance_data['be_one_shift'] += $countingOvertime['be_one_shift'];
                                                            }

                                                            if ($attendance_employee_in_dates->count() > 1) {
                                                                $countingSalary = $this->countingSalary($employee_department_local, $range_date, $first_punch, $last_punch, $timetable);
                                                                $attendance_data['HK'] += $countingSalary['HK'] + $attendance_data['be_one_shift'];
                                                                $attendance_data['HK_pay_value'] += $countingSalary['HK'] * $emplocal->daily_salary;
                                                            }
                                                        } else {
                                                        }
                                                    }
                                                } else {
                                                    // MASUKK KESINI KLO-KLO DATA SHIFTNYA BLOM DI INPUT
                                                }
                                            }
                                        } else {
                                            if ($range_date['is_holiday'] && $employee_department_local->still_paid) {
                                                $attendance_data['HK']++;
                                                $attendance_data['HK_pay_value'] += $attendance_data['HK'] * $emplocal->daily_salary;
                                            }
                                            // MASUKK KESINI KLO ABSENSI USER DI TANGGAL INI GA ADA
                                        }

                                        $operational_date = $empoperationals->where('date', $date->format('Y-m-d'))->first();
                                        if (!empty($operational_date)) {
                                            $attendance_data = $this->checkAttendaceOperational($date, $operational_date, $attendance_data);
                                        } else {
                                            // MASUKK KESINI KLO OPERATIONAL NYA GA ADA
                                        }

                                        $attendance_tso = AttendanceTso::where('tso_date', $date->format('Y-m-d'))->where('emp_id', $emp['id'])->first();
                                        if (!empty($attendance_tso)) {
                                            $attendance_data['operational_status'] = 'valid';
                                            $attendance_data['operational_node'] = 'kehadiran karyawan telah Disetujui';
                                            $attendance_data['attendance_tso_id'] = $attendance_tso->id;
                                        }

                                        $attendance_data  = $this->checkAttendancLbStatus($date, $emp['id'], $attendance_data);
                                        if ($attendance_data['attendance_lb_status'] == 'accept') {
                                            $attendance_data['tbhn_u_libur_pay_value'] += $employee_department_local->sitting_money ?? 0;
                                        }

                                        if ($range_date['is_addition_date']) {
                                            // INI CUMA TANGGAL TAMBAHAN AJA SUPAYA BISA AMBIL DATA DI HARI SETELAHNYA
                                            // UNTUK AMBIL DATA LEMBURNYA JIKA GANTI HARI
                                        } else {
                                            $attendance_data = $this->buildValueHtml($attendance_data);
                                            $attendances->push(collect($attendance_data));
                                        }
                                    } else {
                                        // INI CUMA TANGGAL TAMBAHAN AJA SUPAYA BISA AMBIL DATA DI HARI SETELAHNYA
                                        // UNTUK AMBIL DATA LEMBURNYA JIKA GANTI HARI
                                    }
                                }
                            } else {
                                // MASUKK KESINI JIKA USER BIOS TIDAK ADA DI LOCAL
                            }
                            foreach ($attendances as $attendance) {
                                $report['HK_value'] += $attendance['HK'];
                                $report['JL_value'] += $attendance['JL'];
                                $report['food_value'] += $attendance['food'];
                                $report['salary_pay_value'] += $attendance['HK_pay_value'];
                                $report['overtime_pay_value'] += $attendance['JL_pay_value'];
                                $report['tbhn_u_libur_pay_value'] += $attendance['tbhn_u_libur_pay_value'];
                            }
                            $report['total_pay_value'] +=  $report['kasbon_pay_value'] + $report['salary_pay_value'] + $report['overtime_pay_value'] + $report['tbhn_u_libur_pay_value'] + $report['tbhn_u_position_pay_value'];
                            $report['attendances'] = $attendances;
                            $attendance_reports->push($report);
                        }

                        $range_dates = $range_dates->filter(fn ($item) => !$item['is_addition_date'])->values();
                        $data = collect([
                            'start_date' => $start_date->format('Y-m-d'),
                            'end_date' => $end_date->format('Y-m-d'),
                            'start_date_work_day' => $start_date_work_day->format('Y-m-d'),
                            'end_date_work_day' => $end_date_work_day->format('Y-m-d'),
                            'start_date_overtime' => $start_date_overtime->format('Y-m-d'),
                            'end_date_overtime' => $end_date_overtime->format('Y-m-d'),
                            'department' => $empdeptbios,
                            'range_dates' => $range_dates,
                            'attendance_reports' => $attendance_reports,
                            'total_HK_value' => 0,
                            'total_JL_value' => 0,
                            'total_food_value' => 0,
                            'total_kasbon_pay_value' => 0,
                            'total_salary_pay_value' => 0,
                            'total_overtime_pay_value' => 0,
                            'total_tbhn_u_position_pay_value' => 0,
                            'total_tbhn_u_libur_pay_value' => 0,
                            'grand_total_pay_value' => 0,
                        ]);

                        foreach ($attendance_reports as $report) {
                            $data['total_HK_value'] += $report['HK_value'];
                            $data['total_JL_value'] += $report['JL_value'];
                            $data['total_food_value'] += $report['food_value'];
                            $data['total_kasbon_pay_value'] += $report['kasbon_pay_value'];
                            $data['total_tbhn_u_position_pay_value'] += $report['tbhn_u_position_pay_value'];
                            $data['total_salary_pay_value'] += $report['salary_pay_value'];
                            $data['total_overtime_pay_value'] += $report['overtime_pay_value'];
                            $data['total_tbhn_u_libur_pay_value'] += $report['tbhn_u_libur_pay_value'];
                            $data['grand_total_pay_value'] += $report['total_pay_value'];
                        }
                        $datas->push($data);
                    }
                } else {
                    // MASUKK KESINI KLO ABSENSI GA ADA
                }
                return $datas;
            } else {
                return $datas;
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function print_payroll_report(Request $request)
    {
        Log::info('[' . request()->route()->getName() . ']::GET');
        try {
            $salary_archive_ths = null;
            $start_date_work_day = null;
            $end_date_work_day = null;
            $start_date_overtime = null;
            $end_date_overtime = null;
            $business_id = Session::get('business_id');
            $business = Business::where('id', $business_id)->select('id', 'pending_day')->first();

            if ($request->has('start_date_work_day') && $request->has('end_date_work_day') && $request->has('start_date_overtime') && $request->has('end_date_overtime')) {
                $start_date_work_day = Carbon::createFromFormat('d-m-Y', $request['start_date_work_day']);
                $end_date_work_day = Carbon::createFromFormat('d-m-Y', $request['end_date_work_day']);
                $start_date_overtime = Carbon::createFromFormat('d-m-Y', $request['start_date_overtime']);
                $end_date_overtime = Carbon::createFromFormat('d-m-Y', $request['end_date_overtime']);
                $salary_archive_ths = SalaryArchiveTh::whereDate('start_date_work_day', $start_date_work_day->format('Y-m-d'))
                    ->whereDate('end_date_work_day', $end_date_work_day->format('Y-m-d'))
                    ->whereDate('start_date_overtime', $start_date_overtime->format('Y-m-d'))
                    ->whereDate('end_date_overtime', $end_date_overtime->format('Y-m-d'));

                if ($request->has('calculation_salary_archive_id')) {
                    $calculation_salary_archive_id = $request->calculation_salary_archive_id;
                    $salary_archive_ths = $salary_archive_ths->with(['salary_archive_tds' => fn ($query) => $query->where('id', $calculation_salary_archive_id)])->get();
                } else {
                    $salary_archive_ths = $salary_archive_ths->with(['salary_archive_tds'])->get();
                }

                $range_dates = $this->getRangeDate(
                    $business,
                    $start_date_work_day->copy()->addDay(),
                    $end_date_work_day->copy()->subDay(),
                    $start_date_overtime->copy()->addDay(),
                    $end_date_overtime->copy()->subDay()
                );

                foreach ($salary_archive_ths as $key => $item) {
                    $item['range_dates'] = $range_dates;
                }

                return view('print.payroll_report', compact('start_date_work_day', 'end_date_work_day', 'start_date_overtime', 'end_date_overtime', 'salary_archive_ths'));
            } else {
                return view('print.payroll_report', compact('start_date_work_day', 'end_date_work_day', 'start_date_overtime', 'end_date_overtime', 'salary_archive_ths'));
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
        }
    }



    public function print_card_report(Request $request)
    {
        Log::info('[' . request()->route()->getName() . ']::GET');
        try {
            // $deparment_code = null;
            // $start_date = null;
            // $end_date = null;
            // $datas = [];
            // if ($request->has('deparment_code')) {
            //     $deparment_code = $request['deparment_code'];
            // }
            // if ($request->has('start_date') && $request->has('end_date')) {
            //     $start_date = Carbon::createFromFormat('d-m-Y', $request['start_date']);
            //     $end_date = Carbon::createFromFormat('d-m-Y', $request['end_date']);
            //     $start_date_work_day = Carbon::createFromFormat('d-m-Y', $request['start_date']);
            //     $end_date_work_day = Carbon::createFromFormat('d-m-Y', $request['end_date']);
            //     $start_date_overtime = Carbon::createFromFormat('d-m-Y', $request['start_date']);
            //     $end_date_overtime = Carbon::createFromFormat('d-m-Y', $request['end_date']);

            //     $datas = $this->getPayrollAttendanceReport(
            //         $start_date_work_day,
            //         $end_date_work_day,
            //         $start_date_overtime,
            //         $end_date_overtime,
            //         $deparment_code,
            //     );

            //     return view('print.card_report', compact('start_date', 'end_date', 'datas'));
            // } else {
            //     return view('print.card_report', compact('start_date', 'end_date'));
            // }

            $salary_archive_ths = null;
            $start_date_work_day = null;
            $end_date_work_day = null;
            $start_date_overtime = null;
            $end_date_overtime = null;
            $business_id = Session::get('business_id');
            $business = Business::where('id', $business_id)->select('id', 'pending_day')->first();

            if ($request->has('start_date_work_day') && $request->has('end_date_work_day') && $request->has('start_date_overtime') && $request->has('end_date_overtime')) {
                $start_date_work_day = Carbon::createFromFormat('d-m-Y', $request['start_date_work_day']);
                $end_date_work_day = Carbon::createFromFormat('d-m-Y', $request['end_date_work_day']);
                $start_date_overtime = Carbon::createFromFormat('d-m-Y', $request['start_date_overtime']);
                $end_date_overtime = Carbon::createFromFormat('d-m-Y', $request['end_date_overtime']);

                $salary_archive_ths = SalaryArchiveTh::whereDate('start_date_work_day', $start_date_work_day)
                    ->whereDate('end_date_work_day', $end_date_work_day)
                    ->whereDate('start_date_overtime', $start_date_overtime)
                    ->whereDate('end_date_overtime', $end_date_overtime);

                if ($request->has('calculation_salary_archive_id')) {
                    $calculation_salary_archive_id = $request->calculation_salary_archive_id;
                    $salary_archive_ths = $salary_archive_ths->with(['salary_archive_tds' => fn ($query) => $query->where('id', $calculation_salary_archive_id)])->get();
                } else {
                    $salary_archive_ths = $salary_archive_ths->with(['salary_archive_tds'])->get();
                }

                // $range_dates = $this->getRangeDate(
                //     $business,
                //     $start_date_work_day->copy()->addDay(),
                //     $end_date_work_day->copy()->subDay(),
                //     $start_date_overtime->copy()->addDay(),
                //     $end_date_overtime->copy()->subDay()
                // );

                // foreach ($salary_archive_ths as $key => $item) {
                //     $item['range_dates'] = $range_dates;
                // }

                return view('print.card_report', compact('start_date_work_day', 'end_date_work_day', 'start_date_overtime', 'end_date_overtime', 'salary_archive_ths'));
            } else {
                return view('print.card_report', compact('start_date_work_day', 'end_date_work_day', 'start_date_overtime', 'end_date_overtime', 'salary_archive_ths'));
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
        }
    }
}
