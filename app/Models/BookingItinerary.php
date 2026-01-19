<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingItinerary extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'day_and_date',
        'time',
        'service_type',
        'location',
        'activity_tour_description',
        'stay_at',
        'sure',
        'remarks',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
