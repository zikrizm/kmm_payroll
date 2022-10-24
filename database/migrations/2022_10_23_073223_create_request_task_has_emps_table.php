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
        Schema::create('request_task_has_emps', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('request_task_id')->unsigned();
            $table->integer('emp_id');
            $table->string('emp_code');
            $table->string('emp_first_name')->nullable();
            $table->string('emp_last_name')->nullable();

            $table->foreign('request_task_id')->references('id')->on('request_tasks')->onDelete('cascade');
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
        Schema::dropIfExists('request_task_has_emps');
    }
};
