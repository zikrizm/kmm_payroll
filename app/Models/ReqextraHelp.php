<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReqextraHelp extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Get the reqextra_help_has_employee.
     */
    public function reqextra_help_has_employees()
    {
        return $this->hasMany(ReqextraHelpHasEmployee::class, 'reqextra_help_has_employee_id');
    }

    /**
     * Get the operational_group.
     */
    public function operational_group()
    {
        return $this->belongsTo(OperationalScheduleHasDepartment::class, 'operational_schedule_has_employee_id');
    }
}
