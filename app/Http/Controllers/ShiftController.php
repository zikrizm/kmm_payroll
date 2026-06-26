<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\ShiftDay;
use App\Models\Timetable;
use App\Models\ActivityLog;
use App\Utils\BusinessUtil;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;
use App\Services\Api\ApiServices;
use Illuminate\Support\Facades\Log;
use App\Models\ShiftDayHasTimetable;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ShiftController extends Controller
{
    private $apiService;
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
        if (!auth()->user()->can('shift.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            if (request()->ajax()) {
                $business_id = Session::get('business_id');
                $shifts = Shift::where('business_id', $business_id);
                if ($request->has('q')) {
                    $search = $request->q;
                    $shifts = $shifts->where(function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%" . $search . "%");
                    });
                }
                if ($request->has('page')) {
                    $filter['page'] = $request->page;
                }

                $order = null;
                if ($request->has('sort')) {
                    $sort = $request->sort;
                    $order = $sort['order'];
                    $shifts->orderBy($sort['name'], $sort['order']);
                }
                $shifts = $shifts->paginate(10);

                $dept_count = $this->apiService->get_departments([]);
                $depts = $this->apiService->get_departments(['page_size' => $dept_count['count']]);
                foreach ($shifts as $shift) {
                    foreach ($depts['data'] as $dept) {
                        if ($shift->dept_id == $dept['id']) {
                            $shift['department'] = $dept;
                            break;
                        }
                    }
                }

                $render = view('Shift.shift.table', compact('shifts', 'order'))->render();

                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            }

            return view('Shift.shift.index');
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
        if (!auth()->user()->can('shift.create') || !request()->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = Session::get('business_id');
            $timetables = Timetable::where('business_id', $business_id)->with(['timetable_has_break_time'])->get();
            $dept_count = $this->apiService->get_departments([]);
            $departments = $this->apiService->get_departments(['page_size' => $dept_count['count']])['data'];

            $render = view('Shift.shift.create', compact('timetables', 'departments'))->render();

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
        if (!auth()->user()->can('shift.create')  || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $validator = Validator::make($request->all(), $this->rules(null));

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $business_id = Session::get('business_id');
                $shift_data = $request->only(['name', 'dept_id', 'timetables']);
                $shift_data['business_id'] = $business_id;
                $shift_data['status'] = Shift::resolveStatusForNew($business_id, (int) $shift_data['dept_id']);
                $shift = new Shift($shift_data);
                $shift->save();

                $daynames = ['minggu', 'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];
                if (!empty($request->input('timetables'))) {
                    foreach ($shift_data['timetables'] as $key => $timetable) {
                        $shift_day_data = [
                            'shift_id' => $shift->id,
                            'name' => $key,
                            'code_day' => array_search($key, $daynames),
                        ];
                        $shift_day = new ShiftDay($shift_day_data);
                        $shift_day->save();

                        foreach ($timetable as $item) {
                            $shift_day_has_timetable_data = [
                                'timetable_id' => $item,
                                'shift_day_id' => $shift_day->id,
                            ];
                            $shift_day_has_timetable = new ShiftDayHasTimetable($shift_day_has_timetable_data);
                            $shift_day_has_timetable->save();
                        }
                    }
                }

                // ** create activity log user
                ActivityLog::created_activity('CRUD shift', 'User ' . auth()->user()->username . ' create new shift');
                return $this->buildRes->RESPONSE_REQ('success', null,  ['success' => ['Add shift succesfully']]);
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
        if (!auth()->user()->can('shift.view')) {
            abort(403, 'Unauthorized action.');
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  int $shift
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(int $shift, Request $request)
    {
        if (!auth()->user()->can('shift.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $shift = Shift::where('id', $shift)->with(['shiftday.shiftday_has_timetable'])->first();

            $business_id = Session::get('business_id');
            $timetables = Timetable::where('business_id', $business_id)->with(['timetable_has_break_time'])->get();
            $department = $this->apiService->read_department($shift->dept_id);
            $render = view('Shift.shift.edit', compact('shift', 'timetables', 'department'))->render();

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
     * @param  Shift  $shift
     * @return \Illuminate\Http\Response
     */
    public function update(Shift $shift, Request $request)
    {
        if (!auth()->user()->can('shift.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validator = Validator::make($request->all(), $this->rules($shift));

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $shift_data = $request->only(['name', 'timetables']);
                $shift_data['business_id'] = Session::get('business_id');
                $shift->update($shift_data);

                ShiftDay::where('shift_id', $shift->id)->each(function ($item) {
                    $item->delete();
                    foreach ($item->shiftday_has_timetable as $item) {
                        $item->delete();
                    }
                });

                $daynames = ['minggu', 'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];
                if (!empty($request->input('timetables'))) {
                    foreach ($shift_data['timetables'] as $key => $timetable) {
                        $shift_day_data = [
                            'shift_id' => $shift->id,
                            'name' => $key,
                            'code_day' => array_search($key, $daynames),
                        ];
                        $shift_day = new ShiftDay($shift_day_data);
                        $shift_day->save();

                        foreach ($timetable as $item) {
                            $shift_day_has_timetable_data = [
                                'timetable_id' => $item,
                                'shift_day_id' => $shift_day->id,
                            ];
                            $shift_day_has_timetable = new ShiftDayHasTimetable($shift_day_has_timetable_data);
                            $shift_day_has_timetable->save();
                        }
                    }
                }

                // ** create activity log user
                ActivityLog::created_activity('CRUD shift', 'User ' . auth()->user()->username . ' edit data shift');
                return $this->buildRes->RESPONSE_REQ('success', null, ['success' => ['Shift Update succesfully']]);
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Shift $shift
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Shift $shift, Request $request)
    {
        if (!auth()->user()->can('shift.delete') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $shift->delete();

            // ** create activity log user
            ActivityLog::created_activity('CRUD shift', 'User ' . auth()->user()->username . ' delete data shift');
            return $this->buildRes->RESPONSE_REQ('success', null, ['success' => ['Shift delete succesfully']]);
        } catch (\Exception $e) {
            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }


    /**
     * Toggle shift active/inactive status.
     *
     * @param  Shift  $shift
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toggleStatus(Shift $shift, Request $request)
    {
        if (!auth()->user()->can('shift.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:active,inactive',
            ]);

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            }

            $business_id = Session::get('business_id');
            if ($shift->business_id != $business_id) {
                abort(403, 'Unauthorized action.');
            }

            $newStatus = $request->input('status');
            $deactivatedShift = null;

            if ($newStatus === Shift::STATUS_ACTIVE) {
                if ($shift->status !== Shift::STATUS_ACTIVE) {
                    $deactivatedShift = Shift::where('business_id', $business_id)
                        ->where('dept_id', $shift->dept_id)
                        ->where('status', Shift::STATUS_ACTIVE)
                        ->where('id', '!=', $shift->id)
                        ->first();

                    $shift->activateForDepartment();
                }
            } else {
                $shift->update(['status' => Shift::STATUS_INACTIVE]);
            }

            ActivityLog::created_activity(
                'CRUD shift',
                'User ' . auth()->user()->username . ' changed shift status to ' . $newStatus
            );

            $message = $newStatus === Shift::STATUS_ACTIVE
                ? 'Shift berhasil diaktifkan.'
                : 'Shift berhasil dinonaktifkan.';

            if ($deactivatedShift) {
                $message .= ' Shift "' . $deactivatedShift->name . '" otomatis dinonaktifkan.';
            }

            return $this->buildRes->RESPONSE_REQ('success', null, ['success' => [$message]]);
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Rules validation shift.
     *
     * @return array
     */
    public function rules($shift)
    {
        return [
            'name' => 'required|string|max:255',
            'dept_id' => empty($shift) ? 'required|string|max:255' : '',
            'timetables.senin' => 'required',
            'timetables.selasa' => 'required',
            'timetables.rabu' => 'required',
            'timetables.kamis' => 'required',
            'timetables.jumat' => 'required',
            'timetables.sabtu' => 'required',
            'timetables.minggu' => 'required',
        ];
    }
}
