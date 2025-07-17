<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScrapingSource extends Model
{
    protected $fillable = [
        'name',
        'url',
        'type',
        'source_type_description',
        'frequency',
        'last_scraped_at',
        'is_active',
    ];

    protected $casts = [
        'last_scraped_at' => 'datetime',
        'is_active' => 'boolean',
    ];
}
