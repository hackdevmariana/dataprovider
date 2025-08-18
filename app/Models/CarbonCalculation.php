<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Cálculo de huella de carbono realizado por usuarios.
 * 
 * Registra los cálculos de CO2 realizados por usuarios o sistemas
 * para poder hacer seguimiento, estadísticas y recomendaciones.
 * 
 * @property int $id
 * @property int|null $user_id Usuario que realiza el cálculo
 * @property int $carbon_equivalence_id Equivalencia utilizada
 * @property float $quantity Cantidad utilizada
 * @property float $co2_result CO2 calculado en kg
 * @property string|null $context Contexto del cálculo
 * @property array|null $parameters Parámetros adicionales
 * @property string|null $session_id ID de sesión para usuarios anónimos
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * 
 * @property-read \App\Models\User|null $user
 * @property-read \App\Models\CarbonEquivalence $carbonEquivalence
 */
class CarbonCalculation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'carbon_equivalence_id',
        'quantity',
        'co2_result',
        'context',
        'parameters',
        'session_id',
    ];

    protected $casts = [
        'quantity' => 'float',
        'co2_result' => 'float',
        'parameters' => 'array',
    ];

    /**
     * Usuario que realizó el cálculo.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Equivalencia de carbono utilizada.
     */
    public function carbonEquivalence(): BelongsTo
    {
        return $this->belongsTo(CarbonEquivalence::class);
    }

    /**
     * Scope para cálculos de un usuario específico.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope para cálculos de una sesión específica.
     */
    public function scopeForSession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    /**
     * Scope para cálculos recientes.
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Obtener el nivel de impacto del cálculo.
     */
    public function getImpactLevelAttribute()
    {
        if ($this->co2_result < 1) {
            return 'bajo';
        } elseif ($this->co2_result < 5) {
            return 'medio';
        } elseif ($this->co2_result < 10) {
            return 'alto';
        } else {
            return 'muy_alto';
        }
    }

    /**
     * Obtener recomendaciones de compensación.
     */
    public function getCompensationRecommendationsAttribute()
    {
        $co2 = $this->co2_result;
        $trees = ceil($co2 / 22);
        
        return [
            'trees_needed' => $trees,
            'planting_cost_eur' => $trees * 2,
            'solar_kwh_equivalent' => round($co2 / 0.04, 1),
            'renewable_months' => round($co2 / (0.04 * 300), 1),
        ];
    }
}