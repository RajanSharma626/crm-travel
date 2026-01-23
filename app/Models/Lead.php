<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'tsq_number',
        'tsq',
        'service_id',
        'destination_id',
        'customer_name',
        'salutation',
        'first_name',
        'last_name',
        'phone',
        'primary_phone',
        'secondary_phone',
        'other_phone',
        'email',
        'address',
        'address_line',
        'city',
        'state',
        'country',
        'pin_code',
        'travel_date',
        'return_date',
        'adults',
        'children',
        'children_2_5',
        'children_6_11',
        'infants',
        'assigned_user_id',
        'created_by',
        'booked_by',
        'booked_on',
        'reassigned_to',
        'selling_price',
        'booked_value',
        'status',
        'sales_stage',
        'post_sales_stage',
        'operations_stage',
        'ticketing_stage',
        'visa_stage',
        'insurance_stage',
        'delivery_stage',
        'post_sales_user_id',
        'operations_user_id',
        'ticketing_user_id',
        'visa_user_id',
        'insurance_user_id',
        'accounts_user_id',
        'delivery_user_id'
    ];

    protected $casts = [
        'travel_date' => 'date',
        'return_date' => 'date',
        'booked_on' => 'datetime',
    ];

    protected static function booted()
    {
        static::saving(function (Lead $lead) {
            $names = array_filter([
                $lead->first_name,
                $lead->last_name,
            ]);

            if (!empty($names)) {
                $lead->customer_name = trim(implode(' ', $names));
            }

            if ($lead->primary_phone) {
                $lead->phone = $lead->primary_phone;
            }
        });
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function postSalesUser()
    {
        return $this->belongsTo(User::class, 'post_sales_user_id');
    }

    public function operationsUser()
    {
        return $this->belongsTo(User::class, 'operations_user_id');
    }

    public function ticketingUser()
    {
        return $this->belongsTo(User::class, 'ticketing_user_id');
    }

    public function visaUser()
    {
        return $this->belongsTo(User::class, 'visa_user_id');
    }

    public function insuranceUser()
    {
        return $this->belongsTo(User::class, 'insurance_user_id');
    }

    public function accountsUser()
    {
        return $this->belongsTo(User::class, 'accounts_user_id');
    }

    public function deliveryUser()
    {
        return $this->belongsTo(User::class, 'delivery_user_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function bookedBy()
    {
        return $this->belongsTo(User::class, 'booked_by');
    }

    public function reassignedTo()
    {
        return $this->belongsTo(User::class, 'reassigned_to');
    }

    public function histories()
    {
        return $this->hasMany(LeadHistory::class);
    }

    public function remarks()
    {
        return $this->hasMany(LeadRemark::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function costComponents()
    {
        return $this->hasMany(CostComponent::class);
    }

    public function operation()
    {
        return $this->hasOne(Operation::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function delivery()
    {
        return $this->hasOne(Delivery::class);
    }

    public function incentives()
    {
        return $this->hasMany(Incentive::class);
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }

    public function bookingDestinations()
    {
        return $this->hasMany(BookingDestination::class);
    }

    public function bookingArrivalDepartures()
    {
        return $this->hasMany(BookingArrivalDeparture::class);
    }

    // Legacy relationships for backward compatibility (can be removed after migration)
    public function bookingFlights()
    {
        return $this->hasMany(BookingFlight::class);
    }

    public function bookingSurfaceTransports()
    {
        return $this->hasMany(BookingSurfaceTransport::class);
    }

    public function bookingSeaTransports()
    {
        return $this->hasMany(BookingSeaTransport::class);
    }

    public function bookingAccommodations()
    {
        return $this->hasMany(BookingAccommodation::class);
    }

    public function bookingItineraries()
    {
        return $this->hasMany(BookingItinerary::class)->orderByRaw('LENGTH(day_and_date) ASC')->orderBy('day_and_date', 'ASC');
    }

    public function accountSummaries()
    {
        return $this->hasMany(AccountSummary::class);
    }

    public function vendorPayments()
    {
        return $this->hasMany(VendorPayment::class);
    }

    public function bookingFileRemarks()
    {
        return $this->hasMany(BookingFileRemark::class);
    }

    public function travellerDocuments()
    {
        return $this->hasMany(TravellerDocument::class);
    }

    // Helper methods
    public function getTotalPaidAttribute()
    {
        return $this->payments()->where('status', 'paid')->sum('amount');
    }

    public function getTotalCostAttribute()
    {
        return $this->costComponents()->sum('amount');
    }

    public function getProfitAttribute()
    {
        return ($this->selling_price ?? 0) - ($this->operation?->nett_cost ?? $this->total_cost);
    }
}
