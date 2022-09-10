<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Contracts\Support\MessageBag;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Illuminate\Routing\Controller;
use LDAP\Result;

class UserManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('user.view')) {
            abort(403, 'Unauthorized action.');
        }
        // return view('page.user_management.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('user.create')) {
            abort(403, 'Unauthorized action.');
        }
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
            $validator = Validator::make($request->all(), $this->rulesCreate());

            $response = [];
            if ($validator->fails()) {
                $response = ['error' => $validator->errors(), 'data' => null];
            } else {
                $role = Role::where('id', $request->role)->first();
                if ($role) {
                    $user = new User();
                    $user->name = $request->name;
                    $user->username = $request->username;
                    $user->email = $request->email;
                    $user->status = $request->status;
                    $user->password =  Hash::make($request->password);
                    $user->save();

                    $user->assignRole($role->name);
                    $response = ['error' => null, 'data' => null];
                } else {
                    $response = ['error' => ['role' => 'Role not found'], 'data' => null];
                }
            }

            return response()->json($response);
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
                    $role_name = $request->role_name;
                    $roles_permissions = $request->roles;

                    $role = Role::create([
                        'name' => $role_name,
                        'guard_name' => 'web',
                        'is_default' => 0,
                    ]);
                    $role->syncPermissions($roles_permissions);

                    $response = ['error' => null, 'data' => null];
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
    public function create(Request $request)
    {
        if (!auth()->user()->can('user.view')) {
            abort(403, 'Unauthorized action.');
        }

        $type = $request->get('type');
        switch ($type) {
            case 'user':
                $roles = Role::all();
                return view('page.user_management.user.create', compact('type', 'roles'));
                break;
            case 'access_control':
                return view('page.user_management.access_control.create', compact('type'));
                break;
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
        if (!auth()->user()->can('user.view')) {
            abort(403, 'Unauthorized action.');
        }

        $type = $request->get('type');
        $id = $request->get('id');
        switch ($type) {
            case 'user':
                $roles = Role::all();
                $user = User::with('roles')->where('id', $id)->first();
                return view('page.user_management.user.edit', compact('type', 'roles', 'user'));
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
    public function update(Request $request)
    {
        if (!auth()->user()->can('user.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validator = Validator::make($request->all(), $this->rulesUpdate());

            $response = [];
            if ($validator->fails()) {
                $response = ['error' => $validator->errors(), 'data' => null];
            } else {

                $emailInvalid = $this->checkEmailUsername($request);
                if (count($emailInvalid) == 0) {
                    $user_data = $request->only(['user_id', 'name', 'status', 'email', 'username', 'password', 'role']);

                    if (!empty($request->input('password'))) {
                        $user_data['password'] = Hash::make($request->input('password'));
                    }

                    $user = User::findOrFail($request->input('user_id'));

                    Log::info($user);

                    $user->update($user_data);
                    $role_id = $request->input('role');
                    $user_role = $user->roles->first();
                    $previous_role = !empty($user_role->id) ? $user_role->id : 0;
                    if ($previous_role != $role_id) {
                        if (!empty($previous_role)) {
                            $user->removeRole($user_role->name);
                        }

                        $role = Role::findOrFail($role_id);
                        $user->assignRole($role->name);
                    }

                    $response = ['error' => null, 'data' => null];
                } else {
                    $response = ['error' => $emailInvalid, 'data' => null];
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
    public function delete(Request $request)
    {
        if (!auth()->user()->can('user.delete')) {
            abort(403, 'Unauthorized action.');
        }

        $type = $request->get('type');
        $id = $request->get('id');
        switch ($type) {
            case 'user':
                $user = User::where('id', $id)->first();
                return view('page.user_management.user.delete', compact('type', 'user'));
                break;
            case 'access_control':
                return view('page.user_management.access_control.create', compact('type'));
                break;
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if (!auth()->user()->can('user.delete')) {
            abort(403, 'Unauthorized action.');
        }
        User::where('id', $request->user_id)->delete();
        return response()->json(['error' => null, 'data' => null]);
    }

    public function checkEmailUsername(Request $request)
    {
        $user = User::where('id', $request->user_id)->first();
        $isSameEmail = $user->email != $request->email;
        $isSameUsername = $user->username != $request->username;
        $error = [];
        if ($isSameEmail || $isSameUsername) {
            $sameEmail = User::where('email',  $request->email)->orWhere('username', $request->username)->first();
            if ($sameEmail) {
                $isSameEmail = $sameEmail->email == $request->email;
                $isSameUsername = $sameEmail->username == $request->username;
                if ($isSameEmail) {
                    $error['email'] = ['0' => 'The email has already been taken.'];
                }
                if ($isSameUsername) {
                    $error['username'] = ['0' => 'The username has already been taken.'];
                }
            }
        }
        return $error;
    }

    public function rulesCreate()
    {
        return [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email:rfc,dns|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'string|exists:roles,id',
            'status' => 'required|string',
        ];
    }

    public function rulesUpdate()
    {
        return [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'email' => 'required|string|email:rfc,dns',
            'password' => 'required|string|min:6',
            'role' => 'string|exists:roles,id',
            'status' => 'required|string',
        ];
    }
}
