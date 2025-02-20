<?php

namespace App\Imports;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Services\Api\ApiServices;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Carbon;


class TransactionExport implements FromArray, WithHeadings
{
    protected $request;

    function __construct(Request $request, ApiServices $apiService)
    {
        $this->request = $request;
        $this->apiService = $apiService;
    }

    public function array(): array
    {
        $filter = [];
        $attenDBs = new Transaction();
        $search = '';
        if (!empty($this->request->input('q'))) {
            $search = strtolower($this->request->q);
        }

        if (!empty($this->request->input('date'))) {
            $filter['start_time'] = Carbon::parse($this->request->date['start_time'])->hour(0)->minute(0)->second(0)->format('Y-m-d H:i:s');
            $filter['end_time'] = Carbon::parse($this->request->date['end_time'])->hour(23)->minute(59)->second(59)->format('Y-m-d H:i:s');
            $attenDBs = $attenDBs->whereBetween('punch_time', [$filter['start_time'], $filter['end_time']]);
        }
      
        $transactions = $this->apiService->get_transactions(array_merge($filter, ['page_size' => 9999]))['data'];

        $attenDBs = $attenDBs->get()->toArray();
        $transactions = collect(array_merge($transactions, $attenDBs));
        if ($search != '') {
            $transactions = $transactions->filter(function ($atten) use ($search) {
                return str_contains(strtolower($atten['emp_code']), $search)||str_contains(strtolower($atten['first_name']), $search)||
                str_contains(strtolower($atten['last_name']), $search)||str_contains(strtolower($atten['verify_type_display']), $search);
            });
        }

        $datas = [];
            foreach ($transactions as  $item) {
               

                    $datas[] = [
                        'emp_code' => $item['emp_code'],
                        'first_name' => $item['first_name'],
                        'last_name' => $item['last_name'],
                        'department' => $item['department'],
                        'date' => $item['punch_time'],
                        'status' => $item['punch_state_display'],
                        'verify_type_display' =>  $item['verify_type_display'],
                    ];
                
            }
            
            return $datas;
        return [];
        // $employees = $this->apiService->get_employees(['page_size' => 9999]);
        // if (!empty($employees)) {
        //     $emp_ids = array_column($employees['data'], 'id');
        //     $employeedbs = Employee::whereIn('emp_id', $emp_ids)->get()->toArray();

        //     $datas = [];
        //     foreach ($employeedbs as  $item) {
        //         $key = array_search($item['emp_code'], array_column($employees['data'], 'emp_code'));
        //         if ($key != '') {
        //             $emp = $employees['data'][$key];

        //             $_areas = [];
        //             if (!empty($emp['area'])) {
        //                $_areas= array_column($emp['area'], 'area_code');
        //             }

        //             $datas[] = [
        //                 'emp_code' => $emp['emp_code'],
        //                 'first_name' => $emp['first_name'],
        //                 'last_name' => $emp['last_name'],
        //                 'emp_type' => $emp['emp_type'],
        //                 'address' => $emp['address'],
        //                 'city' => $emp['city'],
        //                 'gender' =>  $emp['gender'],
        //                 'hire_date' => $emp['hire_date'],
        //                 'area' => implode(",", $_areas),
        //                 'department' => $emp['department']['dept_code'],
        //                 'daily_salary' => $item['daily_salary'],
        //                 'payment_period' => $item['payment_period'],
        //             ];
        //         }
        //     }
            
        //     return $datas;
        // } else {
        //     return [];
        // }
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
