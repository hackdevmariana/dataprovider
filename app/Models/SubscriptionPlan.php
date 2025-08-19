<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'type',
        'billing_cycle',
        'price',
        'setup_fee',
        'trial_days',
        'max_projects',
        'max_cooperatives',
        'max_investments',
        'max_consultations',
        'features',
        'limits',
        'commission_rate',
        'priority_support',
        'verified_badge',
        'analytics_access',
        'api_access',
        'white_label',
        'is_active',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'limits' => 'array',
        'price' => 'decimal:2',
        'setup_fee' => 'decimal:2',
        'commission_rate' => 'decimal:4',
        'priority_support' => 'boolean',
        'verified_badge' => 'boolean',
        'analytics_access' => 'boolean',
        'api_access' => 'boolean',
        'white_label' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    // Relaciones

    /**
     * Suscripciones de usuarios a este plan
     */
    public function userSubscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }

    /**
     * Suscripciones activas
     */
    public function activeSubscriptions(): HasMany
    {
        return $this->userSubscriptions()->where('status', 'active');
    }

    // Scopes

    /**
     * Planes activos
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Planes destacados
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Planes por tipo
     */
    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Planes por ciclo de facturación
     */
    public function scopeByBillingCycle(Builder $query, string $cycle): Builder
    {
        return $query->where('billing_cycle', $cycle);
    }

    /**
     * Ordenar por precio
     */
    public function scopeOrderByPrice(Builder $query, string $direction = 'asc'): Builder
    {
        return $query->orderBy('price', $direction);
    }

    /**
     * Planes gratuitos
     */
    public function scopeFree(Builder $query): Builder
    {
        return $query->where('price', 0);
    }

    /**
     * Planes de pago
     */
    public function scopePaid(Builder $query): Builder
    {
        return $query->where('price', '>', 0);
    }

    // Métodos auxiliares

    /**
     * Verificar si el plan incluye una característica
     */
    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->features ?? []);
    }

    /**
     * Obtener límite específico
     */
    public function getLimit(string $limit): ?int
    {
        return $this->limits[$limit] ?? null;
    }

    /**
     * Verificar si es plan gratuito
     */
    public function isFree(): bool
    {
        return $this->price == 0;
    }

    /**
     * Verificar si tiene período de prueba
     */
    public function hasTrial(): bool
    {
        return $this->trial_days > 0;
    }

    /**
     * Calcular precio anual (si es mensual)
     */
    public function getYearlyPrice(): float
    {
        if ($this->billing_cycle === 'monthly') {
            return $this->price * 12;
        }
        
        return $this->price;
    }

    /**
     * Calcular precio mensual (si es anual)
     */
    public function getMonthlyPrice(): float
    {
        if ($this->billing_cycle === 'yearly') {
            return $this->price / 12;
        }
        
        return $this->price;
    }

    /**
     * Obtener precio formateado
     */
    public function getFormattedPrice(): string
    {
        if ($this->isFree()) {
            return 'Gratis';
        }

        $price = number_format($this->price, 2, ',', '.');
        $cycle = match ($this->billing_cycle) {
            'monthly' => '/mes',
            'yearly' => '/año',
            'one_time' => '',
            default => '',
        };

        return "€{$price}{$cycle}";
    }

    /**
     * Obtener etiqueta del tipo
     */
    public function getTypeLabel(): string
    {
        return match ($this->type) {
            'individual' => 'Individual',
            'cooperative' => 'Cooperativa',
            'business' => 'Empresa',
            'enterprise' => 'Enterprise',
            default => 'Desconocido',
        };
    }

    /**
     * Obtener color del plan
     */
    public function getColor(): string
    {
        return match ($this->type) {
            'individual' => 'blue',
            'cooperative' => 'green',
            'business' => 'purple',
            'enterprise' => 'gold',
            default => 'gray',
        };
    }

    /**
     * Obtener estadísticas del plan
     */
    public function getStats(): array
    {
        return [
            'total_subscriptions' => $this->userSubscriptions()->count(),
            'active_subscriptions' => $this->activeSubscriptions()->count(),
            'monthly_revenue' => $this->activeSubscriptions()
                ->where('billing_cycle', 'monthly')
                ->sum('amount_paid'),
            'yearly_revenue' => $this->activeSubscriptions()
                ->where('billing_cycle', 'yearly')
                ->sum('amount_paid'),
            'churn_rate' => $this->calculateChurnRate(),
        ];
    }

    /**
     * Calcular tasa de cancelación
     */
    private function calculateChurnRate(): float
    {
        $totalSubscriptions = $this->userSubscriptions()->count();
        $cancelledSubscriptions = $this->userSubscriptions()
            ->where('status', 'cancelled')
            ->count();

        if ($totalSubscriptions === 0) {
            return 0;
        }

        return ($cancelledSubscriptions / $totalSubscriptions) * 100;
    }

    // Eventos del modelo

    protected static function booted()
    {
        // Generar slug automáticamente
        static::creating(function (SubscriptionPlan $plan) {
            if (empty($plan->slug)) {
                $plan->slug = Str::slug($plan->name);
            }
        });

        static::updating(function (SubscriptionPlan $plan) {
            if ($plan->isDirty('name') && empty($plan->slug)) {
                $plan->slug = Str::slug($plan->name);
            }
        });
    }
}