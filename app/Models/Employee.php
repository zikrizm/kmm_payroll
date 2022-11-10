<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Get the timetable has break time.
     */
    public function employee_has_position()
    {
        return $this->hasMany(EmployeeHasPosition::class, 'employee_id');
    }

    /**
     * Get the request_task_has_emps.
     */
    public function request_task_has_emps()
    {
        return $this->hasMany(RequestTaskHasEmp::class, 'emp_id', 'emp_id');
    }
}
