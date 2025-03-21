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
        Schema::table('timetables', function (Blueprint $table) {
            $table->integer('warning_check_in_min')->affter('check_out_plus')->default(0);
            $table->integer('warning_check_in_plus')->affter('warning_check_in_min')->default(0);
            $table->integer('warning_check_out_min')->affter('warning_check_in_plus')->default(0);
            $table->integer('warning_check_out_plus')->affter('warning_check_out_min')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('timetables', function (Blueprint $table) {
            //
        });
    }
};
