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
        Schema::create('vendor_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->onDelete('cascade');
            $table->string('vendor_code')->nullable();
            $table->string('booking_type')->nullable(); // Hotel, TT, etc.
            $table->string('location')->nullable();
            $table->decimal('purchase_cost', 10, 2)->nullable();
            $table->date('due_date')->nullable();
            $table->string('status')->DEFAULT('Pending')->nullable(); // Added from 2025_12_11
            $table->decimal('paid_amount', 10, 2)->nullable()->default(0);
            $table->decimal('pending_amount', 10, 2)->nullable()->default(0);
            $table->string('payment_mode')->nullable(); // UPI, Bank Transfer, etc.
            $table->string('ref_no')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_payments');
    }
};
