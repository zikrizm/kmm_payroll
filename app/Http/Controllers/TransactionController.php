<?php

namespace App\Http\Controllers;

use App\Models\Employee;

use App\Models\ActivityLog;
use App\Models\Transaction;
use App\Utils\ResponseUtil;
use App\Utils\AttendanceUtil;
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
use App\Imports\TransactionExport;


class TransactionController extends Controller
{
    private $apiService;
    private $buildRes;
    private $attendanceUtil;
    private $departmentsCache = null;

    public function __construct(ApiServices $apiService, ResponseUtil $buildRes, AttendanceUtil $attendanceUtil)
    {
        $this->apiService = $apiService;
        $this->buildRes = $buildRes;
        $this->attendanceUtil = $attendanceUtil;
    }

    private function departments(): \Illuminate\Support\Collection
    {
        if ($this->departmentsCache === null) {
            $this->departmentsCache = collect($this->attendanceUtil->getDepartment());
        }

        return $this->departmentsCache;
    }

    private function departmentCodesFromRequest(Request $request): ?array
    {
        if ($request->has('department_codes')) {
            $codes = $request->input('department_codes');
            if (!is_array($codes)) {
                $codes = ($codes !== null && $codes !== '') ? [$codes] : [];
            }
            $filtered = array_values(array_filter($codes, fn ($code) => $code !== null && $code !== ''));

            return empty($filtered) ? null : $filtered;
        }

        if ($request->filled('department_code')) {
            $code = $request->input('department_code');
            if (is_array($code)) {
                $filtered = array_values(array_filter($code));

                return empty($filtered) ? null : $filtered;
            }

            return [$code];
        }

        return null;
    }

    private function departmentApiIdsFromCodes(?array $departmentCodes): ?string
    {
        if (empty($departmentCodes)) {
            return null;
        }

        $departments = $this->departments();
        $ids = collect($departmentCodes)
            ->map(function ($code) use ($departments) {
                $byCode = $departments->firstWhere('dept_code', (string) $code);
                if ($byCode) {
                    return $byCode['id'] ?? null;
                }

                $byId = $departments->firstWhere('id', is_numeric($code) ? (int) $code : $code);

                return $byId['id'] ?? null;
            })
            ->filter()
            ->unique()
            ->values();

        return $ids->isEmpty() ? null : $ids->implode(',');
    }

    private function departmentNamesFromCodes(?array $departmentCodes): array
    {
        if (empty($departmentCodes)) {
            return [];
        }

        $departments = $this->departments();

        return collect($departmentCodes)
            ->map(function ($code) use ($departments) {
                $byCode = $departments->firstWhere('dept_code', (string) $code);
                if ($byCode) {
                    return $byCode['dept_name'] ?? null;
                }

                $byId = $departments->firstWhere('id', is_numeric($code) ? (int) $code : $code);

                return $byId['dept_name'] ?? null;
            })
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function resolveTransactionDateRange(Request $request): array
    {
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start = Carbon::createFromFormat('d-m-Y', $request->input('start_date'))->startOfDay();
            $end = Carbon::createFromFormat('d-m-Y', $request->input('end_date'))->endOfDay();

            return [$start, $end];
        }

        if (!empty($request->input('date'))) {
            $start = Carbon::parse($request->date['start_time'])->startOfDay();
            $end = Carbon::parse($request->date['end_time'])->endOfDay();

            return [$start, $end];
        }

        $start = Carbon::now()->startOfWeek()->startOfDay();
        $end = Carbon::now()->endOfWeek()->endOfDay();

        return [$start, $end];
    }

    /**
     * Fetch device punches. /iclock/api/transactions/ has no departments filter,
     * so when departments are selected use /att/api/transactionReport/ (same as AttendanceUtil::getMergeAttendance).
     */
    private function fetchApiTransactions(Carbon $startCarbon, Carbon $endCarbon, ?string $departmentIds): array
    {
        if (!empty($departmentIds)) {
            $reportFilter = [
                'start_date' => $startCarbon->format('Y-m-d'),
                'end_date' => $endCarbon->format('Y-m-d'),
                'departments' => $departmentIds,
            ];

            $atten_datas = $this->apiService->get_transaction_reports($reportFilter) ?? ['count' => 0, 'data' => []];
            $atten_count = $atten_datas['count'] ?? 0;
            if ($atten_count > 0) {
                $atten_datas = $this->apiService->get_transaction_reports(array_merge(['page_size' => $atten_count], $reportFilter))
                    ?? ['count' => 0, 'data' => []];
            }

            return collect($atten_datas['data'] ?? [])->map(function ($item) {
                $punchTime = $item['punch_time'] ?? null;
                if (!empty($item['att_date']) && !empty($item['punch_time']) && !str_contains((string) $item['punch_time'], '-')) {
                    $punchTime = $item['att_date'] . ' ' . $item['punch_time'] . (strlen((string) $item['punch_time']) <= 5 ? ':00' : '');
                }

                $punchState = $item['punch_state'] ?? null;
                $punchStateDisplay = $item['punch_state_display'] ?? null;
                if ($punchStateDisplay === null && $punchState !== null) {
                    if (is_numeric($punchState)) {
                        $punchStateDisplay = ((int) $punchState === 1) ? 'Check In' : 'Check Out';
                    } else {
                        $punchStateDisplay = (string) $punchState;
                    }
                }

                return [
                    'id' => $item['id'] ?? null,
                    'emp' => $item['emp'] ?? $item['emp_code'] ?? null,
                    'emp_code' => $item['emp_code'] ?? null,
                    'first_name' => $item['first_name'] ?? null,
                    'last_name' => $item['last_name'] ?? null,
                    'department' => $item['dept_name'] ?? $item['department'] ?? null,
                    'punch_time' => $punchTime,
                    'punch_state_display' => $punchStateDisplay,
                    'verify_type_display' => $item['verify_type_display'] ?? $item['verify_type'] ?? $item['source'] ?? null,
                ];
            })->values()->all();
        }

        $filter = [
            'start_time' => $startCarbon->format('Y-m-d H:i:s'),
            'end_time' => $endCarbon->format('Y-m-d H:i:s'),
        ];

        $atten_count = $this->apiService->get_transactions($filter)['count'] ?? 0;
        if ($atten_count <= 0) {
            return [];
        }

        return $this->apiService->get_transactions(array_merge($filter, ['page_size' => $atten_count]))['data'] ?? [];
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
            $department_codes = $this->departmentCodesFromRequest($request);
            $start_date = $request->input('start_date');
            $end_date = $request->input('end_date');

            if (request()->ajax()) {
                $order = null;
                $page_size = 10;

                $search = '';
                if (!empty($request->input('q'))) {
                    $search = strtolower($request->q);
                }

                [$startCarbon, $endCarbon] = $this->resolveTransactionDateRange($request);
                $startTime = $startCarbon->format('Y-m-d H:i:s');
                $endTime = $endCarbon->format('Y-m-d H:i:s');

                $departmentIds = $this->departmentApiIdsFromCodes(
                    is_array($department_codes) ? $department_codes : ($department_codes ? [$department_codes] : null)
                );

                $attenDBs = Transaction::whereBetween('punch_time', [$startTime, $endTime]);

                $departmentNames = $this->departmentNamesFromCodes(
                    is_array($department_codes) ? $department_codes : ($department_codes ? [$department_codes] : null)
                );
                if (!empty($departmentNames)) {
                    $attenDBs = $attenDBs->whereIn('department', $departmentNames);
                }

                if ($request->has('page_size')) {
                    $page_size = (int) $request->page_size;
                }

                $page = 1;
                if (!empty($request->input('page'))) {
                    $page = (int) $request->page;
                }

                if ($request->has('sort')) {
                    $order = $request->sort['order'];
                }

                $transactions = $this->fetchApiTransactions($startCarbon, $endCarbon, $departmentIds);

                $attenDBs = $attenDBs->orderBy('punch_time', 'desc')->get()->toArray();
                $transactions = collect(array_merge($transactions, $attenDBs));
                if ($search != '') {
                    $transactions = $transactions->filter(function ($atten) use ($search) {
                        return str_contains(strtolower($atten['emp_code'] ?? ''), $search)
                            || str_contains(strtolower($atten['first_name'] ?? ''), $search)
                            || str_contains(strtolower($atten['last_name'] ?? ''), $search)
                            || str_contains(strtolower($atten['verify_type_display'] ?? ''), $search);
                    });
                }
                $transactions_count = $transactions->count();
                $next = (ceil($transactions_count / $page_size) == $page) ? null : $page + 1;
                $transactions = $transactions->skip(($page - 1) * $page_size)->take($page_size);
                $transactions = collect([
                    'count' => $transactions_count,
                    'data' => $transactions,
                    'next' => $next,
                    'previous' => $page - 1,
                    'lastPage' => ceil($transactions_count / $page_size),
                    'currentPage' => $page,
                ]);

                $render = view('Transaction.transaction.table', compact('transactions', 'order', 'page_size'))->render();
                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            }

            $department_bios = $this->departments();
            if (!is_array($department_codes)) {
                $department_codes = $department_codes ? [$department_codes] : [];
            }

            return view('Transaction.transaction.index', compact('department_bios', 'department_codes', 'start_date', 'end_date'));
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

    public function transactionExport(Request $request)
    {
        return Excel::download(new TransactionExport($request, $this->apiService, $this->attendanceUtil), 'transaction-export.csv', \Maatwebsite\Excel\Excel::CSV);
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
