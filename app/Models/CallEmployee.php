<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CallEmployee extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];


    /**
     * Get the call_employee_helps.
     */
    public function call_employee_helps()
    {
        return $this->hasMany(CallEmployeeHelp::class, 'call_employee_id');
    }
    
    /**
     * Get the operational_group.
     */
    public function operational_group()
    {
        return $this->belongsTo(OperationalGroup::class, 'operational_group_id');
    }
}
