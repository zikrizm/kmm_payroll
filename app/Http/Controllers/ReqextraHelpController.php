<?php

namespace App\Http\Controllers;

use App\Utils\ResponseUtil;
use App\Models\ReqextraHelp;
use Illuminate\Http\Request;
use App\Services\Api\ApiServices;
use App\Models\OperationalSchedule;
use Illuminate\Support\Facades\Log;
use App\Models\ReqextraHelpHasEmployee;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ReqextraHelpController extends Controller
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
        if (!auth()->user()->can('reqextra-help.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            if (request()->ajax()) {
                $business_id = Session::get('business_id');
                $reqtask_help = ReqextraHelp::where('business_id', $business_id)->with(['operational_group', 'call_employee_helps']);
                if ($request->has('q') && !empty($request->input('q'))) {
                    $search = $request->q;
                    $reqtask_help = $reqtask_help->where('dept_name', 'LIKE', "%" . $search . "%")->orWhere('dept_id', 'LIKE', "%" . $search . "%")->orWhere('dept_code', 'LIKE', "%" . $search . "%");
                }
                if ($request->has('date') && !empty($request->input('date'))) {
                    $reqtask_help = $reqtask_help->whereBetween('start_date', [$request['date']['start_date'], $request['date']['end_date']])
                        ->orWhereBetween('end_date', [$request['date']['start_date'], $request['date']['end_date']]);
                }

                $order = null;
                if ($request->has('sort')) {
                    $sort = $request->sort;
                    $order = $sort['order'];
                    $reqtask_help->orderBy($sort['name'], $sort['order']);
                }

                $reqtask_help = $reqtask_help->paginate(10);
                $render =  view('Task.reqextra_help.table', compact('reqtask_help', 'order'))->render();
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
        if (!auth()->user()->can('reqextra-help.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = Session::get('business_id');
            $operational_schedules = OperationalSchedule::where('business_id', $business_id)->with(['operational_has_depertments' => function ($query) {
                $query->where('status', 'active');
            }])->get();

            $render = view('Task.reqextra_help.create', compact('operational_schedules'))->render();
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
        if (!auth()->user()->can('reqextra-help.create')  || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validator = Validator::make($request->all(), $this->rules());

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $request_data = $request->only(['operational_schedule_has_department_id', 'date', 'department', 'emps']);
                $start_date = trim(explode(' - ', $request_data['date'])[0]);
                $end_date = trim(explode(' - ', $request_data['date'])[1]);
                $business_id = Session::get('business_id');

                $reqextra_help = new ReqextraHelp([
                    'business_id' => $business_id,
                    'operational_schedule_has_department_id' => $request_data['operational_schedule_has_department_id'],
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'created_user' => auth()->user()->id,
                    'updated_user' => auth()->user()->id,
                ]);
                $reqextra_help->save();

                foreach ($request_data['emps'] as $item) {
                    $employee = $this->apiService->read_employee($item);
                    $reqextra_help_has_employee = new ReqextraHelpHasEmployee([
                        'reqextra_help_id' => $reqextra_help->id,
                        'emp_id' => $employee['id'],
                        'emp_code' => $employee['emp_code'],
                        'emp_first_name' => $employee['first_name'],
                        'emp_last_name' => $employee['last_name'],
                    ]);
                    $reqextra_help_has_employee->save();
                }

                return $this->buildRes->RESPONSE_REQ('success', null,  ['success' => ['Add call req extra help succesfully']]);
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
        if (!auth()->user()->can('reqextra-help.view')) {
            abort(403, 'Unauthorized action.');
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  ReqextraHelp $reqextra_help
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(ReqextraHelp $reqextra_help, Request $request)
    {
        if (!auth()->user()->can('reqextra-help.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = Session::get('business_id');
            // $call_employee = $reqextra_help->with(['operational.operational_groups' => function ($query) {
            //     $query->where('status', 'active');
            // }, 'call_employee_helps'])->first();
            // $operationals = Operational::where('business_id', $business_id)->with(['operational_groups' => function ($query) {
            //     $query->where('status', 'active');
            // }])->get();

            // $render = view('Task.reqextra_help.edit', compact('call_employee', 'operationals'))->render();
            // return $this->buildRes->RESPONSE_REQ('success', $render, null);
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  ReqextraHelp $reqextra_help
     * @return \Illuminate\Http\Response
     */
    public function update(ReqextraHelp $reqextra_help, Request $request)
    {
        if (!auth()->user()->can('reqextra-help.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validator = Validator::make($request->all(), $this->rules());

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $request_data = $request->only(['operational_schedule_has_department_id', 'date', 'department', 'emps']);
                $start_date = trim(explode(' - ', $request_data['date'])[0]);
                $end_date = trim(explode(' - ', $request_data['date'])[1]);

                $reqextra_help_data = [
                    'operational_schedule_has_department_id' => $request_data['operational_schedule_has_department_id'],
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'updated_user' => auth()->user()->id,
                ];
                $reqextra_help->update($reqextra_help_data);
                ReqextraHelpHasEmployee::where('reqextra_help_id', $reqextra_help->id)->each(function ($item) {
                    $item->delete();
                });
                foreach ($request_data['emps'] as $item) {
                    $employee = $this->apiService->read_employee($item);
                    $call_employee_help = new ReqextraHelpHasEmployee([
                        'reqextra_help_id' => $reqextra_help->id,
                        'emp_id' => $employee['id'],
                        'emp_code' => $employee['emp_code'],
                        'emp_first_name' => $employee['first_name'],
                        'emp_last_name' => $employee['last_name'],
                    ]);
                    $call_employee_help->save();
                }

                return $this->buildRes->RESPONSE_REQ('success', null, ['success' => ['Update reqextra help succesfully']]);
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  ReqextraHelp $additional_employee
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(ReqextraHelp $additional_employee, Request $request)
    {
        if (!auth()->user()->can('reqextra-help.delete') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $additional_employee->delete();

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
            'operational_schedule_has_department_id' => 'required',
            'date' => 'required',
            'emps' => 'required',
        ];
    }
}
