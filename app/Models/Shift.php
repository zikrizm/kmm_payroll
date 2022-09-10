<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shift extends Model
{
    use HasFactory;

    protected $appens = [
        'statusbox',
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function getTimeStartAttribute($date)
    {
        return Carbon::parse($date);
    }
    public function getTimeEndAttribute($date)
    {
        return Carbon::parse($date);
    }

    public function getStatusboxAttribute()
    {
        $classStatus = ($this->status != 'active') ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700';
        return ' <div class="rounded-xl pl-2 pr-1.5 py-0.5 w-max ' . $classStatus . '">
                <p class="text-xs font-normal flex items-center gap-1">' . $this->status . '</p>
            </div>';
    }
}
