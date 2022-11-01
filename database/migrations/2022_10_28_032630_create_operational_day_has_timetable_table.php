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
        Schema::create('operational_has_timetables', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('operational_id')->unsigned();
            $table->integer('timetable_id')->unsigned();
            $table->integer('ot_limit')->default(0);
            $table->enum('status',['active','inactive'])->default('active');

            $table->foreign('timetable_id')->references('id')->on('timetables')->onDelete('cascade');
            $table->foreign('operational_id')->references('id')->on('operationals')->onDelete('cascade');
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
        Schema::dropIfExists('operational_has_timetables');
    }
};
