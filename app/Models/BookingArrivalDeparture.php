<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingArrivalDeparture extends Model
{
    use HasFactory;

    protected $table = 'bookingfile_arrival_departure';

    protected $fillable = [
        'lead_id',
        'mode',
        'info',
        'from_city',
        'to_city',
        'departure_date',
        'departure_time',
        'arrival_date',
        'arrival_time',
        'status',
    ];

    protected $casts = [
        'departure_date' => 'date',
        'arrival_date' => 'date',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
