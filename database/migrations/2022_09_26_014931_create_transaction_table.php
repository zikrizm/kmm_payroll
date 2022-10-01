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
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('emp');
            $table->integer('emp_code');
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('department');
            $table->string('position')->nullable();
            $table->dateTime('punch_time');
            $table->string('punch_state')->nullable();
            $table->string('punch_state_display');
            $table->integer('verify_type')->nullable();
            $table->string('verify_type_display');
            $table->string('work_code')->nullable();
            $table->string('gps_location')->nullable();
            $table->string('area_alias')->nullable();
            $table->string('terminal_sn')->nullable();
            $table->integer('temperature')->nullable();
            $table->string('is_mask')->nullable();
            $table->string('terminal_alias')->nullable();
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
        Schema::dropIfExists('transactions');
    }
};
