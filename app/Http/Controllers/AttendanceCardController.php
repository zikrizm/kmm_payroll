<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Shift;
use App\Utils\ResponseUtil;
use App\Models\EmployeeDebt;
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

class AttendanceCardController extends Controller
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
                    // $start_time = Carbon::parse("2022-10-16");
                    // $end_time = Carbon::parse("2022-10-24");
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

                // Log::info($attenDBs);
                // ** get employee data dari database local
                $emp_form_databases = Employee::where('business_id', $business_id)->get();
                // ** get posisi data dari database local
                $posis = Position::with('employee_has_position')->get();
                // ** get kasbon data dari database local
                $debts = EmployeeDebt::where('business_id', $business_id)->whereBetween('date', array($start_time, $end_time))->get();
                // ** get shift data dari database local
                $shifts = Shift::where('business_id', $business_id)->with(['shiftday'])->get();
                // ** get operational data dari database local
                $operational = Operational::where('business_id', $business_id)->whereBetween('start_date', [$start_time, $end_time])
                    ->orWhereBetween('end_date', [$start_time, $end_time])->with('operational_has_depts')->first();
                $attendance_reports = [];
                Log::info("===================");
                foreach (($emp_bios ?? []) as $emp) {
                    // ** groupping absen karyawan berdasarkan tanggal
                    $attens_groupings = $this->_group_by_date($atten_bios->filter(function ($atten) use ($emp) {
                        return $atten['emp'] === $emp['id'];
                    }));

                    // ** searchDepartment
                    $emp_dept = collect($dept_bios)->search(function ($item) use ($emp) {
                        return $item['id'] === $emp['department']['id'];
                    });
                    $emp['department'] = $dept_bios[$emp_dept];
                    $dept_id = null;
                    if (empty($emp['department']['parent_dept'])) {
                        $dept_id = $emp['department']['id'];
                    } else {
                        $dept_id = $emp['department']['parent_dept']['id'];
                    }

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
                    foreach ($posis as $posi) {
                        foreach ($posi->employee_has_position as $posiHas) {
                            if ($posiHas['emp_id'] === $emp['id']) {
                                $position_extra_pay += $posi->extra_pay;
                            }
                        }
                    }
                    // Log::info(response()->json($attens_groupings));



                    $report_by_date = [];
                    $daily_salary = ($emp_form_db_index != '') ? $emp_form_databases[$emp_form_db_index]->daily_salary : 0;

                    foreach ($dates as $date_key => $date) {
                        if (count($dates) - 1 != $date_key) {
                            if (!empty($attens_groupings[$date])) {
                                $atten_item = $attens_groupings[$date];
                                $atten_first = $atten_item[0];
                                $atten_last = $atten_item[count($atten_item) - 1];

                                $diff_time_punch = Carbon::parse($atten_first['punch_time'])->diff(Carbon::parse($atten_last['punch_time']));
                                $punch_check_in = Carbon::parse($atten_first['punch_time'])->format('H:i:s');
                                $punch_check_out = Carbon::parse($atten_last['punch_time'])->format('H:i:s');
                                $code_day = Carbon::parse($date)->dayOfWeek;

                                $timetable = [];
                                foreach ($shifts as $shift) {
                                    if ($dept_id == $shift->dept_id) {
                                        foreach ($shift->shiftday as $shiftday) {
                                            if ($code_day == $shiftday->code_day) {
                                                foreach ($shiftday->shiftday_has_timetable as $keyHas => $shiftdayHas) {
                                                    $timetable_check_in = Carbon::createFromTimeString($shiftdayHas->timetable->check_in);
                                                    $timetable_check_out = Carbon::createFromTimeString($shiftdayHas->timetable->check_out);
                                                    $punch_check_in = Carbon::createFromTimeString($punch_check_in);
                                                    $punch_check_out = Carbon::createFromTimeString($punch_check_out);
                                                    if ($emp['first_name'] == 'karyawan003') {
                                                        Log::info($punch_check_in);
                                                        Log::info($punch_check_out);
                                                    }

                                                    $timetable_check_in_add_plusmn = Carbon::createFromTimeString($shiftdayHas->timetable->check_in)->subMinutes($shiftdayHas->timetable->check_in_plusmn);
                                                    $timetable_check_in_sub_plusmn = Carbon::createFromTimeString($shiftdayHas->timetable->check_in)->addMinutes($shiftdayHas->timetable->check_in_plusmn);
                                                    if ($punch_check_in->between($timetable_check_in_add_plusmn, $timetable_check_in_sub_plusmn)) {
                                                        $timetable = [
                                                            'id' => '',
                                                            'name' => '',
                                                            'overtime' => 0,
                                                            'early_check_in' => 0,
                                                            'total_overtime_pay_per_day' => 0,
                                                            'count_one_shift' => 0,
                                                            'total_overtime_pay_per_day' => 0,
                                                            'per_day' => 0,
                                                            'is_half_day' => false,
                                                            'break_time_total' => 0,
                                                            'cross_day' => $shiftdayHas->timetable->cross_day,
                                                        ];

                                                        foreach ($shiftdayHas->timetable->timetable_has_break_time as $key => $value) {
                                                            $break_time_start = Carbon::createFromTimeString($value->break_time->start_time);
                                                            $break_time_end = Carbon::createFromTimeString($value->break_time->end_time);
                                                            $break_time_dif = $break_time_start->diffInMinutes($break_time_end);
                                                            $timetable['break_time_total'] += $break_time_dif;
                                                        }

                                                        if ($shiftdayHas->timetable->is_without_break) {
                                                            $timetable_check_out->subMinutes($timetable['break_time_total']);
                                                        }




                                                        $cross_data_attendances = [];
                                                        $no_cross_data_attendances = [];
                                                        $next_date_index = $dates[$date_key + 1];
                                                        if (!empty($attens_groupings[$next_date_index])) {
                                                            $punch_check_out_add_ot_limit = Carbon::createFromTimeString($shiftdayHas->timetable->check_out)->addHours($shiftdayHas->timetable->duration_ot_limit)->format('H:i:s');
                                                            $punch_check_out_add_ot_limit = Carbon::createFromTimeString($punch_check_out_add_ot_limit);

                                                            foreach ($attens_groupings[$next_date_index] as $key_next_atten => $item) {
                                                                $check_in_next = Carbon::parse($item['punch_time'])->format('H:i:s');
                                                                $punch_check_in_next_day = Carbon::createFromTimeString($check_in_next);
                                                                if ($punch_check_in_next_day->lte($punch_check_out_add_ot_limit)) {
                                                                    $cross_data_attendances[] = $item;
                                                                } else $no_cross_data_attendances[] = $item;
                                                            }
                                                        }

                                                        if (!empty($cross_data_attendances)) {
                                                            array_push($attens_groupings[$dates[$date_key]], ...$cross_data_attendances);
                                                            $attens_groupings[$next_date_index] = $no_cross_data_attendances;
                                                            // dirubah karena timetable nya ad CROSS-nya
                                                            // biar perhitungan jam keluarnya berubah
                                                            $atten_last = $cross_data_attendances[count($cross_data_attendances) - 1];
                                                            $diff_time_punch = Carbon::parse($atten_first['punch_time'])->diff(Carbon::parse($atten_last['punch_time']));
                                                            $check_out = Carbon::parse($atten_last['punch_time'])->format('H:i:s');
                                                            $punch_check_out = Carbon::createFromTimeString($check_out)->addDay();
                                                        }

                                                        if ($punch_check_out->gte($timetable_check_out)) {
                                                            $timetable['id'] = $shiftdayHas->timetable->id;
                                                            $timetable['name'] = $shiftdayHas->timetable->name;

                                                            if ($punch_check_in->lt($timetable_check_in)) {
                                                                $diff_time_in = $punch_check_in->diffInSeconds($timetable_check_in);
                                                                $minute = intval(gmdate('i', $diff_time_in));
                                                                $timetable['early_check_in'] += intval(gmdate('G', $diff_time_in));
                                                                if ($minute >= $shiftdayHas->timetable->ot_roundhalf_hr && $minute < $shiftdayHas->timetable->ot_roundone_hr) {
                                                                    $timetable['early_check_in'] = $timetable['early_check_in'] + 0.5;
                                                                } else if ($minute >= $shiftdayHas->timetable->ot_roundone_hr) {
                                                                    $timetable['early_check_in']++;
                                                                }

                                                                $ot_period = $shiftdayHas->timetable->ot_period;
                                                                $ot_pay = $shiftdayHas->timetable->ot_pay;
                                                                $timetable['total_earlyin_pay_per_day']  = ((($timetable['early_check_in'] ?? 0) * 60) / $ot_period) * $ot_pay;
                                                            }

                                                            if (count($attens_groupings[$date]) != 1) {
                                                                $timetable['per_day'] += 1;
                                                                if ($punch_check_out->gt($timetable_check_out)) {
                                                                    $diff_time_out = $timetable_check_out->diffInSeconds($punch_check_out);
                                                                    $minute = intval(gmdate('i', $diff_time_out));
                                                                    $hour = intval(gmdate('G', $diff_time_out));
                                                                    $timetable['overtime'] += $hour;
                                                                    if ($minute >= $shiftdayHas->timetable->ot_roundhalf_hr && $minute < $shiftdayHas->timetable->ot_roundone_hr) {
                                                                        $timetable['overtime'] = $timetable['overtime'] + 0.5;
                                                                    } else if ($minute >= $shiftdayHas->timetable->ot_roundone_hr) {
                                                                        $timetable['overtime']++;
                                                                    }

                                                                    $ot_period = $shiftdayHas->timetable->ot_period;
                                                                    $ot_pay = $shiftdayHas->timetable->ot_pay;
                                                                    $timetable['per_day'] += floor($hour / $shiftdayHas->timetable->duration_count_one_shift);
                                                                    if ($timetable['per_day'] > 1) {
                                                                        $timetable['overtime'] -= ($timetable['per_day'] - 1) * $shiftdayHas->timetable->duration_count_one_shift;
                                                                    }

                                                                    $timetable['total_overtime_pay_per_day'] = ((($timetable['overtime'] ?? 0) * 60) / $ot_period) * $ot_pay;
                                                                }

                                                                
                                                                $temp_timetable_check_out = Carbon::createFromTimeString($shiftdayHas->timetable->check_out);
                                                                $temp_timetable_check_out->subMinutes($timetable['break_time_total']);
                                                                $timetable_check_in_out_dif = $timetable_check_in->diff($temp_timetable_check_out);
                                                                if (($timetable_check_in_out_dif->format('%h') / 2) > $diff_time_punch->format('%h'))
                                                                    $timetable['is_half_day'] = true;
                                                            }
                                                        }
                                                    }

                                                    $timetable['weekday'] = $shiftday->name;
                                                    $timetable['slug'] = $slug_week[$code_day];
                                                }
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
                                    }
                                }

                                $report_by_date[] =  [
                                    "date" => $date,
                                    "first_punch" => $atten_first['punch_time'],
                                    "last_punch" => (count($attens_groupings[$date]) != 1) ? $atten_last['punch_time'] : null,
                                    "total_time" => $diff_time_punch->format('%H:%I'),
                                    "timetable" => $timetable,
                                ];
                            } else {
                                $report_by_date[] =  [
                                    "date" => $date,
                                    "first_punch" => null,
                                    "last_punch" => null,
                                    "total_time" => null,
                                    "timetable" => [],
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
                // Log::info(response()->json($attendance_reports));
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
}
