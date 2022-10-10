<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class UsersImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $user = new User([
            "first_name" => $row['first_name'],
            "last_name" => $row['last_name'],
            "username" => $row['username'],
            "email" => $row['email'],
            "status" => $row['status'],
            "business_id" => 1,
            "password" => Hash::make('password')
        ]);

        return $user;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'email' => 'required|unique:users,email',
            'username' => 'required|unique:users,username',
            'status' => 'required|in:active,inactive',
            'password' => 'required|min:6|max:255',

        ];
    }
}
