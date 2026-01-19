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
        Schema::create('cost_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->onDelete('cascade');
            $table->string('name'); // Added from 2025_11_29, replacing type and description
            $table->decimal('amount', 12, 2);
            $table->foreignId('entered_by_user_id')->constrained('users')->onDelete('cascade'); // Renamed from entered_by in 2025_11_29
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cost_components');
    }
};
