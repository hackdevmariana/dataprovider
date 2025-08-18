<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Equivalencias de carbono para calcular huella ambiental.
 * 
 * Permite calcular el impacto en CO2 de diferentes actividades,
 * productos, servicios y procesos energéticos para fomentar
 * la sostenibilidad y compensación ambiental.
 * 
 * @property int $id
 * @property string $name Nombre del elemento/actividad
 * @property string $slug Slug único para URLs
 * @property float $co2_kg_equivalent CO2 equivalente en kg
 * @property string|null $description Descripción detallada
 * @property string $category Categoría: energy, transport, food, etc
 * @property string $unit Unidad de medida: kwh, km, kg, etc
 * @property float|null $efficiency_ratio Ratio de eficiencia
 * @property float|null $loss_factor Factor de pérdida
 * @property string|null $calculation_method Método de cálculo
 * @property array|null $calculation_params Parámetros adicionales
 * @property string $source Fuente de los datos
 * @property string|null $source_url URL de la fuente
 * @property bool $is_verified Si está verificado oficialmente
 * @property string|null $verification_entity Entidad verificadora
 * @property \Carbon\Carbon|null $last_updated Última actualización
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CarbonSavingLog[] $savingLogs
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CarbonCalculation[] $calculations
 */
class CarbonEquivalence extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'co2_kg_equivalent',
        'description',
        'category',
        'unit',
        'efficiency_ratio',
        'loss_factor',
        'calculation_method',
        'calculation_params',
        'source',
        'source_url',
        'is_verified',
        'verification_entity',
        'last_updated',
    ];

    protected $casts = [
        'co2_kg_equivalent' => 'float',
        'efficiency_ratio' => 'float',
        'loss_factor' => 'float',
        'calculation_params' => 'array',
        'is_verified' => 'boolean',
        'last_updated' => 'datetime',
    ];

    /**
     * Relación con logs de ahorro de carbono.
     */
    public function savingLogs(): BelongsToMany
    {
        return $this->belongsToMany(CarbonSavingLog::class, 'carbon_equivalence_log')
                    ->withPivot('quantity_equivalent')
                    ->withTimestamps();
    }

    /**
     * Relación con cálculos de carbono.
     */
    public function calculations(): HasMany
    {
        return $this->hasMany(CarbonCalculation::class);
    }

    /**
     * Scope para filtrar por categoría.
     */
    public function scopeOfCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope para elementos verificados.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope para elementos energéticos.
     */
    public function scopeEnergy($query)
    {
        return $query->where('category', 'energy');
    }

    /**
     * Scope para transporte.
     */
    public function scopeTransport($query)
    {
        return $query->where('category', 'transport');
    }

    /**
     * Scope para alimentación.
     */
    public function scopeFood($query)
    {
        return $query->where('category', 'food');
    }

    /**
     * Calcular CO2 para una cantidad específica.
     */
    public function calculateCO2($quantity)
    {
        $base = $this->co2_kg_equivalent * $quantity;
        
        if ($this->efficiency_ratio) {
            $base *= $this->efficiency_ratio;
        }
        
        if ($this->loss_factor) {
            $base *= (1 + $this->loss_factor);
        }
        
        return round($base, 3);
    }

    /**
     * Obtener equivalencias comunes para comparación.
     */
    public function getCommonEquivalencesAttribute()
    {
        $co2 = $this->co2_kg_equivalent;
        
        return [
            'trees_to_compensate' => round($co2 / 22, 1), // 1 árbol absorbe ~22kg CO2/año
            'km_car_gasoline' => round($co2 / 0.12, 0), // ~0.12kg CO2/km coche gasolina
            'kwh_coal_electricity' => round($co2 / 0.82, 1), // ~0.82kg CO2/kWh carbón
            'kwh_solar_electricity' => round($co2 / 0.04, 1), // ~0.04kg CO2/kWh solar
            'kg_beef' => round($co2 / 27, 2), // ~27kg CO2/kg carne de res
        ];
    }

    /**
     * Obtener categoría en español.
     */
    public function getCategoryNameAttribute()
    {
        $categories = [
            'energy' => 'Energía',
            'transport' => 'Transporte',
            'food' => 'Alimentación',
            'construction' => 'Construcción',
            'industry' => 'Industria',
            'waste' => 'Residuos',
            'agriculture' => 'Agricultura',
            'other' => 'Otros',
        ];

        return $categories[$this->category] ?? 'Desconocido';
    }

    /**
     * Verificar si es de bajo impacto (<1kg CO2).
     */
    public function getIsLowImpactAttribute()
    {
        return $this->co2_kg_equivalent < 1;
    }

    /**
     * Verificar si es de alto impacto (>10kg CO2).
     */
    public function getIsHighImpactAttribute()
    {
        return $this->co2_kg_equivalent > 10;
    }

    /**
     * Obtener nivel de impacto.
     */
    public function getImpactLevelAttribute()
    {
        if ($this->co2_kg_equivalent < 1) {
            return 'bajo';
        } elseif ($this->co2_kg_equivalent < 5) {
            return 'medio';
        } elseif ($this->co2_kg_equivalent < 10) {
            return 'alto';
        } else {
            return 'muy_alto';
        }
    }

    /**
     * Obtener color para visualización.
     */
    public function getImpactColorAttribute()
    {
        $colors = [
            'bajo' => '#22c55e',      // Verde
            'medio' => '#eab308',     // Amarillo
            'alto' => '#f97316',      // Naranja
            'muy_alto' => '#ef4444',  // Rojo
        ];

        return $colors[$this->impact_level] ?? '#6b7280';
    }

    /**
     * Obtener recomendaciones de compensación.
     */
    public function getCompensationRecommendationsAttribute()
    {
        $co2 = $this->co2_kg_equivalent;
        $trees = ceil($co2 / 22);
        
        return [
            'trees_needed' => $trees,
            'planting_cost_eur' => $trees * 2, // ~2€ por árbol plantado
            'solar_kwh_equivalent' => round($co2 / 0.04, 1),
            'renewable_months' => round($co2 / (0.04 * 300), 1), // ~300kWh/mes hogar
        ];
    }
}
