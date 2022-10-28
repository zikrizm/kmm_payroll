<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\ActivityLog;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\Api\ApiServices;
use Illuminate\Support\Facades\Log;
use App\Exceptions\ResponseExeception;
use Illuminate\Support\Facades\Validator;
use phpDocumentor\Reflection\Types\Boolean;

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
        if (!auth()->user()->can('department.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            if (request()->ajax()) {
                $order = null;
                $filter = [];

                if ($request->has('q')) {
                    $filter['department_icontains'] = $request->q;
                }

                if ($request->has('page')) {
                    $filter['page'] = $request->page;
                }

                if ($request->has('sort')) {
                    $filter['ordering'] = $request->sort['name'];
                    $order = $request->sort['order'];
                }

                $departments = $this->apiService->get_departments($filter);

                $depts = Department::all();
                foreach ($depts as $dept) {
                    foreach ($departments['data'] as $key => $department) {
                        if ($dept->dept_id == $department['id']) {
                            $departments['data'][$key]['sitting_money'] = $dept->sitting_money;
                        }
                    }
                }

                $render =  view('Organization.department.table', compact('departments', 'order'))->render();
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
        if (!auth()->user()->can('department.create') || !request()->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $departments = $this->apiService->get_departments([]);
            $render = view('Organization.department.create', compact('departments'))->render();

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
        if (!auth()->user()->can('department.create')  || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validator = Validator::make($request->all(), $this->rules());

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $dept_reqdata = $request->only(['dept_code', 'dept_name', 'parent_dept', 'sitting_money_check', 'sitting_money']);

                $res = $this->apiService->create_department($dept_reqdata);

                if ($res['status'] == 'success') {
                    $dept_id = $res['data']['id'];
                    $this->__createDepartmentIfNotExists($dept_id, $request);

                    // ** create activity log user
                    ActivityLog::created_activity('CRUD department', 'User ' . auth()->user()->username . ' create new department');
                    return response()->json($res);
                } else {
                    return response()->json($res);
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
     * @param  $department
     * @return \Illuminate\Http\Response
     */
    public function show($department)
    {
        if (!auth()->user()->can('department.view')) {
            abort(403, 'Unauthorized action.');
        }
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
        if (!auth()->user()->can('department.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $dept = $this->apiService->read_department($department);
            $deptDB = Department::where('dept_id', $dept['id'])->with('user')->first();

            if ($deptDB) {
                $dept['sitting_money_check'] = (bool)$deptDB->sitting_money;
                $dept['sitting_money'] = $deptDB->sitting_money;
                $dept['updated_by'] = 'Diperbarui: ' . $deptDB->user->first_name . ', ' . $deptDB->updated_at;
            } else {
                $dept['sitting_money_check'] = false;
                $dept['sitting_money'] = null;
                $dept['updated_by'] = null;
            }

            $departments = $this->apiService->get_departments([]);
            $render = view('Organization.department.edit', compact('dept', 'departments'))->render();

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
     * @param  $department
     * @return \Illuminate\Http\Response
     */
    public function update($department, Request $request)
    {
        if (!auth()->user()->can('department.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validator = Validator::make($request->all(), $this->rules());

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $dept_data = $request->only(['dept_code', 'dept_name', 'parent_dept', 'sitting_money_check', 'sitting_money']);
                $dept_data['id'] = $department;

                $res = $this->apiService->update_department($dept_data);

                if ($res['status'] == 'success') {
                    $dept_id = $res['data']['id'];
                    $this->__createDepartmentIfNotExists($dept_id, $request);
                    Department::where('dept_id', $dept_id)->update(
                        [
                            'sitting_money' => (!empty($dept_data['sitting_money_check'])) ?
                                str_replace('.', '', $dept_data['sitting_money']) : null,
                            'updated_user' => auth()->user()->id,
                        ]
                    );

                    // ** create activity log user
                    ActivityLog::created_activity('CRUD department', 'User ' . auth()->user()->username . ' edit data department');
                    return response()->json($res);
                } else {
                    return response()->json($res);
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
     * @param  $department
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy($department, Request $request)
    {
        if (!auth()->user()->can('department.delete') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $res = $this->apiService->delete_department($department);

            // ** create activity log user
            ActivityLog::created_activity('CRUD department', 'User ' . auth()->user()->username . ' delete data department');
            return response()->json($res);
        } catch (\Exception $e) {
            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Creates new department if doesn't exist
     *
     * @param  int $dept_id
     * @return void
     */
    private function __createDepartmentIfNotExists($dept_id, Request $request)
    {
        $dept = Department::where('dept_id', $dept_id)->first();
        if (empty($dept)) {
            $dept = new Department([
                'dept_id' => $dept_id,
                'created_user' => auth()->user()->id,
                'updated_user' => auth()->user()->id,
                'sitting_money' => (!empty($request->input('sitting_money_check'))) ?
                    str_replace('.', '', $request['sitting_money']) : null
            ]);
            $dept->save();
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
            'sitting_money_check' => 'nullable',
            'sitting_money' => [
                Rule::requiredIf(function () {
                    return request()->get('sitting_money_check');
                })
            ],
        ];
    }
}
