<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Shift;
use App\Utils\ResponseUtil;
use App\Models\EmployeeDebt;
use App\Models\Holiday;
use App\Models\Operational;
use App\Models\Position;
use App\Models\RequestTask;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Services\Api\ApiServices;
use App\Utils\Util;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AttenOpReportController extends Controller
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
        if (!auth()->user()->can('attendance-card.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = Session::get('business_id');
            if (request()->ajax()) {
                // * Pagination page
                $page = 1;
                if (!empty($request->input('page'))) {
                    $page = (int)$request->page;
                }

                // * Employee search
                $search = 'Erwan';
                if (!empty($request->input('q'))) {
                    $search = $request->q;
                }
                // * Employee dept search
                $dept_id = null;
                if ($request->has('dept_id') && $request->dept_id != 'all') {
                    $dept_id = $request->dept_id;
                }

                // * Employee filter
                $filter = [];

                $dates = [];
                $th_dates = [];
                $attenDBs = [];
                $slug_week = ['mgg', 'sen', 'sel', 'rab', 'kam', 'jum', 'sab'];
                if (!empty($request->input('date'))) {
                    $start_time = Carbon::parse("2022-10-16 23:59:59");
                    $end_time = Carbon::parse("2022-10-29 23:59:59");
                    // $start_time = Carbon::parse($request->date['start_time']);
                    // $end_time = Carbon::parse($request->date['end_time']);
                    $filter['start_time'] = $start_time->hour(0)->minute(0)->second(0)->format('Y-m-d H:i:s');
                    $filter['end_time'] = $end_time->addHours(1)->hour(23)->minute(59)->second(59)->format('Y-m-d H:i:s');
                    $attenDBs = Transaction::whereBetween('punch_time', [$filter['start_time'], $filter['end_time']])->get();
                    $dates = $this->util->generateDateRange($start_time, $end_time);

                    foreach ($dates as $date) {
                        $code_day = Carbon::parse($date)->dayOfWeek;
                        $th_dates[] = ['slug' => $slug_week[$code_day], 'date' => $date];
                    }
                }

                // ** get department data dari biotime
                $dept_bios = $this->apiService->get_departments(["page_size" => 999])['data'];
                // ** get employee data dari biotime
                // $emp_bio_count = $this->apiService->get_employees([])["count"];
                $emp_bios = $this->apiService->get_employees(array_merge(["employee_icontains" => $search, "page_size" => 12], (!is_null($dept_id) ? ["departments" => $dept_id] : [])))['data'];
                // ** get absensi data dari biotime
                $atten_bio_count = $this->apiService->get_transactions($filter)['count'];
                $atten_bios = collect($this->apiService->get_transactions(array_merge(['page_size' => $atten_bio_count], $filter))['data']);
                foreach ($attenDBs as $key => $value) {
                    $value['id'] = $value['emp'];
                    $atten_bios[] = $value->toArray();
                }

                // ** get employee data dari database local
                $emp_form_databases = Employee::where('business_id', $business_id)->get();
                // ** get libur data dari database local
                $holidays = Holiday::where('business_id', $business_id)->whereBetween('start_date', array($start_time, $end_time))
                    ->orWhereBetween('end_date', array($start_time, $end_time))->get();
                // ** get shift data dari database local
                $shifts = Shift::where('business_id', $business_id)->with(['shiftday.shiftday_has_timetable'])->get();
                // ** get operational data dari database local
                $operationals = Operational::where('business_id', $business_id)->whereBetween('date', [$start_time, $end_time])
                    ->with('operational_has_timetables.timetable.timetable_has_break_time.break_time')->get()->groupBy(function ($item) {
                        return Carbon::parse($item->date)->format('Y-m-d');
                    });

                $attendance_reports = [];
                Log::info("===============PEMISAH-PEMISAH-PEMISAH-PEMISAH-PEMISAH================");
                // Log::info($holidays);
                // Log::info("============================================");
                foreach (($emp_bios ?? []) as $emp) {
                    $report_by_dates = [];
                    $attens_groupings = $this->_group_by_date($atten_bios->filter(function ($atten) use ($emp) {
                        return $atten['emp'] === $emp['id'];
                    }));
                    // ** searchDepartment
                    $emp_dept = collect($dept_bios)->search(function ($item) use ($emp) {
                        return $item['id'] === $emp['department']['id'];
                    });
                    $emp['department'] = $dept_bios[$emp_dept];
                    $dept_id = !empty($emp['department']['parent_dept'])
                        ? $emp['department']['parent_dept']['id']
                        : $emp['department']['id'];

                    // ** filterkasbon
                    // $emp_debts = $debts->filter(function ($item) use ($emp) {
                    //     return $item->emp_id === $emp['id'];
                    // });
                    $group = !empty($operational) ? ($operational->operational_has_depts ?? [])->filter(function ($item) use ($emp) {
                        return $item->dept_id === $emp['department']['id'];
                    }) : [];


                    $departmentDB = Department::where('dept_id', $emp['department']['id'])->first();
                    $empDB = Employee::where('business_id', $business_id)->where('emp_id', $emp['id'])->first();
                    $diff_date = 0;
                    if (!empty($empDB)) {
                        switch ($empDB->payment_period) {
                            case 'mounthly':
                                $diff_date = $start_time->diffInMonths($end_time);
                                break;
                            case 'weekly':
                                $diff_date = $start_time->diffInWeeks($end_time);
                                break;
                            case 'daily':
                                $diff_date = $start_time->diffInDays($end_time);
                                break;
                        }
                    }
                    $position = Position::whereHas('employee_has_position.employee', function ($e) use ($emp) {
                        $e->where('emp_id', $emp['id']);
                    })->get();
                    $position_no_permanen = $position->where('permanently', 0);
                    $position_permanen = $position->where('permanently', '!=', 0);
                    $request_tasks = RequestTask::whereIn('position_id', array_column($position_no_permanen->toArray(), 'id'))->with('position')->get()->groupBy(function ($item) {
                        return Carbon::parse($item->date)->format('Y-m-d');
                    });
                    $position_extra_pay = array_sum(array_column($position_permanen->toArray(), 'extra_pay')) * $diff_date;
                    $emp['position'] = (!empty($position_permanen)) ? $position_permanen->toArray() : [];

                    $kasbons = EmployeeDebt::where('business_id', $business_id)->where('paid', 0)->where('emp_id', $emp['id'])->whereDate('date', '<=', $end_time)->with('instalments')->get();
                    $cicilan_kasbon_total = 0;
                    $instalment_total = 0;
                    $debt_total = 0;

                    foreach ($kasbons as $key => $kasbon_item) {
                        $cicilan_kasbon_total += (!empty($kasbon_item->instalments)) ? array_sum(array_column($kasbon_item->instalments->toArray(), 'instalment_debt')) : 0;
                        $instalment_total += $kasbon_item->instalment;
                        $debt_total += $kasbon_item->debt;
                    }

                    $sisa_kasbon  = $debt_total - $cicilan_kasbon_total;
                    $instalment_debt_total = $instalment_total * $diff_date;
                    $instalment_debt_total = ($instalment_debt_total > $debt_total) ? $sisa_kasbon : $instalment_debt_total;
                    $daily_salary = $empDB->daily_salary ?? 0;

                    Log::info("sisa_kasbon $sisa_kasbon ");

                    foreach ($dates as $date_key => $date) {
                        if (count($dates) - 1 != $date_key) {
                            // Log::info("=============== date={$date}");
                            $timetable = [];
                            $timetable['id'] = '';
                            $timetable['name'] = '';
                            $timetable['early_check_in'] = 0;
                            $timetable['total_overtime_pay_per_day'] = 0;
                            $timetable['count_one_shift'] = 0;
                            $timetable['total_overtime_pay_per_day'] = 0;
                            $timetable['per_day'] = 0;
                            $timetable['break_time_total'] = 0;
                            $timetable['cross_day'] = 0;
                            $timetable['daily_salary_per_day'] = 0;
                            $timetable['tbhn_u_libur'] = 0;
                            $timetable['is_holiday'] = false;
                            $timetable['is_half_day'] = false;
                            $timetable['is_less_than_time'] = false;
                            $timetable['is_difference_day'] = false;
                            $timetable['holidays_by_date'] = $this->is_holiday($holidays, $date);
                            $timetable['weekday'] = Carbon::create($date)->locale('id_ID')->dayName;
                            $timetable['slug'] = $slug_week[Carbon::parse($date)->dayOfWeek];

                            if (Carbon::parse($date)->isSunday() || !empty($timetable['holidays_by_date'])) {
                                // Log::info($departmentDB);
                                // Log::info("still_paid= {$departmentDB->still_paid} date={$date}");
                                if (!empty($departmentDB) && $departmentDB->still_paid) {
                                    $timetable['is_holiday'] = true;
                                    $timetable['per_day']++;
                                }
                            }

                            // foreach ($attens_groupings[$date] as $key => $value) {
                            //     # code...
                            //     // Log::info("punch_check_in={$value['punch_time']} punch_check_out={$value['punch_time']}");
                            // }

                            if (!empty($attens_groupings[$date])) {
                                $atten_item = $attens_groupings[$date];
                                $atten_first = $atten_item[0];
                                $atten_last = $atten_item[count($atten_item) - 1];
                                $timetable['first_punch'] = $atten_item[0]['punch_time'];
                                $timetable['last_punch'] =  $atten_item[count($atten_item) - 1]['punch_time'];

                                $timetable['diff_time_punch'] = Carbon::parse($timetable['first_punch'])->diff(Carbon::parse($timetable['last_punch']));
                                // $punch_check_in = Carbon::parse($atten_first['punch_time'])->format('H:i:s');
                                // $punch_check_out = Carbon::parse($atten_last['punch_time'])->format('H:i:s');
                                // $punch_check_in = Carbon::createFromTimeString($punch_check_in);
                                // $punch_check_out = Carbon::createFromTimeString($punch_check_out);
                                $punch_check_in = Carbon::parse($timetable['first_punch']);
                                $punch_check_out = Carbon::parse($timetable['last_punch']);
                                $code_day = Carbon::parse($date)->dayOfWeek;
                                $timetable['code_day'] = $code_day;

                                $is_lest_punch = false;
                                $is_diff_day = false;
                                foreach ($shifts as $shift) {
                                    if ($dept_id == $shift->dept_id) {
                                        foreach ($shift->shiftday as $shiftday) {
                                            // CHECK HARI MINGGU BUKAN
                                            if ((!empty($timetable['holidays_by_date']) && count($timetable['holidays_by_date']) ? $shiftday->code_day == 6 : $code_day == $shiftday->code_day)) {
                                                foreach ($shiftday->shiftday_has_timetable as $keyHas => $shiftdayHas) {
                                                    // Log::info("crossday = {$shiftdayHas->timetable->cross_day}");

                                                    // Log::info("name = {$shiftdayHas->timetable->name} date={$date}");
                                                    // $timetable_check_in = Carbon::createFromTimeString($shiftdayHas->timetable->check_in);
                                                    // $timetable_check_out = Carbon::createFromTimeString($shiftdayHas->timetable->check_out);
                                                    // $timetable_check_in_add_plusmn = Carbon::createFromTimeString($shiftdayHas->timetable->check_in)->subMinutes($shiftdayHas->timetable->check_in_plusmn);
                                                    // $timetable_check_in_sub_plusmn = Carbon::createFromTimeString($shiftdayHas->timetable->check_in)->addMinutes($shiftdayHas->timetable->check_in_plusmn);

                                                    $shift_bagian_check_in = Carbon::parse($date . $shiftdayHas->timetable->check_in);
                                                    $shift_bagian_check_out = Carbon::parse($date . $shiftdayHas->timetable->check_out);
                                                    $shift_bagian_check_in_add_plusmn = Carbon::parse($date . $shiftdayHas->timetable->check_in)->subMinutes($shiftdayHas->timetable->check_in_plusmn);
                                                    $shift_bagian_check_in_sub_plusmn = Carbon::parse($date . $shiftdayHas->timetable->check_in)->addMinutes($shiftdayHas->timetable->check_in_plusmn);
                                                    $timetable['check_in_plusmn'] = $shiftdayHas->timetable->check_in_plusmn;
                                                    $timetable['check_out_plusmn'] = $shiftdayHas->timetable->check_out_plusmn;

                                                    $shift_check_out_plus_ot = Carbon::parse($date . $shiftdayHas->timetable->check_out)->addDays($shiftdayHas->timetable->cross_day ?? 0)->addHours($shiftdayHas->timetable->duration_ot_limit);
                                                    $shift_bagian_check_out_cross = Carbon::parse($date . $shiftdayHas->timetable->check_out)->addDays($shiftdayHas->timetable->cross_day ?? 0);
                                                    // Log::info("shift_bagian_check_out_cross {$shift_bagian_check_out_cross}");
                                                    // Log::info('cross_ss'.  Carbon::parse($date . $shiftdayHas->timetable->check_out)->addDays($shiftdayHas->timetable->cross_day ?? 0));

                                                    // foreach ($attens_groupings as $itess) {
                                                    //     foreach ($itess as $key => $value) {
                                                    //         Log::info("tanggal={$value['punch_time']} shift_bagian_check_out_cross={$shift_bagian_check_out_cross}");
                                                    //     }
                                                    // }

                                                    // Log::info("shift_bagian_check_in = {$shift_bagian_check_in} shift_bagian_check_out={$shift_bagian_check_out} ");
                                                    // Log::info("timetable_check_in = {$timetable_check_in} timetable_check_out={$timetable_check_out} duration_ot_limit= {$shiftdayHas->timetable->duration_ot_limit}");

                                                    if ($punch_check_in->between($shift_bagian_check_in_add_plusmn, $shift_bagian_check_in_sub_plusmn)) {
                                                        $timetable['id'] = $shiftdayHas->timetable->id;
                                                        $timetable['name'] = $shiftdayHas->timetable->name;
                                                        $timetable['cross_day'] = $shiftdayHas->timetable->cross_day;
                                                        $timetable['check_out'] = $shiftdayHas->timetable->check_out;
                                                        $timetable['check_in'] = $shiftdayHas->timetable->check_in;
                                                        $timetable['overtime'] = 0;
                                                        $timetable['real_overtime'] = 0;
                                                        // Log::info("timetable_check_in = {$timetable_check_in} timetable_check_out={$timetable_check_out} duration_ot_limit= {$shiftdayHas->timetable->duration_ot_limit}");
                                                        // Log::info("shift_bagian_check_in = {$shift_bagian_check_in} shift_bagian_check_out={$shift_bagian_check_out} duration_ot_limit= {$shiftdayHas->timetable->duration_ot_limit} punch_check_out_add_ot_limit = {$punch_check_out_add_ot_limit}");
                                                        // Log::info("timetable_check_in = {$timetable_check_in} timetable_check_out={$timetable_check_out} duration_ot_limit= {$shiftdayHas->timetable->duration_ot_limit} punch_check_out_add_ot_limit = {$punch_check_out_add_ot_limit}");


                                                        $break_times = $shiftdayHas->timetable->timetable_has_break_time;
                                                        $break_time_first = null;
                                                        if (!empty($break_times) && count($break_times)) {
                                                            $break_time_first = $shiftdayHas->timetable->timetable_has_break_time[0];
                                                            foreach ($shiftdayHas->timetable->timetable_has_break_time as $key => $value) {
                                                                $break_time_start = Carbon::createFromTimeString($value->break_time->start_time);
                                                                $break_time_end = Carbon::createFromTimeString($value->break_time->end_time);
                                                                $break_time_dif = $break_time_start->diffInMinutes($break_time_end);
                                                                $timetable['break_time_total'] += $break_time_dif;
                                                            }

                                                            $timetable['is_without_break'] = $shiftdayHas->timetable->is_without_break;
                                                            // if ($shiftdayHas->timetable->is_without_break) {
                                                            // $shift_bagian_check_out->subMinutes($timetable['break_time_total']);
                                                            // $shift_bagian_check_out_cross->subMinutes($timetable['break_time_total']);
                                                            // }
                                                        }

                                                        // Log::info("sebelum shift_bagian_check_out=$shift_bagian_check_out");

                                                        // Log::info("setelah shift_bagian_check_out=$shift_bagian_check_out");


                                                        $cross_data_attendances = [];
                                                        $no_cross_data_attendances = [];
                                                        $next_date_index = $dates[$date_key + 1];

                                                        if (!empty($attens_groupings[$next_date_index])) {
                                                            // $punch_check_out_add_ot_limit = Carbon::createFromTimeString($shiftdayHas->timetable->check_out)->addHours($shiftdayHas->timetable->duration_ot_limit);
                                                            // $punch_check_out_add_ot_limit = Carbon::createFromTimeString($punch_check_out_add_ot_limit);
                                                            // Log::info(count($attens_groupings[$next_date_index]));

                                                            foreach ($attens_groupings[$next_date_index] as $key_next_atten => $item) {
                                                                $check_in_next = Carbon::parse($item['punch_time']);
                                                                $shiftssss = Carbon::parse($date . $shiftdayHas->timetable->check_out);
                                                                // Log::info("shift_bagian_check_int {$shift_bagian_check_in} shift_bagian_check_out {$shift_bagian_check_out} check_in_next= {$check_in_next} punch_check_out_add_ot_limit= {$punch_check_out_add_ot_limit}");
                                                                // $punch_check_in_next_day = Carbon::createFromTimeString($check_in_next);
                                                                if ($check_in_next->lte($shift_check_out_plus_ot)) {
                                                                    $cross_data_attendances[] = $item;
                                                                } else $no_cross_data_attendances[] = $item;
                                                            }
                                                        }

                                                        // // Log::info(response()->json($cross_data_attendances));

                                                        if (!empty($cross_data_attendances)) {
                                                            array_push($attens_groupings[$dates[$date_key]], ...$cross_data_attendances);
                                                            $attens_groupings[$next_date_index] = $no_cross_data_attendances;
                                                            // dirubah karena timetable nya ad CROSS-nya
                                                            // biar perhitungan jam keluarnya berubah
                                                            $timetable['last_punch'] = $cross_data_attendances[count($cross_data_attendances) - 1]['punch_time'];
                                                            $timetable['diff_time_punch'] = Carbon::parse($timetable['first_punch'])->diff(Carbon::parse($timetable['last_punch']));
                                                            $punch_check_out = Carbon::parse($timetable['last_punch']);
                                                            $is_diff_day = $shift_bagian_check_out_cross->diff($punch_check_out)->days < 1;
                                                        }

                                                        // Log::info(response()->json($attens_groupings));

                                                        if ($punch_check_in->lt($shift_bagian_check_in)) {
                                                            $diff_time_in = $punch_check_in->diffInSeconds($shift_bagian_check_in);
                                                            $minute = intval(gmdate('i', $diff_time_in));
                                                            $timetable['early_check_in'] += intval(gmdate('G', $diff_time_in));
                                                            if ($minute >= $shiftdayHas->timetable->ot_roundhalf_hr && $minute < $shiftdayHas->timetable->ot_roundone_hr) {
                                                                $timetable['early_check_in'] = $timetable['early_check_in'] + 0.5;
                                                            } else if ($minute >= $shiftdayHas->timetable->ot_roundone_hr) {
                                                                $timetable['early_check_in']++;
                                                            }

                                                            $ot_period = $shiftdayHas->timetable->ot_period ?? 1;
                                                            $ot_pay = $shiftdayHas->timetable->ot_pay ?? 1;
                                                            $timetable['total_earlyin_pay_per_day']  = ((($timetable['early_check_in'] ?? 0) * 60) / $ot_period) * $ot_pay;
                                                            $timetable['total_overtime_pay_per_day'] += $timetable['total_earlyin_pay_per_day'];
                                                        }



                                                        if (count($attens_groupings[$date]) != 1) {
                                                            if ($punch_check_out->gte($shift_bagian_check_out_cross)) {
                                                                // if (empty($shiftdayHas->timetable->cross_day))
                                                                //     $punch_check_out = Carbon::createFromTimeString($punch_check_out)->addDays($shiftdayHas->timetable->cross_day);

                                                                // $diff_time_out = Carbon::parse($date . $shiftdayHas->timetable->check_out)->diffInSeconds($punch_check_out);
                                                                $diff_time_out = $shift_bagian_check_out_cross->diffInSeconds($punch_check_out);
                                                                // $diff_time_out_m = $shift_bagian_check_out_cross->diffInMinutes($punch_check_out);
                                                                // Log::info("shift_bagian_check_out={$shift_bagian_check_out} shift_bagian_check_out={$shift_bagian_check_out} punch_check_out={$punch_check_out}");

                                                                $minute = intval(gmdate('i', $diff_time_out));
                                                                $hour = intval(gmdate('G', $diff_time_out));

                                                                $timetable['overtime'] += $hour;
                                                                $timetable['real_overtime'] += $hour;
                                                                if ($minute >= $shiftdayHas->timetable->ot_roundhalf_hr && $minute < $shiftdayHas->timetable->ot_roundone_hr) {
                                                                    $timetable['overtime'] += 0.5;
                                                                    $timetable['real_overtime'] += 0.5;
                                                                } else if ($minute >= $shiftdayHas->timetable->ot_roundone_hr) {
                                                                    $timetable['overtime']++;
                                                                    $timetable['real_overtime']++;
                                                                }

                                                                // Log::info("punch_check_out $punch_check_out duration_count_one_shift {$shiftdayHas->timetable->duration_count_one_shift} overtime={$timetable['overtime']} date $date hour={$hour} minute=" . $minute);

                                                                if ($shiftdayHas->timetable->ot_period) {
                                                                    $ot_period = $shiftdayHas->timetable->ot_period;
                                                                    $ot_pay = $shiftdayHas->timetable->ot_pay;
                                                                    if ($shiftdayHas->timetable->duration_count_one_shift <= $timetable['overtime']) {
                                                                        $timetable['per_day'] += floor($timetable['overtime'] / ($shiftdayHas->timetable->duration_count_one_shift));
                                                                        $timetable['overtime'] = $timetable['overtime'] % ($shiftdayHas->timetable->duration_count_one_shift);
                                                                        // Log::info('overtime onve=' . $hour % ($shiftdayHas->timetable->duration_count_one_shift));
                                                                    }
                                                                    // Log::info("overtime= {$timetable['overtime']}");
                                                                    // if ($timetable['per_day'] >= 1) {
                                                                    //     $timetable['overtime'] -= (!empty($timetable['holidays_by_date']) ?  ($timetable['per_day'] - 1) : $timetable['per_day']) * $shiftdayHas->timetable->duration_count_one_shift ?? 0;
                                                                    // }
                                                                    $timetable['total_overtime_pay_per_day'] += ((($timetable['overtime'] ?? 0) * 60) / $ot_period) * $ot_pay;
                                                                }
                                                                // if ($minute >= $shiftdayHas->timetable->ot_roundhalf_hr && $minute < $shiftdayHas->timetable->ot_roundone_hr) {
                                                                //     $timetable['overtime'] += 0.5;
                                                                // } else if ($minute >= $shiftdayHas->timetable->ot_roundone_hr) {
                                                                //     $timetable['overtime']++;
                                                                // }
                                                                // Log::info("overtime sebelum {$timetable['overtime']} minute {$minute}");

                                                                // Log::info("overtime setelah {$timetable['overtime']}");

                                                            }


                                                            if ($break_time_first) {
                                                                $break_time_start = Carbon::parse($date . $break_time_first->break_time->start_time)->subMinutes($shiftdayHas->timetable->check_out_plusmn);
                                                                $break_time_end = Carbon::parse($date . $break_time_first->break_time->end_time);
                                                                $timetable['is_half_day'] = $punch_check_out->between($break_time_start, $break_time_end);
                                                                $timetable['is_less_than_time'] = $punch_check_out->lt($break_time_start);
                                                            }
                                                            if (!$timetable['is_less_than_time']) {
                                                                if ($timetable['is_half_day']) {
                                                                    if ($shiftdayHas->timetable->is_without_break) {
                                                                        // $shift_bagian_check_out_break_time = Carbon::parse($date . $shiftdayHas->timetable->check_out)->subMinutes($timetable['break_time_total']);
                                                                        // Log::info("shift_bagian_check_out_break_time $shift_bagian_check_out_break_time");
                                                                        // if ($punch_check_out->lt($shift_bagian_check_out_break_time)) {
                                                                        //     $timetable['is_half_day'] = true;
                                                                        //     $timetable['per_day'] += 0.5;
                                                                        // } else {
                                                                        $timetable['per_day'] += 1;
                                                                        $timetable['is_half_day'] = false;
                                                                        // }
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
                                        }
                                    }
                                }
                                // Log::info(response()->json($timetable));
                                // $first_punch = $atten_first['punch_time'];
                                // $last_punch = (count($attens_groupings[$date]) != 1) ? $atten_last['punch_time'] : null;
                                // if ((count($attens_groupings[$date]) > 1)) {


                                $report_by_date =  [
                                    "date" => $date,
                                    "holidays_by_date" => $timetable['holidays_by_date']  ?? null,
                                    "is_less_than_time" => $timetable['is_less_than_time']  ?? null,
                                    "is_diff_day" => $timetable['is_diff_day'] ?? null,
                                    "first_punch" => $timetable['first_punch']  ?? null,
                                    "last_punch" => (count($attens_groupings[$date]) > 1) ? $timetable['last_punch'] : null,
                                    "total_time" => $timetable['diff_time_punch']->format('%H:%I'),
                                    "timetable" => $timetable,
                                ];
                            } else {
                                $report_by_date =  [
                                    "date" => $date,
                                    "holidays_by_date" => $timetable['holidays_by_date']  ?? null,
                                    "is_less_than_time" => false,
                                    "is_diff_day" => false,
                                    "first_punch" => null,
                                    "last_punch" => null,
                                    "total_time" => null,
                                    "timetable" => $timetable,
                                ];
                            }

                            $report_by_date['timetable']['daily_salary_per_day'] += $timetable['per_day']  * $daily_salary;

                            $operational_atten_by_date = $operationals[$date] ?? [];
                            $i_operational = empty($operational_atten_by_date) ? '' : array_search($emp['department']['id'], array_column($operational_atten_by_date->toArray(), 'dept_id'));
                            if ($i_operational != '') {
                                $is_inactive = true;
                                $operational_has_timetables = $operational_atten_by_date[$i_operational]->operational_has_timetables ?? [];
                                foreach ($operational_has_timetables as $key => $op_timetable) {
                                    if ($op_timetable->operational->dept_id == $emp['department']['id']) {
                                        if (!empty($report_by_date['timetable']['id'])) {
                                            if ($op_timetable->status == 'active') {
                                                $is_inactive = false;
                                                $_punch_check_out = Carbon::parse($report_by_date['timetable']['last_punch']);
                                                $_shift_check_out_cross_plus_ot_limit_op = Carbon::parse($date . $report_by_date['timetable']['check_out'])->addDays($report_by_date['timetable']['cross_day'] ?? 0)->addHours($op_timetable->ot_limit ?? 0);
                                                $shift_bagian_check_out_add_plusmn = Carbon::parse($date . $report_by_date['timetable']['check_out'])->subMinutes($report_by_date['timetable']['check_out_plusmn']);
                                                $diff_check_out_mnt = $_shift_check_out_cross_plus_ot_limit_op->diffInMinutes($_punch_check_out, false);
                                                $diff_check_out_hrs = $_shift_check_out_cross_plus_ot_limit_op->diffInHours($_punch_check_out, false);
                                                $hours = $diff_check_out_mnt / 60;
                                                $status_plusm = $report_by_date['timetable']['real_overtime'] - ($op_timetable->ot_limit ?? 0);
                                                Log::info("_punch_check_out=$_punch_check_out date=$date shift_bagian_check_out_add_plusmn=$shift_bagian_check_out_add_plusmn");

                                                if ($_punch_check_out->gte($shift_bagian_check_out_add_plusmn) && $report_by_date['timetable']['real_overtime'] == ($op_timetable->ot_limit ?? 0)) {
                                                    $report_by_date['timetable']['status']['slug'] = 'check';
                                                } else if ($_punch_check_out->gte($shift_bagian_check_out_add_plusmn) && $report_by_date['timetable']['real_overtime'] < $op_timetable->ot_limit || $report_by_date['timetable']['real_overtime'] > $op_timetable->ot_limit) {
                                                    $report_by_date['timetable']['status']['slug'] = 'plusmn';
                                                    $report_by_date['timetable']['status']['value'] = ($report_by_date['timetable']['real_overtime'] > $op_timetable->ot_limit) ?  '+' . $report_by_date['timetable']['real_overtime'] - $op_timetable->ot_limit
                                                        : $report_by_date['timetable']['real_overtime'] - $op_timetable->ot_limit;
                                                } else if ($_punch_check_out->gte($shift_bagian_check_out_add_plusmn) && $_shift_check_out_cross_plus_ot_limit_op->lt($_punch_check_out)) {
                                                    $report_by_date['timetable']['status']['slug'] = 'not-allowed';
                                                } else {
                                                    $report_by_date['timetable']['status']['slug'] = 'plusmn';
                                                    $report_by_date['timetable']['status']['value'] = $diff_check_out_hrs;
                                                }
                                                // if ($_shift_check_out_cross_plus_ot_limit_op->lte($_punch_check_out)) {
                                                //     $diff_check_out_hrs = $_shift_check_out_cross_plus_ot_limit_op->diffInHours($_punch_check_out, false);
                                                //     $report_by_date['timetable']['status']['slug'] = 'plusmn';
                                                //     $report_by_date['timetable']['status']['value'] = $diff_check_out_hrs;
                                                // }else if($_shift_check_out_cross_plus_ot_limit_op->lte($_punch_check_out))
                                                // if (ceil($hours) == 0) {
                                                //     $report_by_date['timetable']['status']['slug'] = 'check';
                                                // } else {
                                                //     $report_by_date['timetable']['status']['slug'] = 'plusmn';
                                                //     $report_by_date['timetable']['status']['value'] = $hours < 0 ? $hours : '+' . $status_plusm;
                                                // }
                                                break;
                                            } else {
                                                $report_by_date['timetable']['status']['slug'] = 'not-allowed';
                                            }
                                        } else {
                                            if ($op_timetable->status == 'active') {
                                                $is_inactive = false;
                                                $report_by_date['timetable']['status']['slug'] = 'not-allowed';
                                                break;
                                            } else {
                                                $report_by_date['timetable']['status']['slug'] = 'check';
                                            }
                                        }
                                    }
                                }

                                if (!empty($attens_groupings[$date]) && count($attens_groupings[$date]) > 1 && !empty($request_tasks[$date])) {
                                    foreach ($request_tasks[$date] as $key => $task) {
                                        $report_by_date['timetable']['tbhn_u_libur'] += $task->position->extra_pay;
                                    }
                                }

                                if ($is_inactive && empty($attens_groupings[$date]) && !$report_by_date['timetable']['is_holiday']) {
                                    if (!empty($departmentDB)) $report_by_date['timetable']['tbhn_u_libur'] += $departmentDB->sitting_money ?? 0;
                                }
                                // $timetable_is_exist = false;
                                // if (!empty($attens_groupings[$date]) && count($attens_groupings[$date]) > 1) {
                                //     foreach ($operational_has_timetables as $key => $op_timetable) {
                                //         if (!empty($report_by_date['timetable']['id']) && $op_timetable->timetable->id == $report_by_date['timetable']['id']) {
                                //             // $timetable_is_exist = true;
                                //             if ($op_timetable->status == 'active') {
                                //                 $_punch_check_out = Carbon::parse($report_by_date['timetable']['last_punch']);
                                //                 $_shift_check_out_cross_plus_ot_limit_op = Carbon::parse($date . $report_by_date['timetable']['check_out'])->addDays($report_by_date['timetable']['cross_day'] ?? 0)->addHours($op_timetable->ot_limit ?? 0);
                                //                 $diff_check_out_mnt = $_shift_check_out_cross_plus_ot_limit_op->diffInMinutes($_punch_check_out, false);
                                //                 $diff_check_out_hrs = $_shift_check_out_cross_plus_ot_limit_op->diffInHours($_punch_check_out, false);
                                //                 $hours = $diff_check_out_mnt / 60;
                                //                 if (ceil($hours) == 0) {
                                //                     $report_by_date['timetable']['status']['slug'] = 'check';
                                //                 } else {
                                //                     $report_by_date['timetable']['status']['slug'] = 'plusmn';
                                //                     $report_by_date['timetable']['status']['value'] = (ceil($hours) < 0) ?  ceil($hours) : '+' . ($report_by_date['timetable']['overtime'] - ($op_timetable->ot_limit ?? 0));
                                //                 }
                                //             } else {
                                //                 $report_by_date['timetable']['status']['slug'] = 'not-allowed';
                                //             }
                                //         }
                                //     }
                                // } else {
                                //     $report_by_date['timetable']['status']['slug'] = 'check';
                                // }
                            } else {
                            }

                            $report_by_dates[] = $report_by_date;
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
                            $daily_salary_total += $value['timetable']['daily_salary_per_day'] ?? 0;
                        }
                    }
                    // $total = (($amout_of_ot_pay + $early_check_in_pay + $tbhn_u_libur_total + $position_extra_pay) - $instalment_debt_total) + ($amount_day * $daily_salary);
                    $total = (($amout_of_ot_pay + $tbhn_u_libur_total + $position_extra_pay) - $instalment_debt_total) + ($amount_day * $daily_salary);
                    Log::info("amout_of_ot_pay $amout_of_ot_pay early_check_in_pay $early_check_in_pay tbhn_u_libur_total $tbhn_u_libur_total position_extra_pay $position_extra_pay instalment_debt_total $instalment_debt_total");
                    $attendance_reports[] = [
                        'employee' => $emp,
                        'range_date' => $request->input('date'),
                        'daily_salary' => $daily_salary,
                        'position_extra_pay' => $position_extra_pay,
                        'instalment_debt_total' => $instalment_debt_total,
                        'sisa_kasbon' => $sisa_kasbon,
                        'amount_of_ot' => $amout_of_ot,
                        'amout_of_ot_pay' => $amout_of_ot_pay,
                        'early_check_in' => $early_check_in,
                        'early_check_in_pay' => $early_check_in_pay,
                        'amount_day' => $amount_day,
                        'tbhn_u_libur_total' => $tbhn_u_libur_total,
                        'daily_salary_total' => $daily_salary_total,
                        'total' => $total,
                        'reports' => $report_by_dates,
                    ];
                }

                // Log::info(response()->json($attendance_reports));
                $order = null;
                $render =  view('Report.attendance_operational.table', compact('attendance_reports', 'order'))->render();

                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            }
            $dept_bios = $this->apiService->get_departments(["page_size" => 999]);
            return  view('Report.attendance_operational.index', compact('dept_bios'));
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

    public function is_holiday($holidays, $date)
    {
        $holiday_datas = [];
        $date_atten = Carbon::parse($date);
        foreach ($holidays as $item) {
            if ($date_atten->between(Carbon::parse($item->start_date), Carbon::parse($item->end_date))) {
                $holiday_datas[] = $item;
            }
        }

        return $holiday_datas;
    }
}
