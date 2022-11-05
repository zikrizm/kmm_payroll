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
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Services\Api\ApiServices;
use App\Utils\Util;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AttendanceReportCardController extends Controller
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

    public function calculate_timetable($timetable, $req_data)
    {
        $diff_time_punch = Carbon::parse($req_data['start_punch_time'])->diff(Carbon::parse($req_data['end_punch_time']));
        $punch_check_in = Carbon::parse($req_data['start_punch_time'])->format('H:i:s');
        $punch_check_out = Carbon::parse($req_data['end_punch_time'])->format('H:i:s');
        $punch_check_in = Carbon::createFromTimeString($punch_check_in);
        $punch_check_out = Carbon::createFromTimeString($punch_check_out);
        $timetable_check_in = Carbon::createFromTimeString($timetable->check_in);
        $timetable_check_out = Carbon::createFromTimeString($timetable->check_out);
        $timetable_check_in_add_plusmn = Carbon::createFromTimeString($timetable->check_in)->subMinutes($timetable->check_in_plusmn);
        $timetable_check_in_sub_plusmn = Carbon::createFromTimeString($timetable->check_in)->addMinutes($timetable->check_in_plusmn);
        $timetable_data = [];
        if ($req_data["punch_check_in"]->between($timetable_check_in_add_plusmn, $timetable_check_in_sub_plusmn)) {
            $timetable_data['id'] = $timetable->id;
            $timetable_data['name'] = $timetable->name;
            $timetable_data['overtime'] = 0;
            $timetable_data['early_check_in'] = 0;
            $timetable_data['total_overtime_pay_per_day'] = 0;
            $timetable_data['count_one_shift'] = 0;
            $timetable_data['total_overtime_pay_per_day'] = 0;
            $timetable_data['is_half_day'] = false;
            $timetable_data['per_day'] = 0;
            $timetable_data['break_time_total'] = 0;
            $timetable_data['cross_day'] = $timetable->cross_day;


            // BREAK TIME
            $break_times = $timetable->timetable_has_break_time;
            $break_times = array_column($break_times->toArray(), 'break_time');
            foreach ($break_times as $key => $break_time) {
                $break_time_start = Carbon::createFromTimeString($break_time['start_time']);
                $break_time_end = Carbon::createFromTimeString($break_time['end_time']);
                $break_time_dif = $break_time_start->diffInHours($break_time_end);
                $timetable_data['break_time_total'] += $break_time_dif;
            }

            // BISA TANPA ISTIRAHAT
            if ($timetable->is_without_break) {
                $timetable_check_out->subMinutes($timetable_data['break_time_total']);
            }


            // GET CROSS ATTENDANCE
            $atten_cross_days = [];
            $noatten_cross_days = [];
            $dates = $req_data['dates'];
            $date = $req_data['date'];
            $date_key = $req_data['date_key'];
            $attens_groupings = $req_data['attens_groupings'];
            $next_date_index = $dates[$date_key + 1];
            if (!empty($attens_groupings[$next_date_index])) {
                $punch_check_out_add_ot_limit = Carbon::createFromTimeString($timetable->check_out)->addHours($timetable->duration_ot_limit)->format('H:i:s');
                $punch_check_out_add_ot_limit = Carbon::createFromTimeString($punch_check_out_add_ot_limit);

                foreach ($attens_groupings[$next_date_index] as $key_next_atten => $item) {
                    $check_in_next = Carbon::parse($item['punch_time'])->format('H:i:s');
                    $punch_check_in_next_day = Carbon::createFromTimeString($check_in_next);
                    if ($punch_check_in_next_day->lte($punch_check_out_add_ot_limit)) {
                        $atten_cross_days[] = $item;
                    } else $noatten_cross_days[] = $item;
                }
            }

            if (!empty($atten_cross_days)) {
                array_push($attens_groupings[$dates[$date_key]], ...$atten_cross_days);
                $attens_groupings[$next_date_index] = $noatten_cross_days;
                // dirubah karena timetable nya ada CROSS hari/ ganti hari/ beda hari
                // biar perhitungan jam keluarnya berubah
                $atten_last = $atten_cross_days[count($atten_cross_days) - 1];
                $diff_time_punch = Carbon::parse($req_data['start_punch_time'])->diff(Carbon::parse($atten_last['punch_time']));
                $punch_check_out = Carbon::createFromTimeString(Carbon::parse($atten_last['punch_time'])->format('H:i:s'));
            }

            if (count($attens_groupings[$date]) != 1) {
                // EARLY PUNCH CHECK
                if ($punch_check_in->lt($timetable_check_in)) {
                    $diff_time_in = $punch_check_in->diffInSeconds($timetable_check_in);
                    $minute = intval(gmdate('i', $diff_time_in));
                    $timetable_data['early_check_in'] += intval(gmdate('G', $diff_time_in));
                    if ($minute >= $timetable->ot_roundhalf_hr && $minute < $timetable->ot_roundone_hr) {
                        $timetable_data['early_check_in'] = $timetable_data['early_check_in'] + 0.5;
                    } else if ($minute >= $timetable->ot_roundone_hr) {
                        $timetable_data['early_check_in']++;
                    }

                    $ot_period = $timetable->ot_period;
                    $ot_pay = $timetable->ot_pay;
                    $timetable_data['total_earlyin_pay_per_day']  = ((($timetable_data['early_check_in'] ?? 0) * 60) / $ot_period) * $ot_pay;
                }

                // CHECL PUNCH LEMBUR
                if ($punch_check_out->gt($timetable_check_out)) {
                    if (empty($timetable->cross_day))
                        $punch_check_out = Carbon::createFromTimeString($punch_check_out)->addDay();

                    $diff_time_out = $timetable_check_out->diffInSeconds($punch_check_out);
                    $minute = intval(gmdate('i', $diff_time_out));
                    $hour = intval(gmdate('G', $diff_time_out));
                    $timetable_data['overtime'] += $hour;
                    if ($minute >= $timetable->ot_roundhalf_hr && $minute < $timetable->ot_roundone_hr) {
                        $timetable_data['overtime'] = $timetable_data['overtime'] + 0.5;
                    } else if ($minute >= $timetable->ot_roundone_hr) {
                        $timetable_data['overtime']++;
                    }

                    if (!empty($timetable->ot_period) && !empty($timetable->duration_count_one_shift) && !empty($timetable->ot_pay)) {
                        $ot_period = $timetable->ot_period ?? 0;
                        $ot_pay = $timetable->ot_pay ?? 0;
                        $timetable_data['per_day'] += floor($hour / ($timetable->duration_count_one_shift ?? 0));
                        if ($timetable_data['per_day'] >= 1) {
                            // JIKA LEBIH DURASI LEMBUR LEBIH BESAR
                            $timetable_data['overtime'] -= ($timetable_data['per_day']) * $timetable->duration_count_one_shift ?? 0;
                        }
                        // TOTAL BAYAR LEMBUR
                        $timetable_data['total_overtime_pay_per_day'] = ((($timetable_data['overtime'] ?? 0) * 60) / $ot_period) * $ot_pay;
                    }
                }

                // CHECK SETENGAH HARI
                $temp_timetable_check_out = Carbon::createFromTimeString($timetable->check_out);
                $timetable_check_in_out_dif = $timetable_check_in->diff($temp_timetable_check_out);
                $half_cal = ($timetable_check_in_out_dif->format('%h') / 2) + $timetable_data['break_time_total'];
                if ($half_cal  > $diff_time_punch->format('%h')) {
                    $timetable_data['is_half_day'] = true;
                    $timetable_data['per_day'] += 0.5;
                } else {
                    $timetable_data['per_day'] += 1;
                }
            }
        }

        return $timetable_data;
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
                $search = '';
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
                    // $start_time = Carbon::parse("2022-10-16 23:59:59");
                    // $end_time = Carbon::parse("2022-10-30 23:59:59");
                    $start_time = Carbon::parse($request->date['start_time']);
                    $end_time = Carbon::parse($request->date['end_time']);
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
                // ** get posisi data dari database local
                $position = Position::with('employee_has_position')->get();
                // ** get kasbon data dari database local
                $debts = EmployeeDebt::where('business_id', $business_id)->whereBetween('date', array($start_time, $end_time))->get();
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
                    // ** groupping absen karyawan berdasarkan tanggal
                    $attens_groupings = $this->_group_by_date($atten_bios->filter(function ($atten) use ($emp) {
                        return $atten['emp'] === $emp['id'];
                    }));
                    // foreach ($attens_groupings as $itess) {
                    //     foreach ($itess as $key => $value) {
                    //         Log::info("tanggal={$value['punch_time']} count " . count($itess));
                    //     }
                    // }
                    // Log::info($dates);

                    // Log::info(response()->json($attens_groupings));

                    // ** searchDepartment
                    $emp_dept = collect($dept_bios)->search(function ($item) use ($emp) {
                        return $item['id'] === $emp['department']['id'];
                    });
                    $emp['department'] = $dept_bios[$emp_dept];
                    $dept_id = !empty($emp['department']['parent_dept'])
                        ? $emp['department']['parent_dept']['id']
                        : $emp['department']['id'];

                    // ** searchkaryawan untuk group
                    $departmentDB = Department::where('dept_id', $emp['department']['id'])->first();
                    // ** filterkasbon
                    $emp_debts = $debts->filter(function ($item) use ($emp) {
                        return $item->emp_id === $emp['id'];
                    });
                    // ** searchkaryawan yang dari data local
                    $emp_form_db_index = $emp_form_databases->search(function ($item) use ($emp) {
                        return $item->emp_id === $emp['id'];
                    });
                    // ** searchkaryawan untuk group
                    $group = !empty($operational) ? ($operational->operational_has_depts ?? [])->filter(function ($item) use ($emp) {
                        return $item->dept_id === $emp['department']['id'];
                    }) : [];
                    // ** sum upah tambahan dari jabatan
                    $position_extra_pay = 0;
                    foreach ($position as $posi) {
                        foreach ($posi->employee_has_position as $posiHas) {
                            if ($posiHas['emp_id'] === $emp['id']) {
                                $position_extra_pay += $posi->extra_pay;
                            }
                        }
                    }

                    $report_by_date = [];
                    $daily_salary = ($emp_form_db_index != '') ? $emp_form_databases[$emp_form_db_index]->daily_salary : 0;

                    foreach ($dates as $date_key => $date) {

                        // Log::info("COUNT ".count($attens_groupings[$date] ?? []));

                        if (count($dates) - 1 != $date_key) {
                            // Log::info("=============== date={$date}");
                            $timetable = [];
                            $timetable['id'] = '';
                            $timetable['name'] = '';
                            $timetable['early_check_in'] = 0;
                            $timetable['total_overtime_pay_per_day'] = 0;
                            $timetable['count_one_shift'] = 0;
                            $timetable['total_overtime_pay_per_day'] = 0;
                            $timetable['is_half_day'] = false;
                            $timetable['per_day'] = 0;
                            $timetable['break_time_total'] = 0;
                            $timetable['cross_day'] = 0;
                            $timetable['is_holiday'] = false;

                            $is_holiday = $this->is_holiday($holidays, $date);
                            if (Carbon::parse($date)->isSunday() || !empty($is_holiday)) {
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

                                $diff_time_punch = Carbon::parse($atten_first['punch_time'])->diff(Carbon::parse($atten_last['punch_time']));
                                // $punch_check_in = Carbon::parse($atten_first['punch_time'])->format('H:i:s');
                                // $punch_check_out = Carbon::parse($atten_last['punch_time'])->format('H:i:s');
                                // $punch_check_in = Carbon::createFromTimeString($punch_check_in);
                                // $punch_check_out = Carbon::createFromTimeString($punch_check_out);
                                $punch_check_in = Carbon::parse($atten_first['punch_time']);
                                $punch_check_out = Carbon::parse($atten_last['punch_time']);
                                $code_day = Carbon::parse($date)->dayOfWeek;

                                // ** filter operasional berdasarkan tanggal
                                // $operational_atten_by_date = $operationals[$date] ?? [];
                                // $operational_atten = [];
                                // foreach ($operational_atten_by_date as $key => $value) {
                                //     if ($value['dept_id'] == $dept_id && $value['status'] == 'active') {
                                //         $operational_atten = $value;
                                //     }
                                // }

                                // if (!empty($operational_atten)) {
                                //     foreach ($operational_atten->operational_has_timetables as $key => $value) {
                                //         if ($value->status == 'active') {
                                //             $timetable = $this->calculate_timetable(
                                //                 $value->timetable,
                                //                 [
                                //                     'attens_groupings' => $attens_groupings,
                                //                     'start_punch_time' => $atten_first['punch_time'],
                                //                     'end_punch_time' => $atten_last['punch_time'],
                                //                     'punch_check_in' => $punch_check_in,
                                //                     'punch_check_out' => $punch_check_out,
                                //                     'dates' => $dates,
                                //                     'date' => $date,
                                //                     'date_key' => $date_key,
                                //                 ],
                                //             );

                                //             if (!empty($timetable)) {
                                //                 foreach ($holidays as $holiday) {
                                //                     $date_atten = Carbon::parse($date);
                                //                     if ($date_atten->between(Carbon::parse($holiday->start_date), Carbon::parse($holiday->end_date))) {
                                //                         $timetable['per_day']++;
                                //                     }
                                //                 }
                                //             }
                                //             $timetable['weekday'] = Carbon::create($date)->locale('id_ID')->dayName;
                                //             $timetable['slug'] = $slug_week[$code_day];

                                //             Log::info(response()->json($timetable));
                                //         }
                                //     }
                                // } else {
                                //     foreach ($shifts as $shift) {
                                //         if ($dept_id == $shift->dept_id) {
                                //             foreach ($shift->shiftday as $shiftday) {
                                //                 if ($code_day == $shiftday->code_day) {
                                //                     foreach ($shiftday->shiftday_has_timetable as $keyHas => $shiftdayHas) {
                                //                         $timetable = $this->calculate_timetable(
                                //                             $shiftdayHas->timetable,
                                //                             [
                                //                                 'attens_groupings' => $attens_groupings,
                                //                                 'start_punch_time' => $atten_first['punch_time'],
                                //                                 'end_punch_time' => $atten_last['punch_time'],
                                //                                 'punch_check_in' => $punch_check_in,
                                //                                 'punch_check_out' => $punch_check_out,
                                //                                 'dates' => $dates,
                                //                                 'date' => $date,
                                //                                 'date_key' => $date_key,
                                //                             ],
                                //                         );

                                //                         if (!empty($timetable)) {
                                //                             foreach ($holidays as $holiday) {
                                //                                 $date_atten = Carbon::parse($date);
                                //                                 if ($date_atten->between(Carbon::parse($holiday->start_date), Carbon::parse($holiday->end_date))) {
                                //                                     $timetable['per_day']++;
                                //                                 }
                                //                             }
                                //                         }
                                //                         $timetable['weekday'] = Carbon::create($date)->locale('id_ID')->dayName;
                                //                         $timetable['slug'] = $slug_week[$code_day];
                                //                     }
                                //                 }
                                //             }
                                //         }
                                //     }
                                // }

                                // $cross_days = array_column( array_column($shifts->toArray(), 'shiftday'), 'shiftday_has_timetable');
                                // Log::info(response()->json($cross_days));
                                // Log::info("date={$date} punch_check_out={$punch_check_out} punch_check_in={$punch_check_in}");


                                $is_lest_punch = false;
                                $is_diff_day = false;
                                foreach ($shifts as $shift) {
                                    if ($dept_id == $shift->dept_id) {
                                        foreach ($shift->shiftday as $shiftday) {
                                            // CHECK HARI MINGGU BUKAN
                                            if ((!empty($is_holiday) && count($is_holiday) ? $shiftday->code_day == 6 : $code_day == $shiftday->code_day)) {
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
                                                        $timetable['overtime'] = 0;
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
                                                            if ($shiftdayHas->timetable->is_without_break) {
                                                                // $shift_bagian_check_out->subMinutes($timetable['break_time_total']);
                                                                // $shift_bagian_check_out_cross->subMinutes($timetable['break_time_total']);
                                                            }
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
                                                            $atten_last = $cross_data_attendances[count($cross_data_attendances) - 1];
                                                            $diff_time_punch = Carbon::parse($atten_first['punch_time'])->diff(Carbon::parse($atten_last['punch_time']));
                                                            $punch_check_out = Carbon::parse($atten_last['punch_time']);
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
                                                                if ($minute >= $shiftdayHas->timetable->ot_roundhalf_hr && $minute < $shiftdayHas->timetable->ot_roundone_hr) {
                                                                    $timetable['overtime'] += 0.5;
                                                                } else if ($minute >= $shiftdayHas->timetable->ot_roundone_hr) {
                                                                    $timetable['overtime']++;
                                                                }

                                                                Log::info("punch_check_out $punch_check_out duration_count_one_shift {$shiftdayHas->timetable->duration_count_one_shift} overtime={$timetable['overtime']} date $date hour={$hour} minute=" . $minute);

                                                                if ($shiftdayHas->timetable->ot_period) {
                                                                    $ot_period = $shiftdayHas->timetable->ot_period;
                                                                    $ot_pay = $shiftdayHas->timetable->ot_pay;
                                                                    if ($shiftdayHas->timetable->duration_count_one_shift <= $timetable['overtime']) {
                                                                        $timetable['per_day'] += floor($timetable['overtime'] / ($shiftdayHas->timetable->duration_count_one_shift));
                                                                        $timetable['overtime'] = $timetable['overtime'] % ($shiftdayHas->timetable->duration_count_one_shift);
                                                                        Log::info('overtime onve=' . $hour % ($shiftdayHas->timetable->duration_count_one_shift));
                                                                    }
                                                                    // Log::info("overtime= {$timetable['overtime']}");
                                                                    // if ($timetable['per_day'] >= 1) {
                                                                    //     $timetable['overtime'] -= (!empty($is_holiday) ?  ($timetable['per_day'] - 1) : $timetable['per_day']) * $shiftdayHas->timetable->duration_count_one_shift ?? 0;
                                                                    // }
                                                                    $timetable['total_overtime_pay_per_day'] = ((($timetable['overtime'] ?? 0) * 60) / $ot_period) * $ot_pay;
                                                                }
                                                                // if ($minute >= $shiftdayHas->timetable->ot_roundhalf_hr && $minute < $shiftdayHas->timetable->ot_roundone_hr) {
                                                                //     $timetable['overtime'] += 0.5;
                                                                // } else if ($minute >= $shiftdayHas->timetable->ot_roundone_hr) {
                                                                //     $timetable['overtime']++;
                                                                // }
                                                                // Log::info("overtime sebelum {$timetable['overtime']} minute {$minute}");

                                                                // Log::info("overtime setelah {$timetable['overtime']}");

                                                            }


                                                            $is_half_day = false;
                                                            if ($break_time_first) {
                                                                $break_time_start = Carbon::parse($date . $break_time_first->break_time->start_time)->subMinutes($shiftdayHas->timetable->check_out_plusmn);
                                                                $break_time_end = Carbon::parse($date . $break_time_first->break_time->end_time);
                                                                $is_half_day = $punch_check_out->between($break_time_start, $break_time_end);
                                                                $is_lest_punch = $punch_check_out->lt($break_time_start);
                                                            }
                                                            if (!$is_lest_punch) {
                                                                if ($is_half_day) {
                                                                    if ($shiftdayHas->timetable->is_without_break) {
                                                                        // $shift_bagian_check_out_break_time = Carbon::parse($date . $shiftdayHas->timetable->check_out)->subMinutes($timetable['break_time_total']);
                                                                        // Log::info("shift_bagian_check_out_break_time $shift_bagian_check_out_break_time");
                                                                        // if ($punch_check_out->lt($shift_bagian_check_out_break_time)) {
                                                                        //     $timetable['is_half_day'] = true;
                                                                        //     $timetable['per_day'] += 0.5;
                                                                        // } else {
                                                                            $timetable['per_day'] += 1;
                                                                        // }
                                                                    } else {
                                                                        $timetable['is_half_day'] = true;
                                                                        $timetable['per_day'] += 0.5;
                                                                    }
                                                                } else {
                                                                    $timetable['per_day'] += 1;
                                                                }
                                                            } else {
                                                                $timetable['is_lest_punch'] = true;
                                                            }
                                                        }
                                                    }
                                                }

                                                $timetable['weekday'] = $shiftday->name;
                                                $timetable['slug'] = $slug_week[$code_day];
                                            }
                                        }
                                    }
                                }
                                // Log::info(response()->json($timetable));
                                $report_by_date[] =  [
                                    "date" => $date,
                                    "is_holiday" => $is_holiday,
                                    "is_lest_punch" => $is_lest_punch,
                                    "is_diff_day" => $is_diff_day,
                                    "first_punch" => $atten_first['punch_time'],
                                    "last_punch" => (count($attens_groupings[$date]) != 1) ? $atten_last['punch_time'] : null,
                                    "total_time" => $diff_time_punch->format('%H:%I'),
                                    "timetable" => $timetable,
                                ];
                            } else {
                                $report_by_date[] =  [
                                    "date" => $date,
                                    "is_holiday" => $is_holiday,
                                    "is_lest_punch" => false,
                                    "is_diff_day" => false,
                                    "first_punch" => null,
                                    "last_punch" => null,
                                    "total_time" => null,
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
                    $total = 0;

                    foreach ($report_by_date as $value) {
                        if (!empty($value['timetable'])) {
                            $amout_of_ot += $value['timetable']['overtime'] ?? 0;
                            $amout_of_ot_pay += $value['timetable']['total_overtime_pay_per_day'] ?? 0;
                            $early_check_in += $value['timetable']['early_check_in'] ?? 0;
                            $early_check_in_pay += $value['timetable']['total_earlyin_pay_per_day'] ?? 0;
                            $amount_day += $value['timetable']['per_day'] ?? 0;
                        }
                    }
                    $total = ($amout_of_ot_pay + $early_check_in_pay) + ($amount_day * $daily_salary);

                    $attendance_reports[] = [
                        'employee' => $emp,
                        'range_date' => $request->input('date'),
                        'daily_salary' => $daily_salary,
                        'position_extra_pay' => $position_extra_pay,
                        'dept' => array_sum(array_column($emp_debts->toArray(), 'remainder_debt')),
                        'amount_of_ot' => $amout_of_ot,
                        'amout_of_ot_pay' => $amout_of_ot_pay,
                        'early_check_in' => $early_check_in,
                        'early_check_in_pay' => $early_check_in_pay,
                        'amount_day' => $amount_day,
                        'total' => $total,
                        'reports' => $report_by_date,
                    ];
                }

                $order = null;
                $render =  view('Report.attendance_card.table', compact('attendance_reports', 'order'))->render();

                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            }
            $dept_bios = $this->apiService->get_departments(["page_size" => 999]);
            return  view('Report.attendance_card.index', compact('dept_bios'));
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
 // foreach ($shift->shiftday as $shiftday) {
                                        //     if ($code_day == $shiftday->code_day) {
                                        //         foreach ($shiftday->shiftday_has_timetable as $keyHas => $shiftdayHas) {
                                        //             $in = Carbon::createFromTimeString($shiftdayHas->timetable->check_in);
                                        //             $out = Carbon::createFromTimeString($shiftdayHas->timetable->check_out);
                                        //             $in_diff_out = $in->diff($out);
                                        //             $punchIn = Carbon::createFromTimeString($check_in);
                                        //             $punchOut = Carbon::createFromTimeString($check_out);

                                        //             $check_in_add_plusmn = Carbon::createFromTimeString($shiftdayHas->timetable->check_in);
                                        //             $check_in_sub_plusmn = Carbon::createFromTimeString($shiftdayHas->timetable->check_in);
                                        //             $check_in_sub_plusmn->subMinutes($shiftdayHas->timetable->check_in_plusmn);
                                        //             $check_in_add_plusmn->addMinutes($shiftdayHas->timetable->check_in_plusmn);

                                        //             $break_time_total = 0;
                                        //             if ($shiftdayHas->timetable->is_without_break) {
                                        //                 foreach ($shiftdayHas->timetable->timetable_has_break_time as $key => $value) {
                                        //                     $break_in = Carbon::createFromTimeString($value->break_time->start_time);
                                        //                     $break_out = Carbon::createFromTimeString($value->break_time->end_time);
                                        //                     $Bin_diff_Bout = $break_in->diffInMinutes($break_out);
                                        //                     $break_time_total += $Bin_diff_Bout;
                                        //                 }
                                        //             }

                                        //             if ($punchIn->between($check_in_add_plusmn, $check_in_sub_plusmn)) {
                                        //                 $timetable = [
                                        //                     'id' => '',
                                        //                     'name' => '',
                                        //                     'overtime' => 0,
                                        //                     'early_check_in' => 0,
                                        //                     'total_overtime_pay_per_day' => 0,
                                        //                     'count_one_shift' => 0,
                                        //                     'total_overtime_pay_per_day' => 0,
                                        //                     'per_day' => 0,
                                        //                     'is_half_day' => false,
                                        //                 ];

                                        //                 $cross_data_attendances = [];
                                        //                 $no_cross_data_attendances = [];
                                        //                 $next_date_index = $dates[$date_key + 1];
                                        //                 if (!empty($attens_groupings[$next_date_index])) {
                                        //                     $check_out_add_ot_limit = Carbon::createFromTimeString($shiftdayHas->timetable->check_out)->addHours($shiftdayHas->timetable->duration_ot_limit)->format('H:i:s');
                                        //                     $check_out_add_ot_limit = Carbon::createFromTimeString($check_out_add_ot_limit);

                                        //                     foreach ($attens_groupings[$next_date_index] as $key_next_atten => $item) {
                                        //                         $check_in_next = Carbon::parse($item['punch_time'])->format('H:i:s');
                                        //                         $punchInNext = Carbon::createFromTimeString($check_in_next);
                                        //                         if ($punchInNext->lte($check_out_add_ot_limit)) {
                                        //                             $cross_data_attendances[] = $item;
                                        //                         } else {
                                        //                             $no_cross_data_attendances[] = $item;
                                        //                         }
                                        //                     }
                                        //                 }

                                        //                 if (!empty($shiftdayHas->timetable->cross_day)) {
                                        //                     $timetable['cross_day'] = $shiftdayHas->timetable->cross_day;
                                        //                 }

                                        //                 array_push($attens_groupings[$dates[$date_key]], ...$cross_data_attendances);
                                        //                 $attens_groupings[$next_date_index] = $no_cross_data_attendances;
                                        //                 if (!empty($cross_data_attendances)) {
                                        //                     // dirubah karena timetable nya ad CROSS-nya
                                        //                     // biar perhitungan jam keluarnya berubah
                                        //                     $atten_last = $cross_data_attendances[count($cross_data_attendances) - 1];
                                        //                     $diff_time_punch = Carbon::parse($atten_first['punch_time'])->diff(Carbon::parse($atten_last['punch_time']));
                                        //                     $check_out = Carbon::parse($atten_last['punch_time'])->format('H:i:s');
                                        //                     $punchOut = Carbon::createFromTimeString($check_out)->addDay();
                                        //                 }

                                        //                 $out->subMinutes($break_time_total);
                                        //                 if ($punchOut->gte($out)) {
                                        //                     $timetable['id'] = $shiftdayHas->timetable->id;
                                        //                     $timetable['name'] = $shiftdayHas->timetable->name;

                                        //                     if ($punchIn->lt($in)) {
                                        //                         $diff_time_in = $punchIn->diffInSeconds($in);
                                        //                         $minute = intval(gmdate('i', $diff_time_in));
                                        //                         $timetable['early_check_in'] += intval(gmdate('G', $diff_time_in));
                                        //                         if ($minute >= $shiftdayHas->timetable->ot_roundhalf_hr && $minute < $shiftdayHas->timetable->ot_roundone_hr) {
                                        //                             $timetable['early_check_in'] = $timetable['early_check_in'] + 0.5;
                                        //                         } else if ($minute >= $shiftdayHas->timetable->ot_roundone_hr) {
                                        //                             $timetable['early_check_in']++;
                                        //                         }

                                        //                         $ot_period = $shiftdayHas->timetable->ot_period;
                                        //                         $ot_pay = $shiftdayHas->timetable->ot_pay;
                                        //                         $timetable['total_earlyin_pay_per_day']  = ((($timetable['early_check_in'] ?? 0) * 60) / $ot_period) * $ot_pay;
                                        //                     }

                                        //                     if (count($attens_groupings[$date]) != 1) {
                                        //                         $timetable['per_day'] += 1;
                                        //                         if ($punchOut->gt($out)) {
                                        //                             $diff_time_out = $out->diffInSeconds($punchOut);
                                        //                             $minute = intval(gmdate('i', $diff_time_out));
                                        //                             $hour = intval(gmdate('G', $diff_time_out));
                                        //                             $timetable['overtime'] += $hour;
                                        //                             if ($minute >= $shiftdayHas->timetable->ot_roundhalf_hr && $minute < $shiftdayHas->timetable->ot_roundone_hr) {
                                        //                                 $timetable['overtime'] = $timetable['overtime'] + 0.5;
                                        //                             } else if ($minute >= $shiftdayHas->timetable->ot_roundone_hr) {
                                        //                                 $timetable['overtime']++;
                                        //                             }

                                        //                             $ot_period = $shiftdayHas->timetable->ot_period;
                                        //                             $ot_pay = $shiftdayHas->timetable->ot_pay;
                                        //                             $timetable['per_day'] += floor($hour / $shiftdayHas->timetable->duration_count_one_shift);
                                        //                             if ($timetable['per_day'] > 1) {
                                        //                                 $timetable['overtime'] -= ($timetable['per_day'] - 1) * $shiftdayHas->timetable->duration_count_one_shift;
                                        //                             }

                                        //                             $timetable['total_overtime_pay_per_day'] = ((($timetable['overtime'] ?? 0) * 60) / $ot_period) * $ot_pay;
                                        //                         }

                                        //                         if (($in_diff_out->format('%h') / 2) > $diff_time_punch->format('%h'))
                                        //                             $timetable['is_half_day'] = true;
                                        //                     }
                                        //                 }
                                        //             }
                                        //             $timetable['weekday'] = $shiftday->name;
                                        //             $timetable['slug'] = $slug_week[$code_day];
                                        //         }
                                        //     }
                                        // }

                                        // foreach ($dates as $date_key => $date) {
                    //     if (count($dates) - 1 != $date_key) {
                    //         if (!empty($attens_groupings[$date])) {
                    //             $atten_item = $attens_groupings[$date];
                    //             $atten_first = $atten_item[0];
                    //             $atten_last = $atten_item[count($atten_item) - 1];

                    //             $diff_time_punch = Carbon::parse($atten_first['punch_time'])->diff(Carbon::parse($atten_last['punch_time']));
                    //             $punch_check_in = Carbon::parse($atten_first['punch_time'])->format('H:i:s');
                    //             $punch_check_out = Carbon::parse($atten_last['punch_time'])->format('H:i:s');
                    //             $code_day = Carbon::parse($date)->dayOfWeek;

                    //             $timetable = [];
                    //             foreach ($shifts as $shift) {
                    //                 if ($dept_id == $shift->dept_id) {
                    //                     foreach ($shift->shiftday as $shiftday) {
                    //                         if ($code_day == $shiftday->code_day) {
                    //                             $timetable = ['per_day' => 0];
                    //                             foreach ($shiftday->shiftday_has_timetable as $keyHas => $shiftdayHas) {
                    //                                 $timetable_check_in = Carbon::createFromTimeString($shiftdayHas->timetable->check_in);
                    //                                 $timetable_check_out = Carbon::createFromTimeString($shiftdayHas->timetable->check_out);
                    //                                 $punch_check_in = Carbon::createFromTimeString($punch_check_in);
                    //                                 $punch_check_out = Carbon::createFromTimeString($punch_check_out);
                    //                                 if ($emp['first_name'] == 'karyawan003') {
                    //                                     // Log::info($punch_check_in);
                    //                                     // Log::info($punch_check_out);
                    //                                 }


                    //                                 $timetable_check_in_add_plusmn = Carbon::createFromTimeString($shiftdayHas->timetable->check_in)->subMinutes($shiftdayHas->timetable->check_in_plusmn);
                    //                                 $timetable_check_in_sub_plusmn = Carbon::createFromTimeString($shiftdayHas->timetable->check_in)->addMinutes($shiftdayHas->timetable->check_in_plusmn);
                    //                                 if ($punch_check_in->between($timetable_check_in_add_plusmn, $timetable_check_in_sub_plusmn)) {
                    //                                     $timetable = [
                    //                                         'id' => '',
                    //                                         'name' => '',
                    //                                         'overtime' => 0,
                    //                                         'early_check_in' => 0,
                    //                                         'total_overtime_pay_per_day' => 0,
                    //                                         'count_one_shift' => 0,
                    //                                         'total_overtime_pay_per_day' => 0,
                    //                                         'is_half_day' => false,
                    //                                         'per_day' => 0,
                    //                                         'break_time_total' => 0,
                    //                                         'cross_day' => $shiftdayHas->timetable->cross_day,
                    //                                     ];

                    //                                     foreach ($shiftdayHas->timetable->timetable_has_break_time as $key => $value) {
                    //                                         $break_time_start = Carbon::createFromTimeString($value->break_time->start_time);
                    //                                         $break_time_end = Carbon::createFromTimeString($value->break_time->end_time);
                    //                                         $break_time_dif = $break_time_start->diffInMinutes($break_time_end);
                    //                                         $timetable['break_time_total'] += $break_time_dif;
                    //                                     }

                    //                                     if ($shiftdayHas->timetable->is_without_break) {
                    //                                         $timetable_check_out->subMinutes($timetable['break_time_total']);
                    //                                     }

                    //                                     $cross_data_attendances = [];
                    //                                     $no_cross_data_attendances = [];
                    //                                     $next_date_index = $dates[$date_key + 1];
                    //                                     if (!empty($attens_groupings[$next_date_index])) {
                    //                                         $punch_check_out_add_ot_limit = Carbon::createFromTimeString($shiftdayHas->timetable->check_out)->addHours($shiftdayHas->timetable->duration_ot_limit)->format('H:i:s');
                    //                                         $punch_check_out_add_ot_limit = Carbon::createFromTimeString($punch_check_out_add_ot_limit);

                    //                                         foreach ($attens_groupings[$next_date_index] as $key_next_atten => $item) {
                    //                                             $check_in_next = Carbon::parse($item['punch_time'])->format('H:i:s');
                    //                                             $punch_check_in_next_day = Carbon::createFromTimeString($check_in_next);
                    //                                             if ($punch_check_in_next_day->lte($punch_check_out_add_ot_limit)) {
                    //                                                 $cross_data_attendances[] = $item;
                    //                                             } else $no_cross_data_attendances[] = $item;
                    //                                         }
                    //                                     }

                    //                                     if (!empty($cross_data_attendances)) {
                    //                                         array_push($attens_groupings[$dates[$date_key]], ...$cross_data_attendances);
                    //                                         $attens_groupings[$next_date_index] = $no_cross_data_attendances;
                    //                                         // dirubah karena timetable nya ad CROSS-nya
                    //                                         // biar perhitungan jam keluarnya berubah
                    //                                         $atten_last = $cross_data_attendances[count($cross_data_attendances) - 1];
                    //                                         $diff_time_punch = Carbon::parse($atten_first['punch_time'])->diff(Carbon::parse($atten_last['punch_time']));
                    //                                         $punch_check_out = Carbon::createFromTimeString(Carbon::parse($atten_last['punch_time'])->format('H:i:s'));
                    //                                     }

                    //                                     // if ($punch_check_out->gte($timetable_check_out)) {
                    //                                     $timetable['id'] = $shiftdayHas->timetable->id;
                    //                                     $timetable['name'] = $shiftdayHas->timetable->name;

                    //                                     if ($punch_check_in->lt($timetable_check_in)) {
                    //                                         $diff_time_in = $punch_check_in->diffInSeconds($timetable_check_in);
                    //                                         $minute = intval(gmdate('i', $diff_time_in));
                    //                                         $timetable['early_check_in'] += intval(gmdate('G', $diff_time_in));
                    //                                         if ($minute >= $shiftdayHas->timetable->ot_roundhalf_hr && $minute < $shiftdayHas->timetable->ot_roundone_hr) {
                    //                                             $timetable['early_check_in'] = $timetable['early_check_in'] + 0.5;
                    //                                         } else if ($minute >= $shiftdayHas->timetable->ot_roundone_hr) {
                    //                                             $timetable['early_check_in']++;
                    //                                         }

                    //                                         $ot_period = $shiftdayHas->timetable->ot_period;
                    //                                         $ot_pay = $shiftdayHas->timetable->ot_pay;
                    //                                         $timetable['total_earlyin_pay_per_day']  = ((($timetable['early_check_in'] ?? 0) * 60) / $ot_period) * $ot_pay;
                    //                                     }

                    //                                     if (count($attens_groupings[$date]) != 1) {
                    //                                         if ($emp['first_name'] == 'Erwan') {
                    //                                             // Log::info($punch_check_out);
                    //                                             // Log::info($timetable_check_out);
                    //                                         }
                    //                                         if ($punch_check_out->gt($timetable_check_out)) {
                    //                                             if (empty($shiftdayHas->timetable->cross_day))
                    //                                                 $punch_check_out = Carbon::createFromTimeString($punch_check_out)->addDay();

                    //                                             $diff_time_out = $timetable_check_out->diffInSeconds($punch_check_out);
                    //                                             $minute = intval(gmdate('i', $diff_time_out));
                    //                                             $hour = intval(gmdate('G', $diff_time_out));
                    //                                             $timetable['overtime'] += $hour;
                    //                                             if ($minute >= $shiftdayHas->timetable->ot_roundhalf_hr && $minute < $shiftdayHas->timetable->ot_roundone_hr) {
                    //                                                 $timetable['overtime'] = $timetable['overtime'] + 0.5;
                    //                                             } else if ($minute >= $shiftdayHas->timetable->ot_roundone_hr) {
                    //                                                 $timetable['overtime']++;
                    //                                             }

                    //                                             if ($shiftdayHas->timetable->ot_period) {
                    //                                                 $ot_period = $shiftdayHas->timetable->ot_period ?? 0;
                    //                                                 $ot_pay = $shiftdayHas->timetable->ot_pay ?? 0;
                    //                                                 $timetable['per_day'] += floor($hour / ($shiftdayHas->timetable->duration_count_one_shift ?? 0));
                    //                                                 if ($timetable['per_day'] >= 1) {
                    //                                                     $timetable['overtime'] -= ($timetable['per_day']) * $shiftdayHas->timetable->duration_count_one_shift ?? 0;
                    //                                                 }
                    //                                                 $timetable['total_overtime_pay_per_day'] = ((($timetable['overtime'] ?? 0) * 60) / $ot_period) * $ot_pay;
                    //                                             }
                    //                                         }


                    //                                         $temp_timetable_check_out = Carbon::createFromTimeString($shiftdayHas->timetable->check_out);
                    //                                         $timetable_check_in_out_dif = $timetable_check_in->diff($temp_timetable_check_out);
                    //                                         $half_cal = ($timetable_check_in_out_dif->format('%h') / 2) + ($timetable['break_time_total'] / 60);
                    //                                         if ($half_cal  > $diff_time_punch->format('%h')) {
                    //                                             $timetable['is_half_day'] = true;
                    //                                             $timetable['per_day'] += 0.5;
                    //                                         } else {
                    //                                             $timetable['per_day'] += 1;
                    //                                         }
                    //                                     }
                    //                                     // }
                    //                                 }
                    //                             }

                    //                             $timetable['per_day'] += $holiday_count;
                    //                             if (!empty($departmentDB) && $departmentDB->still_paid) {
                    //                                 $timetable['per_day'] += 1;
                    //                             }

                    //                             $timetable['weekday'] = $shiftday->name;
                    //                             $timetable['slug'] = $slug_week[$code_day];
                    //                         }
                    //                     }
                    //                     // foreach ($shift->shiftday as $shiftday) {
                    //                     //     if ($code_day == $shiftday->code_day) {
                    //                     //         foreach ($shiftday->shiftday_has_timetable as $keyHas => $shiftdayHas) {
                    //                     //             $in = Carbon::createFromTimeString($shiftdayHas->timetable->check_in);
                    //                     //             $out = Carbon::createFromTimeString($shiftdayHas->timetable->check_out);
                    //                     //             $in_diff_out = $in->diff($out);
                    //                     //             $punchIn = Carbon::createFromTimeString($check_in);
                    //                     //             $punchOut = Carbon::createFromTimeString($check_out);

                    //                     //             $check_in_add_plusmn = Carbon::createFromTimeString($shiftdayHas->timetable->check_in);
                    //                     //             $check_in_sub_plusmn = Carbon::createFromTimeString($shiftdayHas->timetable->check_in);
                    //                     //             $check_in_sub_plusmn->subMinutes($shiftdayHas->timetable->check_in_plusmn);
                    //                     //             $check_in_add_plusmn->addMinutes($shiftdayHas->timetable->check_in_plusmn);

                    //                     //             $break_time_total = 0;
                    //                     //             if ($shiftdayHas->timetable->is_without_break) {
                    //                     //                 foreach ($shiftdayHas->timetable->timetable_has_break_time as $key => $value) {
                    //                     //                     $break_in = Carbon::createFromTimeString($value->break_time->start_time);
                    //                     //                     $break_out = Carbon::createFromTimeString($value->break_time->end_time);
                    //                     //                     $Bin_diff_Bout = $break_in->diffInMinutes($break_out);
                    //                     //                     $break_time_total += $Bin_diff_Bout;
                    //                     //                 }
                    //                     //             }

                    //                     //             if ($punchIn->between($check_in_add_plusmn, $check_in_sub_plusmn)) {
                    //                     //                 $timetable = [
                    //                     //                     'id' => '',
                    //                     //                     'name' => '',
                    //                     //                     'overtime' => 0,
                    //                     //                     'early_check_in' => 0,
                    //                     //                     'total_overtime_pay_per_day' => 0,
                    //                     //                     'count_one_shift' => 0,
                    //                     //                     'total_overtime_pay_per_day' => 0,
                    //                     //                     'per_day' => 0,
                    //                     //                     'is_half_day' => false,
                    //                     //                 ];

                    //                     //                 $cross_data_attendances = [];
                    //                     //                 $no_cross_data_attendances = [];
                    //                     //                 $next_date_index = $dates[$date_key + 1];
                    //                     //                 if (!empty($attens_groupings[$next_date_index])) {
                    //                     //                     $check_out_add_ot_limit = Carbon::createFromTimeString($shiftdayHas->timetable->check_out)->addHours($shiftdayHas->timetable->duration_ot_limit)->format('H:i:s');
                    //                     //                     $check_out_add_ot_limit = Carbon::createFromTimeString($check_out_add_ot_limit);

                    //                     //                     foreach ($attens_groupings[$next_date_index] as $key_next_atten => $item) {
                    //                     //                         $check_in_next = Carbon::parse($item['punch_time'])->format('H:i:s');
                    //                     //                         $punchInNext = Carbon::createFromTimeString($check_in_next);
                    //                     //                         if ($punchInNext->lte($check_out_add_ot_limit)) {
                    //                     //                             $cross_data_attendances[] = $item;
                    //                     //                         } else {
                    //                     //                             $no_cross_data_attendances[] = $item;
                    //                     //                         }
                    //                     //                     }
                    //                     //                 }

                    //                     //                 if (!empty($shiftdayHas->timetable->cross_day)) {
                    //                     //                     $timetable['cross_day'] = $shiftdayHas->timetable->cross_day;
                    //                     //                 }

                    //                     //                 array_push($attens_groupings[$dates[$date_key]], ...$cross_data_attendances);
                    //                     //                 $attens_groupings[$next_date_index] = $no_cross_data_attendances;
                    //                     //                 if (!empty($cross_data_attendances)) {
                    //                     //                     // dirubah karena timetable nya ad CROSS-nya
                    //                     //                     // biar perhitungan jam keluarnya berubah
                    //                     //                     $atten_last = $cross_data_attendances[count($cross_data_attendances) - 1];
                    //                     //                     $diff_time_punch = Carbon::parse($atten_first['punch_time'])->diff(Carbon::parse($atten_last['punch_time']));
                    //                     //                     $check_out = Carbon::parse($atten_last['punch_time'])->format('H:i:s');
                    //                     //                     $punchOut = Carbon::createFromTimeString($check_out)->addDay();
                    //                     //                 }

                    //                     //                 $out->subMinutes($break_time_total);
                    //                     //                 if ($punchOut->gte($out)) {
                    //                     //                     $timetable['id'] = $shiftdayHas->timetable->id;
                    //                     //                     $timetable['name'] = $shiftdayHas->timetable->name;

                    //                     //                     if ($punchIn->lt($in)) {
                    //                     //                         $diff_time_in = $punchIn->diffInSeconds($in);
                    //                     //                         $minute = intval(gmdate('i', $diff_time_in));
                    //                     //                         $timetable['early_check_in'] += intval(gmdate('G', $diff_time_in));
                    //                     //                         if ($minute >= $shiftdayHas->timetable->ot_roundhalf_hr && $minute < $shiftdayHas->timetable->ot_roundone_hr) {
                    //                     //                             $timetable['early_check_in'] = $timetable['early_check_in'] + 0.5;
                    //                     //                         } else if ($minute >= $shiftdayHas->timetable->ot_roundone_hr) {
                    //                     //                             $timetable['early_check_in']++;
                    //                     //                         }

                    //                     //                         $ot_period = $shiftdayHas->timetable->ot_period;
                    //                     //                         $ot_pay = $shiftdayHas->timetable->ot_pay;
                    //                     //                         $timetable['total_earlyin_pay_per_day']  = ((($timetable['early_check_in'] ?? 0) * 60) / $ot_period) * $ot_pay;
                    //                     //                     }

                    //                     //                     if (count($attens_groupings[$date]) != 1) {
                    //                     //                         $timetable['per_day'] += 1;
                    //                     //                         if ($punchOut->gt($out)) {
                    //                     //                             $diff_time_out = $out->diffInSeconds($punchOut);
                    //                     //                             $minute = intval(gmdate('i', $diff_time_out));
                    //                     //                             $hour = intval(gmdate('G', $diff_time_out));
                    //                     //                             $timetable['overtime'] += $hour;
                    //                     //                             if ($minute >= $shiftdayHas->timetable->ot_roundhalf_hr && $minute < $shiftdayHas->timetable->ot_roundone_hr) {
                    //                     //                                 $timetable['overtime'] = $timetable['overtime'] + 0.5;
                    //                     //                             } else if ($minute >= $shiftdayHas->timetable->ot_roundone_hr) {
                    //                     //                                 $timetable['overtime']++;
                    //                     //                             }

                    //                     //                             $ot_period = $shiftdayHas->timetable->ot_period;
                    //                     //                             $ot_pay = $shiftdayHas->timetable->ot_pay;
                    //                     //                             $timetable['per_day'] += floor($hour / $shiftdayHas->timetable->duration_count_one_shift);
                    //                     //                             if ($timetable['per_day'] > 1) {
                    //                     //                                 $timetable['overtime'] -= ($timetable['per_day'] - 1) * $shiftdayHas->timetable->duration_count_one_shift;
                    //                     //                             }

                    //                     //                             $timetable['total_overtime_pay_per_day'] = ((($timetable['overtime'] ?? 0) * 60) / $ot_period) * $ot_pay;
                    //                     //                         }

                    //                     //                         if (($in_diff_out->format('%h') / 2) > $diff_time_punch->format('%h'))
                    //                     //                             $timetable['is_half_day'] = true;
                    //                     //                     }
                    //                     //                 }
                    //                     //             }
                    //                     //             $timetable['weekday'] = $shiftday->name;
                    //                     //             $timetable['slug'] = $slug_week[$code_day];
                    //                     //         }
                    //                     //     }
                    //                     // }
                    //                 }
                    //             }

                    //             $report_by_date[] =  [
                    //                 "date" => $date,
                    //                 "first_punch" => $atten_first['punch_time'],
                    //                 "last_punch" => (count($attens_groupings[$date]) != 1) ? $atten_last['punch_time'] : null,
                    //                 "total_time" => $diff_time_punch->format('%H:%I'),
                    //                 "timetable" => $timetable,
                    //             ];
                    //         } else {
                    //             $report_by_date[] =  [
                    //                 "date" => $date,
                    //                 "first_punch" => null,
                    //                 "last_punch" => null,
                    //                 "total_time" => null,
                    //                 "timetable" => [],
                    //             ];
                    //         }
                    //     }
                    // }