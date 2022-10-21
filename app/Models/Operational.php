<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
     * Get the timetable has break time.
     */
    public function operational_groups()
    {
        return $this->hasMany(OperationalGroup::class, 'operational_id');
    }
}
