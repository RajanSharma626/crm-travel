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
        Schema::create('booking_sea_transports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->onDelete('cascade');
            $table->string('cruise')->nullable();
            $table->string('info')->nullable();
            $table->string('from_city')->nullable();
            $table->date('departure_date')->nullable();
            $table->time('departure_time')->nullable();
            $table->date('arrival_date')->nullable();
            $table->time('arrival_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_sea_transports');
    }
};
