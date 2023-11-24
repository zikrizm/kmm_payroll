<?php

namespace App\Http\Controllers;

use App\Models\FoodArchiveTd;
use App\Models\FoodArchiveTh;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;


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
                $food_archives = FoodArchiveTh::whereBetween('created_at', [$request->start_date, $request->end_date]);
                if ($request->has('q')) {
                    $search = $request->q;
                    $food_archives = $food_archives->where(function ($q) use ($search) {
                        $q->where('dept_code', 'LIKE', "%" . $search . "%")->orWhere('dept_name', 'LIKE', "%" . $search . "%");
                    });
                }
                $food_archives = $food_archives->orderBy('start_date', 'ASC')->orderBy('dept_id', 'ASC')->paginate(10);
                $render = view('report.food_archive.table', compact('food_archives'))->render();

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
            
            $total_food_perhari = [];
            $food_archive = FoodArchiveTd::where('id', $id)->with(['food_archive_th','food_archive_td_emps' => function ($query) {
                $query->with(['food_archive_td_emp_attendances']);
            }])->first()->toArray();

            if (!empty($food_archive)) {
                foreach ($food_archive['food_archive_td_emps'] as  $food_archive_td_emp) {
                    foreach ($food_archive_td_emp['food_archive_td_emp_attendances'] as $item) {
                        $foodDate = Carbon::parse($item['food_date'])->format('Y-m-d');
                        if (!empty($total_food_perhari[$foodDate])) {
                            $total_food_perhari[$foodDate]['total'] += $item['total'];
                            if (!empty($total_food_perhari[$foodDate]['employees']) && empty($total_food_perhari[$foodDate]['employees'][$food_archive_td_emp['emp_code']])) {
                                $total_food_perhari[$foodDate]['employees'][$food_archive_td_emp['emp_code']] = [
                                    'first_name' => $food_archive_td_emp['first_name'],
                                    'last_name' => $food_archive_td_emp['last_name'],
                                    'total' => $item['total'],
                                ];
                            } else {
                                $total_food_perhari[$foodDate]['employees'][$food_archive_td_emp['emp_code']]['total'] += $item['total'];
                            }
                        } else {
                            $total_food_perhari[$foodDate] = [
                                'date' => $foodDate,
                                'total' => $item['total'],
                                'employees' => [
                                    $food_archive_td_emp['emp_code'] => [
                                        'first_name' => $food_archive_td_emp['first_name'],
                                        'last_name' => $food_archive_td_emp['last_name'],
                                        'total' => $item['total'],
                                    ]
                                ]
                            ];
                        }
                    }
                }
            }

            $food_archive['total_food_perhari'] = $total_food_perhari;
            $render =  view('report.food_archive.show', compact('food_archive'))->render();

            return $this->buildRes->RESPONSE_REQ('success', $render, null);
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }
}