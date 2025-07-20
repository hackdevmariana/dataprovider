<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class EmissionFactor extends Model
{
    protected $fillable = [
        'region_id', 'year', 'source', 'co2_kg_per_kwh',
        'emission_context', 'temperature_adjustment_factor', 'source_url',
    ];

    public function region() {
        return $this->belongsTo(Region::class);
    }
}
