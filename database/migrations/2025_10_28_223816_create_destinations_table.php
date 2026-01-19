<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('destinations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            // Removed country, state, city, description, is_active based on 2025_12_03
            // The 2025_12_10 added country back, but typically we want the final state.
            // Looking at 2025_12_10_232431_add_country_to_destinations_table.php, it ADDS country.
            // So the final state should probably HAVE country if that migration ran last.
            // But 2025_12_03 REMOVED it.
            // So: Created (with country) -> Removed (no country) -> Added (with country).
            // Final state: has country.
            
            $table->string('country')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('destinations');
    }
};
