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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->enum('method', ['Cash', 'UPI', 'NEFT', 'RTGS', 'WIB', 'Online', 'Cheque'])->default('Cash');
            $table->string('reference')->nullable(); // Added from 2025_11_29
            $table->date('payment_date'); // Renamed from paid_on in 2025_11_29
            $table->date('due_date')->nullable();
            $table->enum('status', ['pending', 'received', 'refunded'])->default('pending'); // Updated enum in 2025_11_29
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null'); // Added from 2025_11_29
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
