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
        return $this->belongsTo(Employee::class, 'emp_id');
    }
}
