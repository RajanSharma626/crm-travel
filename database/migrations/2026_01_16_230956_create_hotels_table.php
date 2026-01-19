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
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_no_1')->nullable();
            $table->string('contact_no_2')->nullable();
            $table->text('address')->nullable();
            $table->string('country')->nullable();
            $table->foreignId('destination_id')->nullable()->constrained('destinations')->onDelete('set null');
            $table->foreignId('location_id')->nullable()->constrained('locations')->onDelete('set null');
            $table->string('banner_image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
