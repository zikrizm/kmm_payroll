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
        Schema::create('salary_archives', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned();
            $table->date('start_date');
            $table->date('end_date');
            $table->date('start_date_work_day');
            $table->date('end_date_work_day');
            $table->date('start_date_overtime');
            $table->date('end_date_overtime');
            $table->integer('dept_id');
            $table->string('dept_code');
            $table->string('dept_name');
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
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
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
        Schema::dropIfExists('salary_archives');
    }
};
