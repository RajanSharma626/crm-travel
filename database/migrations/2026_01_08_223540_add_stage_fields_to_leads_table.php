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
        Schema::table('leads', function (Blueprint $table) {
            $table->string('operations_stage')->default('Pending')->nullable()->after('status');
            $table->string('ticketing_stage')->default('Pending')->nullable()->after('operations_stage');
            $table->string('visa_stage')->default('Pending')->nullable()->after('ticketing_stage');
            $table->string('insurance_stage')->default('Pending')->nullable()->after('visa_stage');
            $table->string('delivery_stage')->default('Pending')->nullable()->after('insurance_stage');
            $table->string('accounts_stage')->default('Pending')->nullable()->after('delivery_stage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['operations_stage', 'ticketing_stage', 'visa_stage', 'insurance_stage', 'delivery_stage', 'accounts_stage']);
        });
    }
};
