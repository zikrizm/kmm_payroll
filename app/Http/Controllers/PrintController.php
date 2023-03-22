<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
use App\Models\EmployeeDebtPay;
use Illuminate\Support\Carbon;
use App\Services\Api\ApiServices;
use App\Models\EmployeeHasPosition;
use App\Models\EmployeeStatusLb;
use App\Models\EmployeeTso;
use App\Models\OtRicebill;
use App\Models\OtRicebillPerday;
use App\Models\SalaryArchive;
use App\Models\SalaryArchiveEmployee;
use App\Models\SalaryArchivePerday;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class PrintController extends Controller
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

    public function getRangeDate($start_date, $end_date)
    {
        $start_date = Carbon::parse($start_date);
        $end_date = Carbon::parse($end_date);
        $business_id = Session::get('business_id');

        $dates = [];
        for ($date = $start_date->copy(); $date->lte($end_date); $date->addDay()) {
            $data = [];
            $dayOfWeek = $date->dayOfWeek;

            $holidays = Holiday::select('id', 'business_id', 'start_date', 'end_date')->where('business_id', $business_id)
                ->whereDate('start_date', '<=', $date)->whereDate('end_date', '>=', $date)->get();
            if ($date->isSunday() || $holidays->isNotEmpty()) {
                $data['is_holiday'] = true;
            } else {
                $data['is_holiday'] = false;
            }

            $data['d'] = $date->format('d');
            $data['date'] = $date->format('Y-m-d');
            $data['slug'] = $this->slug_week[$dayOfWeek];
            $dates[] = $data;
        }
        return $dates;
    }

    public function compareAttendanceData(array $attendance_data)
    {
        if ($attendance_data['JL_value'] != 0 || $attendance_data['HK_value'] != 0) {
            $attendance_data['value'] = $attendance_data['JL_value'];
            $attendance_data['value_string'] = (string)($attendance_data['JL_value']);
            if ($attendance_data['is_half_day_leave']) {
                if ($attendance_data['value'] > 0) {
                    $attendance_data['value_string'] = '1/2 (' . $attendance_data['value_string'] . ')';
                } else {
                    $attendance_data['value_string'] = '1/2';
                }
            }
        } else if ($attendance_data['JL_value'] == 0 && $attendance_data['HK_value'] == 0 && $attendance_data['attendance_status']['LB']['status']) {
            // TIDAK ADA ABSENSI, TAPI DI LB-KAN
            $attendance_data['value'] = 0;
            $attendance_data['value_string'] = 'LB';
        } else if ($attendance_data['JL_value'] == 0 && $attendance_data['HK_value'] == 0 && $attendance_data['attendance_status']['is_LK']) {
            // TIDAK ADA ABSENSI, TAPI KE KELUAR KOTA
            $attendance_data['value'] = 0;
            $attendance_data['value_string'] = 'LK';
        } else if ($attendance_data['JL_value'] == 0 && $attendance_data['HK_value'] == 0 && $attendance_data['attendance_status']['is_DK']) {
            // TIDAK ADA ABSENSI, TAPI KE DALAM KOTA
            $attendance_data['value'] = 0;
            $attendance_data['value_string'] = 'DK';
        } else if ($attendance_data['JL_value'] == 0 && $attendance_data['HK_value'] == 0 && $attendance_data['attendance_status']['is_holiday'] && !$attendance_data['is_pending_kasbon']) {
            // TIDAK ADA ABSENSI, TAPI LIBUR
            $attendance_data['value'] = 0;
            $attendance_data['value_string'] = '';
        } else if ($attendance_data['JL_value'] == 0 && $attendance_data['HK_value'] == 0 && !$attendance_data['attendance_status']['is_holiday'] && !$attendance_data['is_pending_kasbon']) {
            // TIDAK ADA ABSENSI, TAPI TIDAK LIBUR
            $attendance_data['value'] = 0;
            $attendance_data['value_string'] = 'X';
        } else if ($attendance_data['JL_value'] == 0 && $attendance_data['HK_value'] == 0 && $attendance_data['attendance_status']['is_holiday'] && $attendance_data['is_pending_kasbon']) {
            $attendance_data['value'] = 0;
            $attendance_data['value_string'] = '';
        } else if ($attendance_data['JL_value'] == 0 && $attendance_data['HK_value'] == 0 && !$attendance_data['attendance_status']['is_holiday'] && $attendance_data['is_pending_kasbon']) {
            $attendance_data['value'] = 0;
            $attendance_data['value_string'] = '-';
        } else if ($attendance_data['JL_value'] == 0 && $attendance_data['HK_value'] == 0 && $attendance_data['attendance_status']['is_holiday'] && $attendance_data['is_pending_HK']) {
            $attendance_data['value'] = 0;
            $attendance_data['value_string'] = '';
        } else if ($attendance_data['JL_value'] == 0 && $attendance_data['HK_value'] == 0 && !$attendance_data['attendance_status']['is_holiday'] && $attendance_data['is_pending_HK']) {
            $attendance_data['value'] = 0;
            $attendance_data['value_string'] = 'X';
        }
        return $attendance_data;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function print_payroll_report(Request $request)
    {
        try {

            $start_date = null;
            $end_date = null;
            $deparment_code = null;
            if ($request->has('start_date')) {
                $start_date = Carbon::parse($request['start_date']);
            }

            if ($request->has('end_date')) {
                $end_date = Carbon::parse($request['end_date']);
            }

            if ($request->has('start_date') && $request->has('end_date')) {
                if ($start_date->gt($end_date)) {
                    return;
                }
            }

            if ($request->has('deparment_code')) {
                $deparment_code = $request['deparment_code'];
            }

            $business_id = Session::get('business_id');
            $business = Business::where('id', $business_id)->first();

            $range_dates = $this->getRangeDate(
                $start_date->copy()->subDays($business->pending_day)->hour(0)->minute(0)->second(0),
                $end_date->copy()->addDays(1)->hour(23)->minute(59)->second(59)
            );

            $attendances = $this->getMergeAttendance($start_date, $end_date);
            $attendances_grouping = $attendances->groupBy([
                fn ($item) => $item['emp'],
                fn ($item) => Carbon::parse($item['punch_time'])->format('Y-m-d'),
            ]);

            if (!empty($deparment_code)) {
                $empbios_count = $this->service->get_employees([])["count"];
                $empbios = collect($this->service->get_employees(["page_size" => $empbios_count, 'departments' => $department->id])['data']);
            } else {
                $empbios_count = $this->service->get_employees([])["count"];
                $empbios = collect($this->service->get_employees(["page_size" => $empbios_count])['data']);
            }

            $empbios_grouping_by_dept = $empbios->groupBy([fn ($item) => $item['department']['id']]);

            $departmentbios = collect($this->service->get_departments(["page_size" => 999])['data']);
            $departmentlocal = Department::select('id', 'dept_id', 'sitting_money', 'still_paid')->get();

            $operationals = Operational::where('business_id', $business_id)
                ->whereBetween('date', [$start_date->copy()->subDays($business->pending_day), $end_date])->get();

            if (!empty($department)) {
                $shifts = Shift::where('business_id', $business_id)->where('dept_id', $department)->get();
            } else {
                $shifts = Shift::where('business_id', $business_id)->get();
            }

            $datas = collect();
            if ($attendances_grouping->isNotEmpty()) {
                foreach ($empbios_grouping_by_dept as $key_dept_id => $empdepts) {
                    $empdeptlocal = $departmentlocal->where('dept_id', $key_dept_id)->first();
                    $empdeptbios = $departmentbios->where('id', $key_dept_id)->first();
                    $empshift = $shifts->where('dept_id', $key_dept_id)->first();
                    $empoperationals = $operationals->where('dept_id', $key_dept_id)->values();
                    $reports = collect();
                    foreach ($empdepts as $emp) {
                        $attendances = collect();
                        // $kasbon_value = 0;
                        // $extra_pay_position_value = 0;

                        $attendance_employee = $attendances_grouping[$emp['id']];
                        if ($attendance_employee && count($attendance_employee)) {
                            $emplocal = Employee::where('business_id', $business_id)->where('emp_id', $emp['id'])
                                ->select('id', 'business_id', 'emp_id', 'emp_code', 'daily_salary', 'payment_period')
                                ->first();

                            if (!empty($emplocal)) {
                                // // SET KASBON
                                // $kasbon_value = $this->getEmployeeKasbonPaid($emplocal, $start_date, $end_date);
                                // // SET EXTRAPAYPOSITION
                                // $extra_pay_position_value = $this->getEmployeeExtraPayPosition($emplocal, $start_date, $end_date);

                                $range_date_count = count($range_dates);
                                foreach ($range_dates as $key => $item) {
                                    if ($range_date_count - 1 != $key) {
                                        $date = Carbon::parse($item['date']);

                                        $attendance_employee_date = collect($attendance_employee[$date->format('Y-m-d')] ?? []);
                                        $attendance_data = [
                                            'date' => $date->format('Y-m-d'),
                                            'first_punch' => null,
                                            'last_punch' => null,
                                            'JL_value' => 0,
                                            'HK_value' => 0,
                                            'value' => 0,
                                            'value_string' => '',
                                            'be_one_shift' => 0,

                                            'HK_pay_value' => 0,
                                            'JL_pay_value' => 0,
                                            'tbhn_u_libur_value' => 0,
                                            'attendance_status' => [
                                                'is_valid' => false,
                                                'is_holiday' => $item['is_holiday'],
                                                'is_LK' => false,
                                                'is_DK' => false,

                                                'tso' => ['status' => false, 'date' => null,],
                                                'LB' => ['status' => false, 'type' => null],
                                                'plusmn' => ['status' => false, 'value' => null],
                                                'operational_status' => null,
                                                'note' => null
                                            ]
                                        ];

                                        if (!empty($business->pending_day) && $business->pending_day > 0) {


                                            if ($key < $business->pending_day) {
                                                Log::info('====== ' . $key . ' = ' . $date->format('Y-m-d'));
                                                $attendance_data['is_pending_kasbon'] = true;
                                                $attendance_data['is_pending_HK'] = false;
                                            } else {
                                                if (($range_date_count - 1) - $business->pending_day <= $key) {
                                                    Log::info('====== ' . $key . ' = ' . $date->format('Y-m-d'));
                                                    $attendance_data['is_pending_kasbon'] = false;
                                                    $attendance_data['is_pending_HK'] = true;
                                                } else {
                                                    $attendance_data['is_pending_HK'] = false;
                                                    $attendance_data['is_pending_kasbon'] = false;
                                                }
                                            }
                                        } else {
                                            $attendance_data['is_pending_HK'] = false;
                                            $attendance_data['is_pending_kasbon'] = false;
                                        }

                                        if ($attendance_employee_date->isNotEmpty()) {
                                            // OPERATIONAL
                                            // $operational_dates = $empoperationals->where('date', $date);

                                            // ATTENDANCE
                                            $first = $attendance_employee_date->first();
                                            $last = $attendance_employee_date->last();
                                            $first_punch = Carbon::parse($first['punch_time']);
                                            $last_punch = Carbon::parse($last['punch_time']);
                                            $attendance_data['first_punch'] = $first['punch_time'];
                                            $attendance_data['last_punch'] = $last['punch_time'];

                                            $shiftdays = $empshift->shiftdays->where('code_day', $date->dayOfWeek)->first();
                                            if (!empty($shiftdays)) {
                                                foreach ($shiftdays->shiftday_has_timetables as $shiftday_has_timetable) {
                                                    $timetable = $shiftday_has_timetable->timetable;
                                                    $check_in = Carbon::parse($date->format('Y-m-d') . $timetable->check_in);
                                                    $check_out = Carbon::parse($date->format('Y-m-d') . $timetable->check_out);

                                                    $time_check_in_add_plusmn = $check_in->copy()->subMinutes($timetable->check_in_plusmn);
                                                    $time_check_in_sub_plusmn = $check_in->copy()->addMinutes($timetable->check_in_plusmn);
                                                    $time_check_out_add_plusmn = $check_in->copy()->subMinutes($timetable->check_out_plusmn);
                                                    $time_check_out_sub_plusmn = $check_in->copy()->addMinutes($timetable->check_out_plusmn);

                                                    $time_check_out_plus_ot =  $check_out->copy()->addHours($timetable->duration_ot_limit);
                                                    $time_check_out_cross =  $check_out->copy()->addDays($timetable->cross_day ?? 0);

                                                    if ($first_punch->between($time_check_in_sub_plusmn, $time_check_in_add_plusmn)) {
                                                        $attendance_data['timetable'] = $timetable->toArray();

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

                                                        if ($first_punch->lt($check_in) && !$attendance_data['is_pending_HK']) {
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
                                                            if ($last_punch->gte($time_check_out_plus_ot) && !$attendance_data['is_pending_HK']) {
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
                                                                            $attendance_data['HK_value']++;
                                                                        } else {
                                                                            $attendance_data['HK_value'] += 0.5;
                                                                        }
                                                                    } else {
                                                                        $attendance_data['HK_value']++;
                                                                    }
                                                                }
                                                            }

                                                            if ($timetable->ot_period && $timetable->ot_pay) {
                                                                $attendance_data['JL_pay_value'] += ((($attendance_data['JL_value'] ?? 0) * 60) / $timetable->ot_period) * $timetable->ot_pay;
                                                            } else {
                                                                $attendance_data['JL_pay_value'] += $attendance_data['JL_value'];
                                                            }

                                                            $attendance_data['HK_pay_value'] += $emplocal->daily_salary * $attendance_data['HK_value'];
                                                        }
                                                    } else {
                                                        //
                                                        //
                                                    }
                                                }
                                            } else {
                                                //
                                                //
                                            }
                                        } else {
                                            //
                                            //
                                        }

                                        $operational_dates = $empoperationals->where('date', $date)->first();
                                        if (!empty($operational_dates)) {
                                            foreach ($operational_dates->operational_has_timetables as $timetable_operational) {
                                                if (!empty($attendance_data['timetable']) && $attendance_data['timetable']['id'] == $timetable_operational->timetable_id) {
                                                    $attendance_data['attendance_status']['operational_status'] = $timetable_operational->status;
                                                    if ($timetable_operational->status == 'active') {
                                                        $last_punch = Carbon::parse($attendance_data['last_punch']);
                                                        $max_punch_checkout = Carbon::parse($date->format('Y-m-d') . $attendance_data['timetable']['check_out'])
                                                            ->addHours($attendance_data['timetable']['duration_ot_limit'] ?? 0)
                                                            ->addHours($timetable_operational->ot_limit ?? 0);
                                                        $shift_bagian_checkout_add_plusmn = Carbon::parse($date->format('Y-m-d') . $attendance_data['timetable']['check_out'])->subMinutes($attendance_data['timetable']['check_out_plusmn']);
                                                        $diff_checkout_in_hours = $max_punch_checkout->diffInHours($last_punch, false);

                                                        if ($last_punch->gte($shift_bagian_checkout_add_plusmn)) {
                                                            if ($attendance_data['JL_value'] == $timetable_operational->ot_limit) {
                                                                // OPERATIONAL SUDAH SEASUAI
                                                                $attendance_data['attendance_status']['is_valid'] = true;
                                                                $attendance_data['attendance_status']['note'] = 'Sudah sesuai';
                                                            } else if ($attendance_data['JL_value'] < $timetable_operational->ot_limit) {
                                                                // LEMBUR LEBIH KECIL,DARI WAKTU LEMBUR OPERATIONAL
                                                                $attendance_data['attendance_status']['is_valid'] = false;
                                                                $value = $attendance_data['JL_value'] - $timetable_operational->ot_limit;
                                                                $attendance_data['attendance_status']['plusmn'] = [
                                                                    'status' => true,
                                                                    'value' => $value
                                                                ];
                                                                $attendance_data['attendance_status']['note'] = 'Operasional dan kehadiran karyawan tidak sesuai';
                                                            } else if ($attendance_data['JL_value'] > $timetable_operational->ot_limit) {
                                                                // LEMBUR LEBIH BESAR,DARI WAKTU LEMBUR OPERATIONAL
                                                                $attendance_data['attendance_status']['is_valid'] = false;
                                                                $value = '+' . ($attendance_data['JL_value'] - $timetable_operational->ot_limit);
                                                                $attendance_data['attendance_status']['plusmn'] = [
                                                                    'status' => true,
                                                                    'value' => $value
                                                                ];
                                                                $attendance_data['attendance_status']['note'] = 'Operasional dan kehadiran karyawan tidak sesuai';
                                                            } else if ($last_punch->gt($max_punch_checkout)) {
                                                                // LEMBUR LEBIH BESAR, WAKTU LEMBUR SHIFT
                                                                $attendance_data['attendance_status']['is_valid'] = false;
                                                                $attendance_data['attendance_status']['note'] = 'Operasional dan kehadiran karyawan tidak sesuai';
                                                            }
                                                        } else {
                                                            // OPERATIONAL HADIR, KARYAWAN HADIR TETAPI TIDAK SESUAI DENGAN SHIFT
                                                            $attendance_data['attendance_status']['is_valid'] = false;
                                                            $attendance_data['attendance_status']['plusmn'] = [
                                                                'status' => true,
                                                                'value' => $diff_checkout_in_hours
                                                            ];
                                                            $attendance_data['attendance_status']['note'] = 'Operasional dan kehadiran karyawan tidak sesuai';
                                                        }
                                                    } else {
                                                        // OPERATIONAL DILIBURKAN,TETAPI KARYAWAN HADIR
                                                        $attendance_data['attendance_status']['is_valid'] = false;
                                                        $attendance_data['attendance_status']['note'] = 'Operasional diliburkan, tetapi karyawan masuk';
                                                    }

                                                    break;
                                                } else {
                                                    $attendance_data['attendance_status']['operational_status'] = $timetable_operational->status;

                                                    if ($timetable_operational->status == 'active') {
                                                        if ($attendance_employee_date->isEmpty()) {
                                                            // OPERATIONAL HADIR, KARYAWAN TIDAK HADIR
                                                            $attendance_data['attendance_status']['is_valid'] = false;
                                                            $attendance_data['attendance_status']['note'] = 'Operasional hadir, tetapi karyawan tidak masuk';
                                                        } else {
                                                            // OPERATIONAL HADIR, KARYAWAN HADIR TETAPI TIDAK SESUAI DENGAN SHIFT
                                                            $attendance_data['attendance_status']['is_valid'] = false;
                                                            $attendance_data['attendance_status']['note'] = 'Operasional hadir, karyawan hadir tetapi tidak sesuai dengan shift';
                                                        }
                                                        break;
                                                    } else {
                                                        if ($attendance_employee_date->isEmpty()) {
                                                            // OPERATIONAL SUDAH SEASUAI
                                                            $attendance_data['attendance_status']['is_valid'] = true;
                                                            $attendance_data['attendance_status']['LB']['status'] = true;
                                                            $attendance_data['attendance_status']['LB']['type'] = 'given';
                                                            $attendance_data['attendance_status']['note'] = 'Sudah sesuai';
                                                        } else {
                                                            // OPERATIONAL TIDAK SESUAI OPERASIONAL LIBUR TAPI KARYAWAN MASUK
                                                            $attendance_data['attendance_status']['is_valid'] = false;
                                                            $attendance_data['attendance_status']['note'] = 'Operasional diliburkan, tetapi karyawan masuk';
                                                        }
                                                    }
                                                }
                                            }


                                            $employee_tso = EmployeeTso::where('tso_date', $date->format('Y-m-d'))->where('emp_id', $emp['id'])->first();
                                            if (!empty($employee_tso)) {
                                                $attendance_data['attendance_status']['is_valid'] = false;
                                                $attendance_data['attendance_status']['note'] = 'Kehadiran di setujui di TSO';
                                            }

                                            $employee_status_lb = EmployeeStatusLb::where('lb_date', $date->format('Y-m-d'))->where('emp_id', $emp['id'])->first();
                                            if (!empty($employee_status_lb)) {
                                                $attendance_data['attendance_status']['LB']['status'] = true;
                                                $attendance_data['attendance_status']['LB']['type'] = $employee_status_lb->type;
                                                if ($employee_status_lb->type == 'given') {
                                                    $attendance_data['tbhn_u_libur_value'] += $empdeptlocal->sitting_money ?? 0;
                                                }
                                            } else {
                                                if ($attendance_data['attendance_status']['LB']['status']) {
                                                    $attendance_data['tbhn_u_libur_value'] += $empdeptlocal->sitting_money ?? 0;
                                                }
                                            }
                                        } else {
                                            //
                                            //
                                        }

                                        $attendance_data = $this->compareAttendanceData($attendance_data);
                                        $attendances->push(collect($attendance_data));
                                    }
                                }
                            } else {
                            }
                        } else {
                        }

                        $report = collect([
                            'attendances' => $attendances,
                            'employee' => $emp,
                            'total_HK_value' => 0,
                            'total_JL_value' => 0,
                            'total_HK_pay_value' => 0,
                            'total_JL_pay_value' => 0,
                            'total_kasbon_pay_value' => 0,
                            'total_extra_position_pay_value' => 0,
                            'total_tbhn_u_libur_pay_value' => 0,
                            'total_pay_value' => 0,
                        ]);


                        foreach ($attendances as $attendance) {
                            $report['total_HK_value'] += $attendance['HK_value'];
                            $report['total_JL_value'] += $attendance['JL_value'];
                            $report['total_HK_pay_value'] += $attendance['HK_pay_value'];
                            $report['total_JL_pay_value'] += $attendance['JL_pay_value'];
                            $report['total_tbhn_u_libur_pay_value'] += $attendance['tbhn_u_libur_value'];
                        }
                        $report['total_pay_value'] +=  $report['total_HK_pay_value'] + $report['total_JL_pay_value'] + $report['total_kasbon_pay_value'] + $report['total_extra_position_pay_value'] + $report['total_tbhn_u_libur_pay_value'];
                        $reports->push($report);
                    }


                    $data = collect([
                        'department' => $empdeptbios,
                        'reports' => $reports,
                        'grand_total_HK_value' => 0,
                        'grand_total_JL_value' => 0,
                        'grand_total_HK_pay_value' => 0,
                        'grand_total_JL_pay_value' => 0,
                        'grand_total_kasbon_pay_value' => 0,
                        'grand_total_extra_position_pay_value' => 0,
                        'grand_total_tbhn_u_libur_pay_value' => 0,
                        'grand_total_pay_value' => 0,
                    ]);
                    foreach ($reports as $report) {
                        $data['grand_total_HK_value'] += $report['total_HK_value'];
                        $data['grand_total_JL_value'] += $report['total_JL_value'];
                        $data['grand_total_HK_pay_value'] += $report['total_HK_pay_value'];
                        $data['grand_total_JL_pay_value'] += $report['total_JL_pay_value'];
                        $data['grand_total_kasbon_pay_value'] += $report['total_kasbon_pay_value'];
                        $data['grand_total_extra_position_pay_value'] += $report['total_extra_position_pay_value'];
                        $data['grand_total_tbhn_u_libur_pay_value'] += $report['total_tbhn_u_libur_pay_value'];
                        $data['grand_total_pay_value'] += $report['total_pay_value'];
                    }
                    $datas->push($data);
                }
            } else {
            }


            Log::info($datas);

            // return view('print.payroll_report', compact('start_date', 'end_date','datas'));
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
        }
    }
}


// $datas = [
//     [
//         'department' => [],
//         'range_dates' => [
//             [
//                 'd' => '01',
//                 'key' => 'Jum',
//                 'date' => new Carbon(),
//             ],
//             [
//                 'd' => '02',
//                 'key' => 'Jum',
//                 'date' => new Carbon(),
//             ],
//             [
//                 'd' => '03',
//                 'key' => 'Jum',
//                 'date' => new Carbon(),
//             ],
//             [
//                 'd' => '04',
//                 'key' => 'Jum',
//                 'date' => new Carbon(),
//             ],
//             [
//                 'd' => '05',
//                 'key' => 'Jum',
//                 'date' => new Carbon(),
//             ],
//             [
//                 'd' => '06',
//                 'key' => 'Jum',
//                 'date' => new Carbon(),
//             ],
//             [
//                 'd' => '07',
//                 'key' => 'Jum',
//                 'date' => new Carbon(),
//             ],
//             [
//                 'd' => '08',
//                 'key' => 'Jum',
//                 'date' => new Carbon(),
//             ],
//             [
//                 'd' => '09',
//                 'key' => 'Jum',
//                 'date' => new Carbon(),
//             ],
//             [
//                 'd' => '10',
//                 'key' => 'Jum',
//                 'date' => new Carbon(),
//             ],
//         ],
//         'reports' =>  [
//             [
//                 'attendances' => [
//                     [
//                         'd' => '01',
//                         'key' => 'Jum',
//                         'date' => new Carbon(),
//                         'value' => '01',
//                     ],
//                     [
//                         'd' => '02',
//                         'key' => 'Jum',
//                         'date' => new Carbon(),
//                         'value' => '02',
//                     ],
//                     [
//                         'd' => '03',
//                         'key' => 'Jum',
//                         'date' => new Carbon(),
//                         'value' => '03',
//                     ],
//                     [
//                         'd' => '04',
//                         'key' => 'Jum',
//                         'date' => new Carbon(),
//                         'value' => '04',
//                     ],
//                     [
//                         'd' => '05',
//                         'key' => 'Jum',
//                         'date' => new Carbon(),
//                         'value' => '05',
//                     ],
//                     [
//                         'd' => '06',
//                         'key' => 'Jum',
//                         'date' => new Carbon(),
//                         'value' => '06',
//                     ],
//                     [
//                         'd' => '07',
//                         'key' => 'Jum',
//                         'date' => new Carbon(),
//                         'value' => '07',
//                     ],
//                     [
//                         'd' => '08',
//                         'key' => 'Jum',
//                         'date' => new Carbon(),
//                         'value' => '08',
//                     ],
//                     [
//                         'd' => '09',
//                         'key' => 'Jum',
//                         'date' => new Carbon(),
//                         'value' => '09',
//                     ],
//                     [
//                         'd' => '10',
//                         'key' => 'Jum',
//                         'date' => new Carbon(),
//                         'value' => '10',
//                     ],
//                 ],
//                 'employee' => [
//                     'name' => 'Zikri Zm',
//                 ],
//                 'HK_value' => 7,
//                 'JL_value' => 7,
//                 'salary_value' => 100000,
//                 'kasbon_value' => 100000,
//                 'overtime_value' => 100000,
//                 'tbhn_u_libur_value' => 100000,
//                 'position_value' => 100000,
//                 'total_value' => 100000,
//             ],
//             [
//                 'attendances' => [
//                     [
//                         'd' => '01',
//                         'key' => 'Jum',
//                         'date' => new Carbon(),
//                         'value' => '01',
//                     ],
//                     [
//                         'd' => '02',
//                         'key' => 'Jum',
//                         'date' => new Carbon(),
//                         'value' => '02',
//                     ],
//                     [
//                         'd' => '03',
//                         'key' => 'Jum',
//                         'date' => new Carbon(),
//                         'value' => '03',
//                     ],
//                     [
//                         'd' => '04',
//                         'key' => 'Jum',
//                         'date' => new Carbon(),
//                         'value' => '04',
//                     ],
//                     [
//                         'd' => '05',
//                         'key' => 'Jum',
//                         'date' => new Carbon(),
//                         'value' => '05',
//                     ],
//                     [
//                         'd' => '06',
//                         'key' => 'Jum',
//                         'date' => new Carbon(),
//                         'value' => '06',
//                     ],
//                     [
//                         'd' => '07',
//                         'key' => 'Jum',
//                         'date' => new Carbon(),
//                         'value' => '07',
//                     ],
//                     [
//                         'd' => '08',
//                         'key' => 'Jum',
//                         'date' => new Carbon(),
//                         'value' => '08',
//                     ],
//                     [
//                         'd' => '09',
//                         'key' => 'Jum',
//                         'date' => new Carbon(),
//                         'value' => '09',
//                     ],
//                     [
//                         'd' => '10',
//                         'key' => 'Jum',
//                         'date' => new Carbon(),
//                         'value' => '10',
//                     ],
//                 ],
//                 'employee' => [
//                     'name' => 'Zikri Zm',
//                 ],
//                 'HK_value' => 7,
//                 'JL_value' => 7,
//                 'salary_value' => 100000,
//                 'kasbon_value' => 100000,
//                 'overtime_value' => 100000,
//                 'tbhn_u_libur_value' => 100000,
//                 'position_value' => 100000,
//                 'total_value' => 100000,
//             ]
//         ],
//         'total_salary_value' => 100000,
//         'total_kasbon_value' => 100000,
//         'total_overtime_value' => 100000,
//         'total_tbhn_u_libur_value' => 100000,
//         'total_position_value' => 100000,
//         'grand_total_value' => 100000,
//     ]
// ];