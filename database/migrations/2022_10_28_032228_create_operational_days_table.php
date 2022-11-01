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
        Schema::create('operational_days', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('operational_id')->unsigned();
            $table->string('day_name');
            $table->datetime('date');
            $table->enum('status',['active','inactive'])->default('active');

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
        Schema::dropIfExists('operational_days');
    }
};
