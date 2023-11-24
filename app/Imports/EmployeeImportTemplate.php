<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;


class EmployeeImportTemplate implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [];
    }

    public function headings(): array
    {
        return [
            'emp_code',
            'first_name',
            'department',
            'emp_type',
            'area',
            'gender',
            'daily_salary',
            'payment_period',
        ];
    }
}
