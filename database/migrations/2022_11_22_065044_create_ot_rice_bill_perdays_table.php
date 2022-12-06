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
        Schema::create('ot_rice_bill_perdays', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ot_rice_bill_id')->unsigned();
            $table->integer('emp_id');
            $table->string('emp_code');
            $table->string('emp_first_name');
            $table->string('emp_last_name')->nullable();
            $table->string('photo')->nullable();
            $table->dateTime('rice_date');
            $table->decimal('total', 22, 2)->nullable();

            $table->foreign('ot_rice_bill_id')->references('id')->on('ot_rice_bills')->onDelete('cascade');
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
        Schema::dropIfExists('ot_rice_bill_perdays');
    }
};
