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
        Schema::create('timetable_has_break_times', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('break_time_id')->unsigned();
            $table->integer('timetable_id')->unsigned();
            $table->integer('business_id')->unsigned();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->foreign('break_time_id')->references('id')->on('break_times')->onDelete('cascade');
            $table->foreign('timetable_id')->references('id')->on('timetables')->onDelete('cascade');
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
        Schema::dropIfExists('timetable_has_break_times');
    }
};
