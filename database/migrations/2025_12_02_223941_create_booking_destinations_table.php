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
        Schema::create('booking_destinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->onDelete('cascade');
            $table->string('destination')->nullable();
            $table->string('location')->nullable();
            $table->boolean('only_hotel')->default(false);
            $table->boolean('only_tt')->default(false);
            $table->boolean('hotel_tt')->default(false);
            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
            $table->integer('no_of_days')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_destinations');
    }
};
