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
        Schema::create('attendance_tsos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('emp_id');
            $table->string('emp_code');
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('photo')->nullable();
            $table->integer('dept_id');
            $table->string('dept_code');
            $table->string('dept_name');
            $table->date('tso_date');
            $table->dateTime('first_punch')->nullable();
            $table->dateTime('last_punch')->nullable();
            $table->string('note')->nullable();
            $table->integer('operational_id')->nullable()->unsigned();
            $table->foreign('operational_id')->references('id')->on('operationals')->onDelete('cascade');
            $table->integer('timetable_id')->nullable()->unsigned();
            $table->foreign('timetable_id')->references('id')->on('timetables')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_tsos');
    }
};
