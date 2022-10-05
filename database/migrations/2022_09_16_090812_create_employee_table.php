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
        Schema::create('employees', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned();
            $table->integer('emp_id')->unique();
            // $table->integer('work_section_id')->nullable()->unsigned();
            // $table->integer('group_id')->nullable()->unsigned();
            
            // $table->string('first_name');
            // $table->string('last_name')->nullable();
            // $table->string('nickname');
            // $table->date('birthday')->nullable();
            // $table->date('hire_date')->nullable();
            // $table->string('photo')->nullable();
            // $table->string('gender');
            // $table->string('address', 100)->nullable();

            $table->decimal('daily_salary', 22, 4)->default(0);
            // $table->decimal('pay_component', 22, 4)->default(0);
            $table->enum('payment_period', ['mounthly', 'weekly', 'daily'])->default('weekly');
            // $table->enum('payment_type', ['cash', 'cheque', 'transfer'])->default('cash');
            // $table->string('bank_name')->nullable();
            // $table->string('account_name')->nullable();
            // $table->string('account_number')->nullable();
            // $table->date('cheque_date')->nullable();
            
            $table->boolean('is_device')->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            // $table->foreign('work_section_id')->references('id')->on('work_sections')->onDelete('cascade');
            // $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');

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
        Schema::dropIfExists('employees');
    }
};
