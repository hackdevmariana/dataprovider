<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = [
        'url',
        'alt_text',
        'source',
        'width',
        'height',
    ];
}
