<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeHasPosition extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Get the position.
     */
    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }
    /**
     * Get the employee.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
    /**
     * Get the request_tasks.
     */
    public function request_tasks()
    {
        return $this->hasMany(RequestTask::class, 'position_id', 'position_id');
    }
}
