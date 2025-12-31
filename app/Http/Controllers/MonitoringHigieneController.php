<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Api\ApiServices;
use App\Utils\ResponseUtil;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Carbon;
use Carbon\CarbonPeriod;

class MonitoringHigieneController extends Controller
{
    private $service;
    private $buildRes;

    public function __construct(ApiServices $service, ResponseUtil $buildRes)
    {
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
        if (!auth()->user()->can('payroll-report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = Session::get('business_id');
        $department_code = $request['department_code'];
        $start_date = $request['start_date'];
        $end_date = $request['end_date'];
        $check_all = $request['check_all'];

        try {
            if (request()->ajax()) {
                $start_date = Carbon::createFromFormat('d-m-Y', $request['start_date']);
                $end_date = Carbon::createFromFormat('d-m-Y', $request['end_date']);

                if ($start_date->year === $end_date->year) {
                    $year = (string) $start_date->year;
                } else {
                    $year = $start_date->year . ' - ' . $end_date->year;
                }
                
                Carbon::setLocale('id');
                $range_dates =  collect(CarbonPeriod::create($start_date, $end_date))->map(function ($date) {
                    $dayOfWeek = $date->dayOfWeek;
                
                    return collect([
                        'date' => $date->translatedFormat('l, d-M-Y'),
                    ]);
                });

                $department_bios = null;
                if ($department_code) {
                    $department_bios = collect($this->service->get_departments(["dept_code" => $department_code])['data'])->first();
                }

                if (!empty($department_bios)) {
                    $page_size = $this->service->get_employees(["department" => $department_bios['id']])["count"];
                    $employees = collect($this->service->get_employees(["page_size" => $page_size, "department" => $department_bios['id']])['data']);
                } else {
                    $page_size = $this->service->get_employees([])["count"];
                    $employees = collect($this->service->get_employees(["page_size" => $page_size])['data']);
                }
                
                $render = view('Report.monotoring_higiene.table', compact('range_dates', 'year', 'check_all', 'department_bios', 'employees'))->render();
                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            }

            $department_bios = collect($this->service->get_departments(['page_size' => 999])['data']);
            return  view('Report.monotoring_higiene.index', compact('department_bios', 'department_code', 'start_date', 'end_date'));
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }
}
