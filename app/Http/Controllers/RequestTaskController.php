<?php

namespace App\Http\Controllers;

use App\Utils\ResponseUtil;
use Illuminate\Http\Request;
use App\Services\Api\ApiServices;
use App\Models\Operational;
use App\Models\Position;
use Illuminate\Support\Facades\Log;
use App\Models\RequestTaskHasEmp;
use App\Models\RequestTask;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class RequestTaskController extends Controller
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
        if (!auth()->user()->can('request-task.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            if (request()->ajax()) {
                $business_id = Session::get('business_id');
                $request_tasks = RequestTask::where('business_id', $business_id);
                $position_bios = $this->apiService->get_positions(["page_size" => 999])['data'];
                if ($request->has('q') && !empty($request->input('q'))) {
                    $search = $request->q;
                    $request_tasks = $request_tasks->whereHas('request_task_has_emps', function ($e) use ($search) {
                        $e->where('emp_code', 'LIKE', "%" . $search . "%")
                            ->orWhere('emp_first_name', 'LIKE', "%" . $search . "%")
                            ->orWhere('emp_last_name', 'LIKE', "%" . $search . "%");
                    })->orWhere('position_id', 'LIKE', "%" . $search . "%");
                }
                if ($request->has('date') && !empty($request->input('date'))) {
                    $request_tasks = $request_tasks->whereBetween('start_date', [$request['date']['start_date'], $request['date']['end_date']])
                        ->orWhereBetween('end_date', [$request['date']['start_date'], $request['date']['end_date']]);
                }

                $order = null;
                if ($request->has('sort') && !empty($request->input('sort'))) {
                    $sort = $request->sort;
                    $order = $sort['order'];
                    $request_tasks->orderBy($sort['name'], $sort['order']);
                }

                $request_tasks = $request_tasks->with(['request_task_has_emps'])->paginate(10);
                $render =  view('Task.request_task.table', compact('request_tasks', 'position_bios', 'order'))->render();
                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            }

            return  view('Task.request_task.index');
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
        if (!auth()->user()->can('request-task.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $position_bios = $this->apiService->get_positions(["page_size" => 999])['data'];
            $position = Position::where('permanently', 0)->get();
            foreach ($position as $key => $value) {
                $is_same = array_search($value->position_id, array_column($position_bios, 'id'));
                if ($is_same != '') {
                    $value['position_name'] = $position_bios[$is_same]['position_name'];
                    $value['position_code'] = $position_bios[$is_same]['position_code'];
                }
            }

            $render = view('Task.request_task.create', compact('position'))->render();
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
        if (!auth()->user()->can('request-task.create')  || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validator = Validator::make($request->all(), $this->rules());

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $request_data = $request->only(['date', 'emps', 'position']);
                $start_date = trim(explode(' - ', $request_data['date'])[0]);
                $end_date = trim(explode(' - ', $request_data['date'])[1]);
                $business_id = Session::get('business_id');

                $request_task = new RequestTask([
                    'business_id' => $business_id,
                    'position_id' => $request_data['position'],
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'created_user' => auth()->user()->id,
                    'updated_user' => auth()->user()->id,
                ]);
                $request_task->save();

                foreach ($request_data['emps'] as $item) {
                    $employee = $this->apiService->read_employee($item);
                    $request_task_has_emp = new RequestTaskHasEmp([
                        'request_task_id' => $request_task->id,
                        'emp_id' => $employee['id'],
                        'emp_code' => $employee['emp_code'],
                        'emp_first_name' => $employee['first_name'],
                        'emp_last_name' => $employee['last_name'],
                    ]);
                    $request_task_has_emp->save();
                }

                return $this->buildRes->RESPONSE_REQ('success', null,  ['success' => ['Add request task succesfully']]);
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
        if (!auth()->user()->can('request-task.view')) {
            abort(403, 'Unauthorized action.');
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  RequestTask $request_task
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(RequestTask $request_task, Request $request)
    {
        if (!auth()->user()->can('request-task.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = Session::get('business_id');
            $request_task = $request_task->with(['request_task_has_emps'])->first();
            $position_bios = $this->apiService->get_positions(["page_size" => 999])['data'];
            $position = Position::where('permanently', 0)->get();
            foreach ($position as $key => $value) {
                $is_same = array_search($value->position_id, array_column($position_bios, 'id'));
                if ($is_same != '') {
                    $value['position_name'] = $position_bios[$is_same]['position_name'];
                    $value['position_code'] = $position_bios[$is_same]['position_code'];
                }
            }

            $render = view('Task.request_task.edit', compact('request_task', 'position'))->render();
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
     * @param  RequestTask $request_task
     * @return \Illuminate\Http\Response
     */
    public function update(RequestTask $request_task, Request $request)
    {
        if (!auth()->user()->can('request-task.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validator = Validator::make($request->all(), $this->rules());

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $request_data = $request->only(['date', 'emps', 'position']);
                $start_date = trim(explode(' - ', $request_data['date'])[0]);
                $end_date = trim(explode(' - ', $request_data['date'])[1]);

                $request_task_data = [
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'position_id' => $request_data['position'],
                    'updated_user' => auth()->user()->id,
                ];
                $request_task->update($request_task_data);

                RequestTaskHasEmp::where('request_task_id', $request_task->id)->each(function ($item) {
                    $item->delete();
                });
                foreach ($request_data['emps'] as $item) {
                    $employee = $this->apiService->read_employee($item);
                    $request_task_has_emp = new RequestTaskHasEmp([
                        'request_task_id' => $request_task->id,
                        'emp_id' => $employee['id'],
                        'emp_code' => $employee['emp_code'],
                        'emp_first_name' => $employee['first_name'],
                        'emp_last_name' => $employee['last_name'],
                    ]);
                    $request_task_has_emp->save();
                }

                return $this->buildRes->RESPONSE_REQ('success', null, ['success' => ['Update request task succesfully']]);
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  RequestTask $request_task
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(RequestTask $request_task, Request $request)
    {
        if (!auth()->user()->can('request-help.delete') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $request_task->delete();

            return $this->buildRes->RESPONSE_REQ('success', null, ['success' => ['Delete request task succesfully']]);
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
            'date' => 'required',
            'emps' => 'required',
            'position' => 'required',
        ];
    }
}
