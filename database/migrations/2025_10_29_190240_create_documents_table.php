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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->onDelete('cascade');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade'); // Or received_by if renamed, but keeping original name + new fields
            
            $table->string('type'); // passport, visa, ticket, voucher, invoice, etc.
            
            // Merged from 2025_12_07_144855
            $table->integer('person_number')->nullable()->comment('Person number (1, 2, 3, etc.) for person-wise documents');
            
            // Merged from 2025_10_29_195823: checklist style
            // Removed: file_path, file_name, file_size
            
            // Added from 2025_10_29_195823
            $table->enum('status', ['not_received', 'received', 'verified', 'rejected'])->default('not_received');
            
            $table->foreignId('received_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('received_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
