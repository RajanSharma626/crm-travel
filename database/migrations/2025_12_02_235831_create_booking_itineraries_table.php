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
        Schema::create('booking_itineraries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->onDelete('cascade');
            $table->string('day_and_date')->nullable();
            $table->time('time')->nullable();
            $table->string('service_type')->nullable();
            $table->string('location')->nullable();
            $table->text('activity_tour_description')->nullable();
            $table->string('stay_at')->nullable();
            $table->enum('sure', ['Y', 'N'])->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_itineraries');
    }
};
