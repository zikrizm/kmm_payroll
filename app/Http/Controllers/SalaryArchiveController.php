<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SalaryArchive;
use App\Utils\BusinessUtil;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;
use App\Services\Api\ApiServices;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class SalaryArchiveController extends Controller
{
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
        // if (!auth()->user()->can('salary-archive.view')) {
        //     abort(403, 'Unauthorized action.');
        // }

        try {
            $business_id = Session::get('business_id');
            if (request()->ajax()) {
                $salary_archives = SalaryArchive::where('business_id', $business_id);

                if ($request->has('q')) {
                    $search = $request->q;
                }
                $order = null;
                if ($request->has('sort')) {
                    $sort = $request->sort;
                    $order = $sort['order'];
                }
                $salary_archives = $salary_archives->paginate(10);
                $render =  view('report.salary_archive.table', compact('salary_archives', 'order'))->render();

                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            }

            return  view('report.salary_archive.index');
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
        if (!auth()->user()->can('salary-archive.create') || !request()->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = Session::get('business_id');
            $roles = Role::where('business_id', $business_id)->get();
            $render = view('User.manage_user.create', compact('roles'))->render();

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
        if (!auth()->user()->can('salary-archive.create')  || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }
        try {
            
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Rules validation user.
     *
     * @param  User $user
     * @return array
     */
    public function rules($user)
    {
        return [
            'name' => 'required|string|max:255',
            'username' => (empty($user)) ?  'required|string|max:255|unique:users' : 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => (empty($user)) ? 'required|string|email:rfc,dns|unique:users' : 'required|string|email:rfc,dns|unique:users,email,' . $user->id,
            'password' => 'required|string|min:6',
            'role' => 'required|exists:roles,id',
            'status' => 'required|string',
            'image' => 'image|file|max:2000',
        ];
    }
}
