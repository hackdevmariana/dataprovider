<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectricityPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'hour',
        'type',
        'price_eur_mwh',
        'price_min',
        'price_max',
        'price_avg',
        'forecast_for_tomorrow',
        'price_unit_id',
        'source',
    ];

    protected $casts = [
        'date' => 'date',
        'forecast_for_tomorrow' => 'boolean',
    ];

    public function priceUnit()
    {
        return $this->belongsTo(PriceUnit::class);
    }

    public function intervals()
    {
        return $this->hasMany(ElectricityPriceInterval::class);
    }
}
