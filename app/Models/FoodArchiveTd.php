<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodArchiveTd extends Model
{
    use HasFactory;
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function food_archive_td_emps()
    {
        return $this->hasMany(FoodArchiveTdEmp::class, 'food_archive_td_id');
    }
    public function food_archive_th()
    {
        return $this->belongsTo(FoodArchiveTh::class, 'food_archive_th_id');
    }
}
