<?php

namespace App\Http\Controllers;

use App\Utils\ResponseUtil;
use Illuminate\Http\Request;
use App\Services\Api\ApiServices;
use Illuminate\Support\Facades\Log;
use App\Exceptions\ResponseExeception;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
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
        if (!auth()->user()->can('employee.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            if (request()->ajax()) {
                $order = null;
                $filter = [];

                if ($request->has('q')) {
                    // $filter['emp_code_icontains'] = $request->q;
                    $filter['employee_icontains'] = $request->q;
                    // $filter['last_name_icontains'] = $request->q;
                }

                if ($request->has('sort')) {
                    $filter['ordering'] = $request->sort['name'];
                    $order = $request->sort['order'];
                }

                $employees = $this->apiService->get_employees($filter);
                $render =  view('Employee.employee.table', compact('employees', 'order'))->render();
                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            }

            return view('Employee.employee.index');
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
        if (!auth()->user()->can('employee.create') || !request()->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $csrfmiddlewaretoken = $this->apiService->get_token_upload_employee_photo();
            $departments = $this->apiService->get_departments([]);
            $areas = $this->apiService->get_areas([]);
            $positions = $this->apiService->get_positions([]);
            $render = view('Employee.employee.create', compact('departments', 'areas', 'positions', 'csrfmiddlewaretoken'))->render();

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
        if (!auth()->user()->can('employee.create')  || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validator = Validator::make($request->all(), $this->rules());
            Log::info($request);

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $emp_data = $request->only([
                    'emp_code', 'first_name', 'last_name', 'nickname', 'hired_date', 'gender',
                    'contact_tel', 'office_tel', 'mobile', 'national', 'city', 'address', 'postcode', 'religion', 'email', 'birthday',
                    'verify_mode', 'emp_type', 'app_status', 'app_role',
                    'department', 'position', 'area',
                    'csrfmiddlewaretoken', 'user_capture',
                ]);


                if (!empty($request->input('area')) && count($request->input('area'))) {
                    $areas_data = $request->input('area');
                    if (in_array('all', $areas_data)) {
                        $emp_data['area'] = [];
                        $areas = $this->apiService->get_areas([]);
                        foreach ($areas['data'] as $area) {
                            $emp_data['area'][] = $area['id'];
                        }
                    }
                }

                $res_employee = $this->apiService->create_employee($emp_data);
                $emp_data_photo = [
                    'user_capture' => $emp_data['user_capture'],
                    'csrfmiddlewaretoken' => $emp_data['csrfmiddlewaretoken'],
                    'employee_code' => '50',
                ];
                $res_photo = $this->apiService->post_employee_photo($emp_data_photo);
                Log::info($res_photo);
                // return response()->json($res);
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
        if (!auth()->user()->can('employee.view')) {
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
        if (!auth()->user()->can('employee.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $emp = $this->apiService->get_employees(['emp_code' => $employee]);
            $emp = $emp['data'][0];
            $departments = $this->apiService->get_departments([]);
            $areas = $this->apiService->get_areas([]);
            $positions = $this->apiService->get_positions([]);
            $render = view('Employee.employee.edit', compact('emp', 'departments', 'areas', 'positions'))->render();

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
     * @param  $employee
     * @return \Illuminate\Http\Response
     */
    public function update($employee, Request $request)
    {
        if (!auth()->user()->can('employee.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validator = Validator::make($request->all(), $this->rules());

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $emp_data = $request->only([
                    'emp_code', 'first_name', 'last_name', 'nickname', 'photo', 'hired_date', 'gender',
                    'contact_tel', 'office_tel', 'mobile', 'national', 'city', 'address', 'postcode', 'religion', 'email', 'birthday',
                    'verify_mode', 'emp_type', 'app_status', 'app_role',
                    'department', 'position', 'area',
                ]);
                $emp_data['id'] = $employee;
                $res = $this->apiService->update_employee($emp_data);
                return response()->json($res);
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
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
        if (!auth()->user()->can('employee.delete') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $res = $this->apiService->delete_employee($employee);
            return response()->json($res);
        } catch (\Exception $e) {
            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    public function searchEmployeeForDropdown(Request $request)
    {
        if (!$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->has('q')) {
            $employees = $this->apiService->get_employees(['employee_icontains' => $request->q]);
            return response()->json($employees);
        } else {
            return [];
        }
    }

    /**
     * Rules validation employee.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'emp_code' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'area' => 'required',
        ];
    }
}
