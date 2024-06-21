<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicles extends Model
{
    use HasFactory;

    protected $fillable = [
        'garage',
        'category',
        'make',
        'model',
        'purchase_date',
        'engine_number',
        'chasis_number',
        'vehicle_number',
        'fuel_level',
        'odometer',
        'owner_id',
        'added_by',
        'edited_by',
        'status',
        'deleted',
    ];
    
}
