<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::table('shifts', function (Blueprint $table) {
            $table->enum('status', ['active', 'inactive'])->default('active')->after('name');
        });

        $duplicateGroups = DB::table('shifts')
            ->select('business_id', 'dept_id')
            ->groupBy('business_id', 'dept_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicateGroups as $group) {
            $shiftIds = DB::table('shifts')
                ->where('business_id', $group->business_id)
                ->where('dept_id', $group->dept_id)
                ->orderBy('id')
                ->pluck('id');

            $keepActiveId = $shiftIds->first();

            DB::table('shifts')
                ->where('business_id', $group->business_id)
                ->where('dept_id', $group->dept_id)
                ->where('id', '!=', $keepActiveId)
                ->update(['status' => 'inactive']);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
