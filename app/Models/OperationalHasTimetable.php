<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationalHasTimetable extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function timetable()
    {
        return $this->belongsTo(Timetable::class, 'timetable_id');
    }
    public function operational()
    {
        return $this->belongsTo(Operational::class, 'operational_id');
    }
}
