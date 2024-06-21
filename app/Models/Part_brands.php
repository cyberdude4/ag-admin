<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Part_brands extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'brand_name',
        'picture',
        'status',
        'deleted',
    ];
}
