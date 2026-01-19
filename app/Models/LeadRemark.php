<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeadRemark extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'user_id',
        'remark',
        'follow_up_at',
        'visibility',
    ];

    protected $casts = [
        'follow_up_at' => 'datetime',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
