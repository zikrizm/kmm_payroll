<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserAccountBank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;


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
        Log::info($request);


        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:users',
                'email' => 'required|string|email:rfc,dns',
                // 'phone' => 'required|string|min:11',
                'password' => 'required|string|min:6',
                // 'gender' => 'required|string',
                'role' => 'string|exists:roles,id',
                'status' => 'required|string',
                // 'account_name' => 'string',
                // 'account_number' => 'string',
                // 'bank_name' => 'string',
                // 'branch' => 'string',
                // 'salary' => 'required|string',
                // 'pay_periodic' => 'required|string',
            ]);
            $messages = $validator->messages();
            if ($messages) {
                throw new \Exception($validator->messages());
            } else {
                $user = new User();
                $user->name = $request->name;
                $user->username = $request->username;
                $user->email = $request->email;
                // $user->phone = $request->phone;
                // $user->gender = $request->gender;
                $user->status = $request->status;
                $user->password =  Hash::make($request->password);
                $user->save();

                Log::info($user);
            }
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
    public function show()
    {
        return view('page.user_management.asides.aside_user_management');
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
