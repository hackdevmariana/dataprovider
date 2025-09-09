<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PriceForecast extends Model
{
    use HasFactory;

    protected $fillable = [
        'energy_type',
        'zone',
        'forecast_time',
        'target_time',
        'predicted_price',
        'confidence_level',
        'forecast_model',
        'factors',
        'min_price',
        'max_price',
        'accuracy_score',
    ];

    protected $casts = [
        'forecast_time' => 'datetime',
        'target_time' => 'datetime',
        'predicted_price' => 'decimal:4',
        'confidence_level' => 'decimal:2',
        'min_price' => 'decimal:4',
        'max_price' => 'decimal:4',
        'accuracy_score' => 'string',
        'factors' => 'array',
    ];

    // Atributos calculados
    public function getEnergyTypeLabelAttribute(): string
    {
        return match ($this->energy_type) {
            'electricity' => 'Electricidad',
            'gas' => 'Gas',
            'oil' => 'Petróleo',
            'coal' => 'Carbón',
            'renewable' => 'Renovable',
            'nuclear' => 'Nuclear',
            default => 'Desconocido',
        };
    }

    public function getEnergyTypeColorAttribute(): string
    {
        return match ($this->energy_type) {
            'electricity' => 'warning',
            'gas' => 'info',
            'oil' => 'dark',
            'coal' => 'secondary',
            'renewable' => 'success',
            'nuclear' => 'danger',
            default => 'gray',
        };
    }

    public function getZoneLabelAttribute(): string
    {
        return match ($this->zone) {
            'peninsula' => 'Península',
            'canarias' => 'Canarias',
            'baleares' => 'Baleares',
            'ceuta' => 'Ceuta',
            'melilla' => 'Melilla',
            default => 'Desconocida',
        };
    }

    public function getConfidenceLevelLabelAttribute(): string
    {
        if ($this->confidence_level >= 0.9) {
            return 'Muy Alta';
        } elseif ($this->confidence_level >= 0.7) {
            return 'Alta';
        } elseif ($this->confidence_level >= 0.5) {
            return 'Media';
        } elseif ($this->confidence_level >= 0.3) {
            return 'Baja';
        } else {
            return 'Muy Baja';
        }
    }

    public function getConfidenceLevelColorAttribute(): string
    {
        if ($this->confidence_level >= 0.9) {
            return 'success';
        } elseif ($this->confidence_level >= 0.7) {
            return 'info';
        } elseif ($this->confidence_level >= 0.5) {
            return 'warning';
        } elseif ($this->confidence_level >= 0.3) {
            return 'danger';
        } else {
            return 'secondary';
        }
    }

    public function getForecastMethodLabelAttribute(): string
    {
        return match ($this->forecast_method) {
            'statistical' => 'Estadístico',
            'machine_learning' => 'Machine Learning',
            'expert_opinion' => 'Opinión Experta',
            'market_analysis' => 'Análisis de Mercado',
            'time_series' => 'Serie Temporal',
            'regression' => 'Regresión',
            'neural_network' => 'Red Neuronal',
            'ensemble' => 'Ensemble',
            default => 'Desconocido',
        };
    }

    public function getTrendDirectionLabelAttribute(): string
    {
        return match ($this->trend_direction) {
            'up' => 'Al alza',
            'down' => 'A la baja',
            'stable' => 'Estable',
            'volatile' => 'Volátil',
            'mixed' => 'Mixto',
            default => 'Sin especificar',
        };
    }

    public function getTrendDirectionColorAttribute(): string
    {
        return match ($this->trend_direction) {
            'up' => 'danger',
            'down' => 'success',
            'stable' => 'info',
            'volatile' => 'warning',
            'mixed' => 'secondary',
            default => 'gray',
        };
    }

    public function getFormattedPredictedPriceAttribute(): string
    {
        return number_format($this->predicted_price, 4) . ' EUR/MWh';
    }

    public function getFormattedActualPriceAttribute(): string
    {
        if (!$this->actual_price) {
            return 'Sin precio real';
        }
        return number_format($this->actual_price, 4) . ' EUR/MWh';
    }

    public function getFormattedForecastDateAttribute(): string
    {
        return $this->forecast_date ? $this->forecast_date->format('d/m/Y') : 'Sin fecha';
    }

    public function getFormattedTargetDateAttribute(): string
    {
        return $this->target_date ? $this->target_date->format('d/m/Y') : 'Sin fecha';
    }

    public function getDaysUntilTargetAttribute(): int
    {
        if (!$this->target_date) {
            return 0;
        }
        return Carbon::now()->diffInDays($this->target_date, false);
    }

    public function getDaysSinceForecastAttribute(): int
    {
        if (!$this->forecast_date) {
            return 0;
        }
        return $this->forecast_date->diffInDays(now());
    }

    public function getIsUpcomingAttribute(): bool
    {
        if (!$this->target_date) {
            return false;
        }
        return Carbon::now()->lt($this->target_date);
    }

    public function getIsPastAttribute(): bool
    {
        if (!$this->target_date) {
            return false;
        }
        return Carbon::now()->gt($this->target_date);
    }

    public function getIsTodayAttribute(): bool
    {
        if (!$this->target_date) {
            return false;
        }
        return $this->target_date->isToday();
    }

    public function getAccuracyLabelAttribute(): string
    {
        if (!$this->accuracy_score) {
            return 'Sin evaluar';
        }

        if ($this->accuracy_score >= 0.9) {
            return 'Excelente';
        } elseif ($this->accuracy_score >= 0.8) {
            return 'Muy Buena';
        } elseif ($this->accuracy_score >= 0.7) {
            return 'Buena';
        } elseif ($this->accuracy_score >= 0.6) {
            return 'Aceptable';
        } elseif ($this->accuracy_score >= 0.5) {
            return 'Regular';
        } else {
            return 'Mala';
        }
    }

    public function getAccuracyColorAttribute(): string
    {
        if (!$this->accuracy_score) {
            return 'gray';
        }

        if ($this->accuracy_score >= 0.9) {
            return 'success';
        } elseif ($this->accuracy_score >= 0.8) {
            return 'info';
        } elseif ($this->accuracy_score >= 0.7) {
            return 'warning';
        } elseif ($this->accuracy_score >= 0.6) {
            return 'secondary';
        } else {
            return 'danger';
        }
    }

    public function getPriceDifferenceAttribute(): float
    {
        if (!$this->actual_price || !$this->predicted_price) {
            return 0;
        }
        return $this->actual_price - $this->predicted_price;
    }

    public function getPriceDifferencePercentageAttribute(): float
    {
        if (!$this->actual_price || !$this->predicted_price || $this->predicted_price == 0) {
            return 0;
        }
        return (($this->actual_price - $this->predicted_price) / $this->predicted_price) * 100;
    }

    public function getFactorsCountAttribute(): int
    {
        if (is_array($this->factors_considered)) {
            return count($this->factors_considered);
        }
        return 0;
    }

    // Scopes
    public function scopeByEnergyType($query, string $type)
    {
        return $query->where('energy_type', $type);
    }

    public function scopeByZone($query, string $zone)
    {
        return $query->where('zone', $zone);
    }

    public function scopeByForecastMethod($query, string $method)
    {
        return $query->where('forecast_method', $method);
    }

    public function scopeByConfidenceLevel($query, float $minLevel)
    {
        return $query->where('confidence_level', '>=', $minLevel);
    }

    public function scopeHighConfidence($query, float $minLevel = 0.7)
    {
        return $query->where('confidence_level', '>=', $minLevel);
    }

    public function scopeByTrendDirection($query, string $direction)
    {
        return $query->where('trend_direction', $direction);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('target_date', '>', Carbon::now());
    }

    public function scopePast($query)
    {
        return $query->where('target_date', '<', Carbon::now());
    }

    public function scopeToday($query)
    {
        return $query->whereDate('target_date', Carbon::today());
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('target_date', [$startDate, $endDate]);
    }

    public function scopeAccurate($query, float $minScore = 0.7)
    {
        return $query->where('accuracy_score', '>=', $minScore);
    }

    public function scopeByDataSource($query, string $source)
    {
        return $query->where('data_source', $source);
    }

    public function scopeOrderByTargetDate($query, string $direction = 'asc')
    {
        return $query->orderBy('target_date', $direction);
    }

    public function scopeOrderByConfidence($query, string $direction = 'desc')
    {
        return $query->orderBy('confidence_level', $direction);
    }

    public function scopeOrderByAccuracy($query, string $direction = 'desc')
    {
        return $query->orderBy('accuracy_score', $direction);
    }

    // Métodos
    public function isUpcoming(): bool
    {
        return $this->is_upcoming;
    }

    public function isPast(): bool
    {
        return $this->is_past;
    }

    public function isToday(): bool
    {
        return $this->is_today;
    }

    public function isAccurate(): bool
    {
        return $this->accuracy_score >= 0.7;
    }

    public function isHighConfidence(): bool
    {
        return $this->confidence_level >= 0.7;
    }

    public function hasActualPrice(): bool
    {
        return !is_null($this->actual_price);
    }

    public function hasFactors(): bool
    {
        return $this->factors_count > 0;
    }

    public function hasUncertaintyRange(): bool
    {
        return is_array($this->uncertainty_range) && count($this->uncertainty_range) > 0;
    }

    public function isPriceHigher(): bool
    {
        return $this->price_difference > 0;
    }

    public function isPriceLower(): bool
    {
        return $this->price_difference < 0;
    }

    public function isPriceAccurate(): bool
    {
        return abs($this->price_difference_percentage) <= 10; // 10% de tolerancia
    }

    public function getFactorsList(): array
    {
        if (is_array($this->factors_considered)) {
            return $this->factors_considered;
        }
        return [];
    }

    public function getUncertaintyRangeList(): array
    {
        if (is_array($this->uncertainty_range)) {
            return $this->uncertainty_range;
        }
        return [];
    }

    public function getFormattedPriceDifferenceAttribute(): string
    {
        $difference = $this->price_difference;
        if ($difference === 0) {
            return 'Sin diferencia';
        }
        
        $sign = $difference > 0 ? '+' : '';
        return $sign . number_format($difference, 4) . ' EUR/MWh';
    }

    public function getFormattedPriceDifferencePercentageAttribute(): string
    {
        $percentage = $this->price_difference_percentage;
        if ($percentage === 0) {
            return '0%';
        }
        
        $sign = $percentage > 0 ? '+' : '';
        return $sign . number_format($percentage, 1) . '%';
    }

    public function getTrendIconAttribute(): string
    {
        return match ($this->trend_direction) {
            'up' => '📈',
            'down' => '📉',
            'stable' => '➡️',
            'volatile' => '📊',
            'mixed' => '🔄',
            default => '❓',
        };
    }
}
