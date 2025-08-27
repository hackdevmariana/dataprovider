<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnergyService extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'service_name',
        'service_type',
        'description',
        'energy_type',
        'zone',
        'price_structure',
        'contract_terms',
        'features',
        'is_active',
        'launch_date',
        'rating',
        'reviews_count',
        'customers_count',
        'service_level',
        'availability',
        'special_offers',
        'terms_conditions',
    ];

    protected $casts = [
        'price_structure' => 'array',
        'contract_terms' => 'array',
        'features' => 'array',
        'launch_date' => 'date',
        'rating' => 'decimal:1',
        'reviews_count' => 'integer',
        'customers_count' => 'integer',
        'is_active' => 'boolean',
        'special_offers' => 'array',
        'terms_conditions' => 'array',
    ];

    // Relaciones
    public function company(): BelongsTo
    {
        return $this->belongsTo(EnergyCompany::class, 'company_id');
    }

    // Atributos calculados
    public function getServiceTypeLabelAttribute(): string
    {
        return match ($this->service_type) {
            'supply' => 'Suministro',
            'distribution' => 'Distribución',
            'generation' => 'Generación',
            'storage' => 'Almacenamiento',
            'consulting' => 'Consultoría',
            'maintenance' => 'Mantenimiento',
            'installation' => 'Instalación',
            'monitoring' => 'Monitoreo',
            'billing' => 'Facturación',
            'support' => 'Soporte',
            default => 'Otro',
        };
    }

    public function getServiceTypeColorAttribute(): string
    {
        return match ($this->service_type) {
            'supply' => 'primary',
            'distribution' => 'info',
            'generation' => 'success',
            'storage' => 'warning',
            'consulting' => 'secondary',
            'maintenance' => 'dark',
            'installation' => 'light',
            'monitoring' => 'danger',
            'billing' => 'gray',
            'support' => 'primary',
            default => 'gray',
        };
    }

    public function getEnergyTypeLabelAttribute(): string
    {
        return match ($this->energy_type) {
            'electricity' => 'Electricidad',
            'gas' => 'Gas',
            'oil' => 'Petróleo',
            'coal' => 'Carbón',
            'renewable' => 'Renovable',
            'nuclear' => 'Nuclear',
            'hybrid' => 'Híbrido',
            'all' => 'Todos',
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
            'hybrid' => 'primary',
            'all' => 'light',
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
            'national' => 'Nacional',
            'international' => 'Internacional',
            default => 'Desconocida',
        };
    }

    public function getServiceLevelLabelAttribute(): string
    {
        return match ($this->service_level) {
            'basic' => 'Básico',
            'standard' => 'Estándar',
            'premium' => 'Premium',
            'enterprise' => 'Empresarial',
            'custom' => 'Personalizado',
            default => 'Sin especificar',
        };
    }

    public function getServiceLevelColorAttribute(): string
    {
        return match ($this->service_level) {
            'basic' => 'secondary',
            'standard' => 'info',
            'premium' => 'warning',
            'enterprise' => 'success',
            'custom' => 'primary',
            default => 'gray',
        };
    }

    public function getAvailabilityLabelAttribute(): string
    {
        return match ($this->availability) {
            '24_7' => '24/7',
            'business_hours' => 'Horario Comercial',
            'weekdays' => 'Días Laborables',
            'on_demand' => 'Bajo Demanda',
            'limited' => 'Limitado',
            default => 'Sin especificar',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->is_active ? 'Activo' : 'Inactivo';
    }

    public function getStatusColorAttribute(): string
    {
        return $this->is_active ? 'success' : 'secondary';
    }

    public function getRatingLabelAttribute(): string
    {
        if (!$this->rating) {
            return 'Sin calificación';
        }

        if ($this->rating >= 4.5) {
            return 'Excelente';
        } elseif ($this->rating >= 4.0) {
            return 'Muy Bueno';
        } elseif ($this->rating >= 3.5) {
            return 'Bueno';
        } elseif ($this->rating >= 3.0) {
            return 'Regular';
        } elseif ($this->rating >= 2.0) {
            return 'Malo';
        } else {
            return 'Muy Malo';
        }
    }

    public function getRatingColorAttribute(): string
    {
        if (!$this->rating) {
            return 'gray';
        }

        if ($this->rating >= 4.5) {
            return 'success';
        } elseif ($this->rating >= 4.0) {
            return 'info';
        } elseif ($this->rating >= 3.5) {
            return 'warning';
        } elseif ($this->rating >= 3.0) {
            return 'secondary';
        } else {
            return 'danger';
        }
    }

    public function getFormattedRatingAttribute(): string
    {
        if (!$this->rating) {
            return 'Sin calificación';
        }
        return number_format($this->rating, 1) . '/5.0';
    }

    public function getFormattedLaunchDateAttribute(): string
    {
        return $this->launch_date ? $this->launch_date->format('d/m/Y') : 'Sin fecha';
    }

    public function getFormattedCustomersCountAttribute(): string
    {
        if ($this->customers_count >= 1000000) {
            return round($this->customers_count / 1000000, 1) . 'M';
        } elseif ($this->customers_count >= 1000) {
            return round($this->customers_count / 1000, 1) . 'K';
        }
        return number_format($this->customers_count);
    }

    public function getFormattedReviewsCountAttribute(): string
    {
        if ($this->reviews_count >= 1000000) {
            return round($this->reviews_count / 1000000, 1) . 'M';
        } elseif ($this->reviews_count >= 1000) {
            return round($this->reviews_count / 1000, 1) . 'K';
        }
        return number_format($this->reviews_count);
    }

    public function getFeaturesCountAttribute(): int
    {
        if (is_array($this->features)) {
            return count($this->features);
        }
        return 0;
    }

    public function getSpecialOffersCountAttribute(): int
    {
        if (is_array($this->special_offers)) {
            return count($this->special_offers);
        }
        return 0;
    }

    public function getIsNewAttribute(): bool
    {
        if (!$this->launch_date) {
            return false;
        }
        return $this->launch_date->diffInDays(now()) <= 90;
    }

    public function getIsEstablishedAttribute(): bool
    {
        if (!$this->launch_date) {
            return false;
        }
        return $this->launch_date->diffInDays(now()) >= 365;
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

    public function scopeByServiceType($query, string $type)
    {
        return $query->where('service_type', $type);
    }

    public function scopeByEnergyType($query, string $type)
    {
        return $query->where('energy_type', $type);
    }

    public function scopeByZone($query, string $zone)
    {
        return $query->where('zone', $zone);
    }

    public function scopeByServiceLevel($query, string $level)
    {
        return $query->where('service_level', $level);
    }

    public function scopeByAvailability($query, string $availability)
    {
        return $query->where('availability', $availability);
    }

    public function scopeByCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByRating($query, float $minRating)
    {
        return $query->where('rating', '>=', $minRating);
    }

    public function scopeHighRated($query, float $minRating = 4.0)
    {
        return $query->where('rating', '>=', $minRating);
    }

    public function scopeByCustomersCount($query, int $minCustomers)
    {
        return $query->where('customers_count', '>=', $minCustomers);
    }

    public function scopePopular($query, int $minCustomers = 1000)
    {
        return $query->where('customers_count', '>=', $minCustomers);
    }

    public function scopeNew($query, int $days = 90)
    {
        return $query->where('launch_date', '>=', now()->subDays($days));
    }

    public function scopeEstablished($query, int $days = 365)
    {
        return $query->where('launch_date', '<=', now()->subDays($days));
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('launch_date', $date);
    }

    public function scopeOrderByRating($query, string $direction = 'desc')
    {
        return $query->orderBy('rating', $direction);
    }

    public function scopeOrderByCustomers($query, string $direction = 'desc')
    {
        return $query->orderBy('customers_count', $direction);
    }

    public function scopeOrderByLaunchDate($query, string $direction = 'desc')
    {
        return $query->orderBy('launch_date', $direction);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('service_name', 'like', '%' . $search . '%')
              ->orWhere('description', 'like', '%' . $search . '%');
        });
    }

    // Métodos
    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function isNew(): bool
    {
        return $this->is_new;
    }

    public function isEstablished(): bool
    {
        return $this->is_established;
    }

    public function isHighRated(): bool
    {
        return $this->rating >= 4.0;
    }

    public function isPopular(): bool
    {
        return $this->customers_count >= 1000;
    }

    public function hasFeatures(): bool
    {
        return $this->features_count > 0;
    }

    public function hasSpecialOffers(): bool
    {
        return $this->special_offers_count > 0;
    }

    public function hasRating(): bool
    {
        return !is_null($this->rating);
    }

    public function hasCustomers(): bool
    {
        return $this->customers_count > 0;
    }

    public function hasReviews(): bool
    {
        return $this->reviews_count > 0;
    }

    public function getFeaturesList(): array
    {
        if (is_array($this->features)) {
            return $this->features;
        }
        return [];
    }

    public function getSpecialOffersList(): array
    {
        if (is_array($this->special_offers)) {
            return $this->special_offers;
        }
        return [];
    }

    public function getPriceStructureList(): array
    {
        if (is_array($this->price_structure)) {
            return $this->price_structure;
        }
        return [];
    }

    public function getContractTermsList(): array
    {
        if (is_array($this->contract_terms)) {
            return $this->contract_terms;
        }
        return [];
    }

    public function getStarRatingAttribute(): string
    {
        if (!$this->rating) {
            return '☆☆☆☆☆';
        }

        $rating = $this->rating;
        $fullStars = floor($rating);
        $halfStar = ($rating - $fullStars) >= 0.5;
        $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);

        $stars = str_repeat('★', $fullStars);
        if ($halfStar) {
            $stars .= '☆';
        }
        $stars .= str_repeat('☆', $emptyStars);

        return $stars;
    }

    public function getServiceDescriptionAttribute(): string
    {
        $type = $this->service_type_label;
        $energy = $this->energy_type_label;
        $level = $this->service_level_label;
        
        return "Servicio de {$type} de {$energy} - Nivel {$level}";
    }
}
