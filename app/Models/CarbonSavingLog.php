<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

class CarbonSavingLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'cooperative_id', 
        'kw_installed', 
        'production_kwh',
        'co2_saved_kg', 
        'date_range_start', 
        'date_range_end',
        'estimation_source', 
        'carbon_saving_method', 
        'created_by_system',
        'metadata',
    ];

    protected $casts = [
        'date_range_start' => 'date',
        'date_range_end' => 'date',
        'kw_installed' => 'decimal:2',
        'production_kwh' => 'decimal:2',
        'co2_saved_kg' => 'decimal:2',
        'created_by_system' => 'boolean',
        'metadata' => 'array',
    ];

    // Relaciones
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cooperative(): BelongsTo
    {
        return $this->belongsTo(Cooperative::class);
    }

    public function equivalences(): BelongsToMany
    {
        return $this->belongsToMany(CarbonEquivalence::class, 'carbon_equivalence_log')
                    ->withPivot('quantity_equivalent')
                    ->withTimestamps();
    }

    // Scopes
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByCooperative($query, $cooperativeId)
    {
        return $query->where('cooperative_id', $cooperativeId);
    }

    public function scopeByDateRange($query, $startDate, $endDate = null)
    {
        if ($endDate) {
            return $query->whereBetween('date_range_start', [$startDate, $endDate]);
        }
        return $query->where('date_range_start', '>=', $startDate);
    }

    public function scopeSystemGenerated($query)
    {
        return $query->where('created_by_system', true);
    }

    public function scopeUserGenerated($query)
    {
        return $query->where('created_by_system', false);
    }

    // Métodos
    public function getFormattedPower(): string
    {
        return number_format($this->kw_installed, 2) . ' kW';
    }

    public function getFormattedProduction(): string
    {
        if (!$this->production_kwh) {
            return 'No especificada';
        }
        return number_format($this->production_kwh, 2) . ' kWh';
    }

    public function getFormattedCarbonSavings(): string
    {
        if (!$this->co2_saved_kg) {
            return 'No calculado';
        }
        return number_format($this->co2_saved_kg, 2) . ' kg CO2';
    }

    public function getDateRangeLabel(): string
    {
        $start = $this->date_range_start->format('d/m/Y');
        
        if ($this->date_range_end) {
            $end = $this->date_range_end->format('d/m/Y');
            return "{$start} - {$end}";
        }
        
        return $start;
    }

    public function getPeriodDays(): int
    {
        if (!$this->date_range_end) {
            return 1; // Solo un día
        }
        
        return $this->date_range_start->diffInDays($this->date_range_end) + 1;
    }

    public function getAverageDailyProduction(): float
    {
        if (!$this->production_kwh || $this->getPeriodDays() <= 0) {
            return 0;
        }
        
        return round($this->production_kwh / $this->getPeriodDays(), 2);
    }

    public function getAverageDailyCarbonSavings(): float
    {
        if (!$this->co2_saved_kg || $this->getPeriodDays() <= 0) {
            return 0;
        }
        
        return round($this->co2_saved_kg / $this->getPeriodDays(), 2);
    }

    public function getEstimationSourceLabel(): string
    {
        if (!$this->estimation_source) {
            return 'No especificada';
        }

        return match($this->estimation_source) {
            'manual' => 'Manual',
            'sensor' => 'Sensor',
            'calculation' => 'Cálculo',
            'api' => 'API Externa',
            'estimate' => 'Estimación',
            default => ucfirst($this->estimation_source),
        };
    }

    public function getCarbonSavingMethodLabel(): string
    {
        if (!$this->carbon_saving_method) {
            return 'No especificado';
        }

        return match($this->carbon_saving_method) {
            'solar_panel' => 'Panel Solar',
            'wind_turbine' => 'Turbina Eólica',
            'energy_efficiency' => 'Eficiencia Energética',
            'tree_planting' => 'Plantación de Árboles',
            'recycling' => 'Reciclaje',
            'public_transport' => 'Transporte Público',
            'electric_vehicle' => 'Vehículo Eléctrico',
            'insulation' => 'Aislamiento',
            'led_lighting' => 'Iluminación LED',
            'smart_thermostat' => 'Termostato Inteligente',
            default => ucfirst($this->carbon_saving_method),
        };
    }

    public function getRegionalInfo(): string
    {
        if ($this->cooperative) {
            return $this->cooperative->name;
        }
        
        if ($this->user && $this->user->municipality) {
            return $this->user->municipality->name;
        }
        
        return 'Sin ubicación regional';
    }

    // Constantes
    const ESTIMATION_SOURCES = [
        'manual' => 'Manual',
        'sensor' => 'Sensor',
        'calculation' => 'Cálculo',
        'api' => 'API Externa',
        'estimate' => 'Estimación',
    ];

    const CARBON_SAVING_METHODS = [
        'solar_panel' => 'Panel Solar',
        'wind_turbine' => 'Turbina Eólica',
        'energy_efficiency' => 'Eficiencia Energética',
        'tree_planting' => 'Plantación de Árboles',
        'recycling' => 'Reciclaje',
        'public_transport' => 'Transporte Público',
        'electric_vehicle' => 'Vehículo Eléctrico',
        'insulation' => 'Aislamiento',
        'led_lighting' => 'Iluminación LED',
        'smart_thermostat' => 'Termostato Inteligente',
    ];

    // Validaciones
    public static function getValidationRules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'cooperative_id' => 'nullable|exists:cooperatives,id',
            'kw_installed' => 'required|numeric|min:0.01|max:100000',
            'production_kwh' => 'nullable|numeric|min:0|max:10000000',
            'co2_saved_kg' => 'nullable|numeric|min:0|max:1000000',
            'date_range_start' => 'required|date',
            'date_range_end' => 'nullable|date|after_or_equal:date_range_start',
            'estimation_source' => 'nullable|string|max:255',
            'carbon_saving_method' => 'nullable|string|max:255',
            'created_by_system' => 'boolean',
            'metadata' => 'nullable|array',
        ];
    }
}