<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_no_1',
        'contact_no_2',
        'contact_person_name',
        'contact_person_phone',
        'address',
        'country',
        'destination_id',
        'location_id',
        'banner_image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
