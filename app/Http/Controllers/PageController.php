<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{

    public function index(Request $request)
    {
        switch ($request->get('type')) {
            case 'user':
                return view('page.user_management.user.index');
                break;
            case 'access_control':
                return view('page.user_management.access_control.index');
                break;
        }
    }

    public function handle($request, \Closure $next)
    {
        if (!$request->ajax()) {
            return response('Forbidden.', 403);
        }

        return $next($request);
    }
}
