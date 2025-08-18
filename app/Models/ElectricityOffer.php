<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectricityOffer extends Model
{
    use HasFactory;
    protected $fillable = [
        'energy_company_id', 'name', 'slug', 'description',
        'price_fixed_eur_month', 'price_variable_eur_kwh',
        'price_unit_id', 'offer_type', 'valid_from', 'valid_until',
        'conditions_url', 'contract_length_months',
        'requires_smart_meter', 'renewable_origin_certified'
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_until' => 'date',
        'requires_smart_meter' => 'boolean',
        'renewable_origin_certified' => 'boolean',
    ];

    public function energyCompany() {
        return $this->belongsTo(EnergyCompany::class);
    }

    public function priceUnit() {
        return $this->belongsTo(PriceUnit::class);
    }
}