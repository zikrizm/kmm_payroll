<?php

namespace App\Http\Controllers;

use App\Utils\Util;
use App\Models\Business;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;
use App\Models\SalaryArchive;
use Illuminate\Support\Carbon;
use App\Models\SalaryArchiveEmp;
use App\Services\Api\ApiServices;
use App\Models\SalaryArchiveEmpAtt;
use App\Models\SalaryArchiveEmpAttLb;
use App\Models\SalaryArchiveEmpAttOp;
use App\Models\SalaryArchiveEmpAttTso;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class PayrollReportController extends Controller
{
    private $service;
    private $buildRes;
    private $util;

    public function __construct(ApiServices $service, Util $util, ResponseUtil $buildRes)
    {
        $this->service = $service;
        $this->buildRes = $buildRes;
        $this->util = $util;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        Log::info('[' . request()->route()->getName() . ']::GET');
        if (!auth()->user()->can('payroll-report.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            if (request()->ajax()) {
                $datas = collect([]);
                $deparment_code = null;
                $start_date = null;
                $end_date = null;

                $business_id = Session::get('business_id');
                $business = Business::where('id', $business_id)->select('id', 'pending_day')->first();

                if ($request->has('deparment_code')) {
                    $deparment_code = $request['deparment_code'];
                }

                if ($request->has('start_date') && $request->has('end_date')) {
                    $start_date = Carbon::createFromFormat('d-m-Y', $request['start_date']);
                    $end_date = Carbon::createFromFormat('d-m-Y', $request['end_date']);

                    $start_date_work_day = Carbon::createFromFormat('d-m-Y', $request['start_date']);
                    $end_date_work_day = Carbon::createFromFormat('d-m-Y', $request['end_date']);
                    $start_date_overtime = Carbon::createFromFormat('d-m-Y', $request['start_date'])->subDays($business->pending_day);
                    $end_date_overtime = Carbon::createFromFormat('d-m-Y', $request['end_date'])->subDays($business->pending_day);

                    $datas = app(PrintReportContoller::class)->getAttendanceData(
                        $start_date_work_day,
                        $end_date_work_day,
                        $start_date_overtime,
                        $end_date_overtime,
                        $deparment_code,
                    );

                    foreach ($datas as $data) {
                        foreach ($data['attendance_reports'] as $report) {
                            $report['attendances'] = $report['attendances']->map(function ($e) {
                                return app(PrintReportContoller::class)->buildValueHtml($e);
                            });
                        }
                    }

                    $render = view('report.payroll_report.table', compact('datas', 'start_date_work_day', 'end_date_work_day', 'start_date_overtime', 'end_date_overtime'))->render();
                    return $this->buildRes->RESPONSE_REQ('success', $render, null);
                } else {
                    $render = view('report.payroll_report.table', compact('datas', 'start_date_work_day', 'end_date_work_day', 'start_date_overtime', 'end_date_overtime'))->render();
                    return $this->buildRes->RESPONSE_REQ('success', $render, null);
                }
            }

            $department_bios = collect($this->service->get_departments(['page_size' => 999])['data']);
            return  view('report.payroll_report.index', compact('department_bios'));
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
        if (!auth()->user()->can('payroll-report.calculate')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $start_date = Carbon::parse($request->start_date);
            $end_date = Carbon::parse($request->end_date);
            $dates = $this->util->generateDateRange($start_date, $end_date);
            $departments = $this->service->get_departments(['page_size' => 999])['data'];

            $render = view('report.payroll_report.calculation', compact('dates', 'departments'))->render();
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
        if (!auth()->user()->can('payroll-report.calculate')  || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $datas = collect([]);
            $deparment_code = null;
            $start_date = null;
            $end_date = null;

            $business_id = Session::get('business_id');
            $business = Business::where('id', $business_id)->select('id', 'pending_day')->first();

            if ($request->has('deparment_code')) {
                $deparment_code = $request['deparment_code'];
            }

            if ($request->has('start_date') && $request->has('end_date')) {
                $start_date = Carbon::createFromFormat('d-m-Y', $request['start_date']);
                $end_date = Carbon::createFromFormat('d-m-Y', $request['end_date']);

                $start_date_work_day = Carbon::createFromFormat('d-m-Y', $request['start_date']);
                $end_date_work_day = Carbon::createFromFormat('d-m-Y', $request['end_date']);
                $start_date_overtime = Carbon::createFromFormat('d-m-Y', $request['start_date'])->subDays($business->pending_day);
                $end_date_overtime = Carbon::createFromFormat('d-m-Y', $request['end_date'])->subDays($business->pending_day);

                $datas = app(PrintReportContoller::class)->getAttendanceData(
                    $start_date_work_day,
                    $end_date_work_day,
                    $start_date_overtime,
                    $end_date_overtime,
                    $deparment_code,
                );

                foreach ($datas as $data) {
                    $salary_archive = new SalaryArchive([
                        'business_id' => $business_id,
                        'start_date' => $data['start_date'],
                        'end_date' => $data['end_date'],
                        'start_date_work_day' => $data['start_date_work_day'],
                        'end_date_work_day' =>  $data['end_date_work_day'],
                        'start_date_overtime' => $data['start_date_overtime'],
                        'end_date_overtime' => $data['start_date_overtime'],
                        'dept_id' => $data['department']['id'],
                        'dept_code' => $data['department']['dept_code'],
                        'dept_name' =>  $data['department']['dept_name'],
                        'total_HK_value' => $data['total_HK_value'],
                        'total_JL_value' => $data['total_JL_value'],
                        'total_kasbon_pay_value' => $data['total_kasbon_pay_value'],
                        'total_salary_pay_value' => $data['total_salary_pay_value'],
                        'total_overtime_pay_value' => $data['total_overtime_pay_value'],
                        'total_tbhn_u_position_pay_value' => $data['total_tbhn_u_position_pay_value'],
                        'total_tbhn_u_libur_pay_value' => $data['total_tbhn_u_libur_pay_value'],
                        'grand_total_pay_value' => $data['grand_total_pay_value'],
                        'created_user' => auth()->user()->id,
                        'updated_user' => auth()->user()->id,
                    ]);
                    foreach ($data['attendance_reports'] as $report) {
                        $salary_archive_emp = new SalaryArchiveEmp([
                            'salary_archive_id' => $salary_archive->id,
                            'HK_value' => $data['HK_value'],
                            'JL_value' => $data['JL_value'],
                            'emp_id' => $data['employee']['id'],
                            'emp_code' => $data['employee']['emp_code'],
                            'first_name' =>  $data['employee']['first_name'],
                            'last_name' =>  $data['employee']['last_name'],
                            'photo' =>  $data['employee']['photo'],
                            'kasbon_pay_value' => $data['kasbon_pay_value'],
                            'remaining_kasbon_pay_value' => $data['remaining_kasbon_pay_value'],
                            'salary_pay_value' => $data['salary_pay_value'],
                            'overtime_pay_value' => $data['overtime_pay_value'],
                            'tbhn_u_position_pay_value' => $data['tbhn_u_position_pay_value'],
                            'tbhn_u_libur_pay_value' => $data['tbhn_u_libur_pay_value'],
                            'total_pay_value' => $data['total_pay_value'],
                        ]);
                        foreach ($report['attendances'] as $attendance) {
                            $salary_archive_emp_att = new SalaryArchiveEmpAtt([
                                'salary_archive_emp_id' => $salary_archive_emp->id,
                                'timetable_id' => (!empty($data['timetable'])) ? $data['timetable']['id'] : null,
                                'attendance_date' => $data['attendance_date'],
                                'first_punch' => $data['first_punch'],
                                'last_punch' => $data['last_punch'],
                                'JL' => $data['JL'],
                                'HK' => $data['HK'],
                                'be_one_shift' => $data['be_one_shift'],
                                'HK_pay_value' => $data['HK_pay_value'],
                                'JL_pay_value' => $data['JL_pay_value'],
                                'tbhn_u_libur_pay_value' => $data['tbhn_u_libur_pay_value'],
                                'be_one_shift' => $data['be_one_shift'],
                                'is_holiday' => $data['is_holiday'],
                                'is_addition_date' => $data['is_addition_date'],
                                'is_counting_salary' => $data['is_counting_salary'],
                                'is_counting_overtime' => $data['is_counting_overtime'],
                            ]);

                            $salary_archive_emp_att_op = new SalaryArchiveEmpAttOp([
                                'salary_archive_emp_att_id' => $salary_archive_emp_att->id,
                                'operational_id' => (!empty($data['operational'])) ? $data['operational']['id'] : null,
                                'status' => (!empty($data['operational'])) ? $data['operational']['status'] : null,
                                'plusm_value' => (!empty($data['operational'])) ? $data['operational']['plusm_value'] : null,
                                'note' => (!empty($data['operational'])) ? $data['operational']['note'] : null,
                            ]);
                            $salary_archive_emp_att_lb = new SalaryArchiveEmpAttLb([
                                'salary_archive_emp_att_id' => $salary_archive_emp_att->id,
                                'attendance_lb_id' => (!empty($data['lb_status'])) ? $data['lb_status']['id'] : null,
                                'attendance_lb_date' => (!empty($data['lb_status'])) ? $data['lb_status']['ld_date'] : null,
                                'note' => (!empty($data['lb_status'])) ? $data['lb_status']['note'] : null,
                            ]);
                            $salary_archive_emp_att_tso = new SalaryArchiveEmpAttTso([
                                'salary_archive_emp_att_id' => $salary_archive_emp_att->id,
                                'attendance_tso_id' => (!empty($data['tso'])) ? $data['tso']['id'] : null,
                            ]);
                        }
                    }
                }
            } else {
            }

            // Log::info(response()->json($data_rices));
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    function _group_by_date($array)
    {
        $return = array();
        foreach ($array as $val) {
            $date = Carbon::parse($val['punch_time'])->format('Y-m-d');
            if (!empty($date)) {
                $return[$date][] = $val;
                usort($return[$date], function ($a, $b) {
                    return strtotime($a['punch_time']) - strtotime($b['punch_time']);
                });
            }
        }
        return $return;
    }

    /**
     * Rules validation group.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'holiday_date' => 'required',
        ];
    }
}
