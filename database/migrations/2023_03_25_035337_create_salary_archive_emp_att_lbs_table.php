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
        Schema::create('payroll_archive_employee_attendance_lbstatus', function (Blueprint $table) {
            $table->increments('id');
            $table->date('attendance_lb_date')->nullable();
            $table->enum('attendance_lb_type', ['accept', 'cancel'])->nullable();
            $table->string('note')->nullable();
            $table->integer('salary_archive_emp_att_id')->unsigned();
            $table->foreign('salary_archive_emp_att_id')->references('id')->on('salary_archive_emp_atts')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('attendance_lb_id')->unsigned();
            $table->foreign('attendance_lb_id')->references('id')->on('attendance_lbs')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payroll_archive_employee_attendance_lbstatus');
    }
};
