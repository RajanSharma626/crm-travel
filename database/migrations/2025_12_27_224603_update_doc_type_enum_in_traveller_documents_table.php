<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update ENUM values to match the form options
        DB::statement("ALTER TABLE traveller_documents MODIFY COLUMN doc_type ENUM(
            'passport',
            'visa',
            'aadhar_card',
            'pan_card',
            'voter_id',
            'driving_license',
            'govt_id',
            'school_id',
            'birth_certificate',
            'marriage_certificate',
            'photos',
            'insurance',
            'other_document'
        )");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to old ENUM values
        DB::statement("ALTER TABLE traveller_documents MODIFY COLUMN doc_type ENUM(
            'passport',
            'aadhar_pan_others',
            'not_required',
            'required_again',
            'flight_ticket',
            'visa',
            'insurance',
            'other'
        )");
    }
};
