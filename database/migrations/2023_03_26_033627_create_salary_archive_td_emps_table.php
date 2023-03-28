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
        Schema::create('salary_archive_td_emps', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('emp_id');
            $table->string('emp_code');
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('photo')->nullable();
            $table->integer('salary_archive_td_id')->unsigned();
            $table->foreign('salary_archive_td_id')->references('id')->on('salary_archive_tds')->onDelete('cascade');
            $table->float('HK_value')->nullable();
            $table->float('JL_value')->nullable();
            $table->decimal('kasbon_pay_value', 22,0)->nullable();
            $table->decimal('overtime_pay_value', 22,0)->nullable();
            $table->decimal('remaining_kasbon_pay_value', 22,0)->nullable();
            $table->decimal('salary_pay_value', 22,0)->nullable();
            $table->decimal('tbhn_u_libur_pay_value', 22,0)->nullable();
            $table->decimal('tbhn_u_position_pay_value', 22,0)->nullable();
            $table->decimal('total_pay_value', 22,0)->nullable();
            $table->decimal('grand_total_pay_value', 22,0)->nullable();
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
        Schema::dropIfExists('salary_archive_td_emps');
    }
};
