<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class CarbonSavingRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'installation_power_kw',
        'production_kwh',
        'province_id',
        'municipality_id',
        'period',
        'start_date',
        'end_date',
        'efficiency_ratio',
        'loss_factor',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'installation_power_kw' => 'decimal:2',
        'production_kwh' => 'decimal:2',
        'efficiency_ratio' => 'decimal:4',
        'loss_factor' => 'decimal:4',
    ];

    // Relaciones
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function municipality(): BelongsTo
    {
        return $this->belongsTo(Municipality::class);
    }

    // Scopes
    public function scopeByPeriod($query, $period)
    {
        return $query->where('period', $period);
    }

    public function scopeByProvince($query, $provinceId)
    {
        return $query->where('province_id', $provinceId);
    }

    public function scopeByMunicipality($query, $municipalityId)
    {
        return $query->where('municipality_id', $municipalityId);
    }

    public function scopeWithRegionalFactors($query)
    {
        return $query->whereNotNull('province_id')->orWhereNotNull('municipality_id');
    }

    // Métodos de cálculo
    public function calculateEstimatedProduction(): float
    {
        if ($this->production_kwh) {
            return $this->production_kwh;
        }

        // Estimación basada en potencia y período
        $hoursInPeriod = $this->getHoursInPeriod();
        $estimatedProduction = $this->installation_power_kw * $hoursInPeriod;
        
        // Aplicar factor de eficiencia si está disponible
        if ($this->efficiency_ratio) {
            $estimatedProduction *= $this->efficiency_ratio;
        }
        
        // Aplicar factor de pérdidas si está disponible
        if ($this->loss_factor) {
            $estimatedProduction *= (1 - $this->loss_factor);
        }
        
        return round($estimatedProduction, 2);
    }

    public function calculateCarbonSavings(): float
    {
        // Factor de emisión promedio de la red eléctrica española (gCO2/kWh)
        $gridEmissionFactor = 0.275; // kg CO2/kWh
        
        $production = $this->calculateEstimatedProduction();
        $carbonSavings = $production * $gridEmissionFactor;
        
        return round($carbonSavings, 2);
    }

    public function getFormattedPower(): string
    {
        return number_format($this->installation_power_kw, 2) . ' kW';
    }

    public function getFormattedProduction(): string
    {
        $production = $this->calculateEstimatedProduction();
        return number_format($production, 2) . ' kWh';
    }

    public function getFormattedCarbonSavings(): string
    {
        $savings = $this->calculateCarbonSavings();
        return number_format($savings, 2) . ' kg CO2';
    }

    public function getFormattedEfficiencyRatio(): string
    {
        if (!$this->efficiency_ratio) {
            return 'No especificado';
        }
        return number_format($this->efficiency_ratio * 100, 2) . '%';
    }

    public function getFormattedLossFactor(): string
    {
        if (!$this->loss_factor) {
            return 'No especificado';
        }
        return number_format($this->loss_factor * 100, 2) . '%';
    }

    private function getHoursInPeriod(): int
    {
        return match($this->period) {
            'annual' => 8760, // 24 * 365
            'monthly' => 730,  // 24 * 30.42 (promedio)
            'daily' => 24,
            default => 8760,
        };
    }

    public function getPeriodLabel(): string
    {
        return match($this->period) {
            'annual' => 'Anual',
            'monthly' => 'Mensual',
            'daily' => 'Diario',
            default => 'Desconocido',
        };
    }

    public function getRegionalInfo(): string
    {
        if ($this->municipality && $this->province) {
            return $this->municipality->name . ', ' . $this->province->name;
        } elseif ($this->province) {
            return $this->province->name;
        } else {
            return 'Sin ubicación regional';
        }
    }

    // Constantes
    const PERIODS = [
        'annual' => 'Anual',
        'monthly' => 'Mensual',
        'daily' => 'Diario',
    ];

    // Validaciones
    public static function getValidationRules(): array
    {
        return [
            'installation_power_kw' => 'required|numeric|min:0.01|max:100000',
            'production_kwh' => 'nullable|numeric|min:0|max:10000000',
            'province_id' => 'nullable|exists:provinces,id',
            'municipality_id' => 'nullable|exists:municipalities,id',
            'period' => 'required|in:annual,monthly,daily',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'efficiency_ratio' => 'nullable|numeric|min:0|max:1',
            'loss_factor' => 'nullable|numeric|min:0|max:1',
        ];
    }
}
