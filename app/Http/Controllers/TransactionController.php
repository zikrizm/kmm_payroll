<?php

namespace App\Http\Controllers;

use App\Models\Employee;

use App\Models\ActivityLog;
use App\Models\Transaction;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Services\Api\ApiServices;
use App\Imports\TransactionsImport;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exceptions\ResponseExeception;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Validators\ValidationException;
use Maatwebsite\Excel\Exceptions\NoTypeDetectedException;


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
        if (!auth()->user()->can('attendance-manual.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            if (request()->ajax()) {
                $order = null;
                $filter = [];
                $page_size = 10;

                $attenDBs = new Transaction();
                $search = '';
                if (!empty($request->input('q'))) {
                    $search = strtolower($request->q);
                }

                if (!empty($request->input('date'))) {
                    $filter['start_time'] = Carbon::parse($request->date['start_time'])->hour(0)->minute(0)->second(0)->format('Y-m-d H:i:s');
                    $filter['end_time'] = Carbon::parse($request->date['end_time'])->hour(23)->minute(59)->second(59)->format('Y-m-d H:i:s');
                    $attenDBs = $attenDBs->whereBetween('punch_time', [$filter['start_time'], $filter['end_time']]);
                }

                if ($request->has('page_size')) {
                    $filter['page_size'] = $request->page_size;
                    $page_size = $request->page_size;
                }

                $page = 1;
                if (!empty($request->input('page'))) {
                    $page = (int)$request->page;
                }

                if ($request->has('sort')) {
                    $filter['ordering'] = $request->sort['name'];
                    $order = $request->sort['order'];
                }
                $atten_count = $this->apiService->get_transactions($filter)['count'];
                $transactions = $this->apiService->get_transactions(array_merge($filter, ['page_size' => $atten_count]))['data'];

                $attenDBs = $attenDBs->get()->toArray();
                $transactions = collect(array_merge($transactions, $attenDBs));
                if ($search != '') {
                    $transactions = $transactions->filter(function ($atten) use ($search) {
                        return str_contains(strtolower($atten['emp_code']), $search)||str_contains(strtolower($atten['first_name']), $search)||
                        str_contains(strtolower($atten['last_name']), $search)||str_contains(strtolower($atten['verify_type_display']), $search);
                    });
                }
                $transactions_count = $transactions->count();
                $next = (ceil($transactions_count / $page_size) == $page) ?  null : $page + 1;
                $transactions = $transactions->skip(($page - 1) * $page_size)->take($page_size);
                $transactions = collect([
                    'count' => $transactions_count,
                    'data' => $transactions,
                    'next' => $next,
                    'previous' => $page - 1,
                    'lastPage' => ceil($transactions_count / $page_size),
                    'currentPage' => $page,
                ]);

                $render =  view('Transaction.transaction.table', compact('transactions', 'order', 'page_size'))->render();
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
        if (!auth()->user()->can('attendance-manual.create') || !request()->ajax()) {
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
        if (!auth()->user()->can('attendance-manual.create')  || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validator = Validator::make($request->all(), $this->rules());

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $transaction_data = $request->only(['emp_id', 'punch_time', 'punch_state', 'apply_reason']);

                $employee = $this->apiService->read_employee($transaction_data['emp_id']);
                $transaction_data['emp'] = $transaction_data['emp_id'];
                $transaction_data['emp_code'] = $employee['emp_code'];
                $transaction_data['first_name'] = $employee['first_name'];
                $transaction_data['last_name'] = $employee['last_name'];
                $transaction_data['punch_state_display'] = ($transaction_data['punch_state'] == 1) ? 'Check In' : 'Check Out';
                $transaction_data['verify_type_display'] = 'Manual';
                $transaction_data['department'] = (!empty($employee['department'])) ? $employee['department']['dept_name'] : null;
                $transaction_data['position'] = (!empty($employee['position'])) ? $employee['position']['position_name'] : null;

                $transaction = new Transaction($transaction_data);
                $transaction->save();

                // ** create activity log user
                ActivityLog::created_activity('CRUD manual attendance', 'User ' . auth()->user()->username . ' create new manual attendance');
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
     * @param  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show($transaction)
    {
        if (!auth()->user()->can('attendance-manual.view')) {
            abort(403, 'Unauthorized action.');
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  $transaction
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit($transaction, Request $request)
    {
        if (!auth()->user()->can('attendance-manual.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => ['Edit menu not available']]);
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Transaction $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(Transaction $transaction, Request $request)
    {
        if (!auth()->user()->can('attendance-manual.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validator = Validator::make($request->all(), $this->rules());

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                return $this->buildRes->RESPONSE_REQ('error', null, ['error' => ['Update menu not available']]);
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $transaction
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy($transaction, Request $request)
    {
        if (!auth()->user()->can('attendance-manual.delete') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $res = $this->apiService->delete_transaction($transaction);
            $transaction = Transaction::where('id', $transaction)->first();
            if (!empty($transaction)) {
                $transaction->delete();
            }

            // ** create activity log user
            ActivityLog::created_activity('CRUD manual attendance', 'User ' . auth()->user()->username . ' delete data manual attendance');
            return $this->buildRes->RESPONSE_REQ('success', null,  ['success' => 'Delete transaction succesfully']);
        } catch (\Exception $e) {
            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
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
            'punch_state' => 'required',
        ];
    }
}
