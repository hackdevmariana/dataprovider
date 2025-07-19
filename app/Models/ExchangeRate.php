<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    protected $fillable = [
        'from_currency', 'to_currency', 'rate', 'date', 'source', 'market_type',
        'precision', 'unit', 'volume_usd', 'market_cap', 'retrieved_at',
        'is_active', 'is_promoted'
    ];

    protected $casts = [
        'date' => 'date',
        'retrieved_at' => 'datetime',
        'is_active' => 'boolean',
        'is_promoted' => 'boolean',
    ];
}