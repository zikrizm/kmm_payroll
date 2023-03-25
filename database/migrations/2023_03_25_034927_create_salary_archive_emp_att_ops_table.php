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
        Schema::create('salary_archive_emp_att_ops', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('status', ['invalid', 'valid', null])->nullable();
            $table->integer('plusm_value', 22)->nullable();
            $table->string('note')->nullable();
            $table->integer('salary_archive_emp_att_id')->unsigned();
            $table->foreign('salary_archive_emp_att_id')->references('id')->on('salary_archive_emp_atts')->onDelete('cascade');
            $table->integer('operational_id')->nullable()->unsigned();
            $table->foreign('operational_id')->references('id')->on('operationals')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salary_archive_emp_att_ops');
    }
};
