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
        Schema::table('booking_accommodations', function (Blueprint $table) {
            $table->integer('twin')->default(0)->after('cwb');
            $table->integer('trpl')->default(0)->after('twin');
            $table->integer('cnb')->default(0)->after('inf');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_accommodations', function (Blueprint $table) {
            $table->dropColumn(['twin', 'trpl', 'cnb']);
        });
    }
};

