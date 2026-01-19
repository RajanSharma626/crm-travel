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
        Schema::create('profit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->onDelete('cascade');
            $table->decimal('total_selling_price', 12, 2);
            $table->decimal('total_cost', 12, 2);
            $table->decimal('profit', 12, 2);
            $table->timestamp('computed_at');
            $table->timestamps();
            
            $table->index('lead_id');
            $table->index('computed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profit_logs');
    }
};
