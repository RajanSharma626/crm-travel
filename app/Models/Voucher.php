<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'operation_id',
        'voucher_type',
        'voucher_number',
        'service_provided',
        'comments',
        'accommodation_id',
        'created_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function operation()
    {
        return $this->belongsTo(Operation::class);
    }

    public function accommodation()
    {
        return $this->belongsTo(BookingAccommodation::class, 'accommodation_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Generate a unique voucher number
     */
    public static function generateVoucherNumber($type = 'service')
    {
        $prefix = match($type) {
            'service' => 'SV',
            'itinerary' => 'IT',
            'accommodation' => 'AV',
            default => 'VO',
        };

        $year = date('Y');
        $month = date('m');
        
        // Get the last voucher number for this type and month
        $lastVoucher = self::where('voucher_type', $type)
            ->where('voucher_number', 'like', "{$prefix}-{$year}{$month}%")
            ->orderBy('voucher_number', 'desc')
            ->first();

        if ($lastVoucher) {
            // Extract the sequence number and increment
            $lastNumber = (int) substr($lastVoucher->voucher_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf("%s-%s%s%04d", $prefix, $year, $month, $newNumber);
    }
}
