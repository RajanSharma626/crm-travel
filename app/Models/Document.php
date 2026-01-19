<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'uploaded_by',
        'received_by',
        'verified_by',
        'type',
        'person_number',
        'status',
        'file_path',
        'file_name',
        'file_size',
        'notes',
        'received_at',
        'verified_at',
    ];

    protected $casts = [
        'received_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
