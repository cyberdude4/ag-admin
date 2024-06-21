<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parts_images extends Model
{
    use HasFactory;

    protected $fillable = [
        'part_id',
        'picture',
        'thumbnail',
        'default',
        'added_by',
        'edited_by',
        'status',
        'deleted',
    ];
}
