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
        Schema::create('shift_day_has_timetables', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shift_day_id')->unsigned();
            $table->integer('timetable_id')->unsigned();

            $table->foreign('shift_day_id')->references('id')->on('shift_days')->onDelete('cascade');
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
        Schema::dropIfExists('shift_day_has_timetables');
    }
};
