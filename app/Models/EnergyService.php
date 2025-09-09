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
        'description',
        'service_type',
        'energy_source',
        'features',
        'requirements',
        'base_price',
        'pricing_model',
        'pricing_details',
        'contract_duration',
        'terms_conditions',
        'is_available',
        'is_featured',
        'popularity_score',
    ];

    protected $casts = [
        'features' => 'array',
        'requirements' => 'array',
        'pricing_details' => 'array',
        'terms_conditions' => 'array',
        'base_price' => 'decimal:2',
        'is_available' => 'boolean',
        'is_featured' => 'boolean',
        'popularity_score' => 'integer',
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
            'energy_audit' => 'Auditoría Energética',
            'efficiency' => 'Eficiencia Energética',
            'renewable' => 'Energías Renovables',
            'smart_home' => 'Hogar Inteligente',
            'electric_vehicle' => 'Vehículo Eléctrico',
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
            'energy_audit' => 'info',
            'efficiency' => 'success',
            'renewable' => 'success',
            'smart_home' => 'warning',
            'electric_vehicle' => 'info',
            default => 'gray',
        };
    }

    public function getEnergySourceLabelAttribute(): string
    {
        return match ($this->energy_source) {
            'electricity' => 'Electricidad',
            'gas' => 'Gas Natural',
            'oil' => 'Petróleo',
            'coal' => 'Carbón',
            'solar' => 'Solar',
            'wind' => 'Eólico',
            'hydro' => 'Hidroeléctrico',
            'nuclear' => 'Nuclear',
            'biomass' => 'Biomasa',
            'geothermal' => 'Geotérmico',
            'hybrid' => 'Híbrido',
            'all' => 'Todos',
            default => 'No especificado',
        };
    }

    public function getEnergySourceColorAttribute(): string
    {
        return match ($this->energy_source) {
            'electricity' => 'warning',
            'gas' => 'info',
            'oil' => 'dark',
            'coal' => 'secondary',
            'solar' => 'success',
            'wind' => 'info',
            'hydro' => 'primary',
            'nuclear' => 'danger',
            'biomass' => 'success',
            'geothermal' => 'warning',
            'hybrid' => 'primary',
            'all' => 'light',
            default => 'gray',
        };
    }

    public function getPricingModelLabelAttribute(): string
    {
        return match ($this->pricing_model) {
            'fixed' => 'Precio Fijo',
            'variable' => 'Precio Variable',
            'tiered' => 'Por Escalones',
            'subscription' => 'Suscripción',
            'pay_per_use' => 'Pago por Uso',
            'contract' => 'Contrato',
            'free' => 'Gratuito',
            'custom' => 'Personalizado',
            default => 'No especificado',
        };
    }

    public function getPricingModelColorAttribute(): string
    {
        return match ($this->pricing_model) {
            'fixed' => 'success',
            'variable' => 'warning',
            'tiered' => 'info',
            'subscription' => 'primary',
            'pay_per_use' => 'secondary',
            'contract' => 'dark',
            'free' => 'light',
            'custom' => 'gray',
            default => 'gray',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->is_available ? 'Disponible' : 'No Disponible';
    }

    public function getStatusColorAttribute(): string
    {
        return $this->is_available ? 'success' : 'danger';
    }

    public function getFormattedBasePriceAttribute(): string
    {
        if (!$this->base_price) {
            return 'Consultar precio';
        }
        return number_format($this->base_price, 2) . ' €';
    }

    public function getFormattedPopularityScoreAttribute(): string
    {
        if ($this->popularity_score >= 1000) {
            return round($this->popularity_score / 1000, 1) . 'K';
        }
        return number_format($this->popularity_score);
    }

    public function getFeaturesCountAttribute(): int
    {
        if (is_array($this->features)) {
            return count($this->features);
        }
        return 0;
    }

    public function getRequirementsCountAttribute(): int
    {
        if (is_array($this->requirements)) {
            return count($this->requirements);
        }
        return 0;
    }

    public function getIsPopularAttribute(): bool
    {
        return $this->popularity_score >= 100;
    }

    public function getIsHighDemandAttribute(): bool
    {
        return $this->popularity_score >= 500;
    }

    public function getIsNewAttribute(): bool
    {
        return $this->created_at->diffInDays(now()) <= 30;
    }

    public function getIsEstablishedAttribute(): bool
    {
        return $this->created_at->diffInDays(now()) >= 365;
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeUnavailable($query)
    {
        return $query->where('is_available', false);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByServiceType($query, string $type)
    {
        return $query->where('service_type', $type);
    }

    public function scopeByEnergySource($query, string $source)
    {
        return $query->where('energy_source', $source);
    }

    public function scopeByPricingModel($query, string $model)
    {
        return $query->where('pricing_model', $model);
    }

    public function scopeByCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopePopular($query, int $minScore = 100)
    {
        return $query->where('popularity_score', '>=', $minScore);
    }

    public function scopeHighDemand($query, int $minScore = 500)
    {
        return $query->where('popularity_score', '>=', $minScore);
    }

    public function scopeNew($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeEstablished($query, int $days = 365)
    {
        return $query->where('created_at', '<=', now()->subDays($days));
    }

    public function scopeOrderByPopularity($query, string $direction = 'desc')
    {
        return $query->orderBy('popularity_score', $direction);
    }

    public function scopeOrderByPrice($query, string $direction = 'asc')
    {
        return $query->orderBy('base_price', $direction);
    }

    public function scopeOrderByCreated($query, string $direction = 'desc')
    {
        return $query->orderBy('created_at', $direction);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('service_name', 'like', '%' . $search . '%')
              ->orWhere('description', 'like', '%' . $search . '%');
        });
    }

    // Métodos
    public function isAvailable(): bool
    {
        return $this->is_available;
    }

    public function isFeatured(): bool
    {
        return $this->is_featured;
    }

    public function isPopular(): bool
    {
        return $this->is_popular;
    }

    public function isHighDemand(): bool
    {
        return $this->is_high_demand;
    }

    public function isNew(): bool
    {
        return $this->is_new;
    }

    public function isEstablished(): bool
    {
        return $this->is_established;
    }

    public function hasFeatures(): bool
    {
        return $this->features_count > 0;
    }

    public function hasRequirements(): bool
    {
        return $this->requirements_count > 0;
    }

    public function hasPricingDetails(): bool
    {
        return is_array($this->pricing_details) && count($this->pricing_details) > 0;
    }

    public function hasTermsConditions(): bool
    {
        return is_array($this->terms_conditions) && count($this->terms_conditions) > 0;
    }

    public function getFeaturesList(): array
    {
        if (is_array($this->features)) {
            return $this->features;
        }
        return [];
    }

    public function getRequirementsList(): array
    {
        if (is_array($this->requirements)) {
            return $this->requirements;
        }
        return [];
    }

    public function getPricingDetailsList(): array
    {
        if (is_array($this->pricing_details)) {
            return $this->pricing_details;
        }
        return [];
    }

    public function getTermsConditionsList(): array
    {
        if (is_array($this->terms_conditions)) {
            return $this->terms_conditions;
        }
        return [];
    }

    public function getServiceDescriptionAttribute(): string
    {
        $type = $this->service_type_label;
        $source = $this->energy_source_label;
        
        return "Servicio de {$type} - {$source}";
    }
}