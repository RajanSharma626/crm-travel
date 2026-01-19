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
        Schema::table('exit_clearances', function (Blueprint $table) {
            $table->string('credit_card_handover')->nullable()->after('handed_over_id_card');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exit_clearances', function (Blueprint $table) {
            $table->dropColumn('credit_card_handover');
        });
    }
};