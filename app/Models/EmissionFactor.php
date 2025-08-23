<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmissionFactor extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity',
        'factor_kg_co2e_per_unit',
        'unit',
    ];

    protected $casts = [
        'factor_kg_co2e_per_unit' => 'decimal:4',
    ];

    /**
     * Obtener el factor formateado con unidades
     */
    public function getFormattedFactorAttribute(): string
    {
        return number_format($this->factor_kg_co2e_per_unit, 4) . ' kg CO2e/' . $this->unit;
    }

    /**
     * Obtener el factor en toneladas CO2e por unidad
     */
    public function getFactorTonnesCo2ePerUnitAttribute(): float
    {
        return $this->factor_kg_co2e_per_unit / 1000;
    }

    /**
     * Obtener el factor formateado en toneladas
     */
    public function getFormattedFactorTonnesAttribute(): string
    {
        return number_format($this->factor_tonnes_co2e_per_unit, 6) . ' t CO2e/' . $this->unit;
    }

    /**
     * Calcular emisiones para una cantidad específica
     */
    public function calculateEmissions(float $amount): float
    {
        return $amount * $this->factor_kg_co2e_per_unit;
    }

    /**
     * Calcular emisiones en toneladas
     */
    public function calculateEmissionsTonnes(float $amount): float
    {
        return $this->calculateEmissions($amount) / 1000;
    }

    /**
     * Obtener la categoría de actividad
     */
    public function getActivityCategoryAttribute(): string
    {
        $activity = strtolower($this->activity);
        
        // Energía eléctrica
        if (str_contains($activity, 'electricidad') || str_contains($activity, 'electricity')) {
            return 'Energía';
        }
        
        // Transporte
        if (str_contains($activity, 'coche') || 
            str_contains($activity, 'tren') || 
            str_contains($activity, 'avión') || 
            str_contains($activity, 'autobús') || 
            str_contains($activity, 'metro') || 
            str_contains($activity, 'tranvía') || 
            str_contains($activity, 'barco') ||
            str_contains($activity, 'ferry')) {
            return 'Transporte';
        }
        
        // Combustibles fósiles
        if (str_contains($activity, 'gasolina') || 
            str_contains($activity, 'gasóleo') || 
            str_contains($activity, 'gas natural') || 
            str_contains($activity, 'propano') || 
            str_contains($activity, 'butano') || 
            str_contains($activity, 'carbón')) {
            return 'Combustibles';
        }
        
        // Agua
        if (str_contains($activity, 'agua')) {
            return 'Agua';
        }
        
        // Residuos
        if (str_contains($activity, 'residuos') || str_contains($activity, 'waste')) {
            return 'Residuos';
        }
        
        // Alimentación
        if (str_contains($activity, 'carne') || 
            str_contains($activity, 'pescado') || 
            str_contains($activity, 'huevos') || 
            str_contains($activity, 'leche') || 
            str_contains($activity, 'queso') || 
            str_contains($activity, 'arroz') || 
            str_contains($activity, 'trigo') || 
            str_contains($activity, 'maíz') || 
            str_contains($activity, 'patatas') || 
            str_contains($activity, 'tomates') || 
            str_contains($activity, 'manzanas') || 
            str_contains($activity, 'plátanos') || 
            str_contains($activity, 'naranjas') || 
            str_contains($activity, 'uvas') || 
            str_contains($activity, 'aceite') || 
            str_contains($activity, 'vino') || 
            str_contains($activity, 'cerveza') || 
            str_contains($activity, 'café') || 
            str_contains($activity, 'té') || 
            str_contains($activity, 'chocolate')) {
            return 'Alimentación';
        }
        
        // Materiales y construcción
        if (str_contains($activity, 'cemento') || 
            str_contains($activity, 'acero') || 
            str_contains($activity, 'aluminio') || 
            str_contains($activity, 'vidrio') || 
            str_contains($activity, 'plástico') || 
            str_contains($activity, 'madera') || 
            str_contains($activity, 'papel') || 
            str_contains($activity, 'cartón')) {
            return 'Materiales';
        }
        
        // Servicios
        if (str_contains($activity, 'hotel') || 
            str_contains($activity, 'restaurante') || 
            str_contains($activity, 'lavadora') || 
            str_contains($activity, 'secadora') || 
            str_contains($activity, 'lavavajillas') || 
            str_contains($activity, 'televisión') || 
            str_contains($activity, 'ordenador') || 
            str_contains($activity, 'smartphone') || 
            str_contains($activity, 'internet')) {
            return 'Servicios';
        }
        
        return 'Otros';
    }

    /**
     * Obtener el color del badge según la categoría
     */
    public function getCategoryColorAttribute(): string
    {
        return match($this->activity_category) {
            'Energía' => 'primary',
            'Transporte' => 'warning',
            'Combustibles' => 'danger',
            'Agua' => 'info',
            'Residuos' => 'secondary',
            'Alimentación' => 'success',
            default => 'gray',
        };
    }
}
