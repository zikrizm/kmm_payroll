<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftDay extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];


    /**
     * Get the shiftday_has_timetable.
     */
    public function shiftday_has_timetable()
    {
        return $this->hasMany(ShiftDayHasTimetable::class, 'shift_day_id');
    }
    /**
     * Get the shiftday_has_timetable.
     */
    public function shiftday_has_timetables()
    {
        return $this->hasMany(ShiftayDayHasTimetable::class, 'shift_day_id');
    }
}
