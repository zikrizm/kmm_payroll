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
        Schema::create('reqextra_help_has_employees', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('reqextra_help_id')->unsigned();
            $table->integer('emp_id');
            $table->string('emp_code');
            $table->string('emp_first_name')->nullable();
            $table->string('emp_last_name')->nullable();

            $table->foreign('reqextra_help_id')->references('id')->on('reqextra_helps')->onDelete('cascade');
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
        Schema::dropIfExists('reqextra_help_has_employees');
    }
};
