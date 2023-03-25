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
        Schema::create('salary_archive_emp_att_tsos', function (Blueprint $table) {
            $table->primary(['attendance_lb_id', 'salary_archive_emp_att_id']);
            $table->dateTime('tso_approve_date');
            $table->integer('salary_archive_emp_att_id')->unsigned();
            $table->foreign('salary_archive_emp_att_id')->references('id')->on('salary_archive_emp_atts')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('attendance_tso_id')->unsigned();
            $table->foreign('attendance_tso_id')->references('id')->on('attendance_tsos')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salary_archive_emp_att_tsos');
    }
};
