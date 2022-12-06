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

class PayrollReportController extends Controller
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

    public function calculate_payroll(Request $request)
    {
        $q = '';
        if (!empty($request->input('q'))) {
            $q = $request->q;
        }

        $department_id = '1';
        if ($request->has('department_id')) {
            $department_id = $request->department_id;
        }

        $dates = [];
        $th_dates = [];
        $slug_week = ['Mgg', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        $business_id = Session::get('business_id');
        $business = Business::where('id', $business_id)->first();
        // $start_time = Carbon::parse("2022-10-16 23:59:59");
        // $end_time = Carbon::parse("2022-10-22 22:59:59");
        $start_time = Carbon::parse($request->date['start_time'])->subDay()->subDays($business->pending_day);
        $end_time = Carbon::parse($request->date['end_time']);
        $filter['start_time'] = $start_time->hour(0)->minute(0)->second(0)->format('Y-m-d H:i:s');
        $filter['end_time'] = $end_time->addDays(1)->hour(23)->minute(59)->second(59)->format('Y-m-d H:i:s');
        $dates = $this->util->generateDateRange($start_time, $end_time);
        foreach ($dates as $date_key => $date) {
            if (count($dates) - 1 != $date_key) {
                $code_day = Carbon::parse($date)->dayOfWeek;
                $th_dates[] = [
                    'slug' => $slug_week[$code_day],
                    'is_holiday' => false,
                    'holiday_name' => false,
                    'date' => $date,
                ];
            }
        }

        $employee_bio_count = $this->apiService->get_employees(($department_id != '' ? ["departments" => $department_id] : []))["count"];
        $employee_bios = $this->apiService->get_employees(array_merge(["employee_icontains" => $q, "page_size" => $employee_bio_count], ($department_id != '' ? ["departments" => $department_id] : [])))['data'];
        $attendance_manuals = Transaction::whereBetween('punch_time', [$filter['start_time'], $filter['end_time']])->get();
        $attendance_device_count = $this->apiService->get_transactions($filter)['count'];
        $attendance_devices = collect($this->apiService->get_transactions(array_merge(['page_size' => $attendance_device_count], $filter))['data']);
        foreach ($attendance_manuals as $key => $itemAttendanceM) {
            $itemAttendanceM['id'] = $itemAttendanceM['emp'];
            $attendance_devices[] = $itemAttendanceM->toArray();
        }

        $department_bios = collect($this->apiService->get_departments(["page_size" => 999])['data']);
        $shifts = Shift::where('business_id', $business_id)->get();

        $attendance_reports = [];
        Log::info("===============PEMISAH-PEMISAH-PEMISAH-PEMISAH-PEMISAH================");
        // Log::info("============================================");
        foreach (($employee_bios ?? []) as $itemEmp) {
            $report_by_dates = [];
            $attendance_employee = $attendance_devices->where('emp', $itemEmp['id'])->sortBy('punch_time')->groupBy(function ($item) {
                return Carbon::parse($item['punch_time'])->format('Y-m-d');
            });

            $employee = Employee::where('business_id', $business_id)->where('emp_id', $itemEmp['id'])->first();
            $employee_dept_parent = $department_bios->where('id', $itemEmp['department']['id'])->first()['parent_dept'];
            $department = Department::where('dept_id', $itemEmp['department']['id'])->first();
            $employee_shift = $shifts->where('dept_id', empty($employee_dept_parent) ? $itemEmp['department']['id'] : $employee_dept_parent['id'])->first();
            $daily_salary = $employee->daily_salary ?? 0;
            $operational_start_time = Carbon::parse($request->date['start_time'])->format('Y-m-d');
            $operational_end_time = Carbon::parse($request->date['end_time'])->format('Y-m-d');
            // $operational_start_time = "2022-10-16";
            // $operational_end_time = "2022-10-22";
            $operational = Operational::where('business_id', $business_id)->where('dept_id', $itemEmp['department']['id'])->whereBetween('date', [$operational_start_time, $operational_end_time])
                ->with('operational_has_timetables.timetable')->get()->groupBy(function ($item) {
                    return Carbon::parse($item->date)->format('Y-m-d');
                });

            $range_payment_period = 0;
            if (!empty($employee)) {
                switch ($employee->payment_period) {
                    case 'mounthly':
                        $range_payment_period = $start_time->diffInMonths($end_time);
                        break;
                    case 'weekly':
                        $range_payment_period = $start_time->diffInWeeks($end_time);
                        break;
                    case 'daily':
                        $range_payment_period = $start_time->diffInDays($end_time);
                        break;
                }
            }

            $kasbons = EmployeeDebt::where('business_id', $business_id)->where('paid', 0)->where('emp_id', $itemEmp['id'])->whereDate('date', '<=', $end_time)->get();
            $cicilan_kasbon_total = 0;
            $instalment_total = $kasbons->sum('instalment');
            $debt_total = $kasbons->sum('debt');

            foreach ($kasbons as $itemKasbon) {
                $cicilan_kasbon_total += $itemKasbon->instalments->isNotEmpty() ? $itemKasbon->instalments->sum('instalment_debt') : 0;
            }

            $sisa_kasbon  = $debt_total - $cicilan_kasbon_total;
            $instalment_debt_total = $instalment_total * $range_payment_period;
            $instalment_debt_total = ($instalment_debt_total > $debt_total) ? $sisa_kasbon : $instalment_debt_total;

            $employee_id = $itemEmp['id'];
            $position = Position::whereHas('employee_has_position.employee', function ($e) use ($employee_id) {
                $e->where('emp_id', $employee_id);
            })->get();
            $position_no_permanen = $position->where('permanently', 0);
            $position_permanen = $position->where('permanently', '!=', 0);
            $position_extra_pay_total = $position_permanen->sum('extra_pay') * $range_payment_period;
            $emp['position'] = $position->toArray();

            $request_tasks = RequestTask::whereIn('position_id', array_column($position_no_permanen->toArray(), 'id'))->with('position')->get()->groupBy(function ($item) {
                return Carbon::parse($item->date)->format('Y-m-d');
            });


            $dates_count = count($dates);
            foreach ($dates as $date_key => $date) {
                if ($dates_count - 1 != $date_key) {
                    // Log::info("=============== date={$date}");
                    $C_date = Carbon::parse($date);
                    $timetable = [];
                    $timetable['id'] = '';
                    $timetable['check_out'] = null;
                    $timetable['check_in'] = null;
                    $timetable['overtime'] = 0;
                    $timetable['real_overtime'] = 0;
                    $timetable['early_check_in'] = 0;
                    $timetable['total_overtime_pay_per_day'] = 0;
                    $timetable['per_day'] = 0;
                    $timetable['cross_day'] = 0;
                    $timetable['calculate_one_shift'] = 0;
                    $timetable['overtime_rice_count'] = 0;
                    $timetable['daily_salary_day'] = 0;
                    $timetable['tbhn_u_libur'] = 0;
                    $timetable['is_holiday'] = false;
                    $timetable['is_half_day'] = false;
                    $timetable['is_less_than_time'] = false;
                    $timetable['is_pending_day'] = $date_key < $business->pending_day + 1;
                    $timetable['is_pending_kasbon'] = (($dates_count - $business->pending_day) - 2) < $date_key;
                    $timetable['atten_value_day'] = '';
                    $timetable['code_day'] = $C_date->dayOfWeek;

                    $holiday = Holiday::where('business_id', $business_id)->whereDate('start_date', '<=', $C_date)
                        ->whereDate('end_date', '>=', $C_date)->first();
                    if ($C_date->isSunday() || !empty($holiday)) {
                        if (!empty($department) && $department->still_paid) {
                            $th_dates[$date_key]['is_holiday'] = true;
                            $th_dates[$date_key]['holiday_name'] = empty($holiday) ? 'Hari libur' : $holiday->name;
                            $timetable['is_holiday'] = true;
                            $timetable['code_day'] = 6;
                            $timetable['per_day']++;
                        }
                    }

                    $attendance_item_perdate = collect($attendance_employee[$date] ?? []);
                    if ($attendance_item_perdate->isNotEmpty()) {
                        // $attendance_item_perdate
                        $timetable['first_punch'] = $attendance_item_perdate->first()['punch_time'];
                        $timetable['last_punch'] = $attendance_item_perdate->last()['punch_time'];
                        $punch_check_in = Carbon::parse($timetable['first_punch']);
                        $punch_check_out = Carbon::parse($timetable['last_punch']);

                        $itemShiftDay = $employee_shift->shiftday->where('code_day', $timetable['code_day'])->first();
                        foreach ($itemShiftDay->shiftday_has_timetable as $keyHas => $itemHasTimetable) {
                            $timeT = $itemHasTimetable->timetable;
                            $timeT_check_in = Carbon::parse($date . $timeT->check_in);
                            $timeT_check_out = Carbon::parse($date . $timeT->check_out);
                            $timeT_check_in_add_plusmn = $timeT_check_in->copy()->subMinutes($timeT->check_in_plusmn);
                            $timeT_check_in_sub_plusmn = $timeT_check_in->copy()->addMinutes($timeT->check_in_plusmn);
                            $timeT_check_out_plus_ot =  $timeT_check_out->copy()->addDays($timeT->cross_day ?? 0)->addHours($timeT->duration_ot_limit);
                            $timeT_check_out_cross =  $timeT_check_out->copy()->addDays($timeT->cross_day ?? 0);
                            $timetable['check_in_plusmn'] = $timeT->check_in_plusmn;
                            $timetable['check_out_plusmn'] = $timeT->check_out_plusmn;

                            if ($punch_check_in->between($timeT_check_in_add_plusmn, $timeT_check_in_sub_plusmn)) {
                                $timetable['id'] = $timeT->id;
                                $timetable['name'] = $timeT->name;
                                $timetable['cross_day'] = $timeT->cross_day;
                                $timetable['check_out'] = $timeT->check_out;
                                $timetable['check_in'] = $timeT->check_in;

                                $attendance_time_cross = [];
                                $attendance_time_uncross = [];
                                $next_date_key = $dates[$date_key + 1];
                                if (!empty($attendance_employee[$next_date_key])) {
                                    foreach ($attendance_employee[$next_date_key] as $item) {
                                        $punch_check_in_next_date = Carbon::parse($item['punch_time']);
                                        if ($punch_check_in_next_date->lte($timeT_check_out_plus_ot)) {
                                            $attendance_time_cross[] = $item;
                                        } else $attendance_time_uncross[] = $item;
                                    }
                                }

                                if (!empty($attendance_time_cross)) {
                                    // Log::info($attendance_time_cross);
                                    $attendance_employee[$date]->push(...$attendance_time_cross);
                                    // array_push($attendance_employee[$date], ...$attendance_time_cross);
                                    $attendance_employee[$next_date_key] = collect($attendance_time_uncross);
                                    // dirubah karena timetable nya ad CROSS-nya
                                    // biar perhitungan jam keluarnya berubah
                                    $timetable['last_punch'] = $attendance_time_cross[count($attendance_time_cross) - 1]['punch_time'];
                                    $timetable['diff_time_punch'] = Carbon::parse($timetable['first_punch'])->diff(Carbon::parse($timetable['last_punch']));
                                    $punch_check_out = Carbon::parse($timetable['last_punch']);
                                }

                                if ($punch_check_in->lt($timeT_check_in)) {
                                    $diff_time_in = $punch_check_in->diffInSeconds($timeT_check_in);
                                    $minute = intval(gmdate('i', $diff_time_in));
                                    $timetable['early_check_in'] += intval(gmdate('G', $diff_time_in));
                                    if ($minute >= $timeT->ot_roundhalf_hr && $minute < $timeT->ot_roundone_hr) {
                                        $timetable['early_check_in'] = $timetable['early_check_in'] + 0.5;
                                    } else if ($minute >= $timeT->ot_roundone_hr) {
                                        $timetable['early_check_in']++;
                                    }
                                    $timetable['real_overtime'] += $timetable['early_check_in'];

                                    $ot_period = $timeT->ot_period ?? 1;
                                    $ot_pay = $timeT->ot_pay ?? 1;
                                    $timetable['total_earlyin_pay_per_day']  = ((($timetable['early_check_in'] ?? 0) * 60) / $ot_period) * $ot_pay;
                                    $timetable['total_overtime_pay_per_day'] += $timetable['total_earlyin_pay_per_day'];
                                }

                                if ($attendance_item_perdate->count() != 1) {
                                    if ($punch_check_out->gte($timeT_check_out_cross)) {
                                        $diff_time_out = $timeT_check_out_cross->diffInSeconds($punch_check_out);
                                        $minute = intval(gmdate('i', $diff_time_out));
                                        $hour = intval(gmdate('G', $diff_time_out));

                                        $timetable['overtime'] += $hour + $timetable['early_check_in'];
                                        $timetable['real_overtime'] += $hour;
                                        if ($minute >= $timeT->ot_roundhalf_hr && $minute < $timeT->ot_roundone_hr) {
                                            $timetable['overtime'] += 0.5;
                                            $timetable['real_overtime'] += 0.5;
                                        } else if ($minute >= $timeT->ot_roundone_hr) {
                                            $timetable['overtime']++;
                                            $timetable['real_overtime']++;
                                        }

                                        if ($timeT->ot_period) {
                                            $ot_period = $timeT->ot_period;
                                            $ot_pay = $timeT->ot_pay;
                                            if ($timeT->duration_count_one_shift <= $timetable['overtime']) {
                                                $timetable['calculate_one_shift'] = floor($timetable['overtime'] / ($timeT->duration_count_one_shift));
                                                $timetable['overtime'] = $timetable['overtime'] - ($timeT->duration_count_one_shift * $timetable['calculate_one_shift']);
                                            }
                                            $timetable['total_overtime_pay_per_day'] += ((($timetable['overtime'] ?? 0) * 60) / $ot_period) * $ot_pay;
                                        }
                                    }

                                    if (!empty($timeT->duration_rice_shift) && $timetable['real_overtime'] > $timeT->duration_rice_shift) {
                                        $timetable['overtime_rice_count'] += floor($timetable['real_overtime'] / $timeT->duration_rice_shift);
                                    }

                                    $break_times = $timeT->timetable_has_break_time;
                                    $break_time_first = (!empty($break_times) && count($break_times)) ? $break_times->first() : null;
                                    if ($break_time_first) {
                                        $break_time_start = Carbon::parse($date . $break_time_first->break_time->start_time)->subMinutes($timeT->check_out_plusmn);
                                        $break_time_end = Carbon::parse($date . $break_time_first->break_time->end_time);
                                        $timetable['is_half_day'] = $punch_check_out->between($break_time_start, $break_time_end);
                                        $timetable['is_less_than_time'] = $punch_check_out->lt($break_time_start);
                                    }
                                    if (!$timetable['is_less_than_time']) {
                                        if ($timetable['is_half_day']) {
                                            if ($timeT->is_without_break) {
                                                $timetable['per_day'] += 1;
                                                $timetable['is_half_day'] = false;
                                            } else {
                                                $timetable['is_half_day'] = true;
                                                $timetable['per_day'] += 0.5;
                                            }
                                        } else {
                                            $timetable['per_day'] += 1;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if ($timetable['is_pending_day']) {
                        $timetable['atten_value_day'] = 0;
                        $timetable['atten_value_day'] += $timetable['overtime'];
                        if ($timetable['atten_value_day'] == 0) {
                            $timetable['atten_value_day'] = '-';
                        }
                    } else if ($timetable['is_pending_kasbon']) {
                        if (!$timetable['is_holiday'] && $attendance_item_perdate->isEmpty()) {
                            $timetable['atten_value_day'] = 'x';
                        } else if (!$timetable['is_holiday'] && $timetable['is_half_day']) {
                            $timetable['atten_value_day'] = '1/2';
                        } else if ($timetable['is_holiday']) {
                            $timetable['atten_value_day'] = 0;
                        }
                    } else {
                        if (!$timetable['is_holiday'] && $attendance_item_perdate->isEmpty()) {
                            $timetable['atten_value_day'] = 'x';
                        } else if (!$timetable['is_holiday'] && $timetable['is_half_day']) {
                            $timetable['atten_value_day'] = '1/2';
                        } else if ($timetable['is_holiday']) {
                            if ($attendance_item_perdate->isNotEmpty() && $timetable['id']) {
                                $timetable['atten_value_day'] = 0;
                                $timetable['atten_value_day'] += $timetable['overtime'];
                            }
                        } else {
                            $timetable['atten_value_day'] = 0;
                            $timetable['atten_value_day'] += $timetable['overtime'];
                        }
                    }

                    $timetable['daily_salary_day'] += $timetable['per_day']  * $daily_salary;
                    $operational_atten_by_date = $operational->get($date);
                    if (!empty($operational_atten_by_date)) {

                        foreach ($operational_atten_by_date as $value) {
                            foreach ($value->operational_has_timetables as $timetable_operational) {
                                if ($timetable['id'] != '' && $timetable['id'] == $timetable_operational->timetable->id) {
                                    if ($timetable_operational->status == 'active') {
                                        $timetable['status']['status'] = 'active';
                                        $_punch_check_out = Carbon::parse($timetable['last_punch']);
                                        $_timeT_check_out_cross_plus_ot_limit_op = Carbon::parse($date . $timetable['check_out'])->addDays($timetable['cross_day'] ?? 0)->addHours($timetable_operational->ot_limit ?? 0);
                                        $shift_bagian_check_out_add_plusmn = Carbon::parse($date . $timetable['check_out'])->subMinutes($timetable['check_out_plusmn']);
                                        $diff_check_out_hrs = $_timeT_check_out_cross_plus_ot_limit_op->diffInHours($_punch_check_out, false);

                                        if ($_punch_check_out->gte($shift_bagian_check_out_add_plusmn)) {
                                            if ($timetable['real_overtime'] == $timetable_operational->ot_limit) {
                                                $timetable['status']['valid'] = true;
                                                $timetable['status']['for'] = 'approved-tso';
                                            } else if ($timetable['real_overtime'] < $timetable_operational->ot_limit || $timetable['real_overtime'] > $timetable_operational->ot_limit) {
                                                $timetable['status']['valid'] = false;
                                                $timetable['status']['for'] = 'approved-tso';
                                                $timetable['status']['noted'] = 'Operasional dan kehadiran karyawan tidak sesuai';
                                            } else if ($_timeT_check_out_cross_plus_ot_limit_op->lt($_punch_check_out)) {
                                                $timetable['status']['valid'] = false;
                                                $timetable['status']['for'] = 'approved-tso';
                                                $timetable['status']['noted'] = 'Operasional dan kehadiran karyawan tidak sesuai';
                                            }
                                        } else {
                                            $timetable['status']['valid'] = false;
                                            $timetable['status']['for'] = 'approved-tso';
                                            $timetable['status']['noted'] = 'Operasional dan kehadiran karyawan tidak sesuai';
                                        }
                                    } else {
                                        $timetable['status']['valid'] = false;
                                        $timetable['status']['status'] = 'inactive';
                                        $timetable['status']['for'] = 'approved-tso';
                                        $timetable['status']['noted'] = 'Operasional diliburkan, tetapi karyawan masuk';
                                    }

                                    break;
                                } else {
                                    if ($timetable_operational->status == 'active') {
                                        $timetable['status']['status'] = 'active';
                                        if ($attendance_item_perdate->isEmpty()) {
                                            $timetable['status']['valid'] = false;
                                            $timetable['status']['for'] = 'approved-LB';
                                            $timetable['status']['noted'] = 'Operasional hadir, tetapi karyawan tidak masuk';
                                        } else {
                                            $timetable['status']['valid'] = false;
                                            $timetable['status']['for'] = 'approved-tso';
                                            $timetable['status']['noted'] = 'Operasional dan kehadiran karyawan tidak sesuai';
                                        }
                                        break;
                                    } else {
                                        $timetable['status']['status'] = 'inactive';
                                        if ($attendance_item_perdate->isEmpty()) {
                                            $timetable['status']['valid'] = true;
                                            $timetable['status']['for'] = 'cancel-LB';
                                        } else {
                                            $timetable['status']['valid'] = false;
                                            $timetable['status']['for'] = 'approved-tso';
                                            $timetable['status']['noted'] = 'Operasional diliburkan, tetapi karyawan masuk';
                                        }
                                    }
                                }
                            }
                        }

                        $employee_tso = EmployeeTso::where('tso_date', $date)->where('emp_id', $itemEmp['id'])->first();
                        if (!empty($employee_tso)) {
                            $timetable['status']['valid'] = true;
                            $timetable['status']['noted'] = null;
                        }

                        if ($attendance_item_perdate->isEmpty()) {
                            if (!$timetable['is_holiday'] && !empty($department)) {
                                $employee_status_lb = EmployeeStatusLb::where('lb_date', $date)->where('emp_id', $itemEmp['id'])->where('type', ($timetable['status']['for'] == 'cancel-LB') ? 'cancel' : 'given')->first();
                                if (!empty($employee_status_lb)) {
                                    if ($employee_status_lb->type == 'given') {
                                        $timetable['tbhn_u_libur'] += $department->sitting_money ?? 0;
                                        $timetable['atten_value_day'] = 'LB';
                                    }
                                } else {
                                    if ($timetable['status']['status'] == 'inactive' && $timetable['status']['valid']) {
                                        $timetable['tbhn_u_libur'] += $department->sitting_money ?? 0;
                                        $timetable['atten_value_day'] = 'LB';
                                    }
                                }
                            }
                        } else {
                            if ($attendance_item_perdate->count() > 1 && !empty($request_tasks[$date])) {
                                $timetable['tbhn_u_libur'] += $request_tasks[$date]->sum('position.extra_pay');
                            }
                        }
                    } else {
                        if ($timetable['is_holiday']) {
                            $timetable['status']['valid'] = true;
                        }
                    }

                    if ($date_key != 0) {
                        $report_by_dates[] = [
                            "date" => $date,
                            "is_less_than_time" => $timetable['is_less_than_time']  ?? null,
                            "is_diff_day" => $timetable['is_diff_day'] ?? null,
                            "first_punch" => $timetable['first_punch'] ?? null,
                            "last_punch" => $timetable['last_punch'] ?? null,
                            "total_time" => (!empty($timetable['diff_time_punch'])) ? $timetable['diff_time_punch']->format('%H:%I') : null,
                            "timetable" => $timetable,
                        ];
                    }
                }
            }


            $amout_of_ot = 0;
            $amout_of_ot_pay = 0;
            $early_check_in = 0;
            $early_check_in_pay = 0;
            $amount_day = 0;
            $tbhn_u_libur_total = 0;
            $daily_salary_total = 0;
            $total = 0;
            foreach ($report_by_dates as $value) {
                if (!empty($value['timetable'])) {
                    $amout_of_ot += $value['timetable']['overtime'] ?? 0;
                    $amout_of_ot_pay += $value['timetable']['total_overtime_pay_per_day'] ?? 0;
                    $early_check_in += $value['timetable']['early_check_in'] ?? 0;
                    $early_check_in_pay += $value['timetable']['total_earlyin_pay_per_day'] ?? 0;
                    $amount_day += $value['timetable']['per_day'] ?? 0;
                    $tbhn_u_libur_total += $value['timetable']['tbhn_u_libur'] ?? 0;
                    $daily_salary_total += $value['timetable']['daily_salary_day'] ?? 0;
                }
            }
            $total = (($amout_of_ot_pay  + $tbhn_u_libur_total + $position_extra_pay_total) - $instalment_debt_total) + ($amount_day * $daily_salary);

            $attendance_reports[] = [
                'employee' => [
                    'id' => $itemEmp['id'],
                    'emp_code' => $itemEmp['emp_code'],
                    'first_name' => $itemEmp['first_name'],
                    'last_name' => $itemEmp['last_name'],
                    'photo' => $itemEmp['photo'],
                    'department' => $itemEmp['department'],
                ],
                'range_date' => $request->input('date'),
                'daily_salary' => $daily_salary,
                'amount_day' => $amount_day,
                'amount_of_ot' => $amout_of_ot,
                'amount_of_ot_pay' => $amout_of_ot_pay,
                'amount_early_check_in' => $early_check_in,
                'amount_early_check_in_pay' => $early_check_in_pay,
                'position_extra_pay_total' => $position_extra_pay_total,
                'instalment_debt_total' => $instalment_debt_total,
                'tbhn_u_libur_total' => $tbhn_u_libur_total,
                'daily_salary_total' => $daily_salary_total,
                'total' => $total,
                'reports' => $report_by_dates,
            ];
        }

        unset($th_dates[0]);

        return collect([
            'attendance_reports' => $attendance_reports,
            'th_dates' => $th_dates
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
        if (!auth()->user()->can('payroll-report.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            if (request()->ajax()) {
                $data = $this->calculate_payroll($request);
                $attendance_reports = $data['attendance_reports'];
                $th_dates = $data['th_dates'];
                Log::info(response()->json($attendance_reports));
                $order = null;
                $render =  view('Report.payroll_report.table', compact('attendance_reports', 'th_dates', 'order'))->render();
                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            }

            $department_bios = collect($this->apiService->get_departments(['page_size' => 999])['data']);
            return  view('Report.payroll_report.index', compact('department_bios'));
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
        if (!auth()->user()->can('payroll-report.calculate') || !request()->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $start_time = Carbon::parse($request->start_date);
            $end_time = Carbon::parse($request->end_date);
            $dates = $this->util->generateDateRange($start_time, $end_time);
            $departments = $this->apiService->get_departments(['page_size' => 999])['data'];

            $render = view('Report.payroll_report.calculate', compact('dates', 'departments'))->render();
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
        if (!auth()->user()->can('payroll-report.calculate')  || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = Session::get('business_id');
            $data = $this->calculate_payroll($request);
            $attendance_reports = collect($data['attendance_reports']);
            $data_rices = [];
            foreach ($attendance_reports as $key => $itemReport) {
                $salary_archives = new SalaryArchive([
                    'business_id' => $business_id,
                    'start_date' => $itemReport['range_date']['start_time'],
                    'end_date' => $itemReport['range_date']['end_time'],
                    'emp_id' => $itemReport['employee']['id'],
                    'emp_code' => $itemReport['employee']['emp_code'],
                    'emp_first_name' => $itemReport['employee']['first_name'],
                    'emp_last_name' => $itemReport['employee']['last_name'],
                    'photo' => $itemReport['employee']['photo'],
                    'dept_id' => $itemReport['employee']['department']['id'],
                    'dept_code' => $itemReport['employee']['department']['dept_code'],
                    'dept_name' => $itemReport['employee']['department']['dept_name'],
                    'amount_day' => $itemReport['amount_day'],
                    'amount_of_ot' => $itemReport['amount_of_ot'],
                    'amount_of_ot_pay' => $itemReport['amount_of_ot_pay'],
                    'amount_early_check_in' => $itemReport['amount_early_check_in'],
                    'amount_early_check_in_pay' => $itemReport['amount_early_check_in_pay'],
                    'position_extra_pay_total' => $itemReport['position_extra_pay_total'],
                    'instalment_debt_total' => $itemReport['instalment_debt_total'],
                    'tbhn_u_libur_total' => $itemReport['tbhn_u_libur_total'],
                    'daily_salary_total' => $itemReport['daily_salary_total'],
                    'total' => $itemReport['total'],
                    'created_user' => auth()->user()->id,
                    'updated_user' => auth()->user()->id,
                ]);

                $salary_archives->save();

                $dept_id = $itemReport['employee']['department']['id'];
                if (!isset($data_rices[$dept_id])) {
                    $data_rices[$dept_id] = [
                        "department" => $itemReport['employee']['department'],
                        "start_date" => $request['date']['start_time'],
                        "end_date" => $request['date']['end_time'],
                        "reports" => [],
                    ];
                }

                foreach ($itemReport['reports'] as $key => $itemPerday) {
                    $data_rices[$dept_id]['reports'][$itemPerday['date']][] = [
                        'employee' => [
                            'emp_id' => $itemReport['employee']['id'],
                            'emp_code' =>  $itemReport['employee']['emp_code'],
                            'emp_first_name' =>  $itemReport['employee']['first_name'],
                            'emp_last_name' =>  $itemReport['employee']['last_name'],
                            'photo' =>  $itemReport['employee']['photo'],
                        ],
                        'rice_date' => $itemPerday['date'],
                        'total' => $itemPerday['timetable']['overtime_rice_count'] ?? 0,
                    ];

                    $salary_archives_perday = new SalaryArchivePerday([
                        'salary_archive_id' => $salary_archives->id,
                        'timetable_id' => $itemPerday['timetable']['id'],
                        'date' => $itemPerday['date'],
                        'first_punch' => $itemPerday['first_punch'],
                        'last_punch' => $itemPerday['last_punch'],
                        'is_less_than_time' => $itemPerday['is_less_than_time'],
                        'atten_value_day' => strval($itemPerday['timetable']['atten_value_day']),
                        'status' => (!empty($itemPerday['status'])) ? $itemPerday['status']['valid'] : false,
                        'status' => (!empty($itemPerday['status'])) ? $itemPerday['status']['noted'] : null,
                        'total_tbhn_u_libur_day' => $itemPerday['total_tbhn_u_libur_day'],
                        'total_daily_salary_day' => $itemPerday['total_daily_salary_day'],
                        'total_overtime_day' => $itemPerday['total_overtime_day'],
                    ]);
                    $salary_archives_perday->save();
                }
            }

            foreach ($data_rices as $key => $itemRice) {
                $ot_rice_bill = new OtRicebill([
                    'dept_id' => $itemRice['department']['dept_id'],
                    'dept_code' => $itemRice['department']['dept_code'],
                    'dept_name' => $itemRice['department']['dept_name'],
                    'start_date' => $itemRice['start_date'],
                    'end_date' => $itemRice['end_date'],
                ]);
                $ot_rice_bill->save();

                foreach ($itemRice['reports'] as $key => $itemReportDate) {
                    foreach ($itemReportDate as $key => $itemDate) {
                        $ot_rice_bill_perday = new OtRicebillPerday([
                            'ot_rice_bill_id' => $ot_rice_bill->idate,
                            'emp_id' => $itemRice['department']['emp_id'],
                            'emp_code' => $itemRice['emp_code'],
                            'emp_first_name' => $itemRice['department']['emp_first_name'],
                            'emp_last_name' => $itemRice['department']['emp_last_name'],
                            'photo' => $itemRice['department']['photo'],
                            'rice_date' => $itemRice['rice_date'],
                            'total' => $itemRice['total'],
                        ]);
                        $ot_rice_bill_perday->save();
                    }
                }
            }



            Log::info(response()->json($data_rices));
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
            'name' => 'required|string|max:255',
            'holiday_date' => 'required',
        ];
    }
}
