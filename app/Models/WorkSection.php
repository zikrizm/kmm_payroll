<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkSection extends Model
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
     * Get the group that owns the work section.
     */
    public function group()
    {
        return $this->hasMany(Group::class);
    }
    /**
     * Get the shift that owns the work section.
     */
    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function getStatusboxAttribute()
    {
        $classStatus = ($this->status != 'active') ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700';
        return ' <div class="rounded-xl pl-3 pr-2.5 py-1 w-max ' . $classStatus . '">
                <p class="text-xs font-normal flex items-center gap-1 capitalize">' . $this->status . '</p>
            </div>';
    }
}
