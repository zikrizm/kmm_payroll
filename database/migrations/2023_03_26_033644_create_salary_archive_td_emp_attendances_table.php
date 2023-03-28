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
        Schema::create('salary_archive_td_emp_attendances', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('salary_td_emp_id')->unsigned();
            $table->foreign('salary_td_emp_id')->references('id')->on('salary_archive_td_emps')->onDelete('cascade');
            $table->integer('timetable_id')->nullable()->unsigned();
            $table->foreign('timetable_id')->references('id')->on('timetables')->onDelete('cascade');
            $table->integer('operational_id')->nullable()->unsigned();
            $table->foreign('operational_id')->references('id')->on('operationals')->onDelete('cascade');
            $table->integer('op_has_timetable_id')->nullable()->unsigned();
            $table->foreign('op_has_timetable_id')->references('id')->on('operational_has_timetables')->onDelete('cascade');
            $table->integer('attendance_tso_id')->nullable()->unsigned();
            $table->foreign('attendance_tso_id')->references('id')->on('attendance_tsos')->onDelete('cascade');
            $table->integer('attendance_lb_id')->nullable()->unsigned();
            $table->foreign('attendance_lb_id')->references('id')->on('attendance_lbs')->onDelete('cascade');
            $table->enum('attendance_lb_status', ['accept', 'cancel' ])->nullable();
            $table->float('operational_plusm_value')->nullable();
            $table->enum('operational_status', ['valid', 'invalid'])->nullable();
            $table->string('operational_note')->nullable();

            $table->date('attendance_date')->nullable();
            $table->text('value_string')->nullable();
            $table->dateTime('first_punch')->nullable();
            $table->dateTime('last_punch')->nullable();
            $table->float('JL')->default(0);
            $table->float('HK')->default(0);
            $table->decimal('be_one_shift', 22,0)->default(0);
            $table->decimal('HK_pay_value', 22,0)->default(0);
            $table->decimal('JL_pay_value', 22,0)->default(0);
            $table->decimal('tbhn_u_libur_pay_value', 22,0)->default(0);
            $table->boolean('is_holiday')->default(0);
            $table->boolean('is_addition_date')->default(0);
            $table->boolean('is_counting_salary')->default(0);
            $table->boolean('is_counting_overtime')->default(0);
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
        Schema::dropIfExists('salary_archive_td_emp_attendances');
    }
};
