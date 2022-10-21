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
        Schema::create('operational_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('dept_id');
            $table->string('dept_code');
            $table->string('dept_name');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('note')->nullable();
            $table->integer('operational_id')->unsigned();
            $table->foreign('operational_id')->references('id')->on('operationals')->onDelete('cascade');
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
        Schema::dropIfExists('operational_groups');
    }
};
