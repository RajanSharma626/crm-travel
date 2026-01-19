<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingSeaTransport extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'cruise',
        'info',
        'from_city',
        'departure_date',
        'departure_time',
        'arrival_date',
        'arrival_time',
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
