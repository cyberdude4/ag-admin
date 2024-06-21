<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stocks extends Model
{
    use HasFactory;

    protected $fillable = [
        'garage_id',
        'part_id',
        'quantity',
        'rack',
        'added_by',
        'edited_by',
        'status',
        'deleted',
    ];
}
