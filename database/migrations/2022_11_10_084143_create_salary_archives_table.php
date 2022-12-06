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
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->integer('emp_id');
            $table->string('emp_code');
            $table->string('emp_first_name');
            $table->string('emp_last_name')->nullable();
            $table->string('photo')->nullable();
            $table->integer('dept_id');
            $table->string('dept_code');
            $table->string('dept_name');
            $table->float('amount_day')->nullable();
            $table->float('amount_of_ot')->nullable();
            $table->decimal('amount_of_ot_pay', 22, 2)->nullable();
            $table->float('amount_early_check_in')->nullable();
            $table->decimal('amount_early_check_in_pay', 22, 2)->nullable();
            $table->decimal('daily_salary', 22, 2)->nullable();
            $table->decimal('position_extra_pay_total', 22, 2)->nullable();
            $table->decimal('instalment_debt_total', 22, 2)->nullable();
            $table->decimal('tbhn_u_libur_total', 22, 2)->nullable();
            $table->decimal('daily_salary_total', 22, 2)->nullable();
            $table->decimal('total', 22, 2)->nullable();
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
