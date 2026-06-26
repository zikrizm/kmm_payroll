<?php

namespace App\Http\Controllers;

use App\Utils\Util;
use App\Models\Shift;
use App\Models\Transaction;
use App\Utils\ResponseUtil;
use App\Utils\AttendanceUtil;
use App\Models\EmployeeDebt;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Services\Api\ApiServices;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AttendanceReportController extends Controller
{
    private $apiService;
    private $buildRes;
    private $util;
    private $attendanceUtil;

    public function __construct(ApiServices $apiService, Util $util, AttendanceUtil $attendanceUtil, ResponseUtil $buildRes)
    {
        $this->apiService = $apiService;
        $this->buildRes = $buildRes;
        $this->util = $util;
        $this->attendanceUtil = $attendanceUtil;
    }

    private function departmentCodesFromRequest(Request $request): ?array
    {
        if ($request->has('department_codes')) {
            $codes = $request->input('department_codes');
            if (!is_array($codes)) {
                $codes = ($codes !== null && $codes !== '') ? [$codes] : [];
            }
            $filtered = array_values(array_filter($codes, fn ($code) => $code !== null && $code !== ''));

            return empty($filtered) ? null : $filtered;
        }

        if ($request->filled('department_code')) {
            $code = $request->input('department_code');
            if (is_array($code)) {
                $filtered = array_values(array_filter($code));

                return empty($filtered) ? null : $filtered;
            }

            return [$code];
        }

        return null;
    }

    private function departmentApiIdsFromCodes(?array $departmentCodes): ?string
    {
        if (empty($departmentCodes)) {
            return null;
        }

        $departments = collect($this->attendanceUtil->getDepartment());
        $ids = collect($departmentCodes)
            ->map(function ($code) use ($departments) {
                $byCode = $departments->firstWhere('dept_code', (string) $code);
                if ($byCode) {
                    return $byCode['id'] ?? null;
                }

                $byId = $departments->firstWhere('id', is_numeric($code) ? (int) $code : $code);

                return $byId['id'] ?? null;
            })
            ->filter()
            ->unique()
            ->values();

        return $ids->isEmpty() ? null : $ids->implode(',');
    }

    private function resolveAttendanceReportDateRange(Request $request): array
    {
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start = Carbon::createFromFormat('d-m-Y', $request->input('start_date'))->startOfDay();
            $end = Carbon::createFromFormat('d-m-Y', $request->input('end_date'))->endOfDay();

            return [$start, $end];
        }

        if (!empty($request->input('attendance_report_date'))) {
            $start = Carbon::parse($request->attendance_report_date['start_time'])->startOfDay();
            $end = Carbon::parse($request->attendance_report_date['end_time'])->addHours(1)->endOfDay();

            return [$start, $end];
        }

        $start = Carbon::now()->startOfWeek()->startOfDay();
        $end = Carbon::now()->endOfWeek()->endOfDay();

        return [$start, $end];
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!auth()->user()->can('attendance-report.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = Session::get('business_id');
            $department_codes = $this->departmentCodesFromRequest($request);
            $start_date = $request->input('start_date');
            $end_date = $request->input('end_date');

            if (request()->ajax()) {
                $page = 1;
                if (!empty($request->input('page'))) {
                    $page = (int)$request->page;
                }

                $search = '';
                if (!empty($request->input('q'))) {
                    $search = $request->q;
                }

                [$startCarbon, $endCarbon] = $this->resolveAttendanceReportDateRange($request);
                $reportFilter = [
                    'start_date' => $startCarbon->format('Y-m-d'),
                    'end_date' => $endCarbon->format('Y-m-d'),
                ];
                $departmentIds = $this->departmentApiIdsFromCodes(
                    is_array($department_codes) ? $department_codes : ($department_codes ? [$department_codes] : null)
                );
                if (!empty($departmentIds)) {
                    $reportFilter['departments'] = $departmentIds;
                }

                $attenDBs = Transaction::whereBetween('punch_time', [
                    $startCarbon->format('Y-m-d H:i:s'),
                    $endCarbon->format('Y-m-d H:i:s'),
                ])
                    ->orderBy('punch_time', 'desc')
                    ->get();

                $atten_datas = $this->apiService->get_transaction_reports($reportFilter) ?? ['count' => 0, 'data' => []];
                $atten_bio_count = $atten_datas['count'] ?? 0;
                if ($atten_bio_count > 0) {
                    $atten_datas = $this->apiService->get_transaction_reports(array_merge(['page_size' => $atten_bio_count], $reportFilter))
                        ?? ['count' => 0, 'data' => []];
                }
                $atten_bios = collect($atten_datas['data'] ?? [])->map(function ($item) {
                    $punchTime = $item['punch_time'] ?? null;
                    if (!empty($item['att_date']) && !empty($item['punch_time']) && !str_contains((string) $item['punch_time'], '-')) {
                        $punchTime = $item['att_date'] . ' ' . $item['punch_time'] . (strlen((string) $item['punch_time']) <= 5 ? ':00' : '');
                    }

                    return [
                        'id' => $item['id'] ?? null,
                        'emp' => $item['emp'] ?? $item['emp_code'] ?? null,
                        'emp_code' => $item['emp_code'] ?? null,
                        'first_name' => $item['first_name'] ?? null,
                        'last_name' => $item['last_name'] ?? null,
                        'department' => $item['dept_name'] ?? $item['department'] ?? null,
                        'punch_time' => $punchTime,
                    ];
                });

                foreach ($attenDBs as $value) {
                    $row = $value->toArray();
                    $row['id'] = $row['emp'] ?? $row['id'] ?? null;
                    $atten_bios->push($row);
                }

                $attendance_reports = [];
                $attens_groupings = $this->_group_by_date_and_emp($atten_bios);
                foreach ($attens_groupings as $items) {
                    $item_first = $items[0];
                    $item_last = $items[count($items) - 1];
                    $diff_time = Carbon::parse($item_first['punch_time'])->diff(Carbon::parse($item_last['punch_time']));
                    if (!empty($search)) {
                        if (str_contains(strtolower($item_first['first_name'] ?? ''), strtolower($search))) {
                            $attendance_reports[] =  [
                                "id" => $item_first['id'],
                                "emp_code" => $item_first['emp_code'],
                                "first_name" => $item_first['first_name'],
                                "last_name" => $item_first['last_name'],
                                "department" => $item_first['department'],
                                "att_date" => Carbon::parse($item_first['punch_time'])->format('Y-m-d'),
                                "first_punch" => $item_first['punch_time'],
                                "last_punch" => $item_last['punch_time'],
                                "total_time" => $diff_time->format('%H:%I')
                            ];
                        }
                    } else {
                        $attendance_reports[] =  [
                            "id" => $item_first['id'],
                            "emp_code" => $item_first['emp_code'],
                            "first_name" => $item_first['first_name'],
                            "last_name" => $item_first['last_name'],
                            "department" => $item_first['department'],
                            "att_date" => Carbon::parse($item_first['punch_time'])->format('Y-m-d'),
                            "first_punch" => $item_first['punch_time'],
                            "last_punch" => $item_last['punch_time'],
                            "total_time" => $diff_time->format('%H:%I')
                        ];
                    }
                }
                $atten_count = count($attendance_reports);
                $attendance_reports  = collect($attendance_reports)->skip(($page - 1) * 10)->take(10);

                $next = (ceil($atten_count / 10) == $page) ?  null : $page + 1;
                $attendance_reports = collect([
                    'count' => $atten_count,
                    'data' => $attendance_reports,
                    'next' => $next,
                    'previous' => $page - 1,
                    'lastPage' => ceil($atten_count / 10),
                    'currentPage' => $page,
                ]);

                $order = null;
                $render =  view('Report.attendance_report.table', compact('attendance_reports', 'order'))->render();

                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            }

            $department_bios = collect($this->attendanceUtil->getDepartment());
            if (!is_array($department_codes)) {
                $department_codes = $department_codes ? [$department_codes] : [];
            }

            return view('Report.attendance_report.index', compact('department_bios', 'department_codes', 'start_date', 'end_date'));
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
        if (!auth()->user()->can('kasbon.create') || !request()->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $render = view('Employee.kasbon.create')->render();

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
        if (!auth()->user()->can('kasbon.create')  || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validator = Validator::make($request->all(), $this->rules());

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $kasbon_data = $request->only(['emp_id', 'date', 'debt', 'instalment']);

                $employee = $this->apiService->read_employee($kasbon_data['emp_id']);
                $kasbon_data['business_id'] = Session::get('business_id');
                $kasbon_data['first_name'] = $employee['first_name'];
                $kasbon_data['emp_code'] = $employee['emp_code'];
                $kasbon_data['created_user'] = auth()->user()->id;
                $kasbon_data['updated_user'] = auth()->user()->id;
                $kasbon_data['debt'] = str_replace('.', '', $kasbon_data['debt']);
                $kasbon_data['instalment'] = str_replace('.', '', $kasbon_data['instalment']);

                $kasbon = new EmployeeDebt($kasbon_data);
                $kasbon->save();

                return $this->buildRes->RESPONSE_REQ('success', null,  ['success' => ['Add kasbon succesfully']]);
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('kasbon.view')) {
            abort(403, 'Unauthorized action.');
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  EmployeeDebt $kasbon
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(EmployeeDebt $kasbon, Request $request)
    {
        if (!auth()->user()->can('kasbon.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $employee = $this->apiService->get_employees(['emp_code' => $kasbon->emp_code]);
            $employee = $employee['data'][0];
            Log::info($employee);
            $render = view('Employee.kasbon.edit', compact('kasbon', 'employee'))->render();

            return $this->buildRes->RESPONSE_REQ('success', $render, null);
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  EmployeeDebt $kasbon
     * @return \Illuminate\Http\Response
     */
    public function update(EmployeeDebt $kasbon, Request $request)
    {
        if (!auth()->user()->can('kasbon.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validator = Validator::make($request->all(), $this->rules());

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $kasbon_data = $request->only(['emp_id', 'date', 'debt', 'instalment']);
                $employee = $this->apiService->read_employee($kasbon_data['emp_id']);
                $kasbon_data['business_id'] = Session::get('business_id');
                $kasbon_data['first_name'] = $employee['first_name'];
                $kasbon_data['emp_code'] = $employee['emp_code'];
                $kasbon_data['updated_user'] = auth()->user()->id;
                $kasbon_data['debt'] = str_replace('.', '', $kasbon_data['debt']);
                $kasbon_data['instalment'] = str_replace('.', '', $kasbon_data['instalment']);

                $kasbon->update($kasbon_data);
                return $this->buildRes->RESPONSE_REQ('success', null, ['success' => ['Update kasbon succesfully']]);
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  EmployeeDebt $kasbon
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmployeeDebt $kasbon, Request $request)
    {
        if (!auth()->user()->can('kasbon.delete') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $kasbon->delete();

            return $this->buildRes->RESPONSE_REQ('success', null, ['success' => ['Delete kasbon succesfully']]);
        } catch (\Exception $e) {
            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function showAttendanceCard(Request $request)
    {
        // if (!auth()->user()->can('attendance-card') || !request()->ajax()) {
        //     abort(403, 'Unauthorized action.');
        // }

        try {
            $render = view('Report.attendance_report.modal.attendance_card')->render();

            return $this->buildRes->RESPONSE_REQ('success', $render, null);
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Check employee attendance report data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkAttendanceCard(Request $request)
    {
        // if (!auth()->user()->can('attendance-card') || !request()->ajax()) {
        //     abort(403, 'Unauthorized action.');
        // }

        try {
            $validator = Validator::make($request->all(), $this->rules());

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $attendance_report_data = $request->only(['emp_code', 'date']);

                $filter = [];
                $business_id = Session::get('business_id');
                $filter['emp_code'] = $attendance_report_data['emp_code'];
                $filter['start_time'] = trim(explode(' - ', $attendance_report_data['date'])[0]);
                $filter['end_time'] = trim(explode(' - ', $attendance_report_data['date'])[1]);

                $dates = $this->util->generateDateRange(Carbon::parse($filter['start_time']), Carbon::parse($filter['end_time']));
                $employee = $this->apiService->get_employees(['emp_code' => $attendance_report_data['emp_code']])['data'][0];
                $transactions_count = $this->apiService->get_transactions($filter)['count'];
                $filter['page_size'] = $transactions_count;
                $transactions = $this->apiService->get_transactions($filter);

                $shifts = Shift::where('business_id', $business_id)->where('dept_id', $employee['department']['id'])->active()->with(
                    ['shiftday' => function ($query) {
                        $query->with(['shiftday_has_timetable' => function ($query) {
                            $query->with(['timetable']);
                        }]);
                    }]
                )->get();

                $attendance_emp_report = array_reduce($transactions['data'], function (array $accumulator, array $element) use ($dates) {
                    $transaction_time = Carbon::parse($element['punch_time'])->format('Y-m-d');
                    $accumulator[$transaction_time][] = $element;

                    usort($accumulator[$transaction_time], function ($a, $b) {
                        return strtotime($a['punch_time']) - strtotime($b['punch_time']);
                    });

                    return $accumulator;
                }, []);


                $total_overtime = 0;
                $total_in = 0;
                foreach ($dates as $key => $date) {
                    $data =  [
                        "shift" => [],
                        "date" => $date,
                        "in" => null,
                        "overtime" => null,
                    ];


                    if (!empty($attendance_emp_report[$date])) {
                        $att_emp_first = $attendance_emp_report[$date][0];
                        $att_emp_last = $attendance_emp_report[$date][count($attendance_emp_report[$date]) - 1];

                        $att_emp_timefirst = Carbon::parse($att_emp_first['punch_time'])->format('H:i:s');
                        $att_emp_timelast = Carbon::parse($att_emp_last['punch_time'])->format('H:i:s');
                        $code_day = Carbon::parse($date)->dayOfWeek;

                        $diff_time = Carbon::parse($att_emp_first['punch_time'])->diff(Carbon::parse($att_emp_last['punch_time']));

                        foreach ($shifts as $shift) {
                            foreach ($shift->shiftday as $shiftday) {
                                foreach ($shiftday->shiftday_has_timetable as $keyHas => $shiftdayHas) {
                                    $in = Carbon::createFromTimeString($shiftdayHas->timetable->in_time);
                                    $out = Carbon::createFromTimeString($shiftdayHas->timetable->out_time);
                                    $punchF = Carbon::createFromTimeString($att_emp_timefirst);
                                    $punchL = Carbon::createFromTimeString($att_emp_timelast);


                                    // check apakah out lebih kecil dari in, klo ya tambah 1 hari
                                    // if ($out->lessThan($in)) {
                                    //     $punch->addDay();
                                    //     $out->addDay();
                                    // }

                                    if ($code_day == $shiftday->code_day) {
                                        $data['shift']['weekday'] = $shiftday->name;
                                        if ($punchF->lessThan($in)) {
                                            $data['shift']['shift_id'] = $shift->id;
                                            $data['shift']['name'] = $shift->name;
                                            $diff_time = $punchF->diffInSeconds($in);
                                            $minute = intval(gmdate('i', $diff_time));
                                            $hours = intval(gmdate('G', $diff_time));
                                            if ($minute >= $shiftdayHas->timetable->overtime_half_hour && $minute < $shiftdayHas->timetable->overtime_one_hour) {
                                                $hours = $hours + 0.5;
                                            } else if ($minute >= $shiftdayHas->timetable->overtime_one_hour) {
                                                $hours++;
                                            }
                                            $data['in'] = $hours;
                                            $total_in++;
                                        }

                                        if ($out->lessThan($punchL)) {
                                            $data['shift']['shift_id'] = $shift->id;
                                            $data['shift']['name'] = $shift->name;
                                            $diff_time = $out->diffInSeconds($punchL);
                                            $minute = intval(gmdate('i', $diff_time));
                                            $hours = intval(gmdate('G', $diff_time));
                                            if ($minute >= $shiftdayHas->timetable->overtime_half_hour && $minute < $shiftdayHas->timetable->overtime_one_hour) {
                                                $hours = $hours + 0.5;
                                            } else if ($minute >= $shiftdayHas->timetable->overtime_one_hour) {
                                                $hours++;
                                            }
                                            $data['overtime'] = $hours;
                                            $total_overtime += $hours;
                                        }

                                        if ($punchF->between($in, $out)) {
                                            $data['shift']['shift_id'] = $shift->id;
                                            $data['shift']['name'] = $shift->name;
                                            if (empty($data['in'])) {
                                                $data['in'] = 0;
                                                $total_in++;
                                                $diffPunch = $in->diffInHours($punchL);
                                                $diffShift = $in->diffInHours($out);
                                                if ($diffPunch >= $diffShift / 2 && $diffPunch < $diffShift) {
                                                    $data['in'] = '1/2';
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        $attendance_emp_report[] =  $data;
                    } else {
                        $data['in'] = null;
                        $data['overtime'] = null;
                        $attendance_emp_report[] =  $data;
                    }
                    unset($attendance_emp_report[$date]);
                }

                $attendance_emp_report = [
                    'employee' => $employee,
                    'range_date' => $attendance_report_data['date'],
                    'total_overtime' => $total_overtime,
                    'total_in' => $total_in,
                    'reports' => $attendance_emp_report,
                ];

                // Log::info(response()->json($attendance_emp_report));

                $render = view('Report.attendance_report.modal.attendance_card_content', compact('attendance_emp_report'))->render();

                return $this->buildRes->RESPONSE_REQ('success', $render, ['success' => ['Attendance card successfully obtained']]);
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    function _group_by_date_and_emp($array)
    {
        $return = array();
        foreach ($array as $val) {
            if (empty($val['punch_time'])) {
                continue;
            }
            $date = Carbon::parse($val['punch_time'])->format('Y-m-d');
            $empKey = $val['emp'] ?? $val['emp_code'] ?? null;
            if (!empty($date) && $empKey !== null && $empKey !== '') {
                $return[$date . '-' . $empKey][] = $val;
                usort($return[$date . '-' . $empKey], function ($a, $b) {
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
            'emp_code' => 'required|string|max:255',
            'date' => 'required',
        ];
    }
}
