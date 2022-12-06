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
            $table->integer('salary_archive_id')->unsigned();
            $table->integer('timetable_id')->nullable()->unsigned();
            $table->dateTime('date');
            $table->dateTime('first_punch');
            $table->dateTime('last_punch');
            $table->boolean('is_less_than_time')->default(0);
            $table->string('atten_valur_per_day');
            $table->boolean('status')->default(0);
            $table->string('noted')->nullable();
            $table->decimal('daily_salary_perday', 22, 2)->nullable();
            $table->decimal('tbhn_u_libur_perday', 22, 2)->nullable();
            $table->decimal('overtime_perday', 22, 2)->nullable();

            $table->foreign('timetable_id')->references('id')->on('timetables')->onDelete('cascade');
            $table->foreign('salary_archive_id')->references('id')->on('salary_archives')->onDelete('cascade');
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
