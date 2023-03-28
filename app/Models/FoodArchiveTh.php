<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodArchiveTh extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function food_archive_tds()
    {
        return $this->hasMany(FoodArchiveTd::class, 'food_archive_th_id');
    }
}
