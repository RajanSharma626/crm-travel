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
        Schema::create('booking_accommodations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->onDelete('cascade');
            $table->string('destination')->nullable();
            $table->string('location')->nullable();
            $table->string('stay_at')->nullable();
            $table->date('checkin_date')->nullable();
            $table->date('checkout_date')->nullable();
            $table->text('room_type')->nullable();
            $table->enum('meal_plan', ['EP','CP', 'MAP', 'AP', 'AI'])->nullable();
            $table->enum('booking_status', ['Pending', 'In Progress', 'Complete'])->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_accommodations');
    }
};
