<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use App\Utils\BusinessUtil;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Services\Api\ApiServices;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class HolidayController extends Controller
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
        if (!auth()->user()->can('holiday.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            if (request()->ajax()) {
                $business_id = Session::get('business_id');
                $holidays = Holiday::where('business_id', $business_id);
                if ($request->has('q')) {
                    $search = $request->q;
                    $holidays = $holidays->where(function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%" . $search . "%")->orWhere('notes', 'LIKE', "%" . $search . "%");
                    });
                }

                $holidays = $holidays->orderBy('name', 'ASC')->paginate(10);
                $render =  view('holiday.table', compact('holidays'))->render();

                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            }

            return view('holiday.index');
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, 'something wrong');
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
        if (!auth()->user()->can('holiday.create') || !request()->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $render = view('holiday.create')->render();

            return $this->buildRes->RESPONSE_REQ('success', $render, null);
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, 'something wrong');
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
        if (!auth()->user()->can('group.create')  || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $validator = Validator::make($request->all(), $this->rules());

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $holiday_data = $request->only(['name', 'start_date', 'end_date', 'notes', 'status']);
                $holiday_data['business_id'] = Session::get('business_id');

                if (!empty($holiday_data['start_date'])) {
                    $holiday_data['start_date'] = Carbon::createFromFormat('Y-m-d H:i:s', $holiday_data['start_date']);
                }
                if (!empty($holiday_data['end_date'])) {
                    $holiday_data['end_date'] = Carbon::createFromFormat('Y-m-d H:i:s', $holiday_data['end_date']);
                }
                $holiday = new Holiday($holiday_data);
                $holiday->save();

                return $this->buildRes->RESPONSE_REQ('success', null,  'Add holiday succesfully');
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, 'something wrong');
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
        if (!auth()->user()->can('group.view')) {
            abort(403, 'Unauthorized action.');
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  Holiday $holiday
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(Holiday $holiday, Request $request)
    {
        if (!auth()->user()->can('holiday.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $render = view('holiday.edit', compact('holiday'))->render();

            return $this->buildRes->RESPONSE_REQ('success', $render, null);
        } catch (\Exception $error) {
            return $this->buildRes->RESPONSE_REQ('error', null, 'something wrong');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Holiday $holiday
     * @return \Illuminate\Http\Response
     */
    public function update(Holiday $holiday, Request $request)
    {
        if (!auth()->user()->can('holiday.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $validator = Validator::make($request->all(), $this->rules());

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {

                $holiday_data = $request->only(['name', 'start_date', 'end_date', 'notes', 'status']);
                $holiday_data['business_id'] = Session::get('business_id');

                if (!empty($holiday_data['start_date'])) {
                    $holiday_data['start_date'] = Carbon::createFromFormat('Y-m-d H:i:s', $holiday_data['start_date']);
                }
                if (!empty($holiday_data['end_date'])) {
                    $holiday_data['end_date'] = Carbon::createFromFormat('Y-m-d H:i:s', $holiday_data['end_date']);
                }
                $holiday->update($holiday_data);

                return $this->buildRes->RESPONSE_REQ('success', null, 'group update succesfully');
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, 'something wrong');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Holiday $holiday
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Holiday $holiday, Request $request)
    {
        if (!auth()->user()->can('holiday.delete') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $holiday->delete();

            return $this->buildRes->RESPONSE_REQ('success', null, 'holiday delete succesfully');
        } catch (\Exception $e) {
            return $this->buildRes->RESPONSE_REQ('error', null, 'something wrong');
        }
    }

    /**
     * Rules validation group.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'start_date' => 'required',
            'end_date' => 'required',
            'status' => 'required|string',
            'notes' => 'required|string',
        ];
    }
}
