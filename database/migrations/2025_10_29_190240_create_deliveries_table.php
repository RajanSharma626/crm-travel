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
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->onDelete('cascade');
            $table->foreignId('operation_id')->nullable()->constrained('operations')->nullOnDelete(); // Added from 2025_11_29
            $table->foreignId('assigned_to_delivery_user_id')->nullable()->constrained('users')->nullOnDelete(); // Renamed/Added from 2025_11_29
            
            // Renamed status to delivery_status and updated enum in 2025_11_29
            $table->enum('delivery_status', ['Pending', 'In_Process', 'Delivered'])->default('Pending');
            
            $table->string('courier_id')->nullable();
            
            // Added from 2025_11_29
            $table->enum('delivery_method', ['soft_copy', 'courier', 'hand_delivery'])->nullable();
            $table->text('remarks')->nullable();
            $table->timestamp('delivered_at')->nullable();
            
            // Removed columns based on 2025_11_29 update:
            // assigned_to, attachments, tracking_info, expected_delivery_date, actual_delivery_date, delivery_notes
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
