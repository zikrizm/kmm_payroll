<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salary_archive_td_emp_shifts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('salary_td_emp_attendance_id')->unsigned();
            $table->foreign('salary_td_emp_attendance_id')->references('id')->on('salary_archive_td_emp_attendances')->onDelete('cascade');
            $table->integer('timetable_id')->nullable()->unsigned();
            $table->foreign('timetable_id')->references('id')->on('timetables')->onDelete('cascade');

            $table->dateTime('working_start_punch')->nullable();
            $table->dateTime('working_end_punch')->nullable();
            $table->dateTime('break_time_start_punch')->nullable();
            $table->dateTime('break_time_end_punch')->nullable();
            $table->float('total_jl')->default(0);
            $table->float('total_hk')->default(0);
            $table->decimal('total_shifted_overtime', 22,0)->default(0);
            $table->decimal('total_hk_pay', 22,0)->default(0);
            $table->decimal('total_hk_bonus', 22,0)->default(0);
            $table->decimal('total_overtime', 22,0)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salary_archive_td_emp_shifts');
    }
};
