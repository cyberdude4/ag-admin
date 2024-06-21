<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    use HasFactory;

    protected $fillable = [
        'garage',
        'vehicle',
        'user',
        'order_number',
        'total_amount',
        'paid_amount',
        'due_amount',
        'gst',
        'discount',
        'offer_id',
        'pick_vehicle',
        'pickup_date',
        'delivery_date',
        'latitude',
        'longitude',
        'notes',
        'first_ip',
        'last_ip',
        'added_by',
        'edited_by',
        'status',
        'deleted'
    ]; 
}
