<?php

namespace App\Http\Controllers;

use App\Utils\Util;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;
use App\Models\FoodArchive;
use App\Services\Api\ApiServices;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;


class FoodArchiveController extends Controller
{
    private $buildRes;

    public function __construct(ResponseUtil $buildRes)
    {
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
        if (!auth()->user()->can('food-archive.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = Session::get('business_id');
            if (request()->ajax()) {
                $food_archives = FoodArchive::whereDate('start_date', '<=', $request->start_date)
                    ->whereDate('end_date', '>=', $request->end_date);
                if ($request->has('q')) {
                    $search = $request->q;
                    $food_archives = $food_archives->where(function ($q) use ($search) {
                        $q->where('dept_code', 'LIKE', "%" . $search . "%")->orWhere('dept_name', 'LIKE', "%" . $search . "%")
                            ->orWhere('total', 'LIKE', "%" . $search . "%");
                    });
                }
                $food_archives = $food_archives->orderBy('start_date', 'ASC')->orderBy('dept_id', 'ASC')->paginate(10);
                $render =  view('report.food_archive.table', compact('food_archives'))->render();

                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            }

            return  view('report.food_archive.index');
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('food-archive.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $food_archive = FoodArchive::where('id', $id)->with(['food_archive_emps', 'food_archive_emps.food_archive_emp_attendances'])->first();
            $render =  view('report.food_archive.show', compact('food_archive'))->render();

            return $this->buildRes->RESPONSE_REQ('success', $render, null);
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }
}
