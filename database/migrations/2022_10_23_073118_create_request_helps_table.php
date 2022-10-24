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
        Schema::create('request_helps', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned();
            $table->integer('operational_has_dept_id')->unsigned();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->integer('created_user')->unsigned();
            $table->integer('updated_user')->nullable()->unsigned();

            $table->foreign('operational_has_dept_id')->references('id')->on('operational_has_depts')->onDelete('cascade');
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
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
        Schema::dropIfExists('request_helps');
    }
};
