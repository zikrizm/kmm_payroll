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
        Schema::create('payroll_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('calculate_date');
            $table->dateTime('work_date_start');
            $table->dateTime('work_date_end');
            $table->dateTime('overtime_start');
            $table->dateTime('overtime_end');
            $table->integer('emp_id')->unsigned();
            $table->integer('dept_id')->unsigned();

            $table->double('HK');
            $table->double('JL');
            $table->decimal('debt', 22, 2)->nullable();
            $table->decimal('salary', 22, 2)->nullable();
            $table->decimal('overtime', 22, 2)->nullable();
            $table->decimal('overtime_pay', 22, 2)->nullable();
            $table->decimal('in', 22, 2)->nullable();
            $table->decimal('tbhn_pay', 22, 2)->nullable();
            $table->decimal('position_pay', 22, 2)->nullable();
            $table->integer('count_one_shift')->nullable();
            $table->decimal('total', 22, 2)->nullable();

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
        Schema::dropIfExists('payroll_reports');
    }
};
