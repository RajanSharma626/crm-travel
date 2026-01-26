<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('incentives', function (Blueprint $table) {
            $table->string('emp_code')->nullable()->after('salesperson_id');
            $table->string('department')->nullable()->after('emp_code');
            $table->string('month')->nullable()->after('department');
            $table->integer('target_files')->nullable()->after('month');
            $table->integer('achieved_target')->nullable()->after('target_files');
            $table->decimal('percentage_achieved', 5, 2)->nullable()->after('achieved_target');
            $table->decimal('incentive_payable', 12, 2)->nullable()->after('percentage_achieved');
            $table->enum('payout_status', ['done', 'pending'])->default('pending')->after('payout_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incentives', function (Blueprint $table) {
            $table->dropColumn([
                'emp_code',
                'department',
                'month',
                'target_files',
                'achieved_target',
                'percentage_achieved',
                'incentive_payable',
                'payout_status'
            ]);
        });
    }
};
