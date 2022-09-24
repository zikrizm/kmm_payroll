<?php

namespace App\Http\Controllers;

use ZKLibrary;
use App\Models\Group;
use App\Models\Employee;
use App\Models\WorkSection;
use App\Utils\BusinessUtil;
use App\Utils\ResponseUtil;
use Rats\Zkteco\Lib\ZKTeco;
use Illuminate\Http\Request;
use App\Services\Api\ApiServices;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    private $apiService;
    private $buildRes;
    private $businessUtil;

    public function __construct(BusinessUtil $businessUtil, ApiServices $service, ResponseUtil $buildRes)
    {
        $this->businessUtil = $businessUtil;
        $this->apiService = $service;
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
                // $business_id = Session::get('business_id');
                // $employees = Employee::where('business_id', $business_id);
                // if ($request->has('q')) {
                //     // **
                //     // * search ----->
                //     // *
                //     $search = $request->q;
                //     $employees = $employees->where(function ($q) use ($search) {
                //         $q->where('first_name', 'LIKE', "%" . $search . "%");
                //     });
                // }
                // $employees = $employees->orderBy('first_name', 'ASC')->paginate(10);

                $device_emps = $this->apiService->get_employees();
                $render =  view('employee.table', compact('device_emps'))->render();
                
                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            } else {
                // **
                // * Create employes if not exists  ----->
                // *
                // $device_emps = $this->apiService->get_employees();
                // if ($device_emps && count($device_emps->data)) {
                //     $this->__createEmployesIfNotExists($device_emps->data);
                // }
            }

            return  view('employee.index');
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            return $this->buildRes->RESPONSE_REQ('error', null, ['something_wrong' => ['something wrong']]);
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
        if (!auth()->user()->can('employee.create') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // $business_id = Session::get('business_id');
            // $work_sections = WorkSection::where('business_id', $business_id)->get();
            // $groups = Group::where('business_id', $business_id)->get();
            // $render =  view('employee.create', compact('work_sections', 'groups'))->render();

            // return $this->buildRes->RESPONSE_REQ('success', $render, null);
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['something_wrong' => ['something wrong']]);
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

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $employee_data = $request->only(['name', 'status', 'email', 'gender', 'work_section_id', 'group_id', 'daily_salary', 'pay_component']);
                if (!empty($request->input('password'))) {
                    $employee_data['password'] = Hash::make($request->input('password'));
                }

                $employee_data['business_id'] = Session::get('business_id');

                // upload logo
                $photo_profile = $this->businessUtil->uploadFile($request, 'photo', 'profiles', 'image');
                if (!empty($photo_profile)) {
                    $employee_data['photo'] = $photo_profile;
                }
                $employee = new Employee($employee_data);
                $employee->save();

                return $this->buildRes->RESPONSE_REQ('success', null,  'Add employee data successfully');
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, 'something wrong');
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
        if (!auth()->user()->can('employee.view')) {
            abort(403, 'Unauthorized action.');
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  Employee $employee
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(Employee $employee, Request $request)
    {
        if (!auth()->user()->can('employee.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = Session::get('business_id');
            $work_sections = WorkSection::where('business_id', $business_id)->get();
            $groups = Group::where('business_id', $business_id)->get();

            $render = view('employee.edit', compact('work_sections', 'groups'))->render();

            return $this->buildRes->RESPONSE_REQ('success', $render, null);
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, 'something wrong');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Employee $employee
     * @return \Illuminate\Http\Response
     */
    public function update(Employee $employee, Request $request)
    {
        if (!auth()->user()->can('employee.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validator = Validator::make($request->all(), $this->rules());

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $employee_data = $request->only(['name', 'status', 'email', 'gender', 'work_section_id', 'group_id', 'daily_salary', 'pay_component']);
                if (!empty($request->input('password'))) {
                    $employee_data['password'] = Hash::make($request->input('password'));
                }

                $employee_data['business_id'] = Session::get('business_id');

                // upload logo
                $photo_profile = $this->businessUtil->uploadFile($request, 'photo', 'profiles', 'image');
                if (!empty($photo_profile)) {
                    $employee_data['photo'] = $photo_profile;
                }
                $employee->update($employee_data);

                return $this->buildRes->RESPONSE_REQ('success', null,  'Update employee data successfully');
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, 'something wrong');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Employee $employee
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Employee $employee, Request $request)
    {
        if (!auth()->user()->can('employee.delete') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $employee->delete();

            return $this->buildRes->RESPONSE_REQ('success', null, 'Delete employee data successfully');
        } catch (\Exception $e) {
            return $this->buildRes->RESPONSE_REQ('error', null, 'something wrong');
        }
    }

    /**
     * Check is same data
     *
     * @param  string  $value
     * @param  string  $valueComp
     * @return bool
     */
    public function is_same_validate($value, $valueComp)
    {
        return (strtolower($value) && strtolower($valueComp));
    }

    /**
     * Creates new employes if doesn't exist
     *
     * @param  object  $device_emps
     * @return void
     */
    private function __createEmployesIfNotExists($device_emps)
    {
        if (count($device_emps)) {
            $business_id = Session::get('business_id');
            $employees = Employee::where('business_id', $business_id)->get();

            $new_emps = [];

            $emp_code_ids = collect($device_emps)->map(
                function ($e) {
                    return $e['emp_code'];
                }
            )->toArray();
            $exising_employees = Employee::whereIn('emp_code_id', $emp_code_ids)->pluck('emp_code_id', 'business_id')->toArray();
            $non_existing_employees =  array_diff($emp_code_ids, $exising_employees);

            if (count($non_existing_employees)) {
                foreach ($device_emps as $device_emp) {
                    foreach ($non_existing_employees as $non_existing_employee) {
                        if ($device_emp['emp_code'] == $non_existing_employee) {
                            $new_emps[] = [
                                'business_id' => $business_id,
                                'emp_code_id' => $device_emp['emp_code'],

                                'first_name' => $device_emp['first_name'],
                                'last_name' => $device_emp['last_name'],
                                'nickname' => $device_emp['nickname'],
                                'email' => $device_emp['email'],
                                'birthday' => $device_emp['birthday'],
                                'hire_date' => $device_emp['hire_date'],

                                'photo' => $device_emp['photo'],
                                'gender' => $device_emp['gender'],
                                'mobile' => $device_emp['mobile'],

                                'national' => $device_emp['national'],
                                'city' => $device_emp['city'],
                                'address' => $device_emp['address'],
                                'postcode' => $device_emp['postcode'],

                                'is_device' => 1,
                            ];
                        }
                    }
                }
                if (count($new_emps)) Employee::insert($new_emps);
            }
        }
    }

    /**
     * Rules validation user.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'gender' => 'required|string',
            'email' => 'required|nullable|email|unique:users|max:255',
            'work_section_id' => 'string|exists:work_sections,id',
            'group_id' => 'string|exists:groups,id',
            'daily_salary' => 'required|string',
            'pay_component' => 'string',
            'status' => 'required|string',
        ];
    }
}
