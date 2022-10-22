<?php

namespace App\Http\Controllers;

use App\Rules\NIK;
use App\Rules\Mobile;
use App\Models\Employee;
use App\Models\Operational;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;
use App\Services\Api\ApiServices;
use Illuminate\Support\Facades\DB;
use App\Models\EmployeeHasPosition;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exceptions\ResponseExeception;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\HeadingRowImport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\EmployeePhotoController;
use App\Imports\EmployeesImport;
use Maatwebsite\Excel\Validators\ValidationException;
use Maatwebsite\Excel\Exceptions\NoTypeDetectedException;


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
                    $filter['employee_icontains'] = $request->q;
                }

                if ($request->has('page')) {
                    $filter['page'] = $request->page;
                }

                if ($request->has('sort')) {
                    $filter['ordering'] = $request->sort['name'];
                    $order = $request->sort['order'];
                }

                $employees = $this->apiService->get_employees($filter);
                $positions = $this->apiService->get_positions([])['data'];

                $emp_ids = array_column($employees['data'], 'id');
                $empDBs = Employee::whereIn('emp_id', $emp_ids)->with('employee_has_position')->get();
                foreach ($employees['data'] as $key => $employee) {
                    $employees['data'][$key]['position'] = [];
                }

                foreach ($empDBs as $key => $empDB) {
                    $_positions = [];
                    foreach ($empDB->employee_has_position as $emp_has_position) {
                        $pos_i = array_search($emp_has_position->position_id, array_column($positions, 'id'));
                        $_positions[] = $positions[$pos_i];
                    }
                    $emp_i = array_search($empDB->emp_id, array_column($employees['data'], 'id'));
                    $employees['data'][$emp_i]['position'] = $_positions;
                }

                $render = view('Employee.employee.table', compact('employees', 'order'))->render();
                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            }

            return view('Employee.employee.index');
        } catch (ResponseExeception $e) {
            // return $this->buildRes->RESPONSE_REQ('error', null, $e->getMessages());
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
            $departments = $this->apiService->get_departments([]);
            $areas = $this->apiService->get_areas([]);
            $positions = $this->apiService->get_positions([]);

            $render = view('Employee.employee.create', compact('departments', 'areas', 'positions'))->render();

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
        if (!auth()->user()->can('employee.create') || !auth()->user()->can('employee-photo.create') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validator = Validator::make($request->all(), $this->rules(null));

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                DB::beginTransaction();

                $request['employee_code'] = $request->emp_code;
                $emp_data = $request->only([
                    'user_capture', 'emp_code', 'first_name', 'last_name', 'nickname', 'hired_date', 'gender',
                    'contact_tel', 'office_tel', 'mobile', 'national', 'city', 'address', 'postcode', 'religion', 'email', 'birthday',
                    'verify_mode', 'emp_type', 'app_status', 'app_role', 'hire_date', 'department', 'position', 'area', 'daily_salary', 'payment_period',
                    'is_error_image',
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

                if ((bool)$emp_data['is_error_image']) {
                    // * Check employee already exist or not.
                    $employees = $this->apiService->get_employees(['emp_code' => $request->emp_code]);
                    $emp_data['id'] = $employees['data'][0]['id'];
                    $res = $this->apiService->update_employee($emp_data);

                    $emp_id = $employees['data'][0]['id'];
                    Log::info('update_employee_in_create');
                } else {
                    $res = $this->apiService->create_employee($emp_data);
                    if ($res['status'] == 'success')
                        $emp_id = $res['data']['id'];
                    Log::info('create_employee');
                }

                if ($res['status'] == 'success') {
                    // * Save employee to DB.
                    $business_id = Session::get('business_id');
                    $emp_code = $res['data']['emp_code'];

                    if ((bool)$emp_data['is_error_image']) {
                        $employeeDB = Employee::where('emp_id', $emp_id)->first();
                        $employeeDB->update([
                            'emp_code' => $emp_code,
                            'first_name' => $res['data']['first_name'],
                            'last_name' => $res['data']['last_name'],
                            'daily_salary' => str_replace('.', '', $emp_data['daily_salary']),
                            'payment_period' => $emp_data['payment_period'],
                            'updated_user' => auth()->user()->id,
                        ]);
                    } else {
                        $employeeDB = new Employee([
                            'business_id' => $business_id,
                            'emp_id' => $emp_id,
                            'emp_code' => $emp_code,
                            'first_name' => $res['data']['first_name'],
                            'last_name' => $res['data']['last_name'],
                            'daily_salary' => str_replace('.', '', $emp_data['daily_salary']),
                            'payment_period' => $emp_data['payment_period'],
                            'created_user' => auth()->user()->id,
                            'updated_user' => auth()->user()->id,
                            'is_device' => -1,
                        ]);
                        $employeeDB->save();
                    }

                    // Delete all EmployeeHasPosition IF emp_id == $employee->id
                    EmployeeHasPosition::where('emp_id', $employeeDB->id)->each(function ($item) {
                        $item->delete();
                    });

                    if (!empty($request->input('position'))) {
                        foreach ($emp_data['position'] as $item) {
                            $position = new EmployeeHasPosition([
                                'emp_id' => $employeeDB->id,
                                'position_id' => $item,
                            ]);

                            $position->save();
                        }
                    }

                    if ($request->hasFile('user_capture') && $request->file('user_capture')->isValid()) {
                        $resPhoto = app('App\Http\Controllers\EmployeePhotoController')->store($request);
                        if ($resPhoto['status'] == 'error') {
                            Log::info($resPhoto);
                            $msg_text = $resPhoto['msg']['error'];
                            if (str_contains(strtolower($msg_text), 'invalid photo') || str_contains(strtolower($msg_text), 'cannot write mode')) {
                                $resPhoto['msg']['user_capture'] = [$msg_text];
                                unset($resPhoto['msg']['error']);
                            }
                            DB::commit();
                            return response()->json($resPhoto);
                        } else {
                            return response()->json($res);
                        }
                    } else {
                        DB::commit();
                    }
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
            $employee = $this->apiService->get_employees(['emp_code' => $employee]);
            if (!empty($employee['data'])) {
                $employee = $employee['data'][0];

                $employeeDB = Employee::where('emp_id', $employee['id'])->with('employee_has_position')->first();
                $employee['daily_salary'] = $employeeDB->daily_salary ?? null;
                $employee['payment_period'] = $employeeDB->payment_period ?? null;
                $employee['position'] = $employeeDB->employee_has_position ?? [];

                $departments = $this->apiService->get_departments([]);
                $areas = $this->apiService->get_areas([]);
                $positions = $this->apiService->get_positions([]);
                $render = view('Employee.employee.edit', compact('employee', 'departments', 'areas', 'positions'))->render();

                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            } else {
                return $this->buildRes->RESPONSE_REQ('error', null, ['error' => ['No employee data']]);
            }
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
            $validator = Validator::make($request->all(), $this->rules($employee));
            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                DB::beginTransaction();

                $request['employee_code'] = $request->emp_code;
                $emp_data = $request->only([
                    'user_capture', 'emp_code', 'first_name', 'last_name', 'nickname', 'hired_date', 'gender',
                    'contact_tel', 'office_tel', 'mobile', 'national', 'city', 'address', 'postcode', 'religion', 'email', 'birthday',
                    'verify_mode', 'emp_type', 'app_status', 'app_role', 'hire_date', 'department', 'position', 'area', 'daily_salary', 'payment_period'
                ]);

                Log::info($emp_data);

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

                $emp_data['id'] = $employee;
                $res = $this->apiService->update_employee($emp_data);

                if ($res['status'] == 'success') {
                    // * Save employee to DB.
                    $business_id = Session::get('business_id');
                    $emp_code = $res['data']['emp_code'];

                    $employeeDB = Employee::where('emp_id', $employee)->first();
                    if (empty($employeeDB)) {
                        $employeeDB = new Employee([
                            'business_id' => $business_id,
                            'emp_id' => $employee,
                            'emp_code' => $emp_code,
                            'first_name' => $res['data']['first_name'],
                            'last_name' => $res['data']['last_name'],
                            'daily_salary' => str_replace('.', '', $emp_data['daily_salary']),
                            'payment_period' => $emp_data['payment_period'],
                            'created_user' => auth()->user()->id,
                            'updated_user' => auth()->user()->id,
                            'is_device' => -1,
                        ]);

                        $employeeDB->save();
                    } else {
                        $employeeDB->update([
                            'emp_code' => $emp_code,
                            'first_name' => $res['data']['first_name'],
                            'last_name' => $res['data']['last_name'],
                            'daily_salary' => str_replace('.', '', $emp_data['daily_salary']),
                            'payment_period' => $emp_data['payment_period'],
                            'updated_user' => auth()->user()->id,
                        ]);
                    }

                    // Delete all EmployeeHasPosition IF emp_id == $employee->id
                    EmployeeHasPosition::where('emp_id', $employeeDB->id)->each(function ($item) {
                        $item->delete();
                    });

                    if (!empty($request->input('position'))) {
                        foreach ($emp_data['position'] as $item) {
                            $position = new EmployeeHasPosition([
                                'emp_id' => $employeeDB->id,
                                'position_id' => $item,
                            ]);
                            $position->save();
                        }
                    }

                    if ($request->hasFile('user_capture') && $request->file('user_capture')->isValid()) {
                        $resPhoto = app('App\Http\Controllers\EmployeePhotoController')->store($request);
                        if ($resPhoto['status'] == 'error') {
                            $msg_text = $resPhoto['msg']['error'];
                            if (str_contains(strtolower($msg_text), 'invalid photo') || str_contains(strtolower($msg_text), 'cannot write mode')) {
                                Log::info("SDfsdfsdf");
                                $resPhoto['msg']['user_capture'] = [$msg_text];
                                unset($resPhoto['msg']['error']);
                            }
                            DB::commit();
                            return response()->json($resPhoto);
                        } else {
                            return response()->json($res);
                        }
                    } else {
                        DB::commit();
                    }
                } else {
                    return response()->json($res);
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
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

    public function employee_off_in_depts(Request $request)
    {
        if (!$request->ajax()) abort(403, 'Unauthorized action.');
        if ($request->has('q') && !empty($request->input('q'))) {
            $business_id = Session::get('business_id');
            $operationals = Operational::where('business_id', $business_id)->where('id', $request->operational_id)->with(['operational_groups' => function ($query) {
                $query->where('status', 'inactive');
            }])->first();
            $dept_inactive = implode(',',  array_column($operationals->operational_groups->toArray(), 'dept_id'));
            $employees = $this->apiService->get_employees(['departments' => $dept_inactive, 'employee_icontains' => $request->q]);
            return response()->json($employees['data']);
        } else {
            return [];
        }
    }

    public function uploadCSV()
    {
        $render =  view('Employee.employee.uploadCSV')->render();

        return $this->buildRes->RESPONSE_REQ('success', $render, null);
        # code...
    }
    public function uploadCSV_store(Request $request)
    {
        Log::info($request);
        // try {
        try {
            // $headings = (new HeadingRowImport)->toArray($request->file);

            // Log::info($request);
            // Log::info($request->file('file')[0]);
            $rows = Excel::toCollection(new EmployeesImport, $request->file('file_csv'));
            
            Log::info(response()->json($rows[0]));
            // return 'berhasil';
        } catch (ValidationException $e) {
            $failures = $e->failures();

            Log::info($failures);
            foreach ($failures as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                $failure->values(); // The values of the row that has failed.
            }
        } catch (NoTypeDetectedException $e) {
            // return Redirect::back();
            Log::info("error");
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
        }
    }

    /**
     * Rules validation employee.
     *
     * @param string $employee_id
     * @return array
     */
    public function rules($employee_id)
    {
        return [
            'emp_code' => (empty($employee_id)) ? 'required' : 'sometimes',
            'first_name' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'emp_type' => 'required|string|max:255',
            'hire_date' => 'required|string|max:255',
            'mobile' => ['sometimes', new Mobile, 'nullable'],
            'area' => 'required',
            'gender' => 'required',
            'daily_salary' => 'required',
            'payment_period' => 'required',
            'user_capture' => (empty($employee_id)) ? 'required' : 'sometimes',
        ];
    }
}
