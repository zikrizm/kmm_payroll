<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Utils\ResponseUtil;
use App\Models\EmployeeDebt;
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

                $emp_count = $this->apiService->get_employees([])['count'];
                $emps = $this->apiService->get_employees(["employee_icontains" => $search, "page_size" => $emp_count])['data'];

                // * Employee filter
                $filter = [];
                $slug_week = ['sen', 'sel', 'rab', 'kam', 'jum', 'sab', 'mgg'];
                if (!empty($request->input('date'))) {
                    $filter['start_time'] = $request->date['start_time'];
                    $filter['end_time'] = $request->date['end_time'];
                    $dates = $this->util->generateDateRange(Carbon::parse($filter['start_time']), Carbon::parse($filter['end_time']));
                }

                $atten_count = $this->apiService->get_transactions($filter)['count'];
                $filter['page_size'] = $atten_count;
                $attens = collect($this->apiService->get_transactions($filter)['data']);
                $shifts = Shift::where('business_id', $business_id)->with(
                    ['shiftday' => function ($query) {
                        $query->with(['shiftday_has_timetable' => function ($query) {
                            $query->with(['timetable']);
                        }]);
                    }]
                )->get();

                $attendance_reports = [];
                foreach (($emps ?? []) as $emp) {
                    $attens_groupings = $this->_group_by_date($attens->filter(function ($atten) use ($emp) {
                        return $atten['emp'] === $emp['id'];
                    }));

                    $date_datas = [];
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
                            foreach ($shifts as $shift) {
                                foreach ($shift->shiftday as $shiftday) {
                                    foreach ($shiftday->shiftday_has_timetable as $keyHas => $shiftdayHas) {
                                        $in = Carbon::createFromTimeString($shiftdayHas->timetable->in_time);
                                        $out = Carbon::createFromTimeString($shiftdayHas->timetable->out_time);
                                        $punchIn = Carbon::createFromTimeString($check_in);
                                        $punchOut = Carbon::createFromTimeString($check_out);

                                        // // check apakah out lebih kecil dari in, klo ya tambah 1 hari
                                        // if ($out->lessThan($in)) {
                                        //     $punch->addDay();
                                        //     $out->addDay();
                                        // }

                                        if ($code_day == $shiftday->code_day) {
                                            if ($punchIn->lt($in->addHour())) {
                                                $shift_data['id'] = $shift->id;
                                                $shift_data['name'] = $shift->name;
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
                                "shift" => $shift_data,
                            ];
                        } else {
                            $date_datas[] =  [
                                "date" => $date,
                                "first_punch" => null,
                                "last_punch" => null,
                                "total_time" => null,
                                "shift" => [],
                            ];
                        }
                    }

                    $attendance_reports[] = [
                        'employee' => $emp,
                        'range_date' => $request->input('date'),
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
