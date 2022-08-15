<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\UserAccountBank;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RoleHasPermission;
use Illuminate\Contracts\Support\MessageBag;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class UserManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('page.user_management.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $type = $request->get('type');
        switch ($type) {
            case 'user':
                $res = $this->storeUser($request);
                return $res;
                break;
            case 'access_control':
                $res = $this->storeAccessControl($request);
                return $res;
                break;
        }
    }

    public function storeUser(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:users',
                'email' => 'required|string|email:rfc,dns',
                'password' => 'required|string|min:6',
                'role' => 'string|exists:roles,id',
                'status' => 'required|string',
                // 'phone' => 'required|string|min:11',
                // 'gender' => 'required|string',
                // 'account_name' => 'string',
                // 'account_number' => 'string',
                // 'bank_name' => 'string',
                // 'branch' => 'string',
                // 'salary' => 'required|string',
                // 'pay_periodic' => 'required|string',
            ]);
            if ($validator->fails()) {
                throw new \Exception($validator->messages());
            } else {
                Log::info($request);
                // $user = new User();
                // $user->name = $request->name;
                // $user->username = $request->username;
                // $user->email = $request->email;
                // $user->status = $request->status;
                // $user->password =  Hash::make($request->password);
                // $user->save();

                // Log::info($user);
            }
        } catch (\Exception $error) {
            return $error->getMessage();
        }
    }

    public function storeAccessControl(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'role_name' => 'required|string|max:255',
                'roles' => 'required|array',
            ]);
            $response = [];
            if ($validator->fails()) {
                $response = ['error' => $validator->errors()->first(), 'data' => null];
            } else {
                $user = User::where('id', '1')->first();
                if ($user) {
                    $role_fields = $request->roles;
                    $role_name = $request->role_name;
                    $permissions = Permission::all(['id', 'name']);

                    $role = Role::create(['name' => $role_name]);

                    foreach ($role_fields as $keyRole => $role_field) {
                        foreach ($permissions as $permission) {
                            if ($keyRole == $permission->name) {
                                $roleHasPermission = new RoleHasPermission();
                                $roleHasPermission->role_id = $role->id;
                                $roleHasPermission->permission_id = $permission->id;

                                $roleHasPermission->save();
                            }
                        }
                    }

                } else {
                    $response = ['error' => 'User not found', 'data' => null];
                }
            }

            return response()->json($response);
        } catch (\Exception $error) {
            return $error->getMessage();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $type = $request->get('type');
        switch ($type) {
            case 'user':
                return view('page.user_management.user.create', compact('type'));
                break;
            case 'access_control':
                return view('page.user_management.access_control.create', compact('type'));
                break;
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
