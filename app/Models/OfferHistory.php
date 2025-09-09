<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OfferHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'offer_type',
        'offer_details',
        'valid_from',
        'valid_until',
        'price',
        'currency',
        'unit',
        'terms_conditions',
        'status',
        'restrictions',
        'is_featured',
    ];

    protected $casts = [
        'offer_details' => 'array',
        'terms_conditions' => 'array',
        'restrictions' => 'array',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'price' => 'decimal:4',
        'is_featured' => 'boolean',
    ];
}
