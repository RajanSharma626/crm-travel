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
        Schema::create('traveller_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->onDelete('cascade');
            $table->string('salutation')->nullable(); // Added from 2025_12_20
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            // doc_type updated from 2025_12_18_010000
            $table->enum('doc_type', ['passport', 'aadhar_pan_others', 'not_required', 'required_again', 'flight_ticket', 'visa', 'insurance', 'other']); 
            $table->string('status')->DEFAULT('pending')->nullable(); // status often has default, but keeping nullable as per original
            $table->string('doc_no')->nullable();
            $table->string('nationality')->nullable();
            $table->date('dob')->nullable();
            $table->string('place_of_issue')->nullable();
            $table->date('date_of_expiry')->nullable();
            $table->text('remark')->nullable(); // Added from 2025_12_16
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('traveller_documents');
    }
};
