<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class Incentive extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'salesperson_id',
        'profit_amount',
        'incentive_amount',
        'incentive_rule_id',
        'payout_date',
        'status',
        'approved_by',
        'approved_at',
        'notes',
    ];

    protected $casts = [
        'profit_amount' => 'decimal:2',
        'incentive_amount' => 'decimal:2',
        'payout_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function salesperson()
    {
        return $this->belongsTo(User::class, 'salesperson_id');
    }

    public function incentiveRule()
    {
        return $this->belongsTo(IncentiveRule::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
