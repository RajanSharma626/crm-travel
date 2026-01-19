<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeadHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'from_status',
        'to_status',
        'changed_by',
        'note',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
