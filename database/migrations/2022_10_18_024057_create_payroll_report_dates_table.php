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
        Schema::create('payroll_report_dates', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('date');
            $table->string('slug');
            $table->double('total');
            $table->integer('payroll_report_id')->unsigned();
            $table->foreign('payroll_report_id')->references('id')->on('payroll_reports')->onDelete('cascade');

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
        Schema::dropIfExists('payroll_report_dates');
    }
};
