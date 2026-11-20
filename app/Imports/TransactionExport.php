<?php

namespace App\Imports;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Utils\AttendanceUtil;
use App\Services\Api\ApiServices;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Carbon;


class TransactionExport implements FromArray, WithHeadings
{
    protected $request;
    protected $apiService;
    protected $attendanceUtil;

    function __construct(Request $request, ApiServices $apiService, AttendanceUtil $attendanceUtil = null)
    {
        $this->request = $request;
        $this->apiService = $apiService;
        $this->attendanceUtil = $attendanceUtil;
    }

    private function departmentCodesFromRequest(): ?array
    {
        if ($this->request->has('department_codes')) {
            $codes = $this->request->input('department_codes');
            if (!is_array($codes)) {
                $codes = ($codes !== null && $codes !== '') ? [$codes] : [];
            }
            $filtered = array_values(array_filter($codes, fn ($code) => $code !== null && $code !== ''));

            return empty($filtered) ? null : $filtered;
        }

        return null;
    }

    private function resolveDateRange(): array
    {
        if ($this->request->filled('start_date') && $this->request->filled('end_date')) {
            $start = Carbon::createFromFormat('d-m-Y', $this->request->input('start_date'))->startOfDay();
            $end = Carbon::createFromFormat('d-m-Y', $this->request->input('end_date'))->endOfDay();

            return [$start, $end];
        }

        if (!empty($this->request->input('date'))) {
            $start = Carbon::parse($this->request->date['start_time'])->startOfDay();
            $end = Carbon::parse($this->request->date['end_time'])->endOfDay();

            return [$start, $end];
        }

        $start = Carbon::now()->startOfWeek()->startOfDay();
        $end = Carbon::now()->endOfWeek()->endOfDay();

        return [$start, $end];
    }

    private function resolveDepartments(?array $departmentCodes): array
    {
        if (empty($departmentCodes) || !$this->attendanceUtil) {
            return ['ids' => null, 'names' => []];
        }

        $departments = collect($this->attendanceUtil->getDepartment());
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

        $names = collect($departmentCodes)
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

        return [
            'ids' => $ids->isEmpty() ? null : $ids->implode(','),
            'names' => $names,
        ];
    }

    /**
     * Same strategy as AttendanceUtil::getMergeAttendance:
     * departments selected -> transactionReport API; otherwise transactions API.
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

        return $this->apiService->get_transactions(array_merge($filter, ['page_size' => 9999]))['data'] ?? [];
    }

    public function array(): array
    {
        $search = '';
        if (!empty($this->request->input('q'))) {
            $search = strtolower($this->request->q);
        }

        [$startCarbon, $endCarbon] = $this->resolveDateRange();
        $startTime = $startCarbon->format('Y-m-d H:i:s');
        $endTime = $endCarbon->format('Y-m-d H:i:s');

        $departmentCodes = $this->departmentCodesFromRequest();
        $resolved = $this->resolveDepartments($departmentCodes);

        $attenDBs = Transaction::whereBetween('punch_time', [$startTime, $endTime]);
        if (!empty($resolved['names'])) {
            $attenDBs = $attenDBs->whereIn('department', $resolved['names']);
        }

        $transactions = $this->fetchApiTransactions($startCarbon, $endCarbon, $resolved['ids']);
        $attenDBs = $attenDBs->get()->toArray();
        $transactions = collect(array_merge($transactions, $attenDBs));

        if ($search != '') {
            $transactions = $transactions->filter(function ($atten) use ($search) {
                return str_contains(strtolower($atten['emp_code'] ?? ''), $search)
                    || str_contains(strtolower($atten['first_name'] ?? ''), $search)
                    || str_contains(strtolower($atten['last_name'] ?? ''), $search)
                    || str_contains(strtolower($atten['verify_type_display'] ?? ''), $search);
            });
        }

        $datas = [];
        foreach ($transactions as $item) {
            $datas[] = [
                'emp_code' => $item['emp_code'],
                'first_name' => $item['first_name'],
                'last_name' => $item['last_name'],
                'department' => $item['department'],
                'date' => $item['punch_time'],
                'status' => $item['punch_state_display'],
                'verify_type_display' => $item['verify_type_display'],
            ];
        }

        return $datas;
    }

    public function headings(): array
    {
        return [
            'emp_code',
            'first_name',
            'last_name',
            'department',
            'date',
            'status',
            'verify_type_display',
        ];
    }
}
