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
        Schema::create('bookingfile_arrival_departure', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->onDelete('cascade');
            $table->enum('mode', ['By Air', 'By Surface', 'By Sea']);
            $table->string('info')->nullable();
            $table->string('from_city')->nullable();
            $table->string('to_city')->nullable();
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
        Schema::dropIfExists('bookingfile_arrival_departure');
    }
};
