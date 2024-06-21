<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orderservices extends Model
{
    use HasFactory;

    protected $fillable = ['order', 'service', 'vehicle', 'cost', 'discount', 'tax', 'status', 'deleted'];
}
