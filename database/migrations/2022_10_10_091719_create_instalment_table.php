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
        Schema::create('instalment', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_debt_id')->unsigned();

            $table->dateTime('date');
            $table->foreign('employee_debt_id')->references('id')->on('employee_debts')->onDelete('cascade');
            $table->integer('created_user')->unsigned();
            $table->integer('updated_user')->nullable()->unsigned();
            $table->foreign('created_user')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_user')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('instalment');
    }
};
