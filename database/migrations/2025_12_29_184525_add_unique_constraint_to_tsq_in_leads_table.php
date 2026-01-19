<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, handle any duplicate tsq values by setting duplicates to null
        // Keep the first occurrence (lowest id) and nullify the rest
        $duplicates = DB::table('leads')
            ->select('tsq', DB::raw('MIN(id) as first_id'))
            ->whereNotNull('tsq')
            ->groupBy('tsq')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $duplicate) {
            DB::table('leads')
                ->where('tsq', $duplicate->tsq)
                ->where('id', '!=', $duplicate->first_id)
                ->update(['tsq' => null]);
        }

        Schema::table('leads', function (Blueprint $table) {
            // Add unique constraint to tsq column
            $table->string('tsq')->nullable()->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            // Remove unique constraint
            $table->dropUnique(['tsq']);
        });
    }
};
