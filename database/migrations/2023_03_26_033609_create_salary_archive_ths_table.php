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
        Schema::create('salary_archive_ths', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned();
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->date('start_date_work_day');
            $table->date('end_date_work_day');
            $table->date('start_date_overtime');
            $table->date('end_date_overtime');
            $table->integer('dept_id');
            $table->string('dept_code');
            $table->string('dept_name');
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
        Schema::dropIfExists('salary_archive_ths');
    }
};
