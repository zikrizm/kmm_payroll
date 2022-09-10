<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;
use App\Services\Api\ApiServices;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
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
    public function index()
    {
        if (!auth()->user()->can('access-control.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            if (request()->ajax()) {
                $roles = Role::with('permissions')->get();
                $render =  view('role.table', compact('roles'))->render();

                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            }

            return  view('role.index');
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, 'something wrong');
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

            return $this->buildRes->RESPONSE_REQ('error', null, 'something wrong');
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
                    'guard_name' => 'web',
                    'is_default' => 0,
                ]);
                $role->syncPermissions($role_data['roles']);

                return $this->buildRes->RESPONSE_REQ('success', null,  'Add role succesfully');
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
        if (!auth()->user()->can('access-control.update')) {
            abort(403, 'Unauthorized action.');
        }
    }

     /**
     * Display the specified resource.
     *
     * @param  int  $user
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit($accessControl, Request $request)
    {
        if (!auth()->user()->can('access-control.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
        //     $business_id = request()->session()->get('user.business_id');
        // $role = Role::where('business_id', $business_id)
        //             ->with(['permissions'])
        //             ->find($id);
        // $role_permissions = [];
        // foreach ($role->permissions as $role_perm) {
        //     $role_permissions[] = $role_perm->name;
        // }

        // $selling_price_groups = SellingPriceGroup::where('business_id', $business_id)
        //                             ->active()
        //                             ->get();

        // $module_permissions = $this->moduleUtil->getModuleData('user_permissions');

        // $common_settings = !empty(session('business.common_settings')) ? session('business.common_settings') : [];

        // return view('role.edit')
        //     ->with(compact('role', 'role_permissions', 'selling_price_groups', 'module_permissions', 'common_settings'));
            Log::info($accessControl);
            // $roles = Role::all();
            // $user = app(Services::class)->findUserByIdWith($user, ['roles']);
            // $render = view('manage_user.edit', compact('roles', 'user'))->render();

            // return $this->buildRes->RESPONSE_REQ('success', $render, null);
        } catch (\Exception $error) {
            return $this->buildRes->RESPONSE_REQ('error', null, 'something wrong');
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
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('access-control.delete')) {
            abort(403, 'Unauthorized action.');
        }
    }
}
