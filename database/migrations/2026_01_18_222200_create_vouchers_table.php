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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->onDelete('cascade');
            $table->foreignId('operation_id')->constrained('operations')->onDelete('cascade');
            $table->enum('voucher_type', ['service', 'itinerary', 'accommodation'])->default('service');
            $table->string('voucher_number')->unique();
            $table->text('service_provided')->nullable(); // For service voucher
            $table->text('comments')->nullable(); // For service and accommodation vouchers
            $table->foreignId('accommodation_id')->nullable()->constrained('booking_accommodations')->onDelete('cascade'); // For individual hotel vouchers
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            // Add index for faster queries
            $table->index(['lead_id', 'voucher_type']);
            $table->index('voucher_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
