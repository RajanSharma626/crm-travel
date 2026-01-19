<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CostComponent extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'name',
        'amount',
        'entered_by_user_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function enteredBy()
    {
        return $this->belongsTo(User::class, 'entered_by_user_id');
    }
}
