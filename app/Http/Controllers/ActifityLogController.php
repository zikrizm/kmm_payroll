<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Utils\BusinessUtil;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;
use App\Services\Api\ApiServices;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ActifityLogController extends Controller
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
        try {
            if (request()->ajax()) {
                $business_id = Session::get('business_id');
                $activity_logs = ActivityLog::where('business_id', $business_id);

                if ($request->has('date')) {
                    $activity_logs = $activity_logs->whereBetween('date', [$request['date']['start_date'], $request['date']['end_date']]);
                }
                if ($request->has('q')) {
                    $search = $request->q;
                    $activity_logs = $activity_logs->whereHas('user', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%" . $search . "%")->orWhere('action', 'LIKE', "%" . $search . "%")
                            ->orWhere('description', 'LIKE', "%" . $search . "%");
                    });
                }


                $order = null;
                if ($request->has('sort')) {
                    $sort = $request->sort;
                    $order = $sort['order'];
                    $activity_logs->orderBy($sort['name'], $sort['order']);
                } else {
                    $activity_logs->orderBy('date', 'DESC');
                }
                $activity_logs = $activity_logs->paginate(10);
                $render =  view('Report.activity_log.table', compact('activity_logs', 'order'))->render();

                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            }

            return  view('Report.activity_log.index');
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }
}
