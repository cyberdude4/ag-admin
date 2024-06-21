<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orderworkflow extends Model
{
    use HasFactory;
    protected $table = 'orderworkflow';
    protected $fillable = ['order', 'user', 'type', 'start', 'end', 'status', 'deleted'];
}
