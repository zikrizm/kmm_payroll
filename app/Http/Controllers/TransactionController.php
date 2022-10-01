<?php

namespace App\Http\Controllers;

use App\Utils\ResponseUtil;
use Illuminate\Http\Request;
use App\Services\Api\ApiServices;
use Illuminate\Support\Facades\Log;
use App\Exceptions\ResponseExeception;
use App\Models\Transaction;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
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
        if (!auth()->user()->can('transaction.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            if (request()->ajax()) {
                $order = null;
                $filter = [];
                if (!empty($request->input('q'))) {
                    $filter['emp_code'] = $request->q;
                    // $filter['terminal_sn'] = $request->q;
                }

                if (!empty($request->input('transaction_date'))) {
                    $filter['start_time'] = $request->transaction_date['start_time'];
                    $filter['end_time'] = $request->transaction_date['end_time'];
                }

                if (!empty($request->input('page'))) {
                    $filter['page'] = $request->page;
                }

                if ($request->has('sort')) {
                    $filter['ordering'] = $request->sort['name'];
                    $order = $request->sort['order'];
                }

                $transactionsss = Transaction::all();

                $transactions = $this->apiService->get_transactions($filter);
                $transactions['next'] = $this->getParamsUrl($transactions['next'], 'page');
                $transactions['previous'] = $this->getParamsUrl($transactions['previous'], 'page');

                $render =  view('Transaction.transaction.table', compact('transactions', 'order'))->render();
                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            }

            return view('Transaction.transaction.index');
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
            $render = view('Transaction.transaction.create')->render();

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
        Log::info($request);

        try {
            $validator = Validator::make($request->all(), $this->rules());

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $transaction_data = $request->only(['emp_id', 'punch_time', 'punch_state', 'work_code', 'apply_reason']);

                $employee = $this->apiService->read_employee($transaction_data['emp_id']);
                $transaction_data['emp'] = $employee['emp_code'];
                $transaction_data['emp_code'] = $employee['emp_code'];
                $transaction_data['first_name'] = $employee['first_name'];
                $transaction_data['last_name'] = $employee['last_name'];
                $transaction_data['punch_state_display'] = ($transaction_data['punch_state'] == 1) ? 'Check In' : 'Check Out';
                $transaction_data['verify_type_display'] = 'Manual';
                $transaction_data['department'] = (!empty($employee['department'])) ? $employee['department']['dept_name'] : null;
                $transaction_data['position'] = (!empty($employee['position'])) ? $employee['position']['position_name'] : null;
                // TODO belom tau isinya apa
                $transaction_data['area_alias'] = '';

                $transaction = new Transaction($transaction_data);
                $transaction->save();

                return $this->buildRes->RESPONSE_REQ('success', null,  ['success' => 'Add transaction succesfully']);
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

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $position_data = $request->only(['position_code', 'position_name', 'parent_position']);
                $position_data['id'] = $position;

                $res = $this->apiService->update_position($position_data);
                return response()->json($res);
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
            return response()->json($res);
        } catch (\Exception $e) {
            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    public function getParamsUrl($url, $field)
    {
        if (empty($url)) return null;

        $parts = parse_url($url);
        if (empty($parts['query'])) return 1;

        parse_str($parts['query'], $query);
        return $query[$field] ?? 1;
    }

    /**
     * Rules validation department.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'emp_id' => 'required|string|max:255',
            'punch_time' => 'required',
        ];
    }
}
