<?php

namespace App\Http\Controllers;

use App\Utils\ResponseUtil;
use Illuminate\Http\Request;
use App\Services\Api\ApiServices;
use Illuminate\Support\Facades\Log;
use App\Exceptions\ResponseExeception;
use Illuminate\Support\Facades\Validator;

class ResignController extends Controller
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
        // if (!auth()->user()->can('resign.view')) {
        //     abort(403, 'Unauthorized action.');
        // }

        try {
            if (request()->ajax()) {
                $order = null;
                $filter = [];

                if ($request->has('q')) {
                    $filter['employee'] = $request->q;
                }

                if ($request->has('resign_date')) {
                    $filter['resign_date'] = $request['resign_date'];
                }

                if ($request->has('sort')) {
                    $filter['ordering'] = $request->sort['name'];
                    $order = $request->sort['order'];
                }

                $resigns = $this->apiService->get_resign($filter);
                $render =  view('Employee.resign.table', compact('resigns', 'order'))->render();
                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            }

            return view('Employee.resign.index');
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
        // if (!auth()->user()->can('resign.create') || !request()->ajax()) {
        //     abort(403, 'Unauthorized action.');
        // }

        try {
            // $departments = $this->apiService->get_departments([]);
            // $areas = $this->apiService->get_areas([]);
            // $positions = $this->apiService->get_positions([]);
            $render = view('Employee.resign.create')->render();

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
        // if (!auth()->user()->can('resign.create')  || !$request->ajax()) {
        //     abort(403, 'Unauthorized action.');
        // }

        try {
            $validator = Validator::make($request->all(), $this->rules());
            Log::info($request);

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $emp_data = $request->only([
                    'emp_code', 'first_name', 'last_name', 'nickname', 'photo', 'hired_date', 'gender',
                    'contact_tel', 'office_tel', 'mobile', 'national', 'city', 'address', 'postcode', 'religion', 'email', 'birthday',
                    'verify_mode', 'emp_type', 'app_status', 'app_role',
                    'department', 'position', 'area',
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

                $res = $this->apiService->create_employee($emp_data);
                return response()->json($res);
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  $resign
     * @return \Illuminate\Http\Response
     */
    public function show($resign)
    {
        if (!auth()->user()->can('resign.view')) {
            abort(403, 'Unauthorized action.');
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  $resign
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit($resign, Request $request)
    {
        // if (!auth()->user()->can('resign.update') || !$request->ajax()) {
        //     abort(403, 'Unauthorized action.');
        // }

        try {
            $emp = $this->apiService->get_employees(['emp_code' => $resign]);
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
     * @param  $resign
     * @return \Illuminate\Http\Response
     */
    public function update($resign, Request $request)
    {
        // if (!auth()->user()->can('resign.update') || !$request->ajax()) {
        //     abort(403, 'Unauthorized action.');
        // }

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
                $emp_data['id'] = $resign;
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
     * @param  $resign
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy($resign, Request $request)
    {
        // if (!auth()->user()->can('resign.delete') || !$request->ajax()) {
        //     abort(403, 'Unauthorized action.');
        // }

        try {
            $res = $this->apiService->delete_employee($resign);
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
            // 'emp_code' => 'required|string|max:255',
            // 'first_name' => 'required|string|max:255',
            // 'department' => 'required|string|max:255',
            // 'area' => 'required',
        ];
    }
}
