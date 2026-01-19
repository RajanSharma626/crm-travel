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
        Schema::create('operations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->onDelete('cascade');
            $table->enum('operation_status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->decimal('nett_cost', 12, 2)->nullable();
            $table->boolean('admin_approval_required')->default(false);
            $table->text('approval_reason')->nullable();
            $table->foreignId('approval_requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approval_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approval_requested_at')->nullable();
            $table->timestamp('approval_approved_at')->nullable();
            $table->text('internal_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operations');
    }
};
