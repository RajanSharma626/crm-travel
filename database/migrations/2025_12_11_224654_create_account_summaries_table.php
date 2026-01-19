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
        Schema::create('account_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->onDelete('cascade');
            $table->string('ref_no')->nullable();
            $table->string('vendor_code')->nullable();
            $table->decimal('vendor_cost', 10, 2)->nullable();
            $table->decimal('paid_amount', 10, 2)->nullable();
            $table->string('vendor_payment_status')->nullable(); // Done, Pending
            $table->string('referred_by')->nullable();
            $table->decimal('sales_cost', 10, 2)->nullable();
            $table->decimal('received_amount', 10, 2)->nullable();
            $table->string('customer_payment_status')->nullable(); // Received, Pending
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_summaries');
    }
};
