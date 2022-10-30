<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Services\Api\ApiServices;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;

class AccessControlController extends Controller
{
    private $apiService;
    private $buildRes;

    public function __construct(ApiServices $service, ResponseUtil $buildRes)
    {
        $this->apiService = $service;
        $this->buildRes = $buildRes;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!auth()->user()->can('access-control.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            if (request()->ajax()) {
                $business_id = Session::get('business_id');
                $roles = Role::where('business_id', $business_id)->with('permissions');
                if ($request->has('q')) {
                    $search = $request->q;
                    $roles = $roles->where(function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%" . $search . "%");
                    });
                }

                $roles = $roles->orderBy('name', 'ASC')->paginate(10);
                $render =  view('role.table', compact('roles'))->render();

                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            }

            return  view('role.index');
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('access-control.create') || !request()->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $render = view('role.create')->render();

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
        if (!auth()->user()->can('access-control.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'roles' => 'required|array'
            ]);

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $role_data = $request->only(['name', 'roles']);
                $role = Role::create([
                    'name' => $role_data['name'],
                    'business_id' => Session::get('business_id'),
                    'guard_name' => 'web',
                    'is_default' => 0,
                ]);
                $role->syncPermissions($role_data['roles']);

                return $this->buildRes->RESPONSE_REQ('success', null,  'Add role succesfully');
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
        if (!auth()->user()->can('access-control.update')) {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $access_control
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit($access_control, Request $request)
    {
        if (!auth()->user()->can('access-control.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = Session::get('business_id');
            $role = Role::where('business_id', $business_id)
                ->with(['permissions'])
                ->find($access_control);
            $role_permissions = [];
            foreach ($role->permissions as $role_perm) {
                $role_permissions[] = $role_perm->name;
            }

            $render = view('role.edit', compact('role_permissions', 'role'))->render();

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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('access-control.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $role_name = $request->input('name');
            $permissions = $request->input('roles');
            $business_id = Session::get('business_id');

            $count = Role::where('name', $role_name . '#' . $business_id)
                ->where('id', '!=', $id)
                ->where('business_id', $business_id)
                ->count();
            if ($count == 0) {
                $role = Role::findOrFail($id);
                if (!$role->is_default || $role->name == 'Cashier#' . $business_id) {
                    if ($role->name == 'Cashier#' . $business_id) {
                        $role->is_default = 0;
                    }

                    $role->name = $role_name . '#' . $business_id;
                    $role->save();

                    $this->__createPermissionIfNotExists($permissions);

                    if (!empty($permissions)) {
                        $role->syncPermissions($permissions);
                    }
                    return $this->buildRes->RESPONSE_REQ('success', null, 'Access control update succesfully');
                } else {
                    return $this->buildRes->RESPONSE_REQ('error', null, 'Default role cannot be edited');
                }
            } else {
                return $this->buildRes->RESPONSE_REQ('error', null, ['name' => ['Role name already exists']]);
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Role $access_control
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $access_control, Request $request)
    {
        if (!auth()->user()->can('access-control.delete')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $access_control->delete();

            return $this->buildRes->RESPONSE_REQ('success', null, 'Access control delete succesfully');
        } catch (\Exception $e) {
            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Creates new permission if doesn't exist
     *
     * @param  array  $permissions
     * @return void
     */
    private function __createPermissionIfNotExists($permissions)
    {
        $exising_permissions = Permission::whereIn('name', $permissions)
            ->pluck('name')
            ->toArray();

        $non_existing_permissions = array_diff($permissions, $exising_permissions);

        if (!empty($non_existing_permissions)) {
            foreach ($non_existing_permissions as $new_permission) {
                $time_stamp = Carbon::now()->toDateTimeString();
                Permission::create([
                    'name' => $new_permission,
                    'guard_name' => 'web'
                ]);
            }
        }
    }
}
