<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OfferComparison extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'energy_type',
        'consumption_profile',
        'offers_compared',
        'best_offer_id',
        'savings_amount',
        'savings_percentage',
        'comparison_criteria',
        'comparison_date',
        'is_shared',
    ];

    protected $casts = [
        'offers_compared' => 'array',
        'savings_amount' => 'decimal:2',
        'savings_percentage' => 'decimal:2',
        'comparison_criteria' => 'array',
        'comparison_date' => 'datetime',
        'is_shared' => 'boolean',
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

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

    public function getConsumptionProfileLabelAttribute(): string
    {
        return match ($this->consumption_profile) {
            'residential' => 'Residencial',
            'commercial' => 'Comercial',
            'industrial' => 'Industrial',
            'low_consumption' => 'Bajo Consumo',
            'medium_consumption' => 'Medio Consumo',
            'high_consumption' => 'Alto Consumo',
            'mixed' => 'Mixto',
            default => 'Desconocido',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->is_shared ? 'Compartida' : 'Privada';
    }

    public function getStatusColorAttribute(): string
    {
        return $this->is_shared ? 'success' : 'secondary';
    }

    public function getFormattedSavingsAmountAttribute(): string
    {
        if (!$this->savings_amount) {
            return 'Sin ahorro';
        }
        return number_format($this->savings_amount, 2) . ' EUR';
    }

    public function getFormattedSavingsPercentageAttribute(): string
    {
        if (!$this->savings_percentage) {
            return 'Sin ahorro';
        }
        return number_format($this->savings_percentage, 1) . '%';
    }

    public function getSavingsLabelAttribute(): string
    {
        if (!$this->savings_percentage) {
            return 'Sin ahorro';
        }

        if ($this->savings_percentage >= 20) {
            return 'Ahorro Muy Alto';
        } elseif ($this->savings_percentage >= 15) {
            return 'Ahorro Alto';
        } elseif ($this->savings_percentage >= 10) {
            return 'Ahorro Moderado';
        } elseif ($this->savings_percentage >= 5) {
            return 'Ahorro Bajo';
        } else {
            return 'Ahorro Mínimo';
        }
    }

    public function getSavingsColorAttribute(): string
    {
        if (!$this->savings_percentage) {
            return 'gray';
        }

        if ($this->savings_percentage >= 20) {
            return 'success';
        } elseif ($this->savings_percentage >= 15) {
            return 'info';
        } elseif ($this->savings_percentage >= 10) {
            return 'warning';
        } elseif ($this->savings_percentage >= 5) {
            return 'secondary';
        } else {
            return 'danger';
        }
    }

    public function getFormattedComparisonDateAttribute(): string
    {
        return $this->comparison_date ? $this->comparison_date->format('d/m/Y H:i') : 'Sin fecha';
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

    public function getOffersCountAttribute(): int
    {
        if (is_array($this->offers_compared)) {
            return count($this->offers_compared);
        }
        return 0;
    }

    public function getComparisonCriteriaCountAttribute(): int
    {
        if (is_array($this->comparison_criteria)) {
            return count($this->comparison_criteria);
        }
        return 0;
    }

    // Scopes
    public function scopeByEnergyType($query, string $type)
    {
        return $query->where('energy_type', $type);
    }

    public function scopeByConsumptionProfile($query, string $profile)
    {
        return $query->where('consumption_profile', $profile);
    }

    public function scopeBySavingsPercentage($query, float $minSavings)
    {
        return $query->where('savings_percentage', '>=', $minSavings);
    }

    public function scopeHighSavings($query, float $minSavings = 15)
    {
        return $query->where('savings_percentage', '>=', $minSavings);
    }

    public function scopeShared($query)
    {
        return $query->where('is_shared', true);
    }

    public function scopePrivate($query)
    {
        return $query->where('is_shared', false);
    }

    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('comparison_date', '>=', now()->subDays($days));
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeOrderBySavings($query, string $direction = 'desc')
    {
        return $query->orderBy('savings_percentage', $direction);
    }

    public function scopeOrderByDate($query, string $direction = 'desc')
    {
        return $query->orderBy('comparison_date', $direction);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('energy_type', 'like', '%' . $search . '%')
              ->orWhere('consumption_profile', 'like', '%' . $search . '%')
              ->orWhere('best_offer_id', 'like', '%' . $search . '%');
        });
    }

    // Métodos
    public function isShared(): bool
    {
        return $this->is_shared;
    }

    public function isRecent(): bool
    {
        return $this->is_recent;
    }

    public function isOld(): bool
    {
        return $this->is_old;
    }

    public function hasHighSavings(): bool
    {
        return $this->savings_percentage >= 15;
    }

    public function hasMultipleOffers(): bool
    {
        return $this->offers_count > 1;
    }

    public function hasComparisonCriteria(): bool
    {
        return $this->comparison_criteria_count > 0;
    }

    public function hasBestOffer(): bool
    {
        return !is_null($this->best_offer_id);
    }

    public function getOffersComparedList(): array
    {
        if (is_array($this->offers_compared)) {
            return $this->offers_compared;
        }
        return [];
    }

    public function getComparisonCriteriaList(): array
    {
        if (is_array($this->comparison_criteria)) {
            return $this->comparison_criteria;
        }
        return [];
    }

    public function getBestOffer(): ?array
    {
        if (!$this->best_offer_id || !is_array($this->offers_compared)) {
            return null;
        }

        foreach ($this->offers_compared as $offer) {
            if (isset($offer['offer_id']) && $offer['offer_id'] === $this->best_offer_id) {
                return $offer;
            }
        }

        return null;
    }
}
