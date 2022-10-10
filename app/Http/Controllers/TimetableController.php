<?php

namespace App\Http\Controllers;

use App\Models\BreakTime;
use App\Models\Timetable;
use App\Utils\BusinessUtil;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use App\Services\Api\ApiServices;
use Illuminate\Support\Facades\Log;
use App\Models\TimetableHasBreakTime;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class TimetableController extends Controller
{
    private $apiService;
    private $buildRes;
    private $businessUtil;

    public function __construct(BusinessUtil $businessUtil, ApiServices $service, ResponseUtil $buildRes)
    {
        $this->businessUtil = $businessUtil;
        $this->apiService = $service;
        $this->buildRes = $buildRes;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!auth()->user()->can('timetable.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            if (request()->ajax()) {
                $business_id = Session::get('business_id');
                $timetables = Timetable::where('business_id', $business_id)->with(['timetable_has_break_time']);

                if ($request->has('q')) {
                    $search = $request->q;
                    $timetables = $timetables->where('name', 'LIKE', "%" . $search . "%");
                }

                if ($request->has('page')) {
                    $filter['page'] = $request->page;
                }

                $order = null;
                if ($request->has('sort')) {
                    $sort = $request->sort;
                    $order = $sort['order'];
                    $timetables->orderBy($sort['name'], $sort['order']);
                }
                $timetables = $timetables->paginate(10);
                $render =  view('Shift.timetable.table', compact('timetables', 'order'))->render();

                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            }

            return  view('Shift.timetable.index');
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
        if (!auth()->user()->can('timetable.create') || !request()->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = Session::get('business_id');
            $break_times = BreakTime::where('business_id', $business_id)->get();
            $render = view('Shift.timetable.create', compact('break_times'))->render();

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
        if (!auth()->user()->can('timetable.create')  || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $validator = Validator::make($request->all(), $this->rules());

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $timetable_data = $request->only([
                    'name', 'in_time', 'out_time', 'cross_day', 'work_type', 'overtime_rounded', 'overtime_one_hour', 'overtime_half_hour', 'break_time', 'is_without_break',
                    'is_overtime', 'time_period', 'overtime_pay', 'duration_calculate_one_shift', 'is_overtime_rice', 'duration_rice_shift'
                ]);
                $timetable_data['business_id'] = Session::get('business_id');

                $in_time = Carbon::parse($timetable_data['in_time']);
                $out_time = Carbon::parse($timetable_data['out_time']);
                $timetable_data['work_time'] = $in_time->diffInMinutes($out_time);

                if (empty($request->input('overtime_rounded'))) {
                    $timetable_data['overtime_one_hour'] = null;
                    $timetable_data['overtime_half_hour'] = null;
                } else {
                    $timetable_data['overtime_one_hour'] = 40;
                    $timetable_data['overtime_half_hour'] = 20;
                }

                if (empty($request->input('is_without_break'))) {
                    $timetable_data['is_without_break'] = 0;
                }

                if (!empty($request->input('is_overtime'))) {
                    $timetable_data['overtime_pay'] = str_replace('.', '', $timetable_data['overtime_pay']);
                } else {
                    $timetable_data['time_period'] = null;
                    $timetable_data['overtime_pay'] = null;
                    $timetable_data['duration_calculate_one_shift'] = null;
                }

                if (empty($request->input('is_overtime_rice'))) {
                    $timetable_data['duration_rice_shift'] = null;
                }

                $timetable = new Timetable($timetable_data);
                $timetable->save();
                if (!empty($request->input('break_time'))) {
                    foreach ($timetable_data['break_time']  as $item) {
                        $timetable_has_break_time_data = [
                            'business_id' => Session::get('business_id'),
                            'timetable_id' => $timetable->id,
                            'break_time_id' => $item,
                        ];
                        $timetable_has_break_time = new TimetableHasBreakTime($timetable_has_break_time_data);
                        $timetable_has_break_time->save();
                    }
                }

                return $this->buildRes->RESPONSE_REQ('success', null, ['success' => 'Add timetable succesfully']);
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
        if (!auth()->user()->can('user.view')) {
            abort(403, 'Unauthorized action.');
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  Timetable $break_time
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(Timetable $timetable, Request $request)
    {
        if (!auth()->user()->can('timetable.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = Session::get('business_id');
            $timetable_has_break_times = TimetableHasBreakTime::where('business_id', $business_id)
                ->where('timetable_id', $timetable->id)->get();
            $break_times = BreakTime::where('business_id', $business_id)->get();

            $render = view('Shift.timetable.edit', compact('timetable', 'timetable_has_break_times', 'break_times'))->render();
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
     * @param  Timetable $timetable
     * @return \Illuminate\Http\Response
     */
    public function update(Timetable $timetable, Request $request)
    {
        if (!auth()->user()->can('timetable.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validator = Validator::make($request->all(), $this->rules());

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $timetable_data = $request->only([
                    'name', 'in_time', 'out_time', 'cross_day', 'work_type', 'overtime_rounded', 'overtime_one_hour', 'overtime_half_hour', 'break_time', 'is_without_break',
                    'is_overtime', 'time_period', 'overtime_pay', 'duration_calculate_one_shift', 'is_overtime_rice', 'duration_rice_shift'
                ]);
                $timetable_data['business_id'] = Session::get('business_id');

                $in_time = Carbon::parse($timetable_data['in_time']);
                $out_time = Carbon::parse($timetable_data['out_time']);
                $timetable_data['work_time'] = $in_time->diffInMinutes($out_time);

                if (empty($request->input('overtime_rounded'))) {
                    $timetable_data['overtime_one_hour'] = null;
                    $timetable_data['overtime_half_hour'] = null;
                } else {
                    $timetable_data['overtime_one_hour'] = 40;
                    $timetable_data['overtime_half_hour'] = 20;
                }

                if (empty($request->input('is_without_break'))) {
                    $timetable_data['is_without_break'] = 0;
                }

                if (!empty($request->input('is_overtime'))) {
                    $timetable_data['overtime_pay'] = str_replace('.', '', $timetable_data['overtime_pay']);
                } else {
                    $timetable_data['time_period'] = null;
                    $timetable_data['overtime_pay'] = null;
                    $timetable_data['duration_calculate_one_shift'] = null;
                }

                if (empty($request->input('is_overtime_rice'))) {
                    $timetable_data['duration_rice_shift'] = null;
                }

                $timetable->update($timetable_data);

                // * Remove all timetable has breaktime.
                $business_id = Session::get('business_id');
                TimetableHasBreakTime::where('business_id', $business_id)
                    ->where('timetable_id', $timetable->id)->each(function ($item) {
                        $item->delete();
                    });

                if (!empty($request->input('break_time'))) {
                    // * Insert timetable has breaktime.
                    foreach ($request['break_time']  as $item) {
                        $timetable_has_break_time_data = [
                            'business_id' => Session::get('business_id'),
                            'timetable_id' => $timetable->id,
                            'break_time_id' => $item,
                        ];
                        $timetable_has_break_time = new TimetableHasBreakTime($timetable_has_break_time_data);
                        $timetable_has_break_time->save();
                    }
                }
                return $this->buildRes->RESPONSE_REQ('success', null, ['success' => 'Update timetable succesfully']);
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Timetable $break_time
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Timetable $timetable, Request $request)
    {
        if (!auth()->user()->can('break-time.delete') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $timetable->delete();

            return $this->buildRes->RESPONSE_REQ('success', null, ['success' => 'Delete timetable succesfully']);
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    public function searchTimetableForDropdown(Request $request)
    {
        if (!$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->has('q')) {
            $business_id = Session::get('business_id');
            $search = $request->q;
            $timetables = Timetable::where('business_id', $business_id)->where('name', 'LIKE', "%" . $search . "%")->get();
            return response()->json($timetables);
        } else {
            return [];
        }
    }

    /**
     * Rules validation break_time.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'in_time' => 'required',
            'out_time' => 'required|after:in_time',
            'work_type' => 'required',
            'is_overtime' => 'nullable',
            'time_period' => [
                Rule::requiredIf(function () {
                    return request()->get('is_overtime');
                })
            ],
            'overtime_pay' => [
                Rule::requiredIf(function () {
                    return request()->get('is_overtime');
                })
            ],
            'duration_calculate_one_shift' => [
                Rule::requiredIf(function () {
                    return request()->get('is_overtime');
                })
            ],
            'is_overtime_rice' => 'nullable',
            'duration_rice_shift' => [
                Rule::requiredIf(function () {
                    return request()->get('is_overtime_rice');
                })
            ],
        ];
    }
}
