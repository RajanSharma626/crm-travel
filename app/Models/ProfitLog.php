<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfitLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'total_selling_price',
        'total_cost',
        'profit',
        'computed_at',
    ];

    protected $casts = [
        'total_selling_price' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'profit' => 'decimal:2',
        'computed_at' => 'datetime',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
