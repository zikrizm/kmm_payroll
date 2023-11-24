<?php

namespace App\Imports;

use App\Models\Employee;
use App\Services\Api\ApiServices;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;


class EmployeeImportExport implements FromArray, WithHeadings
{
    private $apiService;

    public function __construct(ApiServices $apiService)
    {
        $this->apiService = $apiService;
    }
    public function array(): array
    {
        $employees = $this->apiService->get_employees(['page_size' => 9999]);
        if (!empty($employees)) {
            $emp_ids = array_column($employees['data'], 'id');
            $employeedbs = Employee::whereIn('emp_id', $emp_ids)->with('employee_has_position')->get()->toArray();

            $datas = [];
            foreach ($employeedbs as  $item) {
                $key = array_search($item->emp_code, array_column($employees, 'emp_code'));
                if ($key == '') {
                    $emp = $employees[$key];

                    $_positions = [];
                    if (!empty($item['employee_has_position'])) {
                       $_positions= array_column($item['employee_has_position'], 'position_id');
                    }

                    $datas[] = [
                        'emp_code' => $emp['emp_code'],
                        'first_name' => $emp['first_name'],
                        'last_name' => $emp['last_name'],
                        'emp_type' => $emp['emp_type'],
                        'address' => $emp['address'],
                        'city' => $emp['city'],
                        'gender' =>  $emp['gender'],
                        'hire_date' => $emp['hire_date'],
                        'area' => $emp['area'],
                        'department' => $emp['department'],
                        'position' => implode(",", $_positions),
                        'daily_salary' => $item['daily_salary'],
                        'payment_period' => $item['payment_period'],
                    ];
                }
            }
            
            Log::info(json_encode($datas));
            return $datas;
        } else {
            return [];
        }
    }

    public function headings(): array
    {
        return [
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
