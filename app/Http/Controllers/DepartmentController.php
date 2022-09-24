<?php

namespace App\Http\Controllers;

use App\Utils\ResponseUtil;
use Illuminate\Http\Request;
use App\Services\Api\ApiServices;
use Illuminate\Support\Facades\Log;
use App\Exceptions\ResponseExeception;
use Illuminate\Support\Facades\Validator;

class DepartmentController extends Controller
{
    private $apiService;
    private $buildRes;

    public function __construct(ApiServices $apiService, ResponseUtil $buildRes)
    {
        $this->apiService = $apiService;
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
        // if (!auth()->user()->can('department.view')) {
        //     abort(403, 'Unauthorized action.');
        // }

        try {
            if (request()->ajax()) {
                $departments = $this->apiService->get_departments();
                $render =  view('Organization.department.table', compact('departments'))->render();
                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            }

            return view('Organization.department.index');
        } catch (ResponseExeception $e) {
            return $this->buildRes->RESPONSE_REQ('error', null, $e->getMessages());
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            return $this->buildRes->RESPONSE_REQ('error', null, ['something_wrong' => 'something wrong']);
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
        // if (!auth()->user()->can('department.create') || !request()->ajax()) {
        //     abort(403, 'Unauthorized action.');
        // }

        try {
            $departments = $this->apiService->get_departments();
            $render = view('Organization.department.create', compact('departments'))->render();

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
        // if (!auth()->user()->can('department.create')  || !$request->ajax()) {
        //     abort(403, 'Unauthorized action.');
        // }

        try {
            $validator = Validator::make($request->all(), $this->rules());

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $dept_data = $request->only(['dept_code', 'dept_name', 'parent_dept']);
                $res = $this->apiService->create_department($dept_data);
                Log::info($res);
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, 'something wrong');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  $department
     * @return \Illuminate\Http\Response
     */
    public function show($department)
    {
        // if (!auth()->user()->can('department.view')) {
        //     abort(403, 'Unauthorized action.');
        // }
    }


    /**
     * Display the specified resource.
     *
     * @param  $department
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit($department, Request $request)
    {
        // if (!auth()->user()->can('department.update') || !$request->ajax()) {
        //     abort(403, 'Unauthorized action.');
        // }

        try {
        } catch (\Exception $error) {
            return $this->buildRes->RESPONSE_REQ('error', null, 'something wrong');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $department
     * @return \Illuminate\Http\Response
     */
    public function update($department, Request $request)
    {
        // if (!auth()->user()->can('department.update') || !$request->ajax()) {
        //     abort(403, 'Unauthorized action.');
        // }

        try {
            $validator = Validator::make($request->all(), $this->rules());

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, 'something wrong');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $department
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy($department, Request $request)
    {
        // if (!auth()->user()->can('department.delete') || !$request->ajax()) {
        //     abort(403, 'Unauthorized action.');
        // }

        try {
        } catch (\Exception $e) {
            return $this->buildRes->RESPONSE_REQ('error', null, 'something wrong');
        }
    }

    /**
     * Rules validation department.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'dept_code' => 'required|string|max:255',
            'dept_name' => 'required|string|max:255',
        ];
    }
}
