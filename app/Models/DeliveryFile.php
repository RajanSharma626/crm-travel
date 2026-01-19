<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeliveryFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_id',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'description',
    ];

    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }
}
