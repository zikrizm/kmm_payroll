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
        Schema::create('employees', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('absen_id');
            $table->integer('business_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->string('email')->nullable()->unique();
            $table->string('name');
            $table->string('gender');
            $table->string('photo')->nullable();
            $table->decimal('daily_salary', 22, 4)->default(0);
            $table->decimal('pay_component', 22, 4)->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->enum('payment_period', ['mounthly', 'weekly', 'daily'])->default('weekly');

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('employees');
    }
};
