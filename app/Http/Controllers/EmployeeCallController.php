<?php

namespace App\Http\Controllers;

use App\Models\Operational;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;
use App\Models\OperationalGroup;
use App\Services\Api\ApiServices;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class EmployeeCallController extends Controller
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
        // if (!auth()->user()->can('employee-call.view')) {
        //     abort(403, 'Unauthorized action.');
        // }

        try {
            if (request()->ajax()) {
                $business_id = Session::get('business_id');
                $operationals = Operational::where('business_id', $business_id);

                if ($request->has('q') && !empty($request->input('q'))) {
                    $search = $request->q;
                    $operationals = $operationals->where('dept_name', 'LIKE', "%" . $search . "%")->orWhere('dept_id', 'LIKE', "%" . $search . "%")->orWhere('dept_code', 'LIKE', "%" . $search . "%");
                }

                if ($request->has('date')) {
                    $operationals = $operationals->whereBetween('start_date', [$request['date']['start_date'], $request['date']['end_date']])
                        ->orWhereBetween('end_date', [$request['date']['start_date'], $request['date']['end_date']]);
                }

                $order = null;
                if ($request->has('sort')) {
                    $sort = $request->sort;
                    $order = $sort['order'];
                    $operationals->orderBy($sort['name'], $sort['order']);
                }
                $operationals = $operationals->paginate(10);
                $render =  view('Task_management.employee_call.table', compact('operationals', 'order'))->render();

                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            }

            return  view('Task_management.employee_call.index');
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
        // if (!auth()->user()->can('employee-call.create') || !request()->ajax()) {
        //     abort(403, 'Unauthorized action.');
        // }

        try {
            $departments = collect($this->apiService->get_departments([]));
            $departments['data'] = collect($departments['data'])->filter(function ($e) {
                return empty($e['parent_dept']);
            });
            $render = view('Task_management.employee_call.create', compact('departments'))->render();

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
        // if (!auth()->user()->can('employee-call.create')  || !$request->ajax()) {
        //     abort(403, 'Unauthorized action.');
        // }

        try {
            $validator = Validator::make($request->all(), $this->rules());

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $request_data = $request->only(['date', 'department', 'group']);
                $department = $this->apiService->read_department($request_data['department']);
                $start_date = trim(explode(' - ', $request_data['date'])[0]);
                $end_date = trim(explode(' - ', $request_data['date'])[1]);
                $business_id = Session::get('business_id');

                $operational = new Operational([
                    'business_id' => $business_id,
                    'dept_id' => $department['id'],
                    'dept_code' => $department['dept_code'],
                    'dept_name' => $department['dept_name'],
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'created_user' => auth()->user()->id,
                    'updated_user' => auth()->user()->id,
                ]);
                $operational->save();

                foreach ($request_data['group'] as $item) {
                    $start_date = trim(explode(' - ', $item['date'])[0]);
                    $end_date = trim(explode(' - ', $item['date'])[1]);
                    $operational_group = new OperationalGroup([
                        'operational_id' => $operational->id,
                        'start_date' => $start_date,
                        'end_date' => $end_date,
                        'status' => $item['status'],
                        'note' => $item['note'],
                    ]);
                    $operational_group->save();
                }

                return $this->buildRes->RESPONSE_REQ('success', null,  ['success' => ['Add operational succesfully']]);
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
     * @param  Operational $operational
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(Operational $operational, Request $request)
    {
        if (!auth()->user()->can('operational.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $departments = collect($this->apiService->get_departments([]));
            $operational = $operational->with('operational_group')->first();
            $departments_group = [];
            foreach ($departments['data'] as $e) {
                if (!empty($e['parent_dept']) && $e['parent_dept']['id'] == $operational->dept_id)  $departments_group[] = $e;
            }
            $departments['data'] = collect($departments['data'])->filter(function ($e) {
                return empty($e['parent_dept']);
            });

            $render = view('Task_management.operational.edit', compact('operational', 'departments', 'departments_group'))->render();
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
     * @param  Operational $operational
     * @return \Illuminate\Http\Response
     */
    public function update(Operational $operational, Request $request)
    {
        if (!auth()->user()->can('operational.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validator = Validator::make($request->all(), $this->rules());

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $request_data = $request->only(['date', 'department', 'group']);
                $department = $this->apiService->read_department($request_data['department']);
                $start_date = trim(explode(' - ', $request_data['date'])[0]);
                $end_date = trim(explode(' - ', $request_data['date'])[1]);

                $operational_data = [
                    'dept_id' => $department['dept_id'],
                    'dept_code' => $department['dept_code'],
                    'dept_name' => $department['dept_name'],
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'updated_user' => auth()->user()->id,
                ];
                $operational->update($operational_data);
                OperationalGroup::where('operational_id', $operational->id)->each(function ($item) {
                    $item->delete();
                });

                foreach ($request_data['group'] as $item) {
                    $start_date = trim(explode(' - ', $item['date'])[0]);
                    $end_date = trim(explode(' - ', $item['date'])[1]);
                    $operational_group = new OperationalGroup([
                        'operational_id' => $operational->id,
                        'start_date' => $start_date,
                        'end_date' => $end_date,
                        'status' => $item['status'],
                        'note' => $item['note'],
                    ]);
                    $operational_group->save();
                }
                return $this->buildRes->RESPONSE_REQ('success', null, ['success' => ['Update operational succesfully']]);
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Operational $operational
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Operational $operational, Request $request)
    {
        if (!auth()->user()->can('operational.delete') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $operational->delete();

            return $this->buildRes->RESPONSE_REQ('success', null, ['success' => ['Delete operational succesfully']]);
        } catch (\Exception $e) {
            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    public function card_sub_dept(Request $request)
    {
        if (!auth()->user()->can('operational.create') || !request()->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $departments = collect($this->apiService->get_departments([])['data']);
            $temp = $departments;
            $departments = [];
            foreach ($temp as $e) {
                if (!empty($e['parent_dept']) && $e['parent_dept']['id'] == $request['dept_id'])  $departments[] = $e;
            }

            $render = view('Task_management.operational.card.card_sub_dept', compact('departments'))->render();
            return $this->buildRes->RESPONSE_REQ('success', $render, null);
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

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
            'date' => 'required',
            'department' => 'required',
            'group' => 'required',
            'group.*.date' => 'required',
            'group.*.status' => 'required',
            'group.*.note' => 'required',
        ];
    }
}
