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
            $table->integer('in_time_plus_minus')->default(0);
            $table->time('out_time');
            $table->integer('out_time_plus_minus')->default(0);
            $table->integer('work_time')->default(0);
            $table->integer('work_type')->default(0);
            $table->integer('cross_day')->nullable();

            $table->boolean('is_without_break')->default(0);

            $table->integer('overtime_one_hour')->nullable();
            $table->integer('overtime_half_hour')->nullable();

            $table->integer('time_period')->nullable();
            $table->integer('overtime_pay')->nullable();
            $table->integer('duration_calculate_one_shift')->nullable();

            $table->integer('duration_rice_shift')->nullable();
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
