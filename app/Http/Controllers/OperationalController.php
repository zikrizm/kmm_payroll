<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use App\Utils\Util;
use App\Models\Shift;
use App\Models\Operational;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;
use App\Models\OperationalDay;
use Illuminate\Support\Carbon;
use App\Services\Api\ApiServices;
use App\Models\OperationalHasDept;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\OperationalHasTimetable;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\OperationalDayHasTimetable;

class OperationalController extends Controller
{
    private $apiService;
    private $buildRes;
    private $util;

    public function __construct(ApiServices $apiService, Util $util, ResponseUtil $buildRes)
    {
        $this->apiService = $apiService;
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
        if (!auth()->user()->can('operational.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {

            if (request()->ajax()) {
                $start_date = Carbon::parse($request['date']['start_date']);
                $end_date = Carbon::parse($request['date']['end_date']);
                $dates = $this->util->generateDateRange($start_date, $end_date);
                $slug_week = ['mgg', 'sen', 'sel', 'rab', 'kam', 'jum', 'sab'];
                foreach ($dates as $date) {
                    $code_day = Carbon::parse($date)->dayOfWeek;
                    $th_dates[] = ['date' => $date, 'slug' => $slug_week[$code_day], 'date' => $date];
                }

                $business_id = Session::get('business_id');
                $operationals = Operational::where('business_id', $business_id)->whereBetween('date', [$start_date, $end_date])
                    ->get()->groupBy(function ($item) {
                        return Carbon::parse($item->date)->format('Y-m-d');
                    });

                    // foreach ($variable as $key => $value) {
                    //     # code...
                    // }

Log::info($operationals);
                // if ($request->has('q') && !empty($request->input('q'))) {
                //     $search = $request->q;
                //     $operationals = $operationals->where('dept_name', 'LIKE', "%" . $search . "%")
                //         ->orWhere('dept_id', 'LIKE', "%" . $search . "%")
                //         ->orWhere('dept_code', 'LIKE', "%" . $search . "%");
                // }

                // if ($request->has('date') && !empty($request->input('date'))) {
                //     $operationals = $operationals->whereBetween('start_date', [$request['date']['start_date'], $request['date']['end_date']])
                //         ->orWhereBetween('end_date', [$request['date']['start_date'], $request['date']['end_date']]);
                // }

                $order = null;
                // if ($request->has('sort')) {
                //     $sort = $request->sort;
                //     $order = $sort['order'];
                //     $operationals->orderBy($sort['name'], $sort['order']);
                // }
                // $operationals = $operationals->with('operational_has_depts')->paginate(10);
                $render =  view('Task.operational.table', compact('th_dates', 'operationals', 'order'))->render();

                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            }

            return  view('Task.operational.index');
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
        if (!auth()->user()->can('operational.create') || !request()->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $date = Carbon::parse($request['date'])->format('Y-m-d');

            $departments = collect($this->apiService->get_departments([]));
            $departments['data'] = collect($departments['data'])->filter(function ($e) {
                return empty($e['parent_dept']);
            });
            $render = view('Task.operational.create', compact('departments', 'date'))->render();

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
        if (!auth()->user()->can('operational.create')  || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }


        try {
            $validator = Validator::make($request->all(), $this->rules());

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $request_data = $request->only(['date', 'department', 'shift']);
                $date = Carbon::parse($request_data['date']);
                $business_id = Session::get('business_id');

                // ** create operational
                $operational = new Operational([
                    'business_id' => $business_id,
                    'date' => $date,
                    'dept_id' => $request_data['department'],
                    'day_name' => Carbon::create($date)->locale('id_ID')->dayName,
                    'created_user' => auth()->user()->id,
                    'updated_user' => auth()->user()->id,
                ]);
                $operational->save();

                foreach ($request_data['shift']['timetables'] as $key => $value) {
                    // ** create operational has timetable
                    $operational_has_timetable = new OperationalHasTimetable([
                        'operational_id' => $operational->id,
                        'timetable_id' => $value['timetable_id'],
                        'ot_limit' => $value['ot_limit'],
                        'status' => (!empty($value['status']) && $value['status'] == -1) ? 'active' : 'inactive',
                    ]);
                    $operational_has_timetable->save();
                }

                return $this->buildRes->RESPONSE_REQ('success', null,  ['success' => ['Add operational succesfully']]);
            }
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
        if (!auth()->user()->can('operational.view')) {
            abort(403, 'Unauthorized action.');
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  int $operational
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(int $operational, Request $request)
    {
        if (!auth()->user()->can('operational.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $departments = collect($this->apiService->get_departments([]));
            $operational = Operational::where('id', $operational)->with('operational_has_depts')->first();

            $sub_departments = [];
            foreach ($departments['data'] as $e) {
                if (!empty($e['parent_dept']) && $e['parent_dept']['id'] == $operational->dept_id)
                    $sub_departments[] = $e;
            }
            $departments['data'] = collect($departments['data'])->filter(function ($e) {
                return empty($e['parent_dept']);
            });

            $render = view('Task.operational.edit', compact('operational', 'departments', 'sub_departments'))->render();
            return $this->buildRes->RESPONSE_REQ('success', $render, null);
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Operational $operational
     * @return \Illuminate\Http\Response
     */
    public function update(Operational $operational, Request $request)
    {
        if (!auth()->user()->can('operational.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $validator = Validator::make($request->all(), $this->rules());

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $request_data = $request->only(['date', 'department', 'group']);
                $department = $this->apiService->read_department($request_data['department']);
                $start_date = trim(explode(' - ', $request_data['date'])[0]);
                $end_date = trim(explode(' - ', $request_data['date'])[1]);

                $operational_data = [
                    'dept_id' => $department['id'],
                    'dept_code' => $department['dept_code'],
                    'dept_name' => $department['dept_name'],
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'updated_user' => auth()->user()->id,
                ];
                $operational->update($operational_data);

                OperationalHasDept::where('operational_id', $operational->id)->each(function ($item) {
                    $item->delete();
                });

                foreach ($request_data['group'] as $item) {
                    $start_date = trim(explode(' - ', $item['date'])[0]);
                    $end_date = trim(explode(' - ', $item['date'])[1]);
                    $status = (empty($item['status'])) ? 'inactive' : 'active';
                    $operational_has_employee = new OperationalHasDept([
                        'operational_id' => $operational->id,
                        'dept_id' => $item['dept_id'],
                        'dept_code' => $item['dept_code'],
                        'dept_name' => $item['dept_name'],
                        'start_date' => $start_date,
                        'end_date' => $end_date,
                        'status' => $status,
                        'note' => $item['note'],
                    ]);
                    $operational_has_employee->save();
                }
                return $this->buildRes->RESPONSE_REQ('success', null, ['success' => ['Update operational succesfully']]);
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Operational $operational
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Operational $operational, Request $request)
    {
        if (!auth()->user()->can('operational.delete') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $operational->delete();

            return $this->buildRes->RESPONSE_REQ('success', null, ['success' => ['Delete operational succesfully']]);
        } catch (\Exception $e) {
            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    public function card_sub_dept(Request $request)
    {
        if (!request()->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $departments = collect($this->apiService->get_departments([])['data']);
            $temp = $departments;
            $departments = [];
            foreach ($temp as $e) {
                if (!empty($e['parent_dept']) && $e['parent_dept']['id'] == $request['dept_id'])
                    $departments[] = $e;
            }

            $render = view('Task.operational.cards.deparment_card', compact('departments'))->render();
            return $this->buildRes->RESPONSE_REQ('success', $render, null);
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }
    public function get_operational_timetable_card(Request $request)
    {
        if (!request()->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $date = Carbon::parse($request->date);
            $holidays = Holiday::where('start_date', $date)->get();
            $shift = Shift::where('dept_id', $request->dept_id)->with('shiftday.shiftday_has_timetable.timetable')->first();
            $timetable_card = [];
            if (!empty($shift)) {
                $code_day = $date->dayOfWeek;
                $timetables = [];
                foreach ($shift->shiftday as $key => $value) {
                    if (!empty($holidays) && $holidays->count() ? $value->code_day == 0 : $code_day == $value->code_day) {
                        $timetables = array_column($value->shiftday_has_timetable->toArray(), 'timetable');
                    }
                }
                $timetable_card = [
                    'date' => $date,
                    'dayname' => Carbon::create($date)->locale('id_ID')->dayName,
                    'timetables' => $timetables
                ];
                $render = view('Task.operational.cards.deparment_card', compact('timetable_card'))->render();
                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            } else {
                return $this->buildRes->RESPONSE_REQ('error', null, ['error' => ['The shift schedule for this section has not been arranged, please arrange in advance']]);
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Rules validation group.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'date' => 'required',
            'department' => 'required',
            'shift' => 'required',
        ];
    }
}
