<?php

namespace App\Http\Controllers;

use App\Utils\Util;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;
use App\Models\FoodBillArchive;
use App\Services\Api\ApiServices;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;


class FoodArchiveController extends Controller
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
        if (!auth()->user()->can('food-archive.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = Session::get('business_id');
            if (request()->ajax()) {
                $food_archives = FoodBillArchive::whereBetween('date', [$request->start_date, $request->end_date])->get();

                if ($request->has('q')) {
                    $search = $request->q;
                    $food_archives = $food_archives->where(function ($q) use ($search) {
                        $q->where('dept_code', 'LIKE', "%" . $search . "%")->orWhere('dept_name', 'LIKE', "%" . $search . "%")
                            ->orWhere('first_name', 'LIKE', "%" . $search . "%")->orWhere('last_name', 'LIKE', "%" . $search . "%")
                            ->orWhere('total', 'LIKE', "%" . $search . "%");
                    });
                }
                $order = null;
                if ($request->has('sort')) {
                    $sort = $request->sort;
                    $order = $sort['order'];
                }
                $food_archives = $food_archives->orderBy('date', 'ASC')->orderBy('dept_id', 'ASC')->paginate(10);
                $render =  view('report.food_archive.table', compact('food_archives', 'order'))->render();

                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            }

            return  view('report.food_archive.index');
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    // /**
    //  * Display the specified resource.
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function show(Request $request)
    // {
    //     if (!auth()->user()->can('food-archive.view') || !request()->ajax()) {
    //         abort(403, 'Unauthorized action.');
    //     }

    //     try {
    //         $validator = Validator::make($request->all(), [
    //             'emp_id' => 'required|string|max:255',
    //             'start_date' => 'required|date_format:Y-m-d|before:end_date',
    //             'end_date' => 'required|date_format:Y-m-d|after:start_date',
    //         ]);
    //         if ($validator->fails()) {
    //             return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
    //         } else {
    //             $food_archives = FoodBillArchive::where('emp_id', $request->emp_id)
    //                 ->whereBetween('date', [$request->start_date, $request->end_date])
    //                 ->get()->orderBy('date', 'ASC');
    //         }
    //     } catch (\Exception $e) {
    //         Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

    //         return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
    //     }
    // }
}
