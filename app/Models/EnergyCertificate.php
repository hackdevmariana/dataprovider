<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EnergyCertificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'building_type',
        'energy_rating',
        'annual_energy_consumption_kwh',
        'annual_emissions_kg_co2e',
        'zone_climate_id'
    ];

    protected $casts = [
        'annual_energy_consumption_kwh' => 'decimal:2',
        'annual_emissions_kg_co2e' => 'decimal:2',
    ];

    // Relaciones
    public function user() 
    { 
        return $this->belongsTo(User::class); 
    }

    public function zoneClimate() 
    { 
        return $this->belongsTo(ZoneClimate::class); 
    }

    // Accessors
    public function getFormattedConsumptionAttribute()
    {
        return number_format($this->annual_energy_consumption_kwh, 0) . ' kWh/año';
    }

    public function getFormattedEmissionsAttribute()
    {
        return number_format($this->annual_emissions_kg_co2e, 0) . ' kg CO2e/año';
    }

    public function getEfficiencyCategoryAttribute()
    {
        return match($this->energy_rating) {
            'A+', 'A' => 'Alta Eficiencia',
            'B', 'C' => 'Eficiencia Media',
            'D', 'E', 'F', 'G' => 'Baja Eficiencia',
            default => 'Sin Calificar'
        };
    }

    public function getEfficiencyColorAttribute()
    {
        return match($this->energy_rating) {
            'A+', 'A' => 'success',
            'B', 'C' => 'warning',
            'D', 'E', 'F', 'G' => 'danger',
            default => 'gray'
        };
    }

    // Scopes
    public function scopeHighEfficiency($query)
    {
        return $query->whereIn('energy_rating', ['A', 'A+']);
    }

    public function scopeMediumEfficiency($query)
    {
        return $query->whereIn('energy_rating', ['B', 'C']);
    }

    public function scopeLowEfficiency($query)
    {
        return $query->whereIn('energy_rating', ['D', 'E', 'F', 'G']);
    }

    public function scopeByBuildingType($query, $type)
    {
        return $query->where('building_type', $type);
    }

    public function scopeByEnergyRating($query, $rating)
    {
        return $query->where('energy_rating', $rating);
    }
}