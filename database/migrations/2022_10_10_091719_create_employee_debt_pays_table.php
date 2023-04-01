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
        Schema::create('employee_debt_pays', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_debt_id')->unsigned();
            $table->foreign('employee_debt_id')->references('id')->on('employee_debts')->onDelete('cascade');
            $table->dateTime('debt_payment_date');
            $table->decimal('payment', 22, 2)->nullable();
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
        Schema::dropIfExists('employee_debt_pays');
    }
};
