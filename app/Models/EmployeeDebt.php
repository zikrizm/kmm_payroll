<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDebt extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Get the user that owns the work section.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'updated_user');
    }

    /**
     * Get the employee that owns the work section.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    /**
     * Get the employee_debt_pays.
     */
    public function employee_debt_pays()
    {
        return $this->hasMany(EmployeeDebtPay::class, 'employee_debt_id');
    }
}
