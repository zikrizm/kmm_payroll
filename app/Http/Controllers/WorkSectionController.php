<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\WorkSection;
use App\Utils\BusinessUtil;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;
use App\Services\Api\ApiServices;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class WorkSectionController extends Controller
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
        if (!auth()->user()->can('work-section.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            if (request()->ajax()) {
                $business_id = Session::get('business_id');
                $work_sections = WorkSection::where('business_id', $business_id);
                if ($request->has('q')) {
                    $search = $request->q;
                    $work_sections = $work_sections->where(function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%" . $search . "%")->orWhere('pay', 'LIKE', "%" . $search . "%")
                            ->orWhere('time_period', 'LIKE', "%" . $search . "%")->orWhereHas('shift', function ($query) use ($search) {
                                return $query->where('name', 'LIKE', "%" . $search . "%");
                            });
                    });
                }
                $work_sections = $work_sections->orderBy('shift_id', 'ASC')->orderBy('name', 'ASC')->paginate(10);
                $render =  view('work_section.table', compact('work_sections'))->render();

                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            }

            return  view('work_section.index');
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
        if (!auth()->user()->can('work-section.create') || !request()->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = Session::get('business_id');
            $shifts = Shift::where('business_id', $business_id)->where('status', 'active')->get();
            $render = view('work_section.create', compact('shifts'))->render();

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
        if (!auth()->user()->can('work-section.create')  || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }
        Log::info($request);
        try {
            $validator = Validator::make($request->all(), $this->rules());

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $work_section_data = $request->only(['name', 'shift_id', 'status', 'time_period', 'pay']);
                $work_section_data['pay'] = str_replace(',', '', $work_section_data['pay']);
                $work_section_data['business_id'] = Session::get('business_id');
                $work_section = new WorkSection($work_section_data);
                Log::info($work_section);
                $work_section->save();

                return $this->buildRes->RESPONSE_REQ('success', null,  'Add work section succesfully');
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
        if (!auth()->user()->can('work-section.view')) {
            abort(403, 'Unauthorized action.');
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $work_section
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(WorkSection $work_section, Request $request)
    {
        if (!auth()->user()->can('work-section.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = Session::get('business_id');
            $shifts = Shift::where('business_id', $business_id)->where('status', 'active')->get();
            $render = view('work_section.edit', compact('work_section', 'shifts'))->render();

            return $this->buildRes->RESPONSE_REQ('success', $render, null);
        } catch (\Exception $error) {
            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  WorkSection  $work_section
     * @return \Illuminate\Http\Response
     */
    public function update(WorkSection $work_section, Request $request)
    {
        if (!auth()->user()->can('work-section.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }
        Log::info($request);

        try {
            $validator = Validator::make($request->all(), $this->rules());

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $work_section_data = $request->only(['name', 'shift_id', 'status', 'time_period', 'pay']);
                $work_section_data['pay'] = str_replace(',', '', $work_section_data['pay']);
                $work_section->update($work_section_data);

                return $this->buildRes->RESPONSE_REQ('success', null, 'Work section update succesfully');
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  WorkSection $work_section
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(WorkSection $work_section, Request $request)
    {
        if (!auth()->user()->can('work-section.delete') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $work_section->delete();

            return $this->buildRes->RESPONSE_REQ('success', null, 'Work section delete succesfully');
        } catch (\Exception $e) {
            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Rules validation work section.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'shift_id' => 'required|exists:shifts,id',
            'status' => 'required|string',
            'time_period' => 'required|numeric',
            'pay' => 'required',
        ];
    }
}
