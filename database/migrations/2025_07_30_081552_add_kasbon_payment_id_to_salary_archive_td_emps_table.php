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
        Schema::table('salary_archive_td_emps', function (Blueprint $table) {
            $table->integer('kasbon_payment_id')->nullable()->unsigned()->after('kasbon_pay_value');
            $table->foreign('kasbon_payment_id')->references('id')->on('employee_debt_pays')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('salary_archive_td_emps', function (Blueprint $table) {
            //
        });
    }
};
