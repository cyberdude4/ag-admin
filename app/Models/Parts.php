<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parts extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'garage_id',
        'category',
        'vehicle_brand',
        'vehicle_model',
        'part_brand',
        'part_model',
        'part_type',
        'part_slug',
        'part_number',
        'part_description',
        'part_warranty',
        'part_guarantee',
        'purchase_price',
        'sale_price',
        'discount_percent',
        'tax_percent',
        'added_by',
        'edited_by',
        'status',
        'deleted',
    ];
}
