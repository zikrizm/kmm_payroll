<?php

namespace App\Http\Controllers;

use App\Models\BreakTime;
use App\Models\Timetable;
use App\Models\ActivityLog;
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
use Illuminate\Validation\Rules\RequiredIf;

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
            Log::info(config('constants.department'));
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
                $business_id = Session::get('business_id');
                $timetable_data = $request->only([
                    'name', 'check_in',  'check_out', 'check_in_plusmn', 'check_out_plusmn', 'cross_day', 'is_ot_rounding', 'ot_roundone_hr', 'ot_roundhalf_hr', 'break_times', 'is_without_break',
                    'is_ot', 'ot_period', 'ot_pay', 'duration_count_one_shift', 'duration_ot_limit', 'is_ot_rice', 'duration_rice_shift'
                ]);
                $timetable_data['business_id'] = $business_id;
                $check_in = Carbon::parse($timetable_data['check_in']);
                $check_out = Carbon::parse($timetable_data['check_out']);
                if ($check_out->lt($check_in)) {
                    $check_out = $check_out->addDays(1);
                }
                $timetable_data['work_time'] = $check_in->diffInMinutes($check_out);

                if (!empty($request->input('is_ot_rounding'))) {
                    $timetable_data['ot_roundone_hr'] = 40;
                    $timetable_data['ot_roundhalf_hr'] = 20;
                } else {
                    $timetable_data['ot_roundone_hr'] = null;
                    $timetable_data['ot_roundhalf_hr'] = null;
                }
                if (!empty($request->input('is_ot'))) {
                    $timetable_data['ot_pay'] = str_replace('.', '', $timetable_data['ot_pay']);
                } else {
                    $timetable_data['ot_period'] = null;
                    $timetable_data['ot_pay'] = null;
                    $timetable_data['duration_count_one_shift'] = null;
                    $timetable_data['duration_ot_limit'] = null;
                }
                if (empty($request->input('is_ot_rice'))) {
                    $timetable_data['duration_rice_shift'] = null;
                }
                if (empty($request->input('break_times'))) {
                    $timetable_data['is_without_break'] = 0;
                }
                $timetable = new Timetable($timetable_data);
                $timetable->save();

                if (!empty($request->input('break_times'))) {
                    // * Insert timetable has breaktime.
                    foreach ($timetable_data['break_times']  as $item) {
                        $timetable_has_break_time_data = [
                            'business_id' => $business_id,
                            'timetable_id' => $timetable->id,
                            'break_time_id' => $item,
                        ];
                        $timetable_has_break_time = new TimetableHasBreakTime($timetable_has_break_time_data);
                        $timetable_has_break_time->save();
                    }
                }

                // ** create activity log user
                ActivityLog::created_activity('CRUD timetable', 'User ' . auth()->user()->username . ' create new timetable');
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
            $break_times = BreakTime::where('business_id', $business_id)->get();
            $timetable_has_break_times = TimetableHasBreakTime::where('business_id', $business_id)
                ->where('timetable_id', $timetable->id)->get();

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
                $business_id = Session::get('business_id');
                $timetable_data = $request->only([
                    'name', 'check_in',  'check_out', 'check_in_plusmn', 'check_out_plusmn', 'cross_day', 'is_ot_rounding', 'ot_roundone_hr', 'ot_roundhalf_hr', 'break_times', 'is_without_break',
                    'is_ot', 'ot_period', 'ot_pay', 'duration_count_one_shift', 'duration_ot_limit', 'is_ot_rice', 'duration_rice_shift'
                ]);
                $timetable_data['business_id'] = $business_id;
                $check_in = Carbon::parse($timetable_data['check_in']);
                $check_out = Carbon::parse($timetable_data['check_out']);
                if ($check_out->lt($check_in)) {
                    $check_out = $check_out->addDays(1);
                }
                $timetable_data['work_time'] = $check_in->diffInMinutes($check_out);

                if (!empty($request->input('is_ot_rounding'))) {
                    $timetable_data['ot_roundone_hr'] = 40;
                    $timetable_data['ot_roundhalf_hr'] = 20;
                } else {
                    $timetable_data['ot_roundone_hr'] = null;
                    $timetable_data['ot_roundhalf_hr'] = null;
                }
                if (!empty($request->input('is_ot'))) {
                    $timetable_data['ot_pay'] = str_replace('.', '', $timetable_data['ot_pay']);
                } else {
                    $timetable_data['ot_period'] = null;
                    $timetable_data['ot_pay'] = null;
                    $timetable_data['duration_count_one_shift'] = null;
                    $timetable_data['duration_ot_limit'] = null;
                }
                if (empty($request->input('is_ot_rice'))) {
                    $timetable_data['duration_rice_shift'] = null;
                }
                if (empty($request->input('break_times'))) {
                    $timetable_data['is_without_break'] = 0;
                }
                if(empty($timetable_data['is_without_break'])) {
                    $timetable_data['is_without_break'] = 0;
                }
                Log::info($timetable_data);
                $timetable->update($timetable_data);

                // * Remove all timetable has breaktime.
                TimetableHasBreakTime::where('business_id', $business_id)
                    ->where('timetable_id', $timetable->id)->each(function ($item) {
                        $item->delete();
                    });

                if (!empty($request->input('break_times'))) {
                    // * Insert timetable has breaktime.
                    foreach ($request['break_times']  as $item) {
                        $timetable_has_break_time_data = [
                            'business_id' => Session::get('business_id'),
                            'timetable_id' => $timetable->id,
                            'break_time_id' => $item,
                        ];
                        $timetable_has_break_time = new TimetableHasBreakTime($timetable_has_break_time_data);
                        $timetable_has_break_time->save();
                    }
                }

                // ** create activity log user
                ActivityLog::created_activity('CRUD timetable', 'User ' . auth()->user()->username . ' edit data timetable');
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

            // ** create activity log user
            ActivityLog::created_activity('CRUD timetable', 'User ' . auth()->user()->username . ' delete data timetable');
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
            'check_in' => 'required',
            'check_out' => 'required',
            'check_in_plusmn' => 'required',
            'check_out_plusmn' => 'required',
            'is_ot' => 'nullable',
            'is_ot_rice' => 'nullable',
            'ot_period' => ['nullable', new RequiredIf(request()->get('is_ot') == true), 'numeric', 'max:60'],
            'ot_pay' => ['nullable', new RequiredIf(request()->get('is_ot') == true)],
            'duration_count_one_shift' => ['nullable', new RequiredIf(request()->get('is_ot') == true), 'numeric', 'max:24'],
            'duration_ot_limit' => ['nullable', new RequiredIf(request()->get('is_ot') == true), 'numeric', 'max:24'],
            'duration_rice_shift' => ['nullable', new RequiredIf(request()->get('is_ot_rice') == true), 'numeric', 'max:24'],
        ];
    }
}
