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
        Schema::create('timetables', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned();
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->string('name');
            $table->time('in_time');
            // $table->time('begin_in');
            // $table->time('end_in');
            $table->time('out_time');
            // $table->time('begin_out');
            // $table->time('end_out');
            // $table->integer('work_day');
            $table->integer('work_time')->default(0);
            $table->integer('work_type')->default(0);
            $table->integer('cross_day')->nullable();
            $table->boolean('is_overtime')->default(0);
            $table->integer('time_period')->default(0);
            $table->integer('upah_lembur')->default(0);
            $table->boolean('is_overtime_rice')->default(0);
            // $table->string('color_setting');
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
        Schema::dropIfExists('timetables');
    }
};
