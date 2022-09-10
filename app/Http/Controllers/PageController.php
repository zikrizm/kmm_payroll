<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;

class PageController extends Controller
{
    // public function index(Request $request)
    // {
    //     switch ($request->get('type')) {
    //         case 'user':
    //             $users = User::with('roles')->get();
    //             return view('page.user_management.user.index', compact('users'));
    //             break;
    //         case 'access_control':
    //             $roles = Role::with('permissions')->get();
    //             return view('page.user_management.access_control.index', compact('roles'));
    //             break;
    //     }
    // }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function userManagement()
    {
        $users = User::with('roles')->get();
        return view('page.user_management', compact('users'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function employeeManagement()
    {
        return view('page.user_management');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function companyProfile()
    {
        return view('page.user_management');
    }


    // public function handle($request, \Closure $next)
    // {
    //     if (!$request->ajax()) {
    //         return response('Forbidden.', 403);
    //     }

    //     return $next($request);
    // }
}
