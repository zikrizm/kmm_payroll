<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryArchiveTdEmp extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function salary_archive_td_emp_attendances()
    {
        return $this->hasMany(SalaryArchiveTdEmpAttendance::class, 'salary_td_emp_id');
    }
}
