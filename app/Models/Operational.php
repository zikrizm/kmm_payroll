<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Operational extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Get the operational_has_depts.
     */
    public function operational_has_depts()
    {
        return $this->hasMany(OperationalHasDept::class, 'operational_id');
    }
    /**
     * Get the operational_has_depts.
     */
    public function operational_has_timetables()
    {
        return $this->hasMany(OperationalHasTimetable::class, 'operational_id');
    }
    /**
     * Get the shift.
     */
    public function shift()
    {
        return $this->belongsTo(Shift::class, 'dept_id', 'dept_id');
    }
}
