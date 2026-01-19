<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccountSummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'ref_no',
        'vendor_code',
        'vendor_cost',
        'paid_amount',
        'vendor_payment_status',
        'referred_by',
        'sales_cost',
        'received_amount',
        'customer_payment_status',
    ];

    protected $casts = [
        'vendor_cost' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'sales_cost' => 'decimal:2',
        'received_amount' => 'decimal:2',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
