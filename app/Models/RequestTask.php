<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestTask extends Model
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
        return $this->belongsTo(Position::class, 'position_id', 'id');
    }
    /**
     * Get the request_task_has_emps.
     */
    public function request_task_has_emps()
    {
        return $this->hasMany(RequestTaskHasEmp::class, 'request_task_id', 'id');
    }
}
