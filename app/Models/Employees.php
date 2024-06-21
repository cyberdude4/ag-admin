<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employees extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'garage_id',
        'added_by',
        'edited_by',
        'created_ip',
        'last_ip',
        'user_role',
    ];
}
