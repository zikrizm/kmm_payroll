<?php

namespace App\Http\Controllers;

use App\Models\OtRiceBill;
use App\Utils\BusinessUtil;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;
use App\Services\Api\ApiServices;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class OtRiceBillController extends Controller
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
        if (!auth()->user()->can('ot-rice-bill.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = Session::get('business_id');
            if (request()->ajax()) {
                $ot_rice_bills = OtRiceBill::whereDate('start_date', '<=', $request->start_date)->whereDate('end_date', '>=',  $request->end_date);

                if ($request->has('q')) {
                    $search = $request->q;
                    $ot_rice_bills = $ot_rice_bills->where(function ($q) use ($search) {
                        $q->where('dept_code', 'LIKE', "%" . $search . "%")->orWhere('dept_name', 'LIKE', "%" . $search . "%")
                            ->orWhere('total', 'LIKE', "%" . $search . "%");
                    });
                }
                $order = null;
                if ($request->has('sort')) {
                    $sort = $request->sort;
                    $order = $sort['order'];
                }
                $ot_rice_bills = $ot_rice_bills->paginate(10);
                $render =  view('report.ot_rice_bill.table', compact('ot_rice_bills', 'order'))->render();

                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            }

            return  view('report.ot_rice_bill.index');
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('ot-rice-bill.view') || !request()->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $ot_rice_bills = OtRiceBill::where('id', $id)->with('ot_rice_bill_perdays')->get();

        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }
}
