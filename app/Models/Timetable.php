<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
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
    public function timetable_has_break_time()
    {
        return $this->hasMany(TimetableHasBreakTime::class, 'timetable_id');
    }
}
