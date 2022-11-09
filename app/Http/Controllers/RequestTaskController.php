<?php

namespace App\Http\Controllers;

use App\Utils\Util;
use App\Models\Position;
use App\Models\RequestTask;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\RequestTaskHasEmp;
use App\Services\Api\ApiServices;
use App\Models\EmployeeHasPosition;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class RequestTaskController extends Controller
{
    private $apiService;
    private $buildRes;
    private $util;

    public function __construct(ApiServices $apiService, Util $util, ResponseUtil $buildRes)
    {
        $this->apiService = $apiService;
        $this->buildRes = $buildRes;
        $this->util = $util;
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
            $date = (!empty($request['date'])) ? Carbon::parse($request['date'])->format('Y-m-d') : null;
            $position_bios = $this->apiService->get_positions(["page_size" => 999])['data'];
            $position = Position::where('permanently', 0)->get();
            foreach ($position as $key => $value) {
                $is_same = array_search($value->position_id, array_column($position_bios, 'id'));
                if ($is_same != '') {
                    $value['position_name'] = $position_bios[$is_same]['position_name'];
                    $value['position_code'] = $position_bios[$is_same]['position_code'];
                }
            }

            $render = view('Task.request_task.create', compact('position', 'date'))->render();
            return $this->buildRes->RESPONSE_REQ('success', $render, null);
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    public function get_employee_position(Request $request)
    {
        if (!empty($request->position)) {
            $employee = [];
            $employees = $this->apiService->get_employees(["page_size" => 999])['data'];
            $employeeDBs = EmployeeHasPosition::where('position_id', $request->position)->with(['position' => function ($query) {
                $query->where('permanently', '!=', 0);
            }, 'employee'])->get();
            foreach ($employeeDBs as $value) {
                $key = array_search($value->employee->emp_code, array_column($employees, 'emp_code'));
                if ($key != '') $employee[] = $employees[$key];
            }

            Log::info($employee);

            $render = view('Task.request_task.partials.dropdown_position_employee', compact('employee'))->render();
            return $this->buildRes->RESPONSE_REQ('success', $render, null);
        } else {
            return $this->buildRes->RESPONSE_REQ('error', ['error' => ['Jabatan belom di pilih']], null);
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
                $rangedate = explode(' - ', $request['date']);
                $business_id = Session::get('business_id');
                $dates = [];
                if (count($rangedate) > 1) {
                    $start_date = Carbon::parse(trim($rangedate[0]));
                    $end_date = Carbon::parse(trim($rangedate[1]));
                    $dates = $this->util->generateDateRange($start_date, $end_date);
                    $request_task = RequestTask::where('position_id', $request_data['position'])->whereBetween('date', [$start_date, $end_date])
                        ->with('request_task_has_emps')->get();
                } else {
                    $date = Carbon::parse($request_data['date']);
                    $dates[] = $date->format('Y-m-d');
                    $request_task = RequestTask::where('position_id', $request_data['position'])
                        ->where('date', $date)->with('request_task_has_emps')->first();
                }

                if (count($rangedate) > 1) {
                    if (empty($request_task)) {
                        // foreach ($dates as $key => $date) {
                        //     $date = Carbon::parse($dates[$key]);
                        //     $request_task_create = new RequestTask([
                        //         'business_id' => $business_id,
                        //         'position_id' => $request_data['position'],
                        //         'date' => $date,
                        //         'created_user' => auth()->user()->id,
                        //         'updated_user' => auth()->user()->id,
                        //     ]);
                        //     $request_task_create->save();

                        //     foreach ($request_data['emps'] as $item) {
                        //         $employee = $this->apiService->read_employee($item);
                        //         $request_task_has_emp = new RequestTaskHasEmp([
                        //             'request_task_id' => $request_task_create->id,
                        //             'emp_id' => $employee['id'],
                        //             'emp_code' => $employee['emp_code'],
                        //             'emp_first_name' => $employee['first_name'],
                        //             'emp_last_name' => $employee['last_name'],
                        //         ]);
                        //         $request_task_has_emp->save();
                        //     }
                        // }
                    } else {
                        // foreach ($dates as $key => $date) {
                        //     $date = Carbon::parse($dates[$key]);
                        //     $task = $request_task->where('date', $date);
                        //     if (empty($task)) {
                        //         $request_task_create = new RequestTask([
                        //             'business_id' => $business_id,
                        //             'position_id' => $request_data['position'],
                        //             'date' => $date,
                        //             'created_user' => auth()->user()->id,
                        //             'updated_user' => auth()->user()->id,
                        //         ]);
                        //         $request_task_create->save();

                        //         foreach ($request_data['emps'] as $item) {
                        //             $employee = $this->apiService->read_employee($item);
                        //             $request_task_has_emp = new RequestTaskHasEmp([
                        //                 'request_task_id' => $request_task_create->id,
                        //                 'emp_id' => $employee['id'],
                        //                 'emp_code' => $employee['emp_code'],
                        //                 'emp_first_name' => $employee['first_name'],
                        //                 'emp_last_name' => $employee['last_name'],
                        //             ]);
                        //             $request_task_has_emp->save();
                        //         }
                        //     } else {
                        //         foreach ($task->request_task_has_emps as $key => $value) {
                        //             foreach ($request_data['emps'] as $item) {
                        //                 if ($value->emp_id != $item) {
                        //                     $employee = $this->apiService->read_employee($item);
                        //                     $request_task_has_emp = new RequestTaskHasEmp([
                        //                         'request_task_id' => $value->id,
                        //                         'emp_id' => $employee['id'],
                        //                         'emp_code' => $employee['emp_code'],
                        //                         'emp_first_name' => $employee['first_name'],
                        //                         'emp_last_name' => $employee['last_name'],
                        //                     ]);
                        //                     $request_task_has_emp->save();
                        //                 }
                        //             }
                        //         }
                        //     }
                        // }
                    }
                } else {
                    if (empty($request_task)) {
                        // $request_task = new RequestTask([
                        //     'business_id' => $business_id,
                        //     'position_id' => $request_data['position'],
                        //     'date' => $date,
                        //     'created_user' => auth()->user()->id,
                        //     'updated_user' => auth()->user()->id,
                        // ]);
                        // $request_task->save();

                        // foreach ($request_data['emps'] as $item) {
                        //     $employee = $this->apiService->read_employee($item);
                        //     $request_task_has_emp = new RequestTaskHasEmp([
                        //         'request_task_id' => $request_task->id,
                        //         'emp_id' => $employee['id'],
                        //         'emp_code' => $employee['emp_code'],
                        //         'emp_first_name' => $employee['first_name'],
                        //         'emp_last_name' => $employee['last_name'],
                        //     ]);
                        //     $request_task_has_emp->save();
                        // }
                    } else {
                        // Log::info($request_task);
                        foreach ($request_data['emps'] as $key => $t) {
                            $is_same = array_search($t, array_column($request_task->request_task_has_emps->toArray(), 'emp_id'));
                            if ($is_same == '') {
                                $employee = $this->apiService->read_employee($t);
                                $request_task_has_emp = new RequestTaskHasEmp([
                                    'request_task_id' => $request_task->id,
                                    'emp_id' => $employee['id'],
                                    'emp_code' => $employee['emp_code'],
                                    'emp_first_name' => $employee['first_name'],
                                    'emp_last_name' => $employee['last_name'],
                                ]);
                                $request_task_has_emp->save();
                            }
                        }
                        // Log::info($temp);
                        // Log::info("==================");
                        // foreach ($request_task->request_task_has_emps as $key => $value) {
                        //     // $is_same = array_search($value->emp_id, $request_data['emps']);
                        //     // if ($is_same == '') {
                        //     //     $employee = $this->apiService->read_employee($item);
                        //     //     $request_task_has_emp = new RequestTaskHasEmp([
                        //     //         'request_task_id' => $request_task->id,
                        //     //         'emp_id' => $employee['id'],
                        //     //         'emp_code' => $employee['emp_code'],
                        //     //         'emp_first_name' => $employee['first_name'],
                        //     //         'emp_last_name' => $employee['last_name'],
                        //     //     ]);
                        //     //     $request_task_has_emp->save();
                        //     // }
                        //     // Log::info("is_same $is_same");
                        //     $id_not_same = array_keys($request_data['emps'], 3);
                        //     // Log::info("emp_id $value->emp_id id_not_same $id_not_same");

                        //     foreach ($request_data['emps'] as $key => $item) {
                        //         if ($value->emp_id != $item) {
                        //             $id_not_same = $item;
                        //             // unset($request_data['emps'][$key]);
                        //             // break;
                        //             // $employee = $this->apiService->read_employee($item);
                        //             // $request_task_has_emp = new RequestTaskHasEmp([
                        //             //     'request_task_id' => $request_task->id,
                        //             //     'emp_id' => $employee['id'],
                        //             //     'emp_code' => $employee['emp_code'],
                        //             //     'emp_first_name' => $employee['first_name'],
                        //             //     'emp_last_name' => $employee['last_name'],
                        //             // ]);
                        //             // $request_task_has_emp->save();
                        //         }
                        //     }

                        //     // Log::info($id_not_same);
                        // }
                    }
                }
                return $this->buildRes->RESPONSE_REQ('success', null,  ['success' => ['Add request task succesfully']]);

                //     return $this->buildRes->RESPONSE_REQ('success', null,  ['success' => ['Add request task succesfully']]);
                // } else {
                //     if (count($rangedate) > 1) {
                //         return $this->buildRes->RESPONSE_REQ('error', null, ['error' => ["Salah satu atau beberapa penugasan dalam range {$start_date->format('d-m-Y')} - {$end_date->format('d-m-Y')} udah tersedia"]]);
                //     } else {
                //         return $this->buildRes->RESPONSE_REQ('error', null, ['error' => ["penugasan untuk karyawan yang ditambahkan, di tanggal {$date->format('d-m-Y')} sudah tersedia"]]);
                //     }
                // }
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
     * @param  $request_task
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit($request_task, Request $request)
    {
        if (!auth()->user()->can('request-task.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $request_task = RequestTask::where('id', $request_task)->with(['request_task_has_emps'])->first();
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
     * @param  $request_task
     * @return \Illuminate\Http\Response
     */
    public function update($request_task, Request $request)
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
                $date = Carbon::createFromFormat('d-m-Y', $request_data['date']);
                $temp_emps = [];
                $request_task = RequestTask::where('id', $request_task)->where('position_id', $request_data['position'])->where('date', $date->format('Y-m-d'))
                    ->with('request_task_has_emps')->first();
                foreach ($request_task->request_task_has_emps as $key => $item) {
                    foreach ($request_data['emps'] as $key => $emp_id) {
                        if ($item->emp_id != $emp_id) {
                            $temp_emps[] = $emp_id;
                        }
                    }
                }

                $request_task_check = RequestTask::where('position_id', $request_data['position'])->where('date', $date->format('Y-m-d'))
                    ->whereHas('request_task_has_emps', function ($e) use ($temp_emps) {
                        $e->whereIn('emp_id', $temp_emps);
                    })->get();
                // Log::info("date $date");
                // $task_tess = RequestTask::where('position_id', $request_data['position'])->where('date', $date->format('Y-m-d'))->get();
                Log::info($request_data['emps']);
                Log::info($request_task_check);
                if (!empty($request_task)) {
                    if (!count($request_task_check)) {
                        $request_task_data = ['updated_user' => auth()->user()->id];
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
                    } else {
                        return $this->buildRes->RESPONSE_REQ('error', null, ['error' => ["penugasan untuk karyawan yang ditambahkan, di tanggal {$date->format('d-m-Y')} sudah tersedia"]]);
                    }
                } else {
                    return $this->buildRes->RESPONSE_REQ('error', null, ['error' => ["Jadwal untuk tanggal {$date->format('d-m-Y')} tidak tersedia"]]);
                }
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
        if (!auth()->user()->can('request-task.delete') || !$request->ajax()) {
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
