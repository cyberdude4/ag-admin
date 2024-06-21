<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    use HasFactory;

    // Category And Subcategory table merged into single table
    protected $fillable = [
        'garage',
        'cat_uid',
        'cat_slug',
        'parent_cat',
        'cat_name',
        'picture',
        'added_by',
        'edited_by',
        'status',
        'deleted',
    ];
}
