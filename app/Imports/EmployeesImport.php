<?php

namespace App\Imports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithProgressBar;
use Maatwebsite\Excel\Concerns\WithValidation;

class EmployeesImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Employee([
            'emp_code' => $row['emp_code'],
            'first_name' => $row['first_name'],
            'department' => $row['department'],
            'emp_type' => $row['emp_type'],
            'area' => $row['area'],
            'gender' => $row['gender'],
            'daily_salary' => $row['daily_salary'],
            'payment_period' => $row['payment_period'],
        ]);
    }

    public function rules(): array
    {
        return [
            'emp_code' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'emp_type' => 'required|string|max:255',
            'area' => 'required|string|max:255',
            'gender' => 'required|string|max:255',
            'daily_salary' => 'required|string|max:255',
            'payment_period' => 'required|string|max:255',
        ];
    }
}
