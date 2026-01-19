<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_accommodations', function (Blueprint $table) {
            if (!Schema::hasColumn('booking_accommodations', 'confirmation_no')) {
                $table->string('confirmation_no')->nullable()->after('booking_status');
            }
        });

        // Migrate any existing non-status values into confirmation_no.
        // Booking status enum historically contained values like 'Pending', 'In Progress', 'Complete'.
        // If booking_status contains something else (a confirmation number), copy it across.
        DB::table('booking_accommodations')
            ->whereNotIn('booking_status', ['Pending', 'In Progress', 'Complete'])
            ->whereNotNull('booking_status')
            ->update(['confirmation_no' => DB::raw('booking_status')]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('booking_accommodations', function (Blueprint $table) {
            if (Schema::hasColumn('booking_accommodations', 'confirmation_no')) {
                $table->dropColumn('confirmation_no');
            }
        });
    }
};

