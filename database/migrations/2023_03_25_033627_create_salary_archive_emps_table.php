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
        Schema::create('salary_archive_emps', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('emp_id');
            $table->string('emp_code');
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('photo')->nullable();
            $table->integer('salary_archive_id')->unsigned();
            $table->foreign('salary_archive_id')->references('id')->on('salary_archives')->onDelete('cascade');
            $table->decimal('HK_value', 22)->nullable();
            $table->decimal('JL_value', 22)->nullable();
            $table->decimal('kasbon_pay_value', 22)->nullable();
            $table->decimal('overtime_pay_value', 22)->nullable();
            $table->decimal('remaining_kasbon_pay_value', 22)->nullable();
            $table->decimal('salary_pay_value', 22)->nullable();
            $table->decimal('tbhn_u_libur_pay_value', 22)->nullable();
            $table->decimal('tbhn_u_position_pay_value', 22)->nullable();
            $table->decimal('total_pay_value', 22)->nullable();
            $table->decimal('grand_total_pay_value', 22)->nullable();
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
        Schema::dropIfExists('salary_archive_emps');
    }
};
