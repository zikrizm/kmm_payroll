<?php

namespace App\Http\Controllers;

use App\Utils\Util;
use App\Models\Shift;
use App\Models\Holiday;
use App\Models\Employee;
use App\Models\Department;
use App\Models\ActivityLog;
use App\Models\EmployeeNotLb;
use App\Models\EmployeeTso;
use App\Models\Operational;
use App\Models\Transaction;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
            $department_bios = collect($this->apiService->get_departments(['page_size' => 999])['data']);
            return  view('Task.TSO.index', compact('department_bios'));
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    public function table_tso(Request $request)
    {
        if (!auth()->user()->can('employee-tso.view') || !request()->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        $page_size = 10;
        $page = 1;
        if (!empty($request->input('page'))) {
            $page = (int)$request->page;
        }

        if ($request->has('page_size')) {
            $page_size = $request->page_size;
        }
        $q = '';
        if (!empty($request->input('q'))) {
            $q = $request->q;
        }

        $department_id = '';
        if ($request->has('department_id')) {
            $department_id = $request->department_id;
        }

        $business_id = Session::get('business_id');
        $slug_week = ['mgg', 'sen', 'sel', 'rab', 'kam', 'jum', 'sab'];
        // $start_time = Carbon::parse("2022-10-16 23:59:59");
        // $end_time = Carbon::parse("2022-10-22 23:59:59");
        $start_time = Carbon::parse($request->date['start_time']);
        $end_time = Carbon::parse($request->date['end_time']);
        $filter['start_time'] = $start_time->hour(0)->minute(0)->second(0)->format('Y-m-d H:i:s');
        $filter['end_time'] = $end_time->addDays(1)->hour(23)->minute(59)->second(59)->format('Y-m-d H:i:s');
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

        // $attens_groupings = $attendance_devices->sortBy('punch_time')->groupBy(function ($item) {
        //     return Carbon::parse($item['punch_time'])->format('Y-m-d') . '(' . $item['emp'] . ')';
        // });
        $shifts = Shift::where('business_id', $business_id)->get();
        $department_bios = collect($this->apiService->get_departments(["page_size" => 999])['data']);
        $attens_groupings = $attendance_devices->sortBy('punch_time')->groupBy([function ($item) {
            return "emp_id_{$item['emp']} - emp_code_{$item['emp_code']}";
        }, function ($item) {
            return Carbon::parse($item['punch_time'])->format('Y-m-d');
        }]);
        $employee_tsos = collect();
        Log::info("===============PEMISAH-PEMISAH-PEMISAH-PEMISAH-PEMISAH================");
        foreach (($attens_groupings ?? []) as $key => $attendance_employee) {
            $emp_code = explode('_', explode(" - ", $key)[1])[2];
            // Log::info('emp_code ' . $emp_code);
            // if ($emp_code == 12)
                // Log::info(response()->json($attendance_employee));
            $employee_bios = $this->apiService->get_employees(array_merge(['emp_code' => $emp_code], ($department_id != '' ? ["departments" => $department_id] : [])))['data'];
            if (empty($employee_bios)) continue;
            else $employee_bios = $employee_bios[0];

            $employee = Employee::where('business_id', $business_id)->where('emp_id', $employee_bios['id'])->first();
            $employee_dept_parent = $department_bios->where('id', $employee_bios['department']['id'])->first()['parent_dept'];
            $department = Department::where('dept_id', $employee_bios['department']['id'])->first();
            $employee_shift = $shifts->where('dept_id', empty($employee_dept_parent) ? $employee_bios['department']['id'] : $employee_dept_parent['id'])->first();
            $operational_start_time = Carbon::parse($request->date['start_time'])->format('Y-m-d');
            $operational_end_time = Carbon::parse($request->date['end_time'])->format('Y-m-d');
            $operational = Operational::where('business_id', $business_id)->where('dept_id', $employee_bios['department']['id'])->whereBetween('date', [$operational_start_time, $operational_end_time])
                ->with('operational_has_timetables.timetable')->get()->groupBy(function ($item) {
                    return Carbon::parse($item->date)->format('Y-m-d');
                });
            // if ($emp_code == 5)
                // Log::info("===============================");

            foreach ($dates as $date_key => $date) {
                if (count($dates) - 1 != $date_key) {
                    // Log::info("=============== date={$date}");
                    $C_date = Carbon::parse($date);
                    $timetable = [];
                    $timetable['id'] = '';
                    $timetable['name'] = '';
                    $timetable['cross_day'] = null;
                    $timetable['check_out'] = null;
                    $timetable['check_in'] = null;
                    $timetable['overtime'] = 0;
                    $timetable['real_overtime'] = 0;
                    $timetable['early_check_in'] = 0;
                    $timetable['total_overtime_pay_per_day'] = 0;
                    $timetable['count_one_shift'] = 0;
                    $timetable['per_day'] = 0;
                    $timetable['break_time_total'] = 0;
                    $timetable['cross_day'] = 0;
                    $timetable['daily_salary_per_day'] = 0;
                    $timetable['tbhn_u_libur'] = 0;
                    $timetable['is_holiday'] = false;
                    $timetable['is_half_day'] = false;
                    $timetable['is_less_than_time'] = false;
                    $timetable['is_difference_day'] = false;
                    $timetable['weekday'] = $C_date->locale('id_ID')->dayName;
                    $timetable['slug'] = $slug_week[$C_date->dayOfWeek];
                    $timetable['calculate_atten_per_day'] = '';

                    $holidays = Holiday::where('business_id', $business_id)->whereDate('start_date', '<=', $C_date)
                        ->whereDate('end_date', '>=', $C_date)->get();

                    $timetable['code_day'] = $C_date->dayOfWeek;
                    if ($C_date->isSunday() || $holidays->isNotEmpty()) {
                        if (!empty($department) && $department->still_paid) {
                            $th_dates[$date_key]['is_holiday'] = true;
                            $timetable['is_holiday'] = true;
                            $timetable['code_day'] = 6;
                            $timetable['per_day']++;
                        }
                    }

                    $attendance_item_perdate = collect($attendance_employee[$date] ?? []);
                    if ($attendance_item_perdate->isNotEmpty()) {
                        // if ($emp_code == 5)
                        // Log::info("emp=$emp_code date $date count {$attendance_item_perdate->count()}");
                        $timetable['first_punch'] = $attendance_item_perdate->first()['punch_time'];
                        $timetable['last_punch'] = $attendance_item_perdate->last()['punch_time'];
                        $punch_check_in = Carbon::parse($timetable['first_punch']);
                        $punch_check_out = Carbon::parse($timetable['last_punch']);

                        $itemShiftDay = $employee_shift->shiftday->where('code_day', $timetable['code_day'])->first();
                        foreach ($itemShiftDay->shiftday_has_timetable as $keyHas => $itemHasTimetable) {
                            $timeT = $itemHasTimetable->timetable;
                            $timeT_check_in = Carbon::parse($date . $timeT->check_in);
                            $timeT_check_out = Carbon::parse($date . $timeT->check_out);
                            $timeT_check_in_add_plusmn = Carbon::parse($date . $timeT->check_in)->subMinutes($timeT->check_in_plusmn);
                            $timeT_check_in_sub_plusmn = Carbon::parse($date . $timeT->check_in)->addMinutes($timeT->check_in_plusmn);
                            $timetable['check_in_plusmn'] = $timeT->check_in_plusmn;
                            $timetable['check_out_plusmn'] = $timeT->check_out_plusmn;
                            $timeT_check_out_plus_ot = Carbon::parse($date . $timeT->check_out)->addDays($timeT->cross_day ?? 0)->addHours($timeT->duration_ot_limit);
                            $timeT_check_out_cross = Carbon::parse($date . $timeT->check_out)->addDays($timeT->cross_day ?? 0);
                            if ($punch_check_in->between($timeT_check_in_add_plusmn, $timeT_check_in_sub_plusmn)) {
                                $timetable['id'] = $timeT->id;
                                $timetable['name'] = $timeT->name;
                                $timetable['cross_day'] = $timeT->cross_day;
                                $timetable['check_out'] = $timeT->check_out;
                                $timetable['check_in'] = $timeT->check_in;

                                $break_times = $timeT->timetable_has_break_time;
                                $break_time_first = null;
                                if (!empty($break_times) && count($break_times)) {
                                    $break_time_first = $timeT->timetable_has_break_time[0];
                                    foreach ($timeT->timetable_has_break_time as $key => $value) {
                                        $break_time_start = Carbon::createFromTimeString($value->break_time->start_time);
                                        $break_time_end = Carbon::createFromTimeString($value->break_time->end_time);
                                        $break_time_dif = $break_time_start->diffInMinutes($break_time_end);
                                        $timetable['break_time_total'] += $break_time_dif;
                                    }

                                    $timetable['is_without_break'] = $timeT->is_without_break;
                                }

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
                                    $is_diff_day = $timeT_check_out_cross->diff($punch_check_out)->days < 1;
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

                                    $ot_period = $timeT->ot_period ?? 1;
                                    $ot_pay = $timeT->ot_pay ?? 1;
                                    $timetable['total_earlyin_pay_per_day']  = ((($timetable['early_check_in'] ?? 0) * 60) / $ot_period) * $ot_pay;
                                    $timetable['total_overtime_pay_per_day'] += $timetable['total_earlyin_pay_per_day'];
                                }

                                if (count($attendance_employee[$date]) != 1) {
                                    if ($punch_check_out->gte($timeT_check_out_cross)) {
                                        $diff_time_out = $timeT_check_out_cross->diffInSeconds($punch_check_out);
                                        $minute = intval(gmdate('i', $diff_time_out));
                                        $hour = intval(gmdate('G', $diff_time_out));

                                        $timetable['overtime'] += $hour;
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
                                                $timetable['per_day'] += floor($timetable['overtime'] / ($timeT->duration_count_one_shift));
                                                $timetable['overtime'] = $timetable['overtime'] % ($timeT->duration_count_one_shift);
                                            }
                                            $timetable['total_overtime_pay_per_day'] += ((($timetable['overtime'] ?? 0) * 60) / $ot_period) * $ot_pay;
                                        }
                                    }


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

                    // if ($attendance_item_perdate->count() > 1) {
                        $operational_atten_by_date = $operational->get($date);
                        // Log::info("date=$date " . !empty($operational_atten_by_date));
                        if (!empty($operational_atten_by_date)) {
                            foreach ($operational_atten_by_date as $value) {
                                foreach ($value->operational_has_timetables as $itemOpTimeT) {
                                    if ($timetable['id'] != '' && $timetable['id'] == $itemOpTimeT->timetable->id) {
                                        if ($itemOpTimeT->status == 'active') {
                                            $_punch_check_out = Carbon::parse($timetable['last_punch']);
                                            $_timeT_check_out_cross_plus_ot_limit_op = Carbon::parse($date . $timetable['check_out'])->addDays($timetable['cross_day'] ?? 0)->addHours($itemOpTimeT->ot_limit ?? 0);
                                            $shift_bagian_check_out_add_plusmn = Carbon::parse($date . $timetable['check_out'])->subMinutes($timetable['check_out_plusmn']);
                                            $diff_check_out_hrs = $_timeT_check_out_cross_plus_ot_limit_op->diffInHours($_punch_check_out, false);
    
                                            if ($_punch_check_out->gte($shift_bagian_check_out_add_plusmn)) {
                                                if ($timetable['real_overtime'] == $itemOpTimeT->ot_limit) {
                                                    $timetable['status']['slug'] = 'check';
                                                    $timetable['status']['valid'] = true;
                                                } else if ($timetable['real_overtime'] < $itemOpTimeT->ot_limit || $timetable['real_overtime'] > $itemOpTimeT->ot_limit) {
                                                    $timetable['status']['slug'] = 'plusmn';
                                                    $timetable['status']['value'] = ($timetable['real_overtime'] > $itemOpTimeT->ot_limit) ?  '+' . $timetable['real_overtime'] - $itemOpTimeT->ot_limit
                                                        : $timetable['real_overtime'] - $itemOpTimeT->ot_limit;
                                                    $timetable['status']['valid'] = false;
                                                } else if ($_timeT_check_out_cross_plus_ot_limit_op->lt($_punch_check_out)) {
                                                    $timetable['status']['slug'] = 'not-allowed';
                                                    $timetable['status']['valid'] = false;
                                                }
                                            } else {
                                                $timetable['status']['slug'] = 'plusmn';
                                                $timetable['status']['value'] = $diff_check_out_hrs;
                                                $timetable['status']['valid'] = false;
                                            }
                                        } else {
                                            $timetable['status']['slug'] = 'not-allowed';
                                            $timetable['status']['valid'] = false;
                                        }
    
                                        break;
                                    } else {
                                        if ($itemOpTimeT->status == 'active') {
                                            $timetable['status']['slug'] = 'not-allowed';
                                            $timetable['status']['valid'] = $attendance_item_perdate->isEmpty();
                                            break;
                                        } else {
                                            if ($attendance_item_perdate->isEmpty()) {
                                                $timetable['status']['slug'] = 'check';
                                                $timetable['status']['valid'] = true;
                                            } else {
                                                $timetable['status']['slug'] = 'not-allowed';
                                                $timetable['status']['valid'] = false;
                                            }
                                        }
                                    }
                                }
    
                                if (!$timetable['status']['valid']) {
                                    $employee_tso = EmployeeTso::where('tso_date', $date)->where('emp_id', $employee_bios['id'])->first();
                                    $employee_tsos[] = [
                                        'employee' => [
                                            'id' => $employee_bios['id'],
                                            'emp_code' => $employee_bios['emp_code'],
                                            'first_name' => $employee_bios['first_name'],
                                            'last_name' => $employee_bios['last_name'],
                                            'photo' => $employee_bios['photo'],
                                            'department' => $employee_bios['department'],
                                        ],
                                        "date" => $date,
                                        "id_operasional" => $value->id,
                                        "is_less_than_time" => $timetable['is_less_than_time']  ?? null,
                                        "is_diff_day" => $timetable['is_diff_day'] ?? null,
                                        "first_punch" => $timetable['first_punch'] ?? null,
                                        "last_punch" => $timetable['last_punch'] ?? null,
                                        "total_time" => (!empty($timetable['diff_time_punch'])) ? $timetable['diff_time_punch']->format('%H:%I') : null,
                                        'is_approved_tso' => !empty($employee_tso),
                                        "timetable" => $timetable,
                                    ];
                                }
                            }
                        } else {
                        }
                    // }
                }
                
            }
        }

        $employee_tsos = $employee_tsos->sortBy('date');
        if ($q != '') {
            $employee_tsos = $employee_tsos->filter(function ($atten) use ($q) {
                return str_contains(strtolower($atten['employee']['emp_code']), strtolower($q)) || str_contains(strtolower($atten['employee']['first_name']), strtolower($q)) ||
                    str_contains(strtolower($atten['employee']['last_name']), strtolower($q));
            });
        }

        $employee_tsos_count = $employee_tsos->count();
        $next = (ceil($employee_tsos_count / $page_size) == $page) ?  null : $page + 1;
        $employee_tsos = $employee_tsos->skip(($page - 1) * $page_size)->take($page_size);
        $employee_tso_datas = collect([
            'count' => $employee_tsos_count,
            'data' => $employee_tsos,
            'next' => $next,
            'previous' => $page - 1,
            'lastPage' => ceil($employee_tsos_count / $page_size),
            'currentPage' => $page,
        ]);

        $order = null;
        $render =  view('Task.TSO.tables.tso', compact('employee_tso_datas', 'order', 'page_size'))->render();
        return $this->buildRes->RESPONSE_REQ('success', $render, null);
    }
    public function table_not_given_lb(Request $request)
    {
        if (!auth()->user()->can('employee-not-given-holiday-pay.view') || !request()->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        $page = 1;
        if ($request->has('page') && !empty($request->input('page'))) {
            $page = (int)$request->page;
        }

        $page_size = 10;
        if ($request->has('page_size') && !empty($request->input('page_size'))) {
            $page_size = $request->page_size;
        }

        $q = '';
        if ($request->input('q') && !empty($request->input('q'))) {
            $q = $request->q;
        }

        $department_id = '';
        if ($request->has('department_id')) {
            $department_id = $request->department_id;
        }

        $business_id = Session::get('business_id');
        // $start_time = Carbon::parse("2022-10-16 23:59:59");
        // $end_time = Carbon::parse("2022-10-22 23:59:59");
        $start_time = Carbon::parse($request->date['start_time']);
        $end_time = Carbon::parse($request->date['end_time']);
        $filter['start_time'] = $start_time->hour(0)->minute(0)->second(0)->format('Y-m-d H:i:s');
        $filter['end_time'] = $end_time->addHours(1)->hour(23)->minute(59)->second(59)->format('Y-m-d H:i:s');
        $dates = $this->util->generateDateRange($start_time, $end_time);

        $attendance_manuals = Transaction::whereBetween('punch_time', [$filter['start_time'], $filter['end_time']])->get();
        $attendance_device_count = $this->apiService->get_transactions($filter)['count'];
        $attendance_devices = collect($this->apiService->get_transactions(array_merge(['page_size' => $attendance_device_count], $filter))['data']);
        foreach ($attendance_manuals as $key => $itemAttendanceM) {
            $itemAttendanceM['id'] = $itemAttendanceM['emp'];
            $attendance_devices[] = $itemAttendanceM->toArray();
        }

        $department_bios = collect($this->apiService->get_departments(["page_size" => 999])['data']);
        $employee_lbs = collect();
        $operational_start_time = Carbon::parse($request->date['start_time'])->format('Y-m-d');
        $operational_end_time = Carbon::parse($request->date['end_time'])->format('Y-m-d');
        $operationals = Operational::where('business_id', $business_id);
        if ($department_id != '') $operationals = $operationals->where('dept_id', $department_id);
        $operationals = $operationals->whereBetween('date', [$operational_start_time, $operational_end_time])->whereHas('operational_has_timetables', function ($query) {
            $query->where('status', 'inactive');
        })->get();

        // Log::info(response()->json($attendance_devices));

        foreach ($operationals as $key => $itemOP) {
            $op_date = Carbon::parse($itemOP->date);
            $timetable = ['is_holiday' => false];
            $holidays = Holiday::where('business_id', $business_id)->whereDate('start_date', '<=', $op_date->format('Y-m-d'))
                ->whereDate('end_date', '>=', $op_date->format('Y-m-d'))->get();
            if ($op_date->isSunday() || $holidays->isNotEmpty()) {
                $department = Department::where('dept_id', $itemOP->dept_id)->first();
                if (!empty($department) && $department->still_paid) {
                    $timetable['is_holiday'] = true;
                }
            }
            $employee_count_bios = $this->apiService->get_employees(['employee_icontains' => $q, 'department' =>  $itemOP->dept_id])['count'];
            $employee_bios = $this->apiService->get_employees(['employee_icontains' => $q, 'department' =>  $itemOP->dept_id, 'page_size' => $employee_count_bios])['data'];
            foreach ($employee_bios as $key => $itemEmp) {
            $op_date_end_day = Carbon::parse($itemOP->date)->hour(23)->minute(59)->second(59);
                $attendance_item_perdate = $attendance_devices->where('emp', $itemEmp['id'])->whereBetween('punch_time', [$op_date, $op_date_end_day]);
                if ($attendance_item_perdate->isEmpty()) {
                    $employee_not_given_lb = EmployeeNotLb::where('lb_date', $op_date->format('Y-m-d'))->where('emp_id', $itemEmp['id'])->first();
                    $employee_lbs[] = [
                        'employee' => [
                            'id' => $itemEmp['id'],
                            'emp_code' => $itemEmp['emp_code'],
                            'first_name' => $itemEmp['first_name'],
                            'last_name' => $itemEmp['last_name'],
                            'photo' => $itemEmp['photo'],
                            'department' => $itemEmp['department'],
                        ],
                        'is_approved_not_given_lb' => !empty($employee_not_given_lb),
                        "date" => $op_date->format('Y-m-d'),
                        "timetable" => $timetable,
                    ];
                }
            }
        }

        $employee_lbs = $employee_lbs->sortBy('date');
        if ($q != '') {
            $employee_lbs = $employee_lbs->filter(function ($atten) use ($q) {
                return str_contains(strtolower($atten['employee']['emp_code']), strtolower($q)) || str_contains(strtolower($atten['employee']['first_name']), strtolower($q)) ||
                    str_contains(strtolower($atten['employee']['last_name']), strtolower($q));
            });
        }

        $employee_lbs_count = $employee_lbs->count();
        $next = (ceil($employee_lbs_count / $page_size) == $page) ?  null : $page + 1;

        $employee_lbs = $employee_lbs->skip(($page - 1) * $page_size)->take($page_size);
        $employee_lb_datas = collect([
            'count' => $employee_lbs_count,
            'data' => $employee_lbs,
            'next' => $next,
            'previous' => $page - 1,
            'lastPage' => ceil($employee_lbs_count / $page_size),
            'currentPage' => $page,
        ]);

        // Log::info(response()->json($employee_tsos));

        $order = null;
        $render =  view('Task.TSO.tables.not_given_lb', compact('employee_lb_datas', 'order', 'page_size'))->render();
        return $this->buildRes->RESPONSE_REQ('success', $render, null);
    }

    public function approved_tso(Request $request)
    {
        if (!auth()->user()->can('approved-employee-TSO.approved')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $slug = $request->slug;
            switch ($slug) {
                case 'plusmn':
                    $render = view('Task.TSO.modals.approved_tso', compact('slug'))->render();
                    return $this->buildRes->RESPONSE_REQ('success', $render, null);
                    break;
                case 'not-allowed':
                    $operational_dept_ids = Operational::select('dept_id')->where('date', $request->tso_date)->get()->pluck('dept_id');
                    $department_bios = collect($this->apiService->get_departments(['page_size' => 999])['data'])->whereIn('id', $operational_dept_ids);
                    $render = view('Task.TSO.modals.approved_tso', compact('slug', 'department_bios'))->render();
                    return $this->buildRes->RESPONSE_REQ('success', $render, null);
                    break;
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }
    public function approved_tso_store(Request $request)
    {
        if (!auth()->user()->can('approved-employee-TSO.approved')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validator = Validator::make($request->all(), [
                'tso_datas.*.tso_date' => 'required',
                'tso_datas.*.emp_id' => 'required',
                'tso_datas.*.dept_id' => 'required',
            ]);
            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $reqdata = $request->only(['tso_datas']);
                foreach ($reqdata['tso_datas'] as $itemTSO) {
                    $employee_tso = new EmployeeTso([
                        'tso_date' => Carbon::parse($itemTSO['tso_date'])->format('Y-m-d'),
                        'emp_id' => $itemTSO['emp_id'],
                        'dept_id' => $itemTSO['dept_id'],
                    ]);
                    $employee_tso->save();
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

    public function approved_not_given_lb(Request $request)
    {
        if (!auth()->user()->can('approved-not-given-employee-holiday-pay.approved')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $render = view('Task.TSO.modals.approved_not_given_lb')->render();
            return $this->buildRes->RESPONSE_REQ('success', $render, null);
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }
    public function approved_not_given_lb_store(Request $request)
    {
        if (!auth()->user()->can('approved-not-given-employee-holiday-pay.approved')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validator = Validator::make($request->all(), [
                'lb_datas.*.lb_date' => 'required',
                'lb_datas.*.emp_id' => 'required',
                'lb_datas.*.dept_id' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $reqdata = $request->only(['lb_datas']);
                foreach ($reqdata['lb_datas'] as $itemLB) {
                    $employee_not_given_lb = new EmployeeNotLb([
                        'lb_date' => $itemLB['lb_date'],
                        'emp_id' => $itemLB['emp_id'],
                        'dept_id' => $itemLB['dept_id'],
                    ]);
                    $employee_not_given_lb->save();
                }

                // ** create activity log user
                ActivityLog::created_activity('Reject wages LB', 'User ' . auth()->user()->username . ' Reject LB (libur operasional)');
                return $this->buildRes->RESPONSE_REQ('success', null,  ['success' => 'Reject wages LB succesfully']);
            }
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
