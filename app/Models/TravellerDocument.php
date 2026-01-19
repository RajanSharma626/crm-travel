<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravellerDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'salutation',
        'first_name',
        'last_name',
        'contact_no',
        'doc_type',
        'status',
        'doc_no',
        'nationality',
        'dob',
        'place_of_issue',
        'date_of_expiry',
        'remark',
    ];

    protected $casts = [
        'dob' => 'date',
        'date_of_expiry' => 'date',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}


