<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Operation extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'operation_status',
        'voucher_number',
        'nett_cost',
        'admin_approval_required',
        'approval_reason',
        'approval_requested_by',
        'approval_approved_by',
        'approval_requested_at',
        'approval_approved_at',
        'internal_notes',
    ];

    protected $casts = [
        'nett_cost' => 'decimal:2',
        'admin_approval_required' => 'boolean',
        'approval_requested_at' => 'datetime',
        'approval_approved_at' => 'datetime',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function approvalRequestedBy()
    {
        return $this->belongsTo(User::class, 'approval_requested_by');
    }

    public function approvalApprovedBy()
    {
        return $this->belongsTo(User::class, 'approval_approved_by');
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }
}
