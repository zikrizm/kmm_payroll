<?php

namespace App\Imports;

use App\Models\Employee;
use App\Services\Api\ApiServices;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;

class EmployeeExport implements FromArray, WithHeadings
{
    protected $apiService;
    protected $request;

    function __construct(ApiServices $apiService, Request $request)
    {
        $this->apiService = $apiService;
        $this->request = $request;
    }

    public function array(): array
    {
        $filter = ['page_size' => 9999];

        if ($this->request->has('q')) {
            $filter['employee_icontains'] = $this->request->q;
        }

        $employees = $this->apiService->get_employees($filter);
        if (!empty($employees)) {
            $emp_ids = array_column($employees['data'], 'id');
            $employeedbs = Employee::whereIn('emp_id', $emp_ids)->get();

            $datas = [];
            foreach ($employees['data'] as $item) {
                $emp_db = $employeedbs->firstWhere('emp_id', $item['id']);
                if($emp_db) {
                    $daily_salary = $emp_db->daily_salary ?? 0;
                    $payment_period = $emp_db->payment_period ?? 'weekly';
                } else {
                    $daily_salary = 0;
                    $payment_period = 'weekly';
                }

                $_areas = [];
                if (!empty($item['area'])) {
                    $_areas= array_column($item['area'], 'area_code');
                }
                $_position = [];
                if (!empty($item['position'])) {
                    $_position= $item['position']['position_code'];
                }

                $datas[] = [
                    'emp_id' => $item['id'],
                    'emp_code' => $item['emp_code'],
                    'first_name' => $item['first_name'],
                    'last_name' => $item['last_name'],
                    'emp_type' => $item['emp_type'],
                    'address' => $item['address'],
                    'city' => $item['city'],
                    'gender' =>  $item['gender'],
                    'hire_date' => $item['hire_date'],
                    'area' => implode(",", $_areas),
                    'department' => $item['department']['dept_code'],
                    'position' => $_position,
                    'daily_salary' => $daily_salary,
                    'payment_period' => $payment_period,
                ];
                
            }
            // foreach ($employeedbs as  $item) {
            //     $key = array_search($item['emp_id'], array_column($employees['data'], 'id'));
            //     if ($key != '') {
            //         $emp = $employees['data'][$key];

            //         $_areas = [];
            //         if (!empty($emp['area'])) {
            //            $_areas= array_column($emp['area'], 'area_code');
            //         }
            //         $_position = [];
            //         if (!empty($emp['position'])) {
            //            $_position= $emp['position']['position_code'];
            //         }

            //         $datas[] = [
            //             'emp_id' => $emp['id'],
            //             'emp_code' => $emp['emp_code'],
            //             'first_name' => $emp['first_name'],
            //             'last_name' => $emp['last_name'],
            //             'emp_type' => $emp['emp_type'],
            //             'address' => $emp['address'],
            //             'city' => $emp['city'],
            //             'gender' =>  $emp['gender'],
            //             'hire_date' => $emp['hire_date'],
            //             'area' => implode(",", $_areas),
            //             'department' => $emp['department']['dept_code'],
            //             'position' => $_position,
            //             'daily_salary' => $item['daily_salary'],
            //             'payment_period' => $item['payment_period'],
            //         ];
            //     }
            // }
            
            return $datas;
        } else {
            return [];
        }
    }

    public function headings(): array
    {
        return [
            'emp_id',
            'emp_code',
            'first_name',
            'last_name',
            'emp_type',
            'address',
            'city',
            'gender',
            'hire_date',
            'area',
            'department',
            'position',
            'daily_salary',
            'payment_period',
        ];
    }
}
