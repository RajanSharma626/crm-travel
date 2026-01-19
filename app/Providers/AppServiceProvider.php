<?php

namespace App\Providers;

use App\Models\Lead;
use App\Models\BookingAccommodation;
use App\Models\BookingItinerary;
use App\Models\BookingArrivalDeparture;
use App\Models\BookingDestination;
use App\Models\BookingFlight;
use App\Models\BookingSeaTransport;
use App\Models\BookingSurfaceTransport;
use App\Models\BookingFileRemark;
use App\Observers\LeadObserver;
use App\Observers\ModelAuditObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Lead::observe(LeadObserver::class);

        // Attach generic audit observer to booking-related models and lead as well.
        $auditObserver = ModelAuditObserver::class;

        BookingAccommodation::observe($auditObserver);
        BookingItinerary::observe($auditObserver);
        BookingArrivalDeparture::observe($auditObserver);
        BookingDestination::observe($auditObserver);
        BookingFlight::observe($auditObserver);
        BookingSeaTransport::observe($auditObserver);
        BookingSurfaceTransport::observe($auditObserver);
        BookingFileRemark::observe($auditObserver);
        Lead::observe($auditObserver);
    }
}
