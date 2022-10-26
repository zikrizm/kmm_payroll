<?php

namespace App\Imports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class TransactionsImport implements ToModel, WithHeadingRow, WithValidation
{
   /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Transaction([
            'emp_code' => $row['emp_code'],
            'department' => $row['department'],
            'punch_time' => $row['punch_time'],
            'punch_state_display' => $row['punch_state_display'],
        ]);
    }

    public function rules(): array
    {
        return [
            'emp_code' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'punch_time' => 'required|string|max:255',
            'punch_state_display' => 'required|string|max:255',
        ];
    }
}
