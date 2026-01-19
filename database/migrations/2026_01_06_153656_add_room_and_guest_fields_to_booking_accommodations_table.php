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
            $table->integer('single_room')->default(0)->nullable()->after('booking_status');
            $table->integer('dbl_room')->default(0)->nullable()->after('single_room');
            $table->integer('quad_room')->default(0)->nullable()->after('dbl_room');
            $table->integer('eba')->default(0)->nullable()->after('quad_room');
            $table->integer('cwb')->default(0)->nullable()->after('eba');
            $table->integer('inf')->default(0)->nullable()->after('cwb');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_accommodations', function (Blueprint $table) {
            $table->dropColumn(['single_room', 'dbl_room', 'quad_room', 'eba', 'cwb', 'inf']);
        });
    }
};
