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
        return $this->hasMany(EmployeeHasPosition::class, 'emp_id');
    }
}
