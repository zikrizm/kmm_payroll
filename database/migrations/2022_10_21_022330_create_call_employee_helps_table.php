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
        Schema::create('call_employee_helps', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('call_employee_id')->unsigned();
            $table->integer('emp_id');
            $table->string('emp_code');
            $table->string('emp_first_name')->nullable();
            $table->string('emp_last_name')->nullable();

            $table->foreign('call_employee_id')->references('id')->on('call_employees')->onDelete('cascade');
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
        Schema::dropIfExists('call_employee_helps');
    }
};
