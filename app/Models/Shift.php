<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class Shift extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Scope a query to only include active shifts.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Resolve status for a newly created shift in a department.
     */
    public static function resolveStatusForNew(int $businessId, int $deptId): string
    {
        $hasActive = static::where('business_id', $businessId)
            ->where('dept_id', $deptId)
            ->where('status', self::STATUS_ACTIVE)
            ->exists();

        return $hasActive ? self::STATUS_INACTIVE : self::STATUS_ACTIVE;
    }

    /**
     * Activate this shift and deactivate other active shifts in the same department.
     */
    public function activateForDepartment(): void
    {
        DB::transaction(function () {
            static::where('business_id', $this->business_id)
                ->where('dept_id', $this->dept_id)
                ->where('id', '!=', $this->id)
                ->where('status', self::STATUS_ACTIVE)
                ->update(['status' => self::STATUS_INACTIVE]);

            $this->update(['status' => self::STATUS_ACTIVE]);
        });
    }

    /**
     * Get the shiftday.
     */
    public function shiftday()
    {
        return $this->hasMany(ShiftDay::class, 'shift_id');
    }

    /**
     * Get the shiftday.
     */
    public function shiftdays()
    {
        return $this->hasMany(ShiftDay::class, 'shift_id');
    }
}
