<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
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


    /**
     * Get the work_section that owns the user.
     */
    public function work_section()
    {
        return $this->belongsTo(WorkSection::class);
    }

    public function getStatusboxAttribute()
    {
        $classStatus = ($this->status != 'active') ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700';
        return ' <div class="rounded-xl pl-2 pr-1.5 py-0.5 w-max ' . $classStatus . '">
                <p class="text-xs font-normal flex items-center gap-1">' . $this->status . '</p>
            </div>';
    }
}
