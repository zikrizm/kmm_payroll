<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use App\Utils\Util;
use App\Models\Shift;
use App\Models\Operational;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Services\Api\ApiServices;
use Illuminate\Support\Facades\Log;
use App\Models\OperationalHasTimetable;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

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
                $business_id = Session::get('business_id');
                $start_date = Carbon::parse($request['date']['start_date']);
                $end_date = Carbon::parse($request['date']['end_date']);
                $dates = $this->util->generateDateRange($start_date, $end_date);
                $dept_bios = $this->apiService->get_departments(['page_size' => 999])['data'];
                $holidays = Holiday::whereBetween('start_date', [$start_date, $end_date])->orWhereBetween('start_date', [$start_date, $end_date])->get();
                $operationals = Operational::where('business_id', $business_id)->whereBetween('date', [$start_date, $end_date])
                    ->with('shift', 'operational_has_timetables.timetable')->get()->groupBy(function ($item) {
                        return Carbon::parse($item->date)->format('Y-m-d');
                    });

                $th_dates = [];
                foreach ($dates as $key => $date) {
                    $is_holiday = false;
                    foreach ($holidays as $key => $holiday) {
                        $holy_start = Carbon::parse($holiday->start_date);
                        $holy_end = Carbon::parse($holiday->end_date);
                        $date = Carbon::parse($date);
                        if ($date->isSunday() || $date->between($holy_start, $holy_end)) {
                            $is_holiday = true;
                        }
                    }

                    $th_dates[] = [
                        "date" => $date,
                        "is_holiday" => $is_holiday,
                    ];
                }

                $operationals  = $operationals->map(function ($element) use ($dept_bios) {
                    $element = $element->map(function ($e_op) use ($dept_bios, $element) {
                        $is_same = array_search($e_op->dept_id, array_column($dept_bios, 'id'));
                        if ($is_same != '') $e_op['department'] = (object)$dept_bios[$is_same];
                        return $e_op;
                    });
                    return $element;
                });


                // foreach ($variable as $key => $value) {
                //     # code...
                // }

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
                $render =  view('Task.operational.table', compact('dates', 'th_dates', 'operationals', 'order'))->render();

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
            $date = (!empty($request['date'])) ? Carbon::parse($request['date'])->format('Y-m-d') : null;
            $departments = collect($this->apiService->get_departments(['page_size' => 999]));
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
            $validator = Validator::make($request->all(), $this->rules(null));

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $request_data = $request->only(['date', 'department', 'shift']);
                $business_id = Session::get('business_id');
                $dept_bio = $this->apiService->read_department($request_data['department']);
                $parent_dept_id = null;
                if (empty($dept_bio['parent_dept'])) {
                    $parent_dept_id = $dept_bio['id'];
                } else {
                    $parent_dept_id = $dept_bio['parent_dept']['id'];
                }

                $rangedate = explode(' - ', $request['date']);
                $dates = [];
                if (count($rangedate) > 1) {
                    $start_date = Carbon::parse(trim($rangedate[0]));
                    $end_date = Carbon::parse(trim($rangedate[1]));
                    $dates = $this->util->generateDateRange($start_date, $end_date);
                    $operational = Operational::where('dept_id', $request->dept_id)->whereBetween('date', [$start_date, $end_date])->first();
                } else {
                    $date = Carbon::parse($request_data['date']);
                    $dates[] = $date->format('Y-m-d');
                    $operational = Operational::where('dept_id', $request_data['department'])->where('date', $date)->first();
                }

                // Log::info(response()->json($request_data['shift']));

                if (empty($operational) && !empty($request_data['shift'])) {
                    foreach ($request_data['shift'] as $key => $shift) {
                        $date = Carbon::parse($dates[$key]);
                        $operational = new Operational([
                            'business_id' => $business_id,
                            'date' => $date,
                            'dept_id' => $request_data['department'],
                            'parent_dept_id' => $parent_dept_id,
                            'day_name' => Carbon::create($date)->locale('id_ID')->dayName,
                            'created_user' => auth()->user()->id,
                            'updated_user' => auth()->user()->id,
                        ]);
                        $operational->save();

                        foreach ($shift['timetables'] as $key => $value) {
                            // ** create operational has timetable
                            $operational_has_timetable = new OperationalHasTimetable([
                                'operational_id' => $operational->id,
                                'timetable_id' => $value['timetable_id'],
                                'ot_limit' => (!empty($value['status']) && $value['status'] == -1) ? $value['ot_limit'] : 0,
                                'status' => (!empty($value['status']) && $value['status'] == -1) ? 'active' : 'inactive',
                            ]);
                            $operational_has_timetable->save();
                        }
                    }
                    return $this->buildRes->RESPONSE_REQ('success', null,  ['success' => ['Add operational succesfully']]);
                } else {
                    if (count($rangedate) > 1) {
                        return $this->buildRes->RESPONSE_REQ('error', null, ['error' => ["Salah satu atau beberapa Jadwal dalam range {$start_date->format('d-m-Y')} - {$end_date->format('d-m-Y')} udah tersedia"]]);
                    } else {
                        return $this->buildRes->RESPONSE_REQ('error', null, ['error' => ["Jadwal untuk tanggal {$date->format('d-m-Y')} sudah tersedia"]]);
                    }
                }
                // if (count($rangedate) > 1) {
                //     $start_date = Carbon::parse(trim($rangedate[0]));
                //     $end_date = Carbon::parse(trim($rangedate[1]));
                //     $operational = Operational::where('dept_id', $request->dept_id)->whereBetween('date', [$start_date, $end_date])->first();
                //     $dates = $this->util->generateDateRange($start_date, $end_date);

                //     if (empty($operational) && !empty($request_data['shift'])) {
                //         foreach ($request_data['shift'] as $key => $shift) {
                //             $date = Carbon::parse($dates[$key]);
                //             $operational = new Operational([
                //                 'business_id' => $business_id,
                //                 'date' => $date,
                //                 'dept_id' => $request_data['department'],
                //                 'parent_dept_id' => $parent_dept_id,
                //                 'day_name' => Carbon::create($date)->locale('id_ID')->dayName,
                //                 'created_user' => auth()->user()->id,
                //                 'updated_user' => auth()->user()->id,
                //             ]);
                //             $operational->save();

                //             foreach ($shift['timetables'] as $key => $value) {
                //                 // ** create operational has timetable
                //                 $operational_has_timetable = new OperationalHasTimetable([
                //                     'operational_id' => $operational->id,
                //                     'timetable_id' => $value['timetable_id'],
                //                     'ot_limit' => (!empty($value['status']) && $value['status'] == -1) ? $value['ot_limit'] : 0,
                //                     'status' => (!empty($value['status']) && $value['status'] == -1) ? 'active' : 'inactive',
                //                 ]);
                //                 $operational_has_timetable->save();
                //             }

                //             return $this->buildRes->RESPONSE_REQ('success', null,  ['success' => ['Add operational succesfully']]);
                //         }
                //     } else {
                //         return $this->buildRes->RESPONSE_REQ('error', null, ['error' => ["Salah satu atau beberapa Jadwal dalam range {$start_date->format('d-m-Y')} - {$end_date->format('d-m-Y')} udah tersedia"]]);
                //     }
                // } else {
                //     $date = Carbon::parse($request_data['date']);
                //     $operational = Operational::where('dept_id', $request_data['department'])->where('date', $date)->first();
                //     if (empty($operational)) {
                //         // ** create operational
                //         $operational = new Operational([
                //             'business_id' => $business_id,
                //             'date' => $date,
                //             'dept_id' => $request_data['department'],
                //             'parent_dept_id' => $parent_dept_id,
                //             'day_name' => Carbon::create($date)->locale('id_ID')->dayName,
                //             'created_user' => auth()->user()->id,
                //             'updated_user' => auth()->user()->id,
                //         ]);
                //         $operational->save();

                //         foreach ($request_data['shift']['timetables'] as $key => $value) {
                //             // ** create operational has timetable
                //             $operational_has_timetable = new OperationalHasTimetable([
                //                 'operational_id' => $operational->id,
                //                 'timetable_id' => $value['timetable_id'],
                //                 'ot_limit' => (!empty($value['status']) && $value['status'] == -1) ? $value['ot_limit'] : 0,
                //                 'status' => (!empty($value['status']) && $value['status'] == -1) ? 'active' : 'inactive',
                //             ]);
                //             $operational_has_timetable->save();
                //         }

                //         return $this->buildRes->RESPONSE_REQ('success', null,  ['success' => ['Add operational succesfully']]);
                //     } else {
                //         return $this->buildRes->RESPONSE_REQ('error', null, ['error' => ["Jadwal untuk tanggal {$date->format('d-m-Y')} sudah tersedia"]]);
                //     }
                // }
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
            $date = Carbon::parse($request->date);
            $departments = collect($this->apiService->get_departments([]));
            $operational = Operational::where('id', $operational)->with('operational_has_timetables.timetable')->first();
            $dept_bios = $this->apiService->get_departments(["page_size" => 999]);
            if (!empty($operational)) {
                $timetable_cards = [];
                $timetables = [];
                foreach ($operational->operational_has_timetables as $key => $value) {
                    $timetable = $value->timetable->toArray();
                    $timetable['status'] =  $value->status;
                    $timetable['ot_limit'] =  $value->ot_limit;
                    $timetables[] = $timetable;
                }
                $timetable_cards[] = [
                    'date' => $date,
                    'is_range' => false,
                    'timetables' => $timetables,
                ];
                $render = view('Task.operational.edit', compact('operational', 'dept_bios', 'timetable_cards'))->render();
                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            } else {
                return $this->buildRes->RESPONSE_REQ('error', null, ['error' => ["Jadwal untuk tanggal {$date->format('d-m-Y')} tidak tersedia"]]);
            }
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
            $validator = Validator::make($request->all(), $this->rules($operational));

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $request_data = $request->only(['date', 'shift']);
                $date = Carbon::createFromFormat('d-m-Y', $request_data['date']);
                $business_id = Session::get('business_id');
                if (!empty($operational)) {
                    // ** create operational
                    $operational_data = [
                        'updated_user' => auth()->user()->id,
                    ];
                    $operational->update($operational_data);


                    OperationalHasTimetable::where('operational_id', $operational->id)->each(function ($item) {
                        $item->delete();
                    });
                    foreach ($request_data['shift'] as $key => $shift) {
                        foreach ($shift['timetables'] as $key => $value) {
                            // ** create operational has timetable
                            $operational_has_timetable = new OperationalHasTimetable([
                                'operational_id' => $operational->id,
                                'timetable_id' => $value['timetable_id'],
                                'ot_limit' => (!empty($value['status']) && $value['status'] == -1) ? $value['ot_limit'] : 0,
                                'status' => (!empty($value['status']) && $value['status'] == -1) ? 'active' : 'inactive',
                            ]);
                            $operational_has_timetable->save();
                        }
                    }


                    return $this->buildRes->RESPONSE_REQ('success', null,  ['success' => ['Update operational succesfully']]);
                } else {
                    return $this->buildRes->RESPONSE_REQ('error', null, ['error' => ["Jadwal untuk tanggal {$date->format('d-m-Y')} tidak tersedia"]]);
                }
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
            Log::info($request);
            $dept_id = null;
            $dept_bio = $this->apiService->read_department($request->dept_id);
            if (empty($dept_bio['parent_dept'])) {
                $dept_id = $dept_bio['id'];
            } else {
                $dept_id = $dept_bio['parent_dept']['id'];
            }
            $shift = Shift::where('dept_id', $dept_id)->with('shiftday.shiftday_has_timetable.timetable')->first();
            if (!empty($shift)) {
                $rangedate = explode(' - ', $request['date']);
                if (count($rangedate) > 1) {
                    $start_date = Carbon::parse(trim($rangedate[0]));
                    $end_date = Carbon::parse(trim($rangedate[1]));
                    $holidays = Holiday::whereBetween('start_date', [$start_date, $end_date])->orWhereBetween('start_date', [$start_date, $end_date])->get();
                    $operational = Operational::where('dept_id', $request->dept_id)->whereBetween('date', [$start_date, $end_date])->first();
                    $dates = $this->util->generateDateRange($start_date, $end_date);
                    $timetable_cards = [];
                    if (empty($operational)) {
                        foreach ($dates as $key => $item_date) {
                            $date = Carbon::parse($item_date);
                            $code_day = $date->dayOfWeek;
                            $timetables = [];
                            foreach ($shift->shiftday as $key => $value) {
                                $is_holiday = $holidays->search(function ($item, $key) use ($date) {
                                    $holi_start = Carbon::parse($item->start_date);
                                    $holi_end = Carbon::parse($item->end_date);
                                    return $date->between($holi_start, $holi_end);
                                });

                                if (($is_holiday != '') ? $value->code_day == 0 : $code_day == $value->code_day) {
                                    $timetables = array_column($value->shiftday_has_timetable->toArray(), 'timetable');
                                }
                            }
                            $timetable_cards[] = [
                                'date' => $date,
                                'is_range' => true,
                                'timetables' => $timetables
                            ];
                        }
                        $render = view('Task.operational.cards.deparment_card', compact('timetable_cards'))->render();
                        return $this->buildRes->RESPONSE_REQ('success', $render, null);
                    } else {
                        return $this->buildRes->RESPONSE_REQ('error', null, ['error' => ["Salah satu atau beberapa Jadwal dalam range {$start_date->format('d-m-Y')} - {$end_date->format('d-m-Y')} udah tersedia"]]);
                    }
                } else {
                    $date = Carbon::parse($request->date);
                    $holidays = Holiday::where('start_date', '<=', $date)
                        ->where('end_date', '>=', $date)->get();
                    $operational = Operational::where('dept_id', $request->dept_id)->where('date', $date)->first();
                    $timetable_cards = [];
                    if (empty($operational)) {
                        $code_day = $date->dayOfWeek;
                        $timetables = [];
                        foreach ($shift->shiftday as $key => $value) {
                            if (!empty($holidays) && $holidays->count() ? $value->code_day == 0 : $code_day == $value->code_day) {
                                $timetables = array_column($value->shiftday_has_timetable->toArray(), 'timetable');
                            }
                        }
                        $timetable_cards[] = [
                            'date' => $date,
                            'is_range' => false,
                            'timetables' => $timetables
                        ];
                        $render = view('Task.operational.cards.deparment_card', compact('timetable_cards'))->render();
                        return $this->buildRes->RESPONSE_REQ('success', $render, null);
                    } else {
                        return $this->buildRes->RESPONSE_REQ('error', null, ['error' => ["Jadwal untuk tanggal {$date->format('d-m-Y')} sudah tersedia"]]);
                    }
                }
            } else {
                return $this->buildRes->RESPONSE_REQ('error', null, ['error' => ['Jadwal shift untuk bagian ini belum diatur, mohon diatur terlebih dahulu']]);
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => ['something wrong']]);
        }
    }

    /**
     * Rules validation group.
     *
     * @return array
     */
    public function rules($operational)
    {
        return [
            'date' => 'required',
            'department' => (empty($operational)) ? 'required' : 'sometimes',
            'shift' => 'required',
        ];
    }
}
