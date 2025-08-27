<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OfferComparison extends Model
{
    use HasFactory;

    protected $fillable = [
        'comparison_name',
        'energy_type',
        'zone',
        'offers_count',
        'best_offer_id',
        'worst_offer_id',
        'price_range_min',
        'price_range_max',
        'savings_potential',
        'comparison_date',
        'is_active',
        'comparison_criteria',
        'notes',
        'created_by',
        'views_count',
        'downloads_count',
    ];

    protected $casts = [
        'offers_count' => 'integer',
        'price_range_min' => 'decimal:4',
        'price_range_max' => 'decimal:4',
        'savings_potential' => 'decimal:2',
        'comparison_date' => 'date',
        'is_active' => 'boolean',
        'comparison_criteria' => 'array',
        'views_count' => 'integer',
        'downloads_count' => 'integer',
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

    public function getStatusLabelAttribute(): string
    {
        return $this->is_active ? 'Activa' : 'Inactiva';
    }

    public function getStatusColorAttribute(): string
    {
        return $this->is_active ? 'success' : 'secondary';
    }

    public function getFormattedPriceRangeAttribute(): string
    {
        if (!$this->price_range_min || !$this->price_range_max) {
            return 'Sin rango de precios';
        }
        return number_format($this->price_range_min, 4) . ' - ' . number_format($this->price_range_max, 4) . ' EUR/MWh';
    }

    public function getPriceRangeWidthAttribute(): float
    {
        if (!$this->price_range_min || !$this->price_range_max) {
            return 0;
        }
        return $this->price_range_max - $this->price_range_min;
    }

    public function getPriceRangeWidthPercentageAttribute(): float
    {
        if (!$this->price_range_min || $this->price_range_min == 0) {
            return 0;
        }
        return ($this->price_range_width / $this->price_range_min) * 100;
    }

    public function getFormattedSavingsPotentialAttribute(): string
    {
        if (!$this->savings_potential) {
            return 'Sin ahorro estimado';
        }
        return number_format($this->savings_potential, 2) . '%';
    }

    public function getSavingsLabelAttribute(): string
    {
        if (!$this->savings_potential) {
            return 'Sin ahorro';
        }

        if ($this->savings_potential >= 20) {
            return 'Ahorro Muy Alto';
        } elseif ($this->savings_potential >= 15) {
            return 'Ahorro Alto';
        } elseif ($this->savings_potential >= 10) {
            return 'Ahorro Moderado';
        } elseif ($this->savings_potential >= 5) {
            return 'Ahorro Bajo';
        } else {
            return 'Ahorro Mínimo';
        }
    }

    public function getSavingsColorAttribute(): string
    {
        if (!$this->savings_potential) {
            return 'gray';
        }

        if ($this->savings_potential >= 20) {
            return 'success';
        } elseif ($this->savings_potential >= 15) {
            return 'info';
        } elseif ($this->savings_potential >= 10) {
            return 'warning';
        } elseif ($this->savings_potential >= 5) {
            return 'secondary';
        } else {
            return 'danger';
        }
    }

    public function getFormattedComparisonDateAttribute(): string
    {
        return $this->comparison_date ? $this->comparison_date->format('d/m/Y') : 'Sin fecha';
    }

    public function getIsRecentAttribute(): bool
    {
        if (!$this->comparison_date) {
            return false;
        }
        return $this->comparison_date->diffInDays(now()) <= 7;
    }

    public function getIsOldAttribute(): bool
    {
        if (!$this->comparison_date) {
            return false;
        }
        return $this->comparison_date->diffInDays(now()) >= 30;
    }

    public function getComparisonCriteriaCountAttribute(): int
    {
        if (is_array($this->comparison_criteria)) {
            return count($this->comparison_criteria);
        }
        return 0;
    }

    public function getPopularityScoreAttribute(): float
    {
        if ($this->views_count === 0) {
            return 0;
        }
        
        // Fórmula: (downloads * 3 + views) / (views * 0.1)
        $score = ($this->downloads_count * 3 + $this->views_count) / ($this->views_count * 0.1);
        return min(1.0, max(0.0, $score));
    }

    public function getPopularityLabelAttribute(): string
    {
        $score = $this->popularity_score;
        if ($score >= 0.8) {
            return 'Muy Popular';
        } elseif ($score >= 0.6) {
            return 'Popular';
        } elseif ($score >= 0.4) {
            return 'Moderado';
        } else {
            return 'Poco Popular';
        }
    }

    public function getPopularityColorAttribute(): string
    {
        $score = $this->popularity_score;
        if ($score >= 0.8) {
            return 'success';
        } elseif ($score >= 0.6) {
            return 'info';
        } elseif ($score >= 0.4) {
            return 'warning';
        } else {
            return 'secondary';
        }
    }

    public function getFormattedViewsCountAttribute(): string
    {
        if ($this->views_count >= 1000000) {
            return round($this->views_count / 1000000, 1) . 'M';
        } elseif ($this->views_count >= 1000) {
            return round($this->views_count / 1000, 1) . 'K';
        }
        return number_format($this->views_count);
    }

    public function getFormattedDownloadsCountAttribute(): string
    {
        if ($this->downloads_count >= 1000000) {
            return round($this->downloads_count / 1000000, 1) . 'M';
        } elseif ($this->downloads_count >= 1000) {
            return round($this->downloads_count / 1000, 1) . 'K';
        }
        return number_format($this->downloads_count);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeByEnergyType($query, string $type)
    {
        return $query->where('energy_type', $type);
    }

    public function scopeByZone($query, string $zone)
    {
        return $query->where('zone', $zone);
    }

    public function scopeByOffersCount($query, int $minCount)
    {
        return $query->where('offers_count', '>=', $minCount);
    }

    public function scopeBySavingsPotential($query, float $minSavings)
    {
        return $query->where('savings_potential', '>=', $minSavings);
    }

    public function scopeHighSavings($query, float $minSavings = 15)
    {
        return $query->where('savings_potential', '>=', $minSavings);
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('comparison_date', $date);
    }

    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('comparison_date', '>=', now()->subDays($days));
    }

    public function scopeByCreator($query, int $userId)
    {
        return $query->where('created_by', $userId);
    }

    public function scopePopular($query, float $minScore = 0.6)
    {
        return $query->whereRaw('(downloads_count * 3 + views_count) / (views_count * 0.1) >= ?', [$minScore]);
    }

    public function scopeByViews($query, int $minViews)
    {
        return $query->where('views_count', '>=', $minViews);
    }

    public function scopeByDownloads($query, int $minDownloads)
    {
        return $query->where('downloads_count', '>=', $minDownloads);
    }

    public function scopeOrderBySavings($query, string $direction = 'desc')
    {
        return $query->orderBy('savings_potential', $direction);
    }

    public function scopeOrderByOffersCount($query, string $direction = 'desc')
    {
        return $query->orderBy('offers_count', $direction);
    }

    public function scopeOrderByDate($query, string $direction = 'desc')
    {
        return $query->orderBy('comparison_date', $direction);
    }

    public function scopeOrderByPopularity($query)
    {
        return $query->orderByRaw('(downloads_count * 3 + views_count) / (views_count * 0.1) DESC');
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('comparison_name', 'like', '%' . $search . '%')
              ->orWhere('notes', 'like', '%' . $search . '%');
        });
    }

    // Métodos
    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function isRecent(): bool
    {
        return $this->is_recent;
    }

    public function isOld(): bool
    {
        return $this->is_old;
    }

    public function isPopular(): bool
    {
        return $this->popularity_score >= 0.6;
    }

    public function hasHighSavings(): bool
    {
        return $this->savings_potential >= 15;
    }

    public function hasMultipleOffers(): bool
    {
        return $this->offers_count > 1;
    }

    public function hasPriceRange(): bool
    {
        return !is_null($this->price_range_min) && !is_null($this->price_range_max);
    }

    public function hasComparisonCriteria(): bool
    {
        return $this->comparison_criteria_count > 0;
    }

    public function hasBestOffer(): bool
    {
        return !is_null($this->best_offer_id);
    }

    public function hasWorstOffer(): bool
    {
        return !is_null($this->worst_offer_id);
    }

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    public function incrementDownloads(): void
    {
        $this->increment('downloads_count');
    }

    public function getComparisonCriteriaList(): array
    {
        if (is_array($this->comparison_criteria)) {
            return $this->comparison_criteria;
        }
        return [];
    }

    public function getPriceRangeMidpointAttribute(): float
    {
        if (!$this->hasPriceRange()) {
            return 0;
        }
        return ($this->price_range_min + $this->price_range_max) / 2;
    }

    public function getFormattedPriceRangeMidpointAttribute(): string
    {
        $midpoint = $this->price_range_midpoint;
        if ($midpoint === 0) {
            return 'Sin precio medio';
        }
        return number_format($midpoint, 4) . ' EUR/MWh';
    }

    public function getPriceVariabilityAttribute(): string
    {
        $percentage = $this->price_range_width_percentage;
        if ($percentage === 0) {
            return 'Sin variabilidad';
        } elseif ($percentage < 10) {
            return 'Muy Baja';
        } elseif ($percentage < 25) {
            return 'Baja';
        } elseif ($percentage < 50) {
            return 'Moderada';
        } elseif ($percentage < 100) {
            return 'Alta';
        } else {
            return 'Muy Alta';
        }
    }

    public function getPriceVariabilityColorAttribute(): string
    {
        $percentage = $this->price_range_width_percentage;
        if ($percentage === 0) {
            return 'gray';
        } elseif ($percentage < 10) {
            return 'success';
        } elseif ($percentage < 25) {
            return 'info';
        } elseif ($percentage < 50) {
            return 'warning';
        } elseif ($percentage < 100) {
            return 'danger';
        } else {
            return 'dark';
        }
    }
}
