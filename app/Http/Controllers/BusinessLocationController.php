<?php

namespace App\Http\Controllers;

use App\Utils\BusinessUtil;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;
use App\Models\BusinessLocation;
use App\Services\Api\ApiServices;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class BusinessLocationController extends Controller
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
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // if (!auth()->user()->can('business-location.view')) {
        //     abort(403, 'Unauthorized action.');
        // }

        try {
            if (request()->ajax()) {
                $business_id = Session::get('business_id');
                $locations = BusinessLocation::where('business_id', $business_id);
                if ($request->has('q')) {
                    $search = $request->q;
                    $locations = $locations->where(function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%" . $search . "%")->orWhere('country', 'LIKE', "%" . $search . "%")
                            ->orWhere('state', 'LIKE', "%" . $search . "%")->orWhere('city', 'LIKE', "%" . $search . "%")
                            ->orWhere('zip_code', 'LIKE', "%" . $search . "%")->orWhere('full_address', 'LIKE', "%" . $search . "%");
                    });
                }

                $locations = $locations->orderBy('name', 'ASC')->paginate(10);
                $render =  view('business_location.table', compact('locations'))->render();

                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            }

            return view('business_location.index');
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
        // if (!auth()->user()->can('business-location.create') || !request()->ajax()) {
        //     abort(403, 'Unauthorized action.');
        // }

        try {
            $render = view('business_location.create')->render();

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
        // if (!auth()->user()->can('business-location.create')) {
        //     abort(403, 'Unauthorized action.');
        // }

        try {
            $validator = Validator::make($request->all(), $this->rules());

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $location_data = $request->only(['name', 'city', 'state', 'country', 'zip_code', 'full_address', 'mobile', 'alternate_number', 'website']);
                $location_data['business_id'] = Session::get('business_id');
                $group = new BusinessLocation($location_data);
                $group->save();

                return $this->buildRes->RESPONSE_REQ('success', null, 'Add location succesfully');
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Display the specified resource.
     *      
     * @param  \App\StoreFront  $storeFront
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('business-location.view')) {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  BusinessLocation $location
     * @param  \App\StoreFront  $storeFront
     * @return \Illuminate\Http\Response
     */
    public function edit(BusinessLocation $location)
    {
        // if (!auth()->user()->can('business-location.create') || !request()->ajax()) {
        //     abort(403, 'Unauthorized action.');
        // }

        try {
            $render = view('business_location.edit', compact('location'))->render();

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
     * @param  BusinessLocation $location
     * @return \Illuminate\Http\Response
     */
    public function update(BusinessLocation $location, Request $request)
    {
        // if (!auth()->user()->can('business-location.update')) {
        //     abort(403, 'Unauthorized action.');
        // }

        try {
            $validator = Validator::make($request->all(), $this->rules());

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $location_data = $request->only(['name', 'city', 'state', 'country', 'zip_code', 'full_address', 'mobile', 'alternate_number', 'website']);
                $location->update($location_data);

                return $this->buildRes->RESPONSE_REQ('success', null, 'Location update succesfully');
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  BusinessLocation $location
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(BusinessLocation $location, Request $request)
    {
        // if (!auth()->user()->can('business-location.delete') || !$request->ajax()) {
        //     abort(403, 'Unauthorized action.');
        // }

        try {
            $location->delete();

            return $this->buildRes->RESPONSE_REQ('success', null, 'Group delete succesfully');
        } catch (\Exception $e) {
            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
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
            'country' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'zip_code' => 'required|string|max:255',
            'full_address' => 'required|string|max:255',
            'status' => 'required|string',
        ];
    }
}
