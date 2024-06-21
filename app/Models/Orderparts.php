<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orderparts extends Model
{
    use HasFactory;

    protected $fillable = ['order', 'part', 'vehicle', 'quantity', 'cost', 'discount', 'tax', 'status', 'deleted'];
}
