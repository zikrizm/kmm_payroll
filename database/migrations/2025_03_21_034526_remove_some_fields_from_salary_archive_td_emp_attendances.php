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
        Schema::table('salary_archive_td_emp_attendances', function (Blueprint $table) {
            $table->dropForeign(['timetable_id']);
            $table->dropForeign(['operational_id']);
            $table->dropForeign(['op_has_timetable_id']);
            $table->dropForeign(['attendance_tso_id']);
            $table->dropForeign(['attendance_lb_id']);
    
            $table->dropColumn([
                'timetable_id', 
                'operational_id', 
                'op_has_timetable_id', 
                'attendance_tso_id', 
                'attendance_lb_id', 
                'attendance_lb_status', 
                'operational_plusm_value', 
                'operational_status', 
                'operational_note',
                'first_punch',
                'last_punch',
                'is_addition_date',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('salary_archive_td_emp_attendances', function (Blueprint $table) {
            $table->integer('timetable_id')->nullable()->unsigned();
            $table->integer('operational_id')->nullable()->unsigned();
            $table->integer('op_has_timetable_id')->nullable()->unsigned();
            $table->integer('attendance_tso_id')->nullable()->unsigned();
            $table->integer('attendance_lb_id')->nullable()->unsigned();
            
            $table->enum('attendance_lb_status', ['accept', 'cancel' ])->nullable();
            $table->float('operational_plusm_value')->nullable();
            $table->enum('operational_status', ['valid', 'invalid'])->nullable();
            $table->string('operational_note')->nullable();
            
            $table->dateTime('first_punch')->nullable();
            $table->dateTime('last_punch')->nullable();
            $table->dateTime('is_addition_date')->nullable();
            
            $table->foreign('attendance_lb_id')->references('id')->on('attendance_lbs')->onDelete('cascade');
            $table->foreign('attendance_tso_id')->references('id')->on('attendance_tsos')->onDelete('cascade');
            $table->foreign('op_has_timetable_id')->references('id')->on('operational_has_timetables')->onDelete('cascade');
            $table->foreign('operational_id')->references('id')->on('operationals')->onDelete('cascade');
            $table->foreign('timetable_id')->references('id')->on('timetables')->onDelete('cascade');
        });
    }
};
