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
        Schema::create('food_bill_archive_emps', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('food_bill_archive_id')->unsigned();
            $table->foreign('food_bill_archive_id')->references('id')->on('food_bill_archives')->onDelete('cascade');
            $table->integer('emp_id');
            $table->string('emp_code');
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('photo')->nullable();
            $table->dateTime('food_time');
            $table->decimal('quantity', 22)->nullable();

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
        Schema::dropIfExists('food_bill_archive_emps');
    }
};
