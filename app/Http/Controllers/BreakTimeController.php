<?php

namespace App\Http\Controllers;

use App\Models\BreakTime;
use App\Models\ActivityLog;
use App\Utils\BusinessUtil;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;
use App\Services\Api\ApiServices;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class BreakTimeController extends Controller
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
        if (!auth()->user()->can('break-time.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            if (request()->ajax()) {
                $business_id = Session::get('business_id');
                $break_times = BreakTime::where('business_id', $business_id);
                if ($request->has('q')) {
                    $search = $request->q;
                    $break_times = $break_times->where('name', 'LIKE', "%" . $search . "%");
                }

                if ($request->has('page')) {
                    $filter['page'] = $request->page;
                }

                $order = null;
                if ($request->has('sort')) {
                    $sort = $request->sort;
                    $order = $sort['order'];
                    $break_times->orderBy($sort['name'], $sort['order']);
                }
                $break_times = $break_times->paginate(10);
                $render =  view('Shift.break_time.table', compact('break_times', 'order'))->render();

                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            }

            return  view('Shift.break_time.index');
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
        if (!auth()->user()->can('break-time.create') || !request()->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $render = view('Shift.break_time.create')->render();

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
        if (!auth()->user()->can('break-time.create')  || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $validator = Validator::make($request->all(), $this->rules(null));

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $break_time_data = $request->only(['name', 'start_time', 'end_time', 'duration']);
                $break_time_data['business_id'] = Session::get('business_id');

                $break_time = new BreakTime($break_time_data);
                $break_time->save();

                // ** create activity log user
                ActivityLog::created_activity('CRUD break time', 'User ' . auth()->user()->username . ' create new break time');
                return $this->buildRes->RESPONSE_REQ('success', null, ['success' => 'Add break-time succesfully']);
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
     * @param  BreakTime $break_time
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(BreakTime $break_time, Request $request)
    {
        log::info($break_time);
        if (!auth()->user()->can('break-time.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $render = view('Shift.break_time.edit', compact('break_time'))->render();

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
     * @param  BreakTime $break_time
     * @return \Illuminate\Http\Response
     */
    public function update(BreakTime $break_time, Request $request)
    {
        if (!auth()->user()->can('break-time.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validator = Validator::make($request->all(), $this->rules($break_time));

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $break_time_data = $request->only(['name', 'start_time', 'end_time', 'duration']);
                $break_time->update($break_time_data);

                // ** create activity log user
                ActivityLog::created_activity('CRUD break time', 'User ' . auth()->user()->username . ' edit data break time');
                return $this->buildRes->RESPONSE_REQ('success', null, ['success' => 'Update break-time succesfully']);
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  BreakTime $break_time
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(BreakTime $break_time, Request $request)
    {
        if (!auth()->user()->can('break-time.delete') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $break_time->delete();

            // ** create activity log user
            ActivityLog::created_activity('CRUD break time', 'User ' . auth()->user()->username . ' delete data break time');
            return $this->buildRes->RESPONSE_REQ('success', null, ['success' => 'Delete break-time succesfully']);
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    public function searchBreakTimeForDropdown(Request $request)
    {
        if (!$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->has('q')) {
            $business_id = Session::get('business_id');
            $search = $request->q;
            $break_times = BreakTime::where('business_id', $business_id)->where('name', 'LIKE', "%" . $search . "%")->get();
            return response()->json($break_times);
        } else {
            return [];
        }
    }

    /**
     * Rules validation break_time.
     *
     * @param  BreakTime $break_time
     * @return array
     */
    public function rules($break_time)
    {
        return [
            'name' => (empty($break_time)) ?  'required|string|max:255|unique:break_times' : 'required|string|max:255|unique:break_times,name,' . $break_time->id,
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'duration' => 'required',
        ];
    }
}
