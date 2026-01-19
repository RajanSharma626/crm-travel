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
            // Check if post_sales_stage doesn't exist, add it
            if (!Schema::hasColumn('leads', 'post_sales_stage')) {
                $table->string('post_sales_stage')->default('Pending')->nullable()->after('delivery_stage');
            }
            // Add sales_stage
            if (!Schema::hasColumn('leads', 'sales_stage')) {
                $table->string('sales_stage')->default('Pending')->nullable()->after('post_sales_stage');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('leads', 'post_sales_stage')) {
                $columnsToDrop[] = 'post_sales_stage';
            }
            if (Schema::hasColumn('leads', 'sales_stage')) {
                $columnsToDrop[] = 'sales_stage';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
