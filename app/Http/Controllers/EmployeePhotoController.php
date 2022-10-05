<?php

namespace App\Http\Controllers;

use App\Utils\ResponseUtil;
use Illuminate\Http\Request;
use App\Services\Api\ApiServices;
use Illuminate\Support\Facades\Log;
use App\Exceptions\ResponseExeception;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Validator;

class EmployeePhotoController extends Controller
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
        if (!auth()->user()->can('employee-photo.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {

            return view('Employee.employee_photo.index');
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
        // if (!auth()->user()->can('employee-photo.create') || !request()->ajax()) {
        //     abort(403, 'Unauthorized action.');
        // }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('employee-photo.create')  || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validator = Validator::make($request->all(), $this->rules());

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $emp_data_photo = $request->only(['user_capture', 'employee_code', 'remark']);
                $emp_data_photo['csrfmiddlewaretoken'] = $this->apiService->get_csrfmiddlewaretoken();
                $res = $this->apiService->update_employee_photo($emp_data_photo);
                if ($res['ret']) {
                    return $this->buildRes->RESPONSE_REQ('error', null, ['error' => $res['message']]);
                } else {
                    return $this->buildRes->RESPONSE_REQ('success', null, ['success' => 'Upload photo employee succesfully']);
                }
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  $employee
     * @return \Illuminate\Http\Response
     */
    public function show($employee)
    {
        if (!auth()->user()->can('employee-photo.view')) {
            abort(403, 'Unauthorized action.');
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  $employee
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit($employee, Request $request)
    {
        if (!auth()->user()->can('employee-photo.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $employee
     * @return \Illuminate\Http\Response
     */
    public function update($employee, Request $request)
    {
        if (!auth()->user()->can('employee-photo.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $employee
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy($employee, Request $request)
    {
        if (!auth()->user()->can('employee-photo.delete') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Rules validation employee photo.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_capture' => 'required|image|file|max:2000',
            'employee_code' => 'required|string|max:255',
        ];
    }
}
