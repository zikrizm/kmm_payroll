<?php

namespace App\Http\Controllers;

use App\Models\CallEmployee;
use App\Models\CallEmployeeHelp;
use App\Models\Operational;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;
use App\Services\Api\ApiServices;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CallEmployeeController extends Controller
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
        // if (!auth()->user()->can('additional-employee.view')) {
        //     abort(403, 'Unauthorized action.');
        // }

        try {
            if (request()->ajax()) {
                $business_id = Session::get('business_id');
                $call_employees = CallEmployee::where('business_id', $business_id)->with(['operational', 'call_employee_helps']);
                if ($request->has('q') && !empty($request->input('q'))) {
                    $search = $request->q;
                    $call_employees = $call_employees->where('dept_name', 'LIKE', "%" . $search . "%")->orWhere('dept_id', 'LIKE', "%" . $search . "%")->orWhere('dept_code', 'LIKE', "%" . $search . "%");
                }
                if ($request->has('date') && !empty($request->input('date'))) {
                    $call_employees = $call_employees->whereBetween('start_date', [$request['date']['start_date'], $request['date']['end_date']])
                        ->orWhereBetween('end_date', [$request['date']['start_date'], $request['date']['end_date']]);
                }

                $order = null;
                if ($request->has('sort')) {
                    $sort = $request->sort;
                    $order = $sort['order'];
                    $call_employees->orderBy($sort['name'], $sort['order']);
                }

                $call_employees = $call_employees->paginate(10);
                $render =  view('Task.additional_employee.table', compact('call_employees', 'order'))->render();
                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            }

            return  view('Task.additional_employee.index');
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
        if (!auth()->user()->can('additional-employee.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = Session::get('business_id');
            $operationals = Operational::where('business_id', $business_id)->with(['operational_groups' => function ($query) {
                $query->where('status', 'active');
            }])->select('id')->get();

            Log::info($operationals);


            // $render = view('Task.call_employee.create', compact('operationals'))->render();
            // return $this->buildRes->RESPONSE_REQ('success', $render, null);
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
        if (!auth()->user()->can('call-employee.create')  || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $validator = Validator::make($request->all(), $this->rules());

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $request_data = $request->only(['operational', 'date', 'department', 'emps']);
                $department = $this->apiService->read_department($request_data['department']);
                $start_date = trim(explode(' - ', $request_data['date'])[0]);
                $end_date = trim(explode(' - ', $request_data['date'])[1]);
                $business_id = Session::get('business_id');

                $call_employee = new CallEmployee([
                    'business_id' => $business_id,
                    'operational_id' => $request_data['operational'],
                    'dept_id' => $department['id'],
                    'dept_code' => $department['dept_code'],
                    'dept_name' => $department['dept_name'],
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'created_user' => auth()->user()->id,
                    'updated_user' => auth()->user()->id,
                ]);
                $call_employee->save();

                foreach ($request_data['emps'] as $item) {
                    $employee = $this->apiService->read_employee($item);
                    $call_employee_help = new CallEmployeeHelp([
                        'call_employee_id' => $call_employee->id,
                        'emp_id' => $employee['id'],
                        'emp_code' => $employee['emp_code'],
                        'emp_first_name' => $employee['first_name'],
                        'emp_last_name' => $employee['last_name'],
                    ]);
                    $call_employee_help->save();
                }

                return $this->buildRes->RESPONSE_REQ('success', null,  ['success' => ['Add call employee succesfully']]);
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
        if (!auth()->user()->can('kasbon.view')) {
            abort(403, 'Unauthorized action.');
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  CallEmployee $call_employee
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(CallEmployee $call_employee, Request $request)
    {
        if (!auth()->user()->can('call-employee.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = Session::get('business_id');
            $call_employee = $call_employee->with(['operational.operational_groups' => function ($query) {
                $query->where('status', 'active');
            }, 'call_employee_helps'])->first();
            $operationals = Operational::where('business_id', $business_id)->with(['operational_groups' => function ($query) {
                $query->where('status', 'active');
            }])->get();

            $render = view('Task.call_employee.edit', compact('call_employee', 'operationals'))->render();
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
     * @param  CallEmployee $call_employee
     * @return \Illuminate\Http\Response
     */
    public function update(CallEmployee $call_employee, Request $request)
    {
        if (!auth()->user()->can('call-employee.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validator = Validator::make($request->all(), $this->rules());

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $request_data = $request->only(['operational', 'date', 'department', 'emps']);
                $department = $this->apiService->read_department($request_data['department']);
                $start_date = trim(explode(' - ', $request_data['date'])[0]);
                $end_date = trim(explode(' - ', $request_data['date'])[1]);

                $call_employee_data = [
                    'operational_id' => $request_data['operational'],
                    'dept_id' => $department['id'],
                    'dept_code' => $department['dept_code'],
                    'dept_name' => $department['dept_name'],
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'updated_user' => auth()->user()->id,
                ];
                $call_employee->update($call_employee_data);
                CallEmployeeHelp::where('call_employee_id', $call_employee->id)->each(function ($item) {
                    $item->delete();
                });
                foreach ($request_data['emps'] as $item) {
                    $employee = $this->apiService->read_employee($item);
                    $call_employee_help = new CallEmployeeHelp([
                        'call_employee_id' => $call_employee->id,
                        'emp_id' => $employee['id'],
                        'emp_code' => $employee['emp_code'],
                        'emp_first_name' => $employee['first_name'],
                        'emp_last_name' => $employee['last_name'],
                    ]);
                    $call_employee_help->save();
                }

                return $this->buildRes->RESPONSE_REQ('success', null, ['success' => ['Update call employee succesfully']]);
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  CallEmployee $call_employee
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(CallEmployee $call_employee, Request $request)
    {
        if (!auth()->user()->can('call-employee.delete') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $call_employee->delete();

            return $this->buildRes->RESPONSE_REQ('success', null, ['success' => ['Delete call employee succesfully']]);
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
            'operational' => 'required',
            'date' => 'required',
            'department' => 'required',
            'emps' => 'required',
        ];
    }
}
