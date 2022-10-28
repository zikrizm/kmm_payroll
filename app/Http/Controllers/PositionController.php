<?php

namespace App\Http\Controllers;

use App\Models\Position;
use App\Models\ActivityLog;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\Api\ApiServices;
use Illuminate\Support\Facades\Log;
use App\Exceptions\ResponseExeception;
use Illuminate\Support\Facades\Validator;

class PositionController extends Controller
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
        if (!auth()->user()->can('position.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            if (request()->ajax()) {
                $order = null;
                $filter = [];

                if ($request->has('q')) {
                    $filter['position_name_icontains'] = $request->q;
                }

                if ($request->has('page')) {
                    $filter['page'] = $request->page;
                }

                if ($request->has('sort')) {
                    $filter['ordering'] = $request->sort['name'];
                    $order = $request->sort['order'];
                }

                $positions = $this->apiService->get_positions($filter);

                $posis = Position::all();
                foreach ($posis as $posi) {
                    foreach ($positions['data'] as $key => $position) {
                        if ($posi->position_id == $position['id']) {
                            $positions['data'][$key]['must_attend'] = $posi->must_attend;
                            $positions['data'][$key]['permanently'] = $posi->permanently;
                            $positions['data'][$key]['extra_pay'] = $posi->extra_pay;
                        }
                    }
                }

                $render =  view('Organization.position.table', compact('positions', 'order'))->render();
                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            }

            return view('Organization.position.index');
        } catch (ResponseExeception $e) {
            return $this->buildRes->RESPONSE_REQ('error', null, $e->getMessages());
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            return $this->buildRes->RESPONSE_REQ('error', null, ['something_wrong' => 'something wrong']);
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
        if (!auth()->user()->can('position.create') || !request()->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $render = view('Organization.position.create')->render();

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
        if (!auth()->user()->can('position.create')  || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validator = Validator::make($request->all(), $this->rules());

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $position_data = $request->only([
                    'position_code', 'position_name', 'must_attend', 'permanently', 'extra_pay_check', 'extra_pay'
                ]);

                $res = $this->apiService->create_position($position_data);
                if ($res['status'] == 'success') {
                    $positions = $this->apiService->get_positions(['position_code' => $res['data']['position_code']]);
                    $position_id = $positions['data'][0]['id'];

                    $this->__createPositionIfNotExists($position_id, $request);

                    // ** create activity log user
                    ActivityLog::created_activity('CRUD position', 'User ' . auth()->user()->username . ' create new position');
                    return response()->json($res);
                } else {
                    return response()->json($res);
                }
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  $position
     * @return \Illuminate\Http\Response
     */
    public function show($position)
    {
        if (!auth()->user()->can('position.view')) {
            abort(403, 'Unauthorized action.');
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  $position
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit($position, Request $request)
    {
        if (!auth()->user()->can('position.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $position = $this->apiService->read_position($position);
            $positionDB = Position::where('position_id', $position['id'])->with('user')->first();
            if ($positionDB) {
                $position['must_attend'] = (bool)$positionDB->must_attend;
                $position['permanently'] = (bool)$positionDB->permanently;
                $position['extra_pay_check'] = (bool)$positionDB->extra_pay;
                $position['extra_pay'] = $positionDB->extra_pay;
                $position['updated_by'] = 'Diperbarui: ' . $positionDB->user->first_name . ', ' . $positionDB->updated_at;
            } else {
                $position['must_attend'] = false;
                $position['permanently'] = false;
                $position['extra_pay_check'] = false;
                $position['extra_pay'] = null;
                $position['updated_by'] = null;
            }

            $positions = $this->apiService->get_positions([]);
            $render = view('Organization.position.edit', compact('position', 'positions'))->render();

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
     * @param  $position
     * @return \Illuminate\Http\Response
     */
    public function update($position, Request $request)
    {
        if (!auth()->user()->can('position.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validator = Validator::make($request->all(), $this->rules());
            Log::info($request);

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $position_data = $request->only([
                    'position_code', 'position_name', 'must_attend', 'extra_pay_check', 'extra_pay', 'permanently'
                ]);
                $position_data['id'] = $position;

                $res = $this->apiService->update_position($position_data);
                if ($res['status'] == 'success') {
                    $this->__createPositionIfNotExists($position, $request);

                    // ** create activity log user
                    ActivityLog::created_activity('CRUD position', 'User ' . auth()->user()->username . ' edit data position');
                    return response()->json($res);
                } else {
                    return response()->json($res);
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
     * @param  $position
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy($position, Request $request)
    {
        if (!auth()->user()->can('position.delete') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $res = $this->apiService->delete_position($position);

            // ** create activity log user
            ActivityLog::created_activity('CRUD position', 'User ' . auth()->user()->username . ' delete data position');
            return response()->json($res);
        } catch (\Exception $e) {
            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Creates new position if doesn't exist
     *
     * @param  int $position_id
     * @return void
     */
    private function __createPositionIfNotExists($position_id, Request $request)
    {
        $position = Position::where('position_id', $position_id)->first();
        if (empty($position)) {
            $position = new Position([
                'position_id' => $position_id,
                'created_user' => auth()->user()->id,
                'updated_user' => auth()->user()->id,
                'must_attend' => $request['must_attend'] ?? 0,
                'permanently' => $request['permanently'] ?? 0,
                'extra_pay' => (!empty($request->input('extra_pay_check'))) ?
                    str_replace('.', '', $request['extra_pay']) : null
            ]);
            $position->save();
        } else {
            $position->update([
                'position_id' => $position_id,
                'extra_pay' => (!empty($request['extra_pay_check'])) ?
                    str_replace('.', '', $request['extra_pay']) : null,
                'updated_user' => auth()->user()->id,
                'must_attend' => $request['must_attend'] ?? 0,
                'permanently' => $request['permanently'] ?? 0,
            ]);
        }
    }


    /**
     * Rules validation Position.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'position_code' => 'required|string|max:255',
            'position_name' => 'required|string|max:255',
            'extra_pay_check' => 'nullable',
            'extra_pay' => [
                Rule::requiredIf(function () {
                    return request()->get('extra_pay_check');
                })
            ],
        ];
    }
}
