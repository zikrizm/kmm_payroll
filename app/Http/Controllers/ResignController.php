<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;
use App\Services\Api\ApiServices;
use Illuminate\Support\Facades\Log;
use App\Exceptions\ResponseExeception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

class ResignController extends Controller
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
        if (!auth()->user()->can('resign.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            if (request()->ajax()) {
                $order = null;
                $filter = [];

                if ($request->has('q')) {
                    $filter['employee'] = $request->q;
                }

                if ($request->has('resign_date')) {
                    $filter['resign_date'] = $request['resign_date'];
                }

                if ($request->has('sort')) {
                    $filter['ordering'] = $request->sort['name'];
                    $order = $request->sort['order'];
                }

                $resigns = $this->apiService->get_resign($filter);
                $render =  view('Employee.resign.table', compact('resigns', 'order'))->render();
                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            }

            return view('Employee.resign.index');
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
        if (!auth()->user()->can('resign.create') || !request()->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $render = view('Employee.resign.create')->render();

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
        if (!auth()->user()->can('resign.create')  || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validator = Validator::make($request->all(), $this->rules());

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $resign_data = $request->only(['employee', 'resign_date', 'resign_type', 'disableatt']);
                $resign_date = Carbon::createFromFormat('d-m-Y', $resign_data['resign_date']);

                $resign_data['resign_date'] =  $resign_date->format('Y-m-d');
                $resign_data['employee'] =  (int)$resign_data['employee'];
                $resign_data['resign_type'] =  (int)$resign_data['resign_type'];
                $resign_data['disableatt'] =  $resign_data['disableatt'] == 'true' ? true : false;
                $res = $this->apiService->create_resign($resign_data);

                // ** create activity log user
                ActivityLog::created_activity('CRUD resign', 'User ' . auth()->user()->username . ' create new resign');
                return response()->json($res);
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  $resign
     * @return \Illuminate\Http\Response
     */
    public function show($resign)
    {
        if (!auth()->user()->can('resign.view')) {
            abort(403, 'Unauthorized action.');
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  $resign
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit($resign, Request $request)
    {
        if (!auth()->user()->can('resign.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $resign = $this->apiService->read_resign($resign);
            $render = view('Employee.resign.edit', compact('resign'))->render();

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
     * @param  $resign
     * @return \Illuminate\Http\Response
     */
    public function update($resign, Request $request)
    {
        if (!auth()->user()->can('resign.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validator = Validator::make($request->all(), $this->rules());

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $resign_data = $request->only(['employee', 'resign_date', 'resign_type', 'disableatt']);
                $resign_data['id'] = $resign;

                $res = $this->apiService->update_resign($resign_data);

                // ** create activity log user
                ActivityLog::created_activity('CRUD resign', 'User ' . auth()->user()->username . ' edit data resign');
                return response()->json($res);
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $resign
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy($resign, Request $request)
    {
        if (!auth()->user()->can('resign.delete') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $res = $this->apiService->delete_resign($resign);

            // ** create activity log user
            ActivityLog::created_activity('CRUD resign', 'User ' . auth()->user()->username . ' delete data resign');
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

    /**
     * Rules validation employee.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'employee' => 'required',
            'resign_date' => 'required',
            'resign_type' => 'required',
            'disableatt' => 'required',
        ];
    }
}
