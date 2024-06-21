<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Part_models extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand',
        'model_name',
        'picture',
        'status',
        'deleted',
    ];
}
