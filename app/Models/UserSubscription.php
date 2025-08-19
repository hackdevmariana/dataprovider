<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class UserSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_plan_id',
        'status',
        'amount_paid',
        'currency',
        'billing_cycle',
        'starts_at',
        'ends_at',
        'trial_ends_at',
        'cancelled_at',
        'next_billing_at',
        'payment_method',
        'external_subscription_id',
        'usage_stats',
        'metadata',
        'cancellation_reason',
        'auto_renew',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'usage_stats' => 'array',
        'metadata' => 'array',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'next_billing_at' => 'datetime',
        'auto_renew' => 'boolean',
    ];

    // Relaciones

    /**
     * Usuario propietario de la suscripción
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Plan de suscripción
     */
    public function subscriptionPlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    /**
     * Pagos relacionados con esta suscripción
     */
    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    // Scopes

    /**
     * Suscripciones activas
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active')
                    ->where(function ($q) {
                        $q->whereNull('ends_at')
                          ->orWhere('ends_at', '>', now());
                    });
    }

    /**
     * Suscripciones en período de prueba
     */
    public function scopeTrial(Builder $query): Builder
    {
        return $query->where('status', 'trial')
                    ->where('trial_ends_at', '>', now());
    }

    /**
     * Suscripciones expiradas
     */
    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('status', 'expired')
                    ->orWhere(function ($q) {
                        $q->where('ends_at', '<=', now());
                    });
    }

    /**
     * Suscripciones canceladas
     */
    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Próximas a facturar
     */
    public function scopeUpcomingBilling(Builder $query, int $days = 7): Builder
    {
        return $query->where('status', 'active')
                    ->where('next_billing_at', '<=', now()->addDays($days))
                    ->where('auto_renew', true);
    }

    // Métodos auxiliares

    /**
     * Verificar si la suscripción está activa
     */
    public function isActive(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        if ($this->ends_at && $this->ends_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Verificar si está en período de prueba
     */
    public function isOnTrial(): bool
    {
        return $this->status === 'trial' && 
               $this->trial_ends_at && 
               $this->trial_ends_at->isFuture();
    }

    /**
     * Verificar si ha expirado
     */
    public function isExpired(): bool
    {
        return $this->status === 'expired' || 
               ($this->ends_at && $this->ends_at->isPast());
    }

    /**
     * Verificar si está cancelada
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Días restantes de suscripción
     */
    public function daysRemaining(): int
    {
        if (!$this->ends_at) {
            return -1; // Suscripción sin fecha de fin
        }

        return max(0, now()->diffInDays($this->ends_at, false));
    }

    /**
     * Días restantes de prueba
     */
    public function trialDaysRemaining(): int
    {
        if (!$this->trial_ends_at) {
            return 0;
        }

        return max(0, now()->diffInDays($this->trial_ends_at, false));
    }

    /**
     * Cancelar suscripción
     */
    public function cancel(string $reason = null): bool
    {
        return $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
            'auto_renew' => false,
        ]);
    }

    /**
     * Reactivar suscripción
     */
    public function reactivate(): bool
    {
        if (!$this->isCancelled()) {
            return false;
        }

        return $this->update([
            'status' => 'active',
            'cancelled_at' => null,
            'cancellation_reason' => null,
            'auto_renew' => true,
        ]);
    }

    /**
     * Renovar suscripción
     */
    public function renew(): bool
    {
        if (!$this->auto_renew || $this->isCancelled()) {
            return false;
        }

        $plan = $this->subscriptionPlan;
        $nextBilling = match ($this->billing_cycle) {
            'monthly' => now()->addMonth(),
            'yearly' => now()->addYear(),
            default => null,
        };

        return $this->update([
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => $nextBilling,
            'next_billing_at' => $nextBilling,
        ]);
    }

    /**
     * Actualizar estadísticas de uso
     */
    public function updateUsageStats(array $stats): bool
    {
        $currentStats = $this->usage_stats ?? [];
        $updatedStats = array_merge($currentStats, $stats);

        return $this->update(['usage_stats' => $updatedStats]);
    }

    /**
     * Verificar si se ha alcanzado un límite
     */
    public function hasReachedLimit(string $limit): bool
    {
        $plan = $this->subscriptionPlan;
        $maxLimit = $plan->getLimit($limit);
        
        if ($maxLimit === null) {
            return false; // Sin límite
        }

        $currentUsage = $this->usage_stats[$limit] ?? 0;
        return $currentUsage >= $maxLimit;
    }

    /**
     * Obtener uso actual de un límite
     */
    public function getCurrentUsage(string $limit): int
    {
        return $this->usage_stats[$limit] ?? 0;
    }

    /**
     * Incrementar uso de un límite
     */
    public function incrementUsage(string $limit, int $amount = 1): bool
    {
        $currentUsage = $this->getCurrentUsage($limit);
        return $this->updateUsageStats([$limit => $currentUsage + $amount]);
    }

    /**
     * Obtener próxima fecha de facturación formateada
     */
    public function getNextBillingFormatted(): string
    {
        if (!$this->next_billing_at) {
            return 'N/A';
        }

        return $this->next_billing_at->format('d/m/Y');
    }

    /**
     * Obtener estado formateado
     */
    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'active' => 'Activa',
            'trial' => 'Período de prueba',
            'cancelled' => 'Cancelada',
            'expired' => 'Expirada',
            'suspended' => 'Suspendida',
            default => 'Desconocido',
        };
    }

    /**
     * Obtener color del estado
     */
    public function getStatusColor(): string
    {
        return match ($this->status) {
            'active' => 'green',
            'trial' => 'blue',
            'cancelled' => 'red',
            'expired' => 'gray',
            'suspended' => 'orange',
            default => 'gray',
        };
    }
}