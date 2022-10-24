<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestHelp extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Get the request_help_has_emps.
     */
    public function request_help_has_emps()
    {
        return $this->hasMany(RequestHelpHasEmp::class, 'request_help_id');
    }

    /**
     * Get the operational_has_dept.
     */
    public function operational_has_dept()
    {
        return $this->belongsTo(OperationalHasDept::class, 'operational_has_dept_id');
    }
}
