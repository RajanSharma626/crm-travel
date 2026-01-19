<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'amount',
        'method',
        'payment_date',
        'due_date',
        'reference',
        'status',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'due_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
