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
        Schema::create('salary_archive_perdays', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('salary_archive_employee_id')->unsigned();
            $table->integer('timetable_id')->nullable()->unsigned();
            $table->dateTime('date');
            $table->dateTime('first_punch')->nullable();
            $table->dateTime('last_punch')->nullable();
            $table->boolean('is_less_than_time')->default(0);
            $table->string('atten_value_day')->nullable();
            $table->boolean('status')->default(0);
            $table->string('noted')->nullable();
            $table->integer('calculate_one_shift')->nullable();
            $table->decimal('total_tbhn_u_libur_day', 22, 2)->nullable();
            $table->decimal('total_daily_salary_day', 22, 2)->nullable();
            $table->decimal('total_overtime_day', 22, 2)->nullable();

            $table->foreign('timetable_id')->references('id')->on('timetables')->onDelete('cascade');
            $table->foreign('salary_archive_employee_id')->references('id')->on('salary_archive_employees')->onDelete('cascade');
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
        Schema::dropIfExists('salary_archive_perdays');
    }
};
