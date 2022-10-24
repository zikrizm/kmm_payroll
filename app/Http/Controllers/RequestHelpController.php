<?php

namespace App\Http\Controllers;

use App\Utils\ResponseUtil;
use App\Models\RequestHelp;
use Illuminate\Http\Request;
use App\Services\Api\ApiServices;
use App\Models\Operational;
use Illuminate\Support\Facades\Log;
use App\Models\RequestHelpHasEmp;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class RequestHelpController extends Controller
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
        if (!auth()->user()->can('request-help.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            if (request()->ajax()) {
                $business_id = Session::get('business_id');
                $request_helps = RequestHelp::where('business_id', $business_id);
                if ($request->has('q') && !empty($request->input('q'))) {
                    $search = $request->q;
                    $request_helps = $request_helps->where('dept_name', 'LIKE', "%" . $search . "%")
                        ->orWhere('dept_id', 'LIKE', "%" . $search . "%")
                        ->orWhere('dept_code', 'LIKE', "%" . $search . "%");
                }
                if ($request->has('date') && !empty($request->input('date'))) {
                    $request_helps = $request_helps->whereBetween('start_date', [$request['date']['start_date'], $request['date']['end_date']])
                        ->orWhereBetween('end_date', [$request['date']['start_date'], $request['date']['end_date']]);
                }

                $order = null;
                if ($request->has('sort') && !empty($request->input('sort'))) {
                    $sort = $request->sort;
                    $order = $sort['order'];
                    $request_helps->orderBy($sort['name'], $sort['order']);
                }

                $request_helps = $request_helps->with(['operational_has_dept', 'request_help_has_emps'])->paginate(10);
                $render =  view('Task.request_help.table', compact('request_helps', 'order'))->render();
                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            }

            return  view('Task.request_help.index');
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
        if (!auth()->user()->can('request-help.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = Session::get('business_id');
            $operationals = Operational::where('business_id', $business_id)->with(['operational_has_depts' => function ($query) {
                $query->where('status', 'active');
            }])->get();

            $render = view('Task.request_help.create', compact('operationals'))->render();
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
        if (!auth()->user()->can('request-help.create')  || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validator = Validator::make($request->all(), $this->rules());

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $request_data = $request->only(['operational_has_dept_id', 'date', 'department', 'emps']);
                $start_date = trim(explode(' - ', $request_data['date'])[0]);
                $end_date = trim(explode(' - ', $request_data['date'])[1]);
                $business_id = Session::get('business_id');

                $request_help = new RequestHelp([
                    'business_id' => $business_id,
                    'operational_has_dept_id' => $request_data['operational_has_dept_id'],
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'created_user' => auth()->user()->id,
                    'updated_user' => auth()->user()->id,
                ]);
                $request_help->save();

                foreach ($request_data['emps'] as $item) {
                    $employee = $this->apiService->read_employee($item);
                    $request_help_has_emp = new RequestHelpHasEmp([
                        'request_help_id' => $request_help->id,
                        'emp_id' => $employee['id'],
                        'emp_code' => $employee['emp_code'],
                        'emp_first_name' => $employee['first_name'],
                        'emp_last_name' => $employee['last_name'],
                    ]);
                    $request_help_has_emp->save();
                }

                return $this->buildRes->RESPONSE_REQ('success', null,  ['success' => ['Add request help succesfully']]);
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
        if (!auth()->user()->can('request-help.view')) {
            abort(403, 'Unauthorized action.');
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  RequestHelp $request_help
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(RequestHelp $request_help, Request $request)
    {
        if (!auth()->user()->can('request-help.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = Session::get('business_id');
            $request_help = $request_help->with(['request_help_has_emps'])->first();

            $operationals = Operational::where('business_id', $business_id)->with(['operational_has_depts' => function ($query) {
                $query->where('status', 'active');
            }])->get();

            $render = view('Task.request_help.edit', compact('request_help', 'operationals'))->render();
            return $this->buildRes->RESPONSE_REQ('success', $render, null);
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  RequestHelp $request_help
     * @return \Illuminate\Http\Response
     */
    public function update(RequestHelp $request_help, Request $request)
    {
        if (!auth()->user()->can('request-help.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validator = Validator::make($request->all(), $this->rules());

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $request_data = $request->only(['operational_has_dept_id', 'date', 'department', 'emps']);
                $start_date = trim(explode(' - ', $request_data['date'])[0]);
                $end_date = trim(explode(' - ', $request_data['date'])[1]);

                $request_help_data = [
                    'operational_has_dept_id' => $request_data['operational_has_dept_id'],
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'updated_user' => auth()->user()->id,
                ];
                $request_help->update($request_help_data);

                RequestHelpHasEmp::where('request_help_id', $request_help->id)->each(function ($item) {
                    $item->delete();
                });
                foreach ($request_data['emps'] as $item) {
                    $employee = $this->apiService->read_employee($item);
                    $request_help_has_emp = new RequestHelpHasEmp([
                        'request_help_id' => $request_help->id,
                        'emp_id' => $employee['id'],
                        'emp_code' => $employee['emp_code'],
                        'emp_first_name' => $employee['first_name'],
                        'emp_last_name' => $employee['last_name'],
                    ]);
                    $request_help_has_emp->save();
                }

                return $this->buildRes->RESPONSE_REQ('success', null, ['success' => ['Update request help succesfully']]);
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  RequestHelp $request_help
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(RequestHelp $request_help, Request $request)
    {
        if (!auth()->user()->can('request-help.delete') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $request_help->delete();

            return $this->buildRes->RESPONSE_REQ('success', null, ['success' => ['Delete request help succesfully']]);
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
            'operational_has_dept_id' => 'required',
            'date' => 'required',
            'emps' => 'required',
        ];
    }
}
