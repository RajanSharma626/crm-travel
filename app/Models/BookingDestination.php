<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingDestination extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'destination',
        'location',
        'only_hotel',
        'only_tt',
        'hotel_tt',
        'from_date',
        'to_date',
        'no_of_days',
    ];

    protected $casts = [
        'only_hotel' => 'boolean',
        'only_tt' => 'boolean',
        'hotel_tt' => 'boolean',
        'from_date' => 'date',
        'to_date' => 'date',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
