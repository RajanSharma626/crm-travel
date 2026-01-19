<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'operation_id',
        'assigned_to_delivery_user_id',
        'delivery_status',
        'courier_id',
        'delivery_method',
        'remarks',
        'delivered_at',
    ];

    protected $casts = [
        'delivered_at' => 'datetime',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function operation()
    {
        return $this->belongsTo(Operation::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to_delivery_user_id');
    }

    public function files()
    {
        return $this->hasMany(DeliveryFile::class);
    }
}
