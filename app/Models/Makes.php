<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Makes extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand_name',
        'category',
        'picture',
        'added_by',
        'edited_by',
        'status',
        'deleted'
    ];
}
