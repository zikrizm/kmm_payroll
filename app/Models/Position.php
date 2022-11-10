<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
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
     * Get the user that owns the work section.
     */
    public function employee_has_position()
    {
        return $this->hasMany(EmployeeHasPosition::class, 'position_id', 'position_id');
    }
    /**
     * Get the request_tasks.
     */
    public function request_tasks()
    {
        return $this->hasMany(RequestTask::class, 'position_id');
    }
}
