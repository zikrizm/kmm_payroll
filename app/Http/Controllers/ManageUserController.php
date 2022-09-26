<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Services;
use App\Utils\BusinessUtil;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;
use App\Services\Api\ApiServices;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ManageUserController extends Controller
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
        if (!auth()->user()->can('user.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            if (request()->ajax()) {
                $business_id = Session::get('business_id');
                $users = User::where('business_id', $business_id);
                Log::info("Sdfsdfsdfsdf");
                if ($request->has('q')) {
                    $search = $request->q;
                    $users = $users->where(function ($q) use ($search) {
                        $q->where('first_name', 'LIKE', "%" . $search . "%")->orWhere('username', 'LIKE', "%" . $search . "%")
                            ->orWhere('email', 'LIKE', "%" . $search . "%");
                    });
                }
                $order = null;
                if ($request->has('sort')) {
                    $sort = $request->sort;
                    $order = $sort['order'];
                    $users->orderBy($sort['name'], $sort['order']);
                }
                $users = $users->paginate(10);
                $render =  view('manage_user.table', compact('users', 'order'))->render();

                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            }

            $roles = Role::all();
            return  view('manage_user.index', compact('roles'));
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, 'something wrong');
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
        if (!auth()->user()->can('user.create') || !request()->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = Session::get('business_id');
            $roles = Role::where('business_id', $business_id)->get();
            $render = view('manage_user.create', compact('roles'))->render();

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
        if (!auth()->user()->can('user.create')  || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $validator = Validator::make($request->all(), $this->rules('POST', null));

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $user_data = $request->only(['surname', 'first_name', 'last_name', 'status', 'email', 'username', 'password', 'role']);
                $role = app(Services::class)->findRoleById($request->role);
                if (!empty($request->input('password'))) {
                    $user_data['password'] = Hash::make($request->input('password'));
                }

                $user_data['business_id'] = Session::get('business_id');

                // upload logo
                $photo_profile = $this->businessUtil->uploadFile($request, 'photo', 'profiles', 'image');
                if (!empty($photo_profile)) {
                    $user_data['photo'] = $photo_profile;
                }
                $user = new User($user_data);
                $user->save();

                $user->assignRole($role->name);
                return $this->buildRes->RESPONSE_REQ('success', null,  'Add user succesfully');
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
        if (!auth()->user()->can('user.view')) {
            abort(403, 'Unauthorized action.');
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  User $user
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user, Request $request)
    {
        if (!auth()->user()->can('user.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = Session::get('business_id');
            $roles = Role::where('business_id', $business_id)->get();
            $render = view('manage_user.edit', compact('roles', 'user'))->render();

            return $this->buildRes->RESPONSE_REQ('success', $render, null);
        } catch (\Exception $error) {
            return $this->buildRes->RESPONSE_REQ('error', null, 'something wrong');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(User $user, Request $request)
    {
        if (!auth()->user()->can('user.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validator = Validator::make($request->all(), $this->rules("PUT", $user));

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $user_data = $request->heonly(['surname', 'first_name', 'last_name', 'status', 'email', 'username', 'password', 'role']);

                if (!empty($request->input('password'))) {
                    $user_data['password'] = Hash::make($request->input('password'));
                }

                // upload logo
                Log::info($request);
                $photo_profile = $this->businessUtil->uploadFile($request, 'photo', 'profiles', 'image');
                if (!empty($photo_profile)) {
                    $user_data['photo'] = $photo_profile;
                }

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

                return $this->buildRes->RESPONSE_REQ('success', null, 'user update succesfully');
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, 'something wrong');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  User  $user
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user, Request $request)
    {
        if (!auth()->user()->can('user.delete') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $user->delete();

            return $this->buildRes->RESPONSE_REQ('success', null, 'user delete succesfully');
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, 'something wrong');
        }
    }

    /**
     * Rules validation user.
     *
     * @param  string  $method
     * @param  User $idUser
     * @return array
     */
    public function rules($method, $user)
    {

        switch ($method) {
            case 'GET':
            case 'DELETE':
            case 'PATCH':
            case 'POST': {
                    return [
                        'first_name' => 'required|string|max:255',
                        'username' => 'required|string|max:255|unique:users',
                        'email' => 'required|string|email:rfc,dns|unique:users',
                        'password' => 'required|string|min:6',
                        'role' => 'string|exists:roles,id',
                        'status' => 'required|string',
                        'image' => 'image|file|max:2000',
                    ];
                }
                break;
            case 'PUT':
                return [
                    'first_name' => 'required|string|max:255',
                    'username' => 'required|string|max:255|unique:users,username,' . $user->id,
                    'email' => 'required|string|email:rfc,dns|unique:users,email,' . $user->id,
                    'password' => 'required|string|min:6',
                    'role' => 'string|exists:roles,id',
                    'status' => 'required|string',
                    'image' => 'image|file|max:2000',
                ];
                break;
            default:
                break;
        }
    }
}
