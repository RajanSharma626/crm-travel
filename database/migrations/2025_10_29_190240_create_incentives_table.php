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
        Schema::create('incentives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->onDelete('cascade');
            $table->foreignId('salesperson_id')->constrained('users')->onDelete('cascade');
            $table->decimal('profit_amount', 12, 2);
            $table->decimal('incentive_amount', 12, 2);
            $table->foreignId('incentive_rule_id')->nullable()->constrained('incentive_rules')->nullOnDelete();
            $table->date('payout_date')->nullable();
            $table->enum('status', ['pending', 'approved', 'paid', 'cancelled'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incentives');
    }
};
