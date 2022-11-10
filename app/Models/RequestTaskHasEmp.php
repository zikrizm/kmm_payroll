<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestTaskHasEmp extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Get the employee.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_id', 'emp_id');
    }
    /**
     * Get the request_task.
     */
    public function request_task()
    {
        return $this->belongsTo(RequestTask::class, 'request_task_id');
    }
    /**
     * Get the employee_has_position.
     */
    public function employee_has_position()
    {
        return $this->hasMany(EmployeeHasPosition::class, 'position_id', 'position_id');
    }
}
