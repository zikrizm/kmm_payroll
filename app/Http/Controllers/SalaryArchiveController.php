<?php

namespace App\Http\Controllers;

use App\Utils\Util;
use App\Models\Business;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;
use App\Models\FoodArchiveTd;
use App\Models\FoodArchiveTh;
use Illuminate\Support\Carbon;
use App\Models\SalaryArchiveTd;
use App\Models\SalaryArchiveTh;
use App\Models\FoodArchiveTdEmp;
use App\Services\Api\ApiServices;
use App\Models\SalaryArchiveTdEmp;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Models\FoodArchiveTdEmpAttendance;
use App\Models\SalaryArchiveTdEmpAttendance;

class SalaryArchiveController extends Controller
{
    private $buildRes;
    private $service;
    private $util;

    public function __construct(Util $util, ApiServices $service, ResponseUtil $buildRes)
    {
        $this->util = $util;
        $this->service = $service;
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
        if (!auth()->user()->can('salary-archive.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = Session::get('business_id');
            if (request()->ajax()) {
                $salary_archives = SalaryArchiveTh::where('business_id', $business_id)->whereDate('start_date_work_day', '<=', $request->start_date)->whereDate('end_date_work_day', '>=', $request->end_date);

                if ($request->has('q')) {
                    $search = $request->q;
                    $salary_archives = $salary_archives->where(function ($q) use ($search) {
                        $q->where('dept_name', 'LIKE', "%" . $search . "%")->where('dept_code', 'LIKE', "%" . $search . "%");
                    });
                }

                $salary_archives = $salary_archives->with(['salary_archive_tds' => function ($query) {
                    $query->orderBy('created_at', 'desc'); 
                }])
                ->orderBy('start_date_work_day', 'ASC')
                ->orderBy('created_at', 'ASC')
                ->paginate(10);
                $render = view('report.salary_archive.table', compact('salary_archives'))->render();

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
    public function get_re_calculate(Request $request)
    {
        // if (!auth()->user()->can('salary-archive.re-calculate') || !request()->ajax()) {
        //     abort(403, 'Unauthorized action.');
        // }

        try {
            $salary_archive = SalaryArchiveTh::where('id', $request->salary_id)->first();

            $start_date = Carbon::parse($salary_archive->start_date_work_day);
            $end_date = Carbon::parse($salary_archive->end_date_work_day);
            $dates = $this->util->generateDateRange($start_date, $end_date);
            $department_code = $salary_archive->dept_code;
            $departments = $this->service->get_departments(['page_size' => 999, 'dept_code' => $department_code])['data'];

            if (!empty($salary_archive)) {
                $render = view('report.salary_archive.modals.re_calculation', compact('dates', 'salary_archive', 'start_date', 'end_date',  'departments', 'department_code'))->render();
                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            } else {
                $render = view('report.salary_archive.modals.contents.empty_salary_archive')->render();
                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            }
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
    public function save_re_calculate(Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('salary-archive.view')  || !request()->ajax()) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $salary_archive = SalaryArchiveTd::where('id', $id)->with(['salary_archive_td_emps', 'salary_archive_td_emps.salary_archive_td_emp_attendances'])->first();
            $render = view('report.salary_archive.show', compact('salary_archive'))->render();
            return $this->buildRes->RESPONSE_REQ('success', $render, null);
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }
}
