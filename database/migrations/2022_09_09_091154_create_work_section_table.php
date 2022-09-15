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
        Schema::create('work_sections', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned();
            $table->integer('shift_id')->unsigned();
            $table->string('name');
            $table->integer('time_period');
            $table->decimal('pay', 22, 2)->default(0);
            $table->decimal('holiday_req_company_pay', 22, 2)->default(0);
            $table->boolean('accumulated_one_shift')->default(0);
            $table->boolean('overtime_food')->default(0);
            $table->boolean('is_foreman')->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->foreign('shift_id')->references('id')->on('shifts')->onDelete('cascade');
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
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
        Schema::dropIfExists('work_sections');
    }
};
