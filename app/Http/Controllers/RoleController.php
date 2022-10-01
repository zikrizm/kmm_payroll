<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Permission;
use App\Services\Services;
use App\Utils\BusinessUtil;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Services\Api\ApiServices;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
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
        if (!auth()->user()->can('role.view')) {
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

                $order = null;
                if ($request->has('sort')) {
                    $sort = $request->sort;
                    $order = $sort['order'];
                    $roles->orderBy($sort['name'], $sort['order']);
                }

                $roles = $roles->paginate(10);
                $render =  view('User.role.table', compact('roles', 'order'))->render();

                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            }

            return  view('User.role.index');
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
        if (!auth()->user()->can('role.create') || !request()->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $permissions = [];
            $permits = Permission::all();
            foreach ($permits as $item) {
                $permissionSplice = explode('.', $item->name)[0];
                if (!isset($permissions[$permissionSplice])) {
                    $permissions[$permissionSplice] = [
                        'name' => $permissionSplice,
                        'title' => ucfirst($permissionSplice),
                        'subtitle' => "Please select access for " . $permissionSplice,
                        'roles' => [$item->toArray()],
                    ];
                } else {
                    $permissions[$permissionSplice]['roles'][] = $item->toArray();
                }
            }

            $render = view('User.role.create', compact('permissions'))->render();

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
        if (!auth()->user()->can('role.create')  || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $validator = Validator::make($request->all(), $this->rules());

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

                return $this->buildRes->RESPONSE_REQ('success', null, ['success' => 'Add role succesfully']);
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
        if (!auth()->user()->can('role.view')) {
            abort(403, 'Unauthorized action.');
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  int $role
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit($role, Request $request)
    {
        if (!auth()->user()->can('role.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = Session::get('business_id');

            $permissions = [];
            $permits = Permission::all();
            foreach ($permits as $item) {
                $permissionSplice = explode('.', $item->name)[0];
                if (!isset($permissions[$permissionSplice])) {
                    $permissions[$permissionSplice] = [
                        'name' => $permissionSplice,
                        'title' => ucfirst($permissionSplice),
                        'subtitle' => "Please select access for " . $permissionSplice,
                        'roles' => [$item->toArray()],
                    ];
                } else {
                    $permissions[$permissionSplice]['roles'][] = $item->toArray();
                }
            }

            $role = Role::where('business_id', $business_id)->with(['permissions'])->find($role);
            $role_permissions = [];
            foreach ($role->permissions as $role_perm) {
                $role_permissions[] = $role_perm->name;
            }

            $render = view('User.role.edit', compact('permissions', 'role_permissions', 'role'))->render();

            return $this->buildRes->RESPONSE_REQ('success', $render, null);
        } catch (\Exception $error) {
            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $role
     * @return \Illuminate\Http\Response
     */
    public function update($role, Request $request)
    {
        if (!auth()->user()->can('role.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $role_data = $request->only(['name', 'roles']);
            $business_id = Session::get('business_id');

            $count = Role::where('name', $role_data['name'] . '#' . $business_id)->where('id', '!=', $role)
                ->where('business_id', $business_id)->count();
            if ($count == 0) {
                $role = Role::findOrFail($role);
                if (!$role->is_default || $role->name == 'Cashier#' . $business_id) {
                    if ($role->name == 'Cashier#' . $business_id) {
                        $role->is_default = 0;
                    }

                    $role->name = $role_data['name'];
                    $role->save();

                    $this->__createPermissionIfNotExists($role_data['roles']);

                    if (!empty($role_data['roles'])) {
                        $role->syncPermissions($role_data['roles']);
                    }
                    return $this->buildRes->RESPONSE_REQ('success', null, ['error' => 'Role update succesfully']);
                } else {
                    return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'Default role cannot be edited']);
                }
            } else {
                return $this->buildRes->RESPONSE_REQ('error', null, ['name' => 'Role name already exists']);
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Role $role
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role, Request $request)
    {
        if (!auth()->user()->can('role.delete') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $role->delete();

            return $this->buildRes->RESPONSE_REQ('success', null, ['success' => 'Delete user succesfully']);
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

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

    /**
     * Rules validation role.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:roles',
            'roles' => 'required|array',
        ];
    }
}
