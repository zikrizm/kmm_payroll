<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use App\Models\ActivityLog;
use App\Models\Operational;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Services\Api\ApiServices;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class HolidayController extends Controller
{
    private $apiService;
    private $buildRes;

    public function __construct(ApiServices $apiService, ResponseUtil $buildRes)
    {
        $this->apiService = $apiService;
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
        if (!auth()->user()->can('holiday.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            if (request()->ajax()) {
                $business_id = Session::get('business_id');
                $holidays = Holiday::where('business_id', $business_id);
                if ($request->has('q')) {
                    $search = $request->q;
                    $holidays = $holidays->where('name', 'LIKE', "%" . $search . "%");
                }

                if ($request->has('page')) {
                    $filter['page'] = $request->page;
                }

                $order = null;
                if ($request->has('sort')) {
                    $sort = $request->sort;
                    $order = $sort['order'];
                    $holidays->orderBy($sort['name'], $sort['order']);
                }
                $holidays = $holidays->paginate(10);
                $render =  view('Shift.holiday.table', compact('holidays', 'order'))->render();

                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            }

            return  view('Shift.holiday.index');
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
        if (!auth()->user()->can('holiday.create') || !request()->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $render = view('Shift.holiday.create')->render();

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
        if (!auth()->user()->can('holiday.create')  || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validator = Validator::make($request->all(), $this->rules());

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $business_id = Session::get('business_id');
                $holiday_data = $request->only(['name', 'holiday_date']);
                $holiday_data['business_id'] = Session::get('business_id');

                $start_date = Carbon::createFromFormat('d-m-Y', trim(explode(' - ', $holiday_data['holiday_date'])[0]));
                $end_date = Carbon::createFromFormat('d-m-Y', trim(explode(' - ', $holiday_data['holiday_date'])[1]));
                // $operational_count = Operational::where('business_id', $business_id)->whereBetween('date', [$start_date->format('Y-m-d'), $end_date->format('Y-m-d')])->count();
                // if (empty($operational_count)) {
                    $holiday_data['start_date'] = $start_date;
                    $holiday_data['end_date'] = $end_date;
                    $holiday_data['created_user'] = auth()->user()->id;
                    $holiday_data['updated_user'] = auth()->user()->id;

                    $holiday = new Holiday($holiday_data);
                    $holiday->save();
                    // ** create activity log user
                    ActivityLog::created_activity('CRUD holiday', 'User ' . auth()->user()->username . ' create new holiday');
                    return $this->buildRes->RESPONSE_REQ('success', null,  ['success' => ['Add holiday succesfully']]);
                // } else {
                //     if ($start_date->isSameDay($end_date)) {
                //         return $this->buildRes->RESPONSE_REQ('error', null,  ['error' => ["Pada tanggal {$start_date->format('d-m-Y')} jadwal operasional telah ditentukan, Anda tidak dapat mengubah status tanggal ini."]]);
                //     } else {
                //         return $this->buildRes->RESPONSE_REQ('error', null,  ['error' => ["Pada tanggal {$start_date->format('d-m-Y')} - {$end_date->format('d-m-Y')} jadwal operasional telah ada yang ditentukan, Anda tidak dapat mengubah status tanggal ini."]]);
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
        if (!auth()->user()->can('group.view')) {
            abort(403, 'Unauthorized action.');
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  Holiday $holiday
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(Holiday $holiday, Request $request)
    {
        if (!auth()->user()->can('holiday.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $render = view('Shift.holiday.edit', compact('holiday'))->render();

            return $this->buildRes->RESPONSE_REQ('success', $render, null);
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Holiday $holiday
     * @return \Illuminate\Http\Response
     */
    public function update(Holiday $holiday, Request $request)
    {
        if (!auth()->user()->can('holiday.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validator = Validator::make($request->all(), $this->rules());

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $business_id = Session::get('business_id');
                $holiday_data = $request->only(['name', 'holiday_date']);
                $start_date = Carbon::createFromFormat('d-m-Y', trim(explode(' - ', $holiday_data['holiday_date'])[0]));
                $end_date = Carbon::createFromFormat('d-m-Y', trim(explode(' - ', $holiday_data['holiday_date'])[1]));
                // $operational_count = Operational::where('business_id', $business_id)->whereBetween('date', [$start_date->format('Y-m-d'), $end_date->format('Y-m-d')])->count();
                // if (empty($operational_count)) {
                    $holiday_data['start_date'] = $start_date;
                    $holiday_data['end_date'] = $end_date;
                    $holiday_data['updated_user'] = auth()->user()->id;
                    $holiday->update($holiday_data);

                    // ** create activity log user
                    ActivityLog::created_activity('CRUD holiday', 'User ' . auth()->user()->username . ' edit data holiday');
                    return $this->buildRes->RESPONSE_REQ('success', null,  ['success' => ['Update holiday succesfully']]);
                // } else {
                //     if ($start_date->isSameDay($end_date)) {
                //         return $this->buildRes->RESPONSE_REQ('error', null,  ['error' => ["Pada tanggal {$start_date->format('d-m-Y')} jadwal operasional telah ditentukan, Anda tidak dapat mengubah status tanggal ini."]]);
                //     } else {
                //         return $this->buildRes->RESPONSE_REQ('error', null,  ['error' => ["Pada tanggal {$start_date->format('d-m-Y')} - {$end_date->format('d-m-Y')} jadwal operasional telah ada yang ditentukan, Anda tidak dapat mengubah status tanggal ini."]]);
                //     }
                // }
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Holiday $holiday
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Holiday $holiday, Request $request)
    {
        if (!auth()->user()->can('holiday.delete') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = Session::get('business_id');
            $start_date = Carbon::parse($holiday->start_date);
            $end_date = Carbon::parse($holiday->end_date);
            $operational_count = Operational::where('business_id', $business_id)->whereBetween('date', [$start_date->format('Y-m-d'), $end_date->format('Y-m-d')])->count();
            if (empty($operational_count)) {
                $holiday->delete();

                // ** create activity log user
                ActivityLog::created_activity('CRUD holiday', 'User ' . auth()->user()->username . ' delete data holiday');
                return $this->buildRes->RESPONSE_REQ('success', null, ['success' => ['Delete holiday succesfully']]);
            } else {
                if ($start_date->isSameDay($end_date)) {
                    return $this->buildRes->RESPONSE_REQ('error', null,  ['error' => ["Pada tanggal {$start_date->format('d-m-Y')} jadwal operasional telah ditentukan, Anda tidak dapat mengubah status tanggal ini."]]);
                } else {
                    return $this->buildRes->RESPONSE_REQ('error', null,  ['error' => ["Pada tanggal {$start_date->format('d-m-Y')} - {$end_date->format('d-m-Y')} jadwal operasional telah ada yang ditentukan, Anda tidak dapat mengubah status tanggal ini."]]);
                }
            }
        } catch (\Exception $e) {
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
            'name' => 'required|string|max:255',
            'holiday_date' => 'required',
        ];
    }
}
