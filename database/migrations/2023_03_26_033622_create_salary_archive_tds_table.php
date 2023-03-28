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
        Schema::create('salary_archive_tds', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('salary_archive_th_id')->unsigned();
            $table->foreign('salary_archive_th_id')->references('id')->on('salary_archive_ths')->onDelete('cascade');
            $table->decimal('total_HK_value', 22,0)->nullable();
            $table->decimal('total_JL_value', 22,0)->nullable();
            $table->decimal('total_kasbon_pay_value', 22,0)->nullable();
            $table->decimal('total_salary_pay_value', 22,0)->nullable();
            $table->decimal('total_overtime_pay_value', 22,0)->nullable();
            $table->decimal('total_tbhn_u_position_pay_value', 22,0)->nullable();
            $table->decimal('total_tbhn_u_libur_pay_value', 22,0)->nullable();
            $table->decimal('grand_total_pay_value', 22,0)->nullable();
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
        Schema::dropIfExists('salary_archive_tds');
    }
};
