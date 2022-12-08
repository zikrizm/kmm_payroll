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
        Schema::create('salary_archive_employees', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('salary_archive_id')->unsigned();
            $table->integer('emp_id');
            $table->string('emp_code');
            $table->string('emp_first_name');
            $table->string('emp_last_name')->nullable();
            $table->string('photo')->nullable();
            $table->float('amount_day')->nullable();
            $table->float('amount_of_ot')->nullable();
            $table->decimal('amount_of_ot_pay', 22, 2)->nullable();
            $table->float('amount_early_check_in')->nullable();
            $table->decimal('amount_early_check_in_pay', 22, 2)->nullable();
            $table->decimal('daily_salary', 22, 2)->nullable();
            $table->decimal('total_position_extra_pay', 22, 2)->nullable();
            $table->decimal('total_instalment_debt', 22, 2)->nullable();
            $table->decimal('total_tbhn_u_libur', 22, 2)->nullable();
            $table->decimal('total_daily_salary', 22, 2)->nullable();
            $table->decimal('total', 22, 2)->nullable();
            $table->foreign('salary_archive_id')->references('id')->on('salary_archives')->onDelete('cascade');
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
        Schema::dropIfExists('salary_archive_employees');
    }
};
