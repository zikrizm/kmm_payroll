<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodArchiveEmp extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function food_archive_emp_attendances()
    {
        return $this->hasMany(FoodArchiveEmpAttendance::class, 'food_archive_emp_id');
    }
}
