<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryArchiveTh extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function salary_archive_tds()
    {
        return $this->hasMany(SalaryArchiveTd::class, 'salary_archive_th_id');
    }
}
