<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Models extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand',
        'model',
        'picture',
        'added_by',
        'edited_by',
        'status',
        'deleted'
    ];
}
