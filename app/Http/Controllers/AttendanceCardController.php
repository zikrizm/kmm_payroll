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

                // * Employee filter
                $filter = [];

                $dates = [];
                $th_dates = [];
                $attenDBs = [];
                $slug_week = ['sen', 'sel', 'rab', 'kam', 'jum', 'sab', 'mgg'];
                if (!empty($request->input('date'))) {
                    $start_time = Carbon::parse($request->date['start_time']);
                    $end_time = Carbon::parse($request->date['end_time']);
                    $filter['start_time'] = $request->date['start_time'];
                    $filter['end_time'] = $request->date['end_time'];
                    $attenDBs = Transaction::whereBetween('punch_time', [$start_time->hour(0)->minute(0)->second(0), $end_time->hour(0)->minute(0)->second(0)])->get();
                    $dates = $this->util->generateDateRange($start_time, $end_time);

                    foreach ($dates as $date) {
                        $code_day = Carbon::parse($date)->dayOfWeek;
                        $th_dates[] = ['slug' => $slug_week[$code_day], 'date' => $date];
                    }
                }

                // ** get employee data dari biotime
                $emp_bio_count = $this->apiService->get_employees([])["count"];
                $emp_bios = $this->apiService->get_employees(["employee_icontains" => $search, "page_size" => 12])['data'];
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
                $shifts = Shift::where('business_id', $business_id)->with(['shiftday.shiftday_has_timetable.timetable'])->get();
                // ** get operational data dari database local
                $operational = Operational::where('business_id', $business_id)->whereBetween('start_date', [$start_time, $end_time])
                    ->orWhereBetween('end_date', [$start_time, $end_time])->with('operational_has_depts')->first();
                $attendance_reports = [];
                foreach (($emp_bios ?? []) as $emp) {
                    // ** groupping absen karyawan berdasarkan tanggal
                    $attens_groupings = $this->_group_by_date($atten_bios->filter(function ($atten) use ($emp) {
                        return $atten['emp'] === $emp['id'];
                    }));
                    // ** filterkasbon
                    $emp_depts = $debts->filter(function ($item) use ($emp) {
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

                    $date_datas = [];
                    $emp_ins = [];
                    $emp_overtimes = [];
                    $daily_salary = ($emp_form_db_index != '') ? $emp_form_databases[$emp_form_db_index]->daily_salary : 0;

                    foreach ($dates as $date) {
                        if (!empty($attens_groupings[$date])) {
                            $items = $attens_groupings[$date];
                            $item_first = $items[0];
                            $item_last = $items[count($items) - 1];

                            $diff_time = Carbon::parse($item_first['punch_time'])->diff(Carbon::parse($item_last['punch_time']));
                            $check_in = Carbon::parse($item_first['punch_time'])->format('H:i:s');
                            $check_out = Carbon::parse($item_last['punch_time'])->format('H:i:s');
                            $code_day = Carbon::parse($date)->dayOfWeek;

                            $shift_data = [];
                            $plusInTime = 0;
                            $overtime = 0;
                            foreach ($shifts as $shift) {
                                foreach ($shift->shiftday as $shiftday) {
                                    foreach ($shiftday->shiftday_has_timetable as $keyHas => $shiftdayHas) {
                                        $in = Carbon::createFromTimeString($shiftdayHas->timetable->in_time);
                                        $out = Carbon::createFromTimeString($shiftdayHas->timetable->out_time);
                                        $punchIn = Carbon::createFromTimeString($check_in);
                                        $punchOut = Carbon::createFromTimeString($check_out);

                                        if ($code_day == $shiftday->code_day) {
                                            if ($punchIn->lt($in->addHour())) {
                                                // $shift_data['id'] = $shiftdayHas->id;
                                                $shift_data['name'] = $shiftdayHas->timetable->name;

                                                if ($punchIn->lt($in)) {
                                                    $diff_time_in = $punchIn->diffInSeconds($in);
                                                    $minute = intval(gmdate('i', $diff_time_in));
                                                    $plusInTime += intval(gmdate('G', $diff_time_in));
                                                    if ($minute >= $shiftdayHas->timetable->overtime_half_hour && $minute < $shiftdayHas->timetable->overtime_one_hour) {
                                                        $plusInTime = $plusInTime + 0.5;
                                                    } else if ($minute >= $shiftdayHas->timetable->overtime_one_hour) {
                                                        $plusInTime++;
                                                    }

                                                    $time_period = $shiftdayHas->timetable->time_period;
                                                    $overtime_pay = $shiftdayHas->timetable->overtime_pay;
                                                    $total_plusIn_date = ((($plusInTime ?? 0) * 60) / $time_period) * $overtime_pay;
                                                    $emp_ins[] = [
                                                        "value" => $plusInTime,
                                                        "in" => $total_plusIn_date,
                                                    ];
                                                }
                                                if ($punchOut->gt($out)) {
                                                    $diff_time_out = $out->diffInSeconds($punchOut);
                                                    $minute = intval(gmdate('i', $diff_time_out));
                                                    $overtime += intval(gmdate('G', $diff_time_out));
                                                    if ($minute >= $shiftdayHas->timetable->overtime_half_hour && $minute < $shiftdayHas->timetable->overtime_one_hour) {
                                                        $overtime = $overtime + 0.5;
                                                    } else if ($minute >= $shiftdayHas->timetable->overtime_one_hour) {
                                                        $overtime++;
                                                    }

                                                    $time_period = $shiftdayHas->timetable->time_period;
                                                    $overtime_pay = $shiftdayHas->timetable->overtime_pay;
                                                    $total_overtime_date = ((($overtime ?? 0) * 60) / $time_period) * $overtime_pay;
                                                    $emp_overtimes[] = [
                                                        "value" => $overtime,
                                                        "overtime" => $total_overtime_date,
                                                        "is_calculate_one_shift" => $punchOut->gt($out->addHour(8)),
                                                    ];
                                                }
                                            }
                                            $shift_data['weekday'] = $shiftday->name;
                                            $shift_data['slug'] = $slug_week[$code_day - 1];
                                        }
                                    }
                                }
                            }

                            $date_datas[] =  [
                                "date" => $date,
                                "first_punch" => $item_first['punch_time'],
                                "last_punch" => $item_last['punch_time'],
                                "total_time" => $diff_time->format('%H:%I'),
                                "overtime" => $overtime,
                                "in" => $plusInTime,
                                "shift" => $shift_data,
                            ];
                        } else {
                            $date_datas[] =  [
                                "date" => $date,
                                "first_punch" => null,
                                "last_punch" => null,
                                "overtime" => null,
                                "in" => null,
                                "shift" => [],
                            ];
                        }
                    }

                    $overtime_count = 0;
                    $overtime_payment = 0;
                    $plus_in_payment = 0;
                    $in_count = 0;
                    $plus_in_payment = 0;
                    $is_calculate_one_shift_count = 0;

                    foreach ($emp_overtimes as $item) {
                        if ($item['is_calculate_one_shift']) {
                            $is_calculate_one_shift_count++;
                        } else {
                            $overtime_count += $item['value'];
                            $overtime_payment += $item['overtime'];
                        }
                    }
                    foreach ($emp_ins as $item) {
                        $in_count += $item['value'];
                        $plus_in_payment += $item['in'];
                    }


                    $total = ($overtime_payment + $plus_in_payment) + (($is_calculate_one_shift_count > 0) ? $is_calculate_one_shift_count * $daily_salary : $daily_salary);
                    $attendance_reports[] = [
                        'employee' => $emp,
                        'range_date' => $request->input('date'),
                        'daily_salary' => $daily_salary,
                        'position_extra_pay' => $position_extra_pay,
                        'dept' => array_sum(array_column($emp_depts->toArray(), 'remainder_debt')),
                        'overtime_count' => $overtime_count,
                        'overtime_payment' => $overtime_payment,
                        'in_count' => $in_count,
                        'plus_in_payment' => $plus_in_payment,
                        'is_calculate_one_shift_count' => $is_calculate_one_shift_count,
                        'total' => $total,
                        'reports' => $date_datas,
                    ];
                }
                $order = null;
                $render =  view('Report.attendance_card.table', compact('attendance_reports', 'order'))->render();

                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            }

            return  view('Report.attendance_card.index');
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
