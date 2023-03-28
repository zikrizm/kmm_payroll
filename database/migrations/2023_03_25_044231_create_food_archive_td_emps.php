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
        Schema::create('food_archive_td_emps', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('emp_id');
            $table->string('emp_code');
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('photo')->nullable();
            $table->integer('food_archive_td_id')->unsigned();
            $table->foreign('food_archive_td_id')->references('id')->on('food_archive_tds')->onDelete('cascade');
            $table->decimal('total', 22,0)->nullable();
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
        Schema::dropIfExists('food_archive_td_emps');
    }
};
