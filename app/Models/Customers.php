<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customers extends Model
{
    use HasFactory;

    protected $fillable = [
        'garage_id',
        'user_id',
        'firstname',
        'lastname',
        'mobile',
        'email',
        'country',
        'state',
        'city',
        'street',
        'zipcode',
        'lat',
        'lng',
        'added_by',
        'edited_by',
        'status',
        'deleted'
    ];

}
