<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryArchiveTd extends Model
{
    use HasFactory;
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function salary_archive_td_emps()
    {
        return $this->hasMany(SalaryArchiveTdEmp::class, 'salary_archive_td_id');
    }
}
