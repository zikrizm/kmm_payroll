<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Transaction;
use App\Models\Device;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;
use App\Services\Api\ApiServices;
use Illuminate\Support\Facades\Log;
use App\Exceptions\ResponseExeception;
use Illuminate\Support\Facades\Validator;

class DeviceController extends Controller
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
        if (!auth()->user()->can('device.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            if (request()->ajax()) {
                $order = null;
                $filter = [];

                if ($request->has('q')) {
                    $filter['alias_icontains'] = $request->q;
                }

                if ($request->has('page')) {
                    $filter['page'] = $request->page;
                }

                if ($request->has('sort')) {
                    $filter['ordering'] = $request->sort['name'];
                    $order = $request->sort['order'];
                }

                $devices = $this->apiService->get_devices($filter);
                $device_dbs = Device::all();
                foreach ($device_dbs as $device_db) {
                    foreach ($devices['data'] as $key => $device) {
                        if ($device_db->device_id == $device['id']) {
                            $devices['data'][$key]['punch_type'] = $device_db->punch_type;
                        }
                    }
                }

                $render =  view('Transaction.device.table', compact('devices', 'order'))->render();
                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            }

            return view('Transaction.device.index');
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
        if (!auth()->user()->can('device.create') || !request()->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $areas = $this->apiService->get_areas([]);
            $render = view('Transaction.device.create', compact('areas'))->render();

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
        if (!auth()->user()->can('device.create')  || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validator = Validator::make($request->all(), $this->rules());

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $device_data = $request->only(['sn', 'alias', 'ip_address', 'area', 'is_attendance', 'terminal_tz', 'punch_type']);
                $res = $this->apiService->create_device($device_data);
                if ($res['status'] == 'success') {
                    $device_id = $res['data']['id'];
                    $this->__createDeviceIfNotExists($device_id, $request);

                    ActivityLog::created_activity('CRUD device', 'User ' . auth()->user()->username . ' create new device');
                    return response()->json($res);
                } else {
                    return response()->json($res);
                }

                // ** create activity log user
                ActivityLog::created_activity('CRUD device', 'User ' . auth()->user()->username . ' create new device');
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
     * @param  $device
     * @return \Illuminate\Http\Response
     */
    public function show($device)
    {
        if (!auth()->user()->can('device.view')) {
            abort(403, 'Unauthorized action.');
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  $device
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit($device, Request $request)
    {
        if (!auth()->user()->can('device.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $device = $this->apiService->read_device($device);
            $deviceDB = Device::where('device_id', $device['id'])->with('user')->first();
            if ($deviceDB) {
                $device['punch_type'] = $deviceDB->punch_type;
                $device['updated_by'] = 'Diperbarui: ' . $deviceDB->user->first_name . ', ' . $deviceDB->updated_at;
            } else {
                $device['punch_type'] = null;
                $device['updated_by'] = null;
            }

            $areas = $this->apiService->get_areas([]);
            $render = view('Transaction.device.edit', compact('device', 'areas'))->render();

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
                $device_data = $request->only(['sn', 'alias', 'ip_address', 'area', 'is_attendance', 'terminal_tz', 'punch_type']);
                $device_data['id'] = $department;

                $res = $this->apiService->update_device($device_data);
                if ($res['status'] == 'success') {
                    $device_id = $res['data']['id'];
                    $this->__createDeviceIfNotExists($device_id, $request);

                    Device::where('device_id', $device_id)->update([
                        'punch_type' => $device_data['punch_type'],
                        'updated_user' => auth()->user()->id,
                    ]);

                    // ** create activity log user
                    ActivityLog::created_activity('CRUD device', 'User ' . auth()->user()->username . ' edit data device');
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
     * @param  $device
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy($device, Request $request)
    {
        if (!auth()->user()->can('device.delete') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $res = $this->apiService->delete_device($device);

            // ** create activity log user
            ActivityLog::created_activity('CRUD device', 'User ' . auth()->user()->username . ' delete data device');
            return response()->json($res);
        } catch (\Exception $e) {
            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Creates new device if doesn't exist
     *
     * @param  int $device_id
     * @return void
     */
    private function __createDeviceIfNotExists($device_id, Request $request)
    {
        $device = Device::where('device_id', $device_id)->first();
        if (empty($device)) {
            $device = new Device([
                'device_id' => $device_id,
                'punch_type' => $request['punch_type'],
                'created_user' => auth()->user()->id,
                'updated_user' => auth()->user()->id,
            ]);
            $device->save();
        }
    }

    /**
     * Rules validation device.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'sn' => 'required|string|max:255',
            'alias' => 'required|string|max:255',
            'ip_address' => 'required|string|max:255',
            'area' => 'required|string|max:255',
        ];
    }
}
