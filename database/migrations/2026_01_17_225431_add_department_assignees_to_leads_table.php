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
            // accountant_user_id seems to be a legacy or incorrect name, replacing with accounts_user_id to match department
            if (Schema::hasColumn('leads', 'accountant_user_id')) {
                // Drop foreign key first if it exists. We assume standard naming convention.
                try {
                    $table->dropForeign(['accountant_user_id']);
                } catch (\Exception $e) {
                    // Ignore if foreign key doesn't exist or has different name, proceed to drop column
                }
                $table->dropColumn('accountant_user_id');
            }
            if (!Schema::hasColumn('leads', 'accounts_user_id')) {
                $table->foreignId('accounts_user_id')->nullable()->constrained('users')->nullOnDelete();
            }
            
            // Other columns already exist, so we skip adding them
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            if (Schema::hasColumn('leads', 'accounts_user_id')) {
                $table->dropForeign(['accounts_user_id']);
                $table->dropColumn('accounts_user_id');
            }
            if (!Schema::hasColumn('leads', 'accountant_user_id')) {
                $table->foreignId('accountant_user_id')->nullable()->constrained('users')->nullOnDelete();
            }
        });
    }
};
