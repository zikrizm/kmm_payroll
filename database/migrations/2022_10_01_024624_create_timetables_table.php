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
        // INSERT INTO `timetables` (`id`, `business_id`, `name`, `check_in`, `check_in_plusmn`, `check_out`, `check_out_plusmn`, `work_time`, `work_type`, `cross_day`, `is_without_break`, `ot_roundone_hr`, `ot_roundhalf_hr`, `ot_period`, `ot_pay`, `duration_count_one_shift`,`duration_ot_limit`, `duration_rice_shift`, `created_at`, `updated_at`) VALUES
        Schema::create('timetables', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned();
            $table->string('name');
            $table->time('check_in');
            $table->time('check_out');
            $table->integer('check_in_plusmn')->default(0);
            $table->integer('check_out_plusmn')->default(0);
            $table->integer('cross_day')->nullable();
            $table->integer('work_time')->default(0);
            // $table->integer('work_type')->default(0);
            $table->boolean('is_without_break')->default(0);
            $table->integer('ot_roundone_hr')->nullable();
            $table->integer('ot_roundhalf_hr')->nullable();
            $table->integer('ot_period')->nullable();
            $table->integer('ot_pay')->nullable();
            $table->integer('duration_count_one_shift')->nullable();
            $table->integer('duration_ot_limit')->nullable();
            $table->integer('duration_rice_shift')->nullable();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
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
