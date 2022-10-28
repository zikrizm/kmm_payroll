<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Utils\ResponseUtil;
use App\Models\EmployeeDebt;
use Illuminate\Http\Request;
use App\Services\Api\ApiServices;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class KasbonController extends Controller
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
        if (!auth()->user()->can('kasbon.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            if (request()->ajax()) {
                $business_id = Session::get('business_id');
                $kasbons = EmployeeDebt::where('business_id', $business_id);
                if ($request->has('q') && !empty($request->input('q'))) {
                    $search = str_replace('.', '', $request->q);
                    $kasbons = $kasbons->where('debt', 'LIKE', "%" . $search . "%")->orWhere('instalment', 'LIKE', "%" . $search . "%")->orWhere('first_name', 'LIKE', "%" . $search . "%");
                }

                if ($request->has('kasbon_date')) {
                    $kasbons = $kasbons->whereBetween('date', [$request['kasbon_date']['start_date'], $request['kasbon_date']['end_date']]);
                }

                $order = null;
                if ($request->has('sort')) {
                    $sort = $request->sort;
                    $order = $sort['order'];
                    $kasbons->orderBy($sort['name'], $sort['order']);
                }
                $kasbons = $kasbons->paginate(10);
                $render =  view('Employee.kasbon.table', compact('kasbons', 'order'))->render();

                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            }

            return  view('Employee.kasbon.index');
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
        if (!auth()->user()->can('kasbon.create') || !request()->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $render = view('Employee.kasbon.create')->render();

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
        if (!auth()->user()->can('kasbon.create')  || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validator = Validator::make($request->all(), $this->rules());

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $kasbon_data = $request->only(['emp_id', 'date', 'debt', 'instalment']);

                $employee = $this->apiService->read_employee($kasbon_data['emp_id']);
                $kasbon_data['business_id'] = Session::get('business_id');
                $kasbon_data['first_name'] = $employee['first_name'];
                $kasbon_data['emp_code'] = $employee['emp_code'];
                $kasbon_data['created_user'] = auth()->user()->id;
                $kasbon_data['updated_user'] = auth()->user()->id;
                $kasbon_data['debt'] = str_replace('.', '', $kasbon_data['debt']);
                $kasbon_data['instalment'] = str_replace('.', '', $kasbon_data['instalment']);

                $kasbon = new EmployeeDebt($kasbon_data);
                $kasbon->save();

                // ** create activity log user
                ActivityLog::created_activity('CRUD debt', 'User ' . auth()->user()->username . ' create new debt');
                return $this->buildRes->RESPONSE_REQ('success', null,  ['success' => ['Add kasbon succesfully']]);
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
     * @param  EmployeeDebt $kasbon
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(EmployeeDebt $kasbon, Request $request)
    {
        if (!auth()->user()->can('kasbon.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $employee = $this->apiService->get_employees(['emp_code' => $kasbon->emp_code]);
            $employee = $employee['data'][0];
            Log::info($employee);
            $render = view('Employee.kasbon.edit', compact('kasbon', 'employee'))->render();

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
     * @param  EmployeeDebt $kasbon
     * @return \Illuminate\Http\Response
     */
    public function update(EmployeeDebt $kasbon, Request $request)
    {
        if (!auth()->user()->can('kasbon.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validator = Validator::make($request->all(), $this->rules());

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $kasbon_data = $request->only(['emp_id', 'date', 'debt', 'instalment']);
                $employee = $this->apiService->read_employee($kasbon_data['emp_id']);
                $kasbon_data['business_id'] = Session::get('business_id');
                $kasbon_data['first_name'] = $employee['first_name'];
                $kasbon_data['emp_code'] = $employee['emp_code'];
                $kasbon_data['updated_user'] = auth()->user()->id;
                $kasbon_data['debt'] = str_replace('.', '', $kasbon_data['debt']);
                $kasbon_data['instalment'] = str_replace('.', '', $kasbon_data['instalment']);

                $kasbon->update($kasbon_data);

                // ** create activity log user
                ActivityLog::created_activity('CRUD debt', 'User ' . auth()->user()->username . ' edit data debt');
                return $this->buildRes->RESPONSE_REQ('success', null, ['success' => ['Update kasbon succesfully']]);
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  EmployeeDebt $kasbon
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmployeeDebt $kasbon, Request $request)
    {
        if (!auth()->user()->can('kasbon.delete') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $kasbon->delete();

            // ** create activity log user
            ActivityLog::created_activity('CRUD debt', 'User ' . auth()->user()->username . ' delete data debt');
            return $this->buildRes->RESPONSE_REQ('success', null, ['success' => ['Delete kasbon succesfully']]);
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
            'emp_id' => 'required|string|max:255',
            'date' => 'required',
            'debt' => 'required',
            'instalment' => 'required',
        ];
    }
}
