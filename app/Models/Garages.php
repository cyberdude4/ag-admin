<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Garages extends Model
{
    use HasFactory;

    protected $fillable = [
        'uid',
        'user_id',
        'slug',
        'name',
        'country',
        'state',
        'city',
        'street',
        'pincode',
        'lat',
        'long',
        'tax_number',
        'pancard',
        'refer_by',
        'refer_id',
        'added_by',
        'edited_by',
        'created_ip',
        'last_ip',
        'status',
        'deleted'
    ];
}
