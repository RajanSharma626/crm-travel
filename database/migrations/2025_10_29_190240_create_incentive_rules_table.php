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
        Schema::create('incentive_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('rule_type', ['fixed_percentage', 'tiered_percentage', 'fixed_amount'])->default('fixed_percentage');
            $table->json('params')->nullable(); // For tiered rules: [{"min": 0, "max": 10000, "percentage": 5}, ...]
            $table->decimal('fixed_percentage', 5, 2)->nullable(); // For fixed percentage
            $table->decimal('fixed_amount', 12, 2)->nullable(); // For fixed amount
            $table->decimal('min_profit_threshold', 12, 2)->default(0); // Minimum profit to trigger incentive
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incentive_rules');
    }
};
