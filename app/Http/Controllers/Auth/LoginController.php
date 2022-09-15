<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\Api\ApiServices;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Session;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    private $apiService;

    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ApiServices $service)
    {
        $this->apiService = $service;
        $this->middleware('guest')->except('logout');
    }

    public function logout()
    {
        request()->session()->flush();
        auth()->logout();
        return redirect('/login');
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'username';
    }

    protected function authenticated(Request $request, User $user)
    {
        if (!$user->business->is_active) {
            Auth::logout();
            return redirect('/login');
        } elseif ($user->status != 'active') {
            Auth::logout();
            return redirect('/login');
        }

        // $res = $this->apiService->get_token_zkteco();
        // Session::put('token_zkteco', $res['token']);
        Session::put('business_id', $user->business->id);
        // try {
        //     $is_error_message = [];
        //     $res = $this->apiService->get_token_zkteco();
        //     if ($res && $res->status != 'error') {
        //         if (!$user->business->is_active) {
        //             $is_error_message = ['business_incative' => ['Inactive bussiness']];
        //         } elseif ($user->status != 'active') {
        //             $is_error_message = ['user_incative' => ['Inactive user']];
        //         } else {
        //             Session::put('token_zkteco', $res->data->token);
        //             Session::put('business_id', $user->business->id);
        //         }
        //     } else {
        //         $is_error_message = $res->msg;
        //     }
        // } catch (\Exception $e) {
        //     Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
        //     $is_error_message = ['something_wrong' => ['Something wrong']];
        // }

        // if ($is_error_message) {
        //     Auth::logout();
        //     return $this->buildRes->RESPONSE_REQ('error', null, $is_error_message);
        // } else {
        //     return $this->buildRes->RESPONSE_REQ('success', null, 'Login success');
        // }
    }
}
