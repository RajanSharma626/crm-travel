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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('salutation', 10)->nullable(); // Added from 2025_12_07
            $table->integer('tsq_number')->nullable();      // for TSQ counter
            $table->string('tsq')->nullable();              // TSQ1600 etc.
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->foreignId('destination_id')->nullable()->constrained('destinations')->nullOnDelete();
            
            $table->string('customer_name');
            $table->string('first_name')->nullable(); // Added from 2025_11_08
            $table->string('middle_name')->nullable(); // Added from 2025_11_08
            $table->string('last_name')->nullable(); // Added from 2025_11_08
            
            $table->string('phone', 20);
            $table->string('primary_phone', 20)->nullable(); // Added from 2025_11_08
            $table->string('secondary_phone', 20)->nullable(); // Added from 2025_11_08
            $table->string('other_phone', 20)->nullable(); // Added from 2025_11_08
            
            $table->string('email')->nullable();
            
            $table->string('address')->nullable();
            $table->string('address_line')->nullable(); // Added from 2025_11_18
            $table->string('city')->nullable(); // Added from 2025_11_18
            $table->string('state')->nullable(); // Added from 2025_11_18
            $table->string('country')->nullable(); // Added from 2025_11_18
            $table->string('pin_code', 20)->nullable(); // Added from 2025_11_18
            
            $table->date('travel_date')->nullable();
            $table->date('return_date')->nullable(); // Added from 2025_12_14
            
            $table->integer('adults')->default(1);
            $table->integer('children')->default(0);
            $table->integer('children_2_5')->default(0); // Added from 2025_11_14
            $table->integer('children_6_11')->default(0); // Added from 2025_11_14
            $table->integer('infants')->default(0);
            
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
            
            // Added from 2025_12_02
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('booked_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('booked_on')->nullable();
            $table->foreignId('reassigned_to')->nullable()->constrained('users')->onDelete('set null');

            $table->decimal('selling_price', 12, 2)->nullable();
            $table->decimal('booked_value', 12, 2)->nullable();
            $table->string('status')->default('new');       // new/contacted/follow_up/booked/closed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
