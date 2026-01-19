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
        Schema::dropIfExists('booking_flights');
        Schema::dropIfExists('booking_surface_transports');
        Schema::dropIfExists('booking_sea_transports');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: This migration drops tables, so down() would need to recreate them
        // For safety, we'll leave this empty as recreating the exact structure would require
        // referencing the original migrations
    }
};
