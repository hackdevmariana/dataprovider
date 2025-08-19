<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Builder;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'payable_type',
        'payable_id',
        'payment_intent_id',
        'status',
        'type',
        'amount',
        'fee',
        'net_amount',
        'currency',
        'payment_method',
        'processor',
        'processor_response',
        'metadata',
        'description',
        'failure_reason',
        'processed_at',
        'failed_at',
        'refunded_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'processor_response' => 'array',
        'metadata' => 'array',
        'processed_at' => 'datetime',
        'failed_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    // Relaciones

    /**
     * Usuario que realiza el pago
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Elemento que se está pagando (polimórfico)
     */
    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    // Scopes

    /**
     * Pagos pendientes
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /**
     * Pagos en proceso
     */
    public function scopeProcessing(Builder $query): Builder
    {
        return $query->where('status', 'processing');
    }

    /**
     * Pagos completados
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    /**
     * Pagos fallidos
     */
    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', 'failed');
    }

    /**
     * Pagos reembolsados
     */
    public function scopeRefunded(Builder $query): Builder
    {
        return $query->where('status', 'refunded');
    }

    /**
     * Pagos por tipo
     */
    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Pagos por procesador
     */
    public function scopeByProcessor(Builder $query, string $processor): Builder
    {
        return $query->where('processor', $processor);
    }

    /**
     * Pagos de hoy
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Pagos de esta semana
     */
    public function scopeThisWeek(Builder $query): Builder
    {
        return $query->whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    /**
     * Pagos de este mes
     */
    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }

    // Métodos auxiliares

    /**
     * Verificar si está completado
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Verificar si falló
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Verificar si está reembolsado
     */
    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }

    /**
     * Marcar como completado
     */
    public function markAsCompleted(array $processorResponse = []): bool
    {
        return $this->update([
            'status' => 'completed',
            'processed_at' => now(),
            'processor_response' => $processorResponse,
            'net_amount' => $this->amount - $this->fee,
        ]);
    }

    /**
     * Marcar como fallido
     */
    public function markAsFailed(string $reason, array $processorResponse = []): bool
    {
        return $this->update([
            'status' => 'failed',
            'failed_at' => now(),
            'failure_reason' => $reason,
            'processor_response' => $processorResponse,
        ]);
    }

    /**
     * Marcar como reembolsado
     */
    public function markAsRefunded(array $processorResponse = []): bool
    {
        return $this->update([
            'status' => 'refunded',
            'refunded_at' => now(),
            'processor_response' => array_merge($this->processor_response ?? [], $processorResponse),
        ]);
    }

    /**
     * Crear pago para suscripción
     */
    public static function createForSubscription(
        UserSubscription $subscription, 
        string $paymentIntentId,
        string $processor = 'stripe'
    ): self {
        return self::create([
            'user_id' => $subscription->user_id,
            'payable_type' => UserSubscription::class,
            'payable_id' => $subscription->id,
            'payment_intent_id' => $paymentIntentId,
            'status' => 'pending',
            'type' => 'subscription',
            'amount' => $subscription->amount_paid,
            'currency' => $subscription->currency,
            'processor' => $processor,
            'description' => "Suscripción: {$subscription->subscriptionPlan->name}",
        ]);
    }

    /**
     * Crear pago para comisión
     */
    public static function createForCommission(
        ProjectCommission $commission,
        string $paymentIntentId,
        string $processor = 'stripe'
    ): self {
        return self::create([
            'user_id' => $commission->user_id,
            'payable_type' => ProjectCommission::class,
            'payable_id' => $commission->id,
            'payment_intent_id' => $paymentIntentId,
            'status' => 'pending',
            'type' => 'commission',
            'amount' => $commission->amount,
            'currency' => $commission->currency,
            'processor' => $processor,
            'description' => $commission->description,
        ]);
    }

    /**
     * Crear pago para verificación
     */
    public static function createForVerification(
        ProjectVerification $verification,
        string $paymentIntentId,
        string $processor = 'stripe'
    ): self {
        return self::create([
            'user_id' => $verification->requested_by,
            'payable_type' => ProjectVerification::class,
            'payable_id' => $verification->id,
            'payment_intent_id' => $paymentIntentId,
            'status' => 'pending',
            'type' => 'verification',
            'amount' => $verification->fee,
            'currency' => $verification->currency,
            'processor' => $processor,
            'description' => "Verificación {$verification->getTypeLabel()}: {$verification->projectProposal->title}",
        ]);
    }

    /**
     * Crear pago para consultoría
     */
    public static function createForConsultation(
        ConsultationService $consultation,
        string $paymentIntentId,
        string $processor = 'stripe'
    ): self {
        return self::create([
            'user_id' => $consultation->client_id,
            'payable_type' => ConsultationService::class,
            'payable_id' => $consultation->id,
            'payment_intent_id' => $paymentIntentId,
            'status' => 'pending',
            'type' => 'consultation',
            'amount' => $consultation->total_amount,
            'currency' => $consultation->currency,
            'processor' => $processor,
            'description' => "Consultoría: {$consultation->title}",
        ]);
    }

    /**
     * Obtener etiqueta del estado
     */
    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'Pendiente',
            'processing' => 'Procesando',
            'completed' => 'Completado',
            'failed' => 'Fallido',
            'cancelled' => 'Cancelado',
            'refunded' => 'Reembolsado',
            default => 'Desconocido',
        };
    }

    /**
     * Obtener color del estado
     */
    public function getStatusColor(): string
    {
        return match ($this->status) {
            'pending' => 'orange',
            'processing' => 'blue',
            'completed' => 'green',
            'failed' => 'red',
            'cancelled' => 'gray',
            'refunded' => 'purple',
            default => 'gray',
        };
    }

    /**
     * Obtener etiqueta del tipo
     */
    public function getTypeLabel(): string
    {
        return match ($this->type) {
            'subscription' => 'Suscripción',
            'commission' => 'Comisión',
            'verification' => 'Verificación',
            'consultation' => 'Consultoría',
            'refund' => 'Reembolso',
            default => 'Desconocido',
        };
    }

    /**
     * Obtener cantidad formateada
     */
    public function getFormattedAmount(): string
    {
        return '€' . number_format($this->amount, 2, ',', '.');
    }

    /**
     * Obtener fee formateado
     */
    public function getFormattedFee(): string
    {
        return '€' . number_format($this->fee, 2, ',', '.');
    }

    /**
     * Obtener cantidad neta formateada
     */
    public function getFormattedNetAmount(): string
    {
        return '€' . number_format($this->net_amount, 2, ',', '.');
    }

    /**
     * Obtener estadísticas de pagos
     */
    public static function getStats(array $filters = []): array
    {
        $query = self::query();

        if (isset($filters['period'])) {
            match ($filters['period']) {
                'today' => $query->today(),
                'week' => $query->thisWeek(),
                'month' => $query->thisMonth(),
                default => null,
            };
        }

        if (isset($filters['type'])) {
            $query->byType($filters['type']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return [
            'total_payments' => $query->count(),
            'total_amount' => $query->sum('amount'),
            'total_fees' => $query->sum('fee'),
            'total_net' => $query->sum('net_amount'),
            'completed_payments' => $query->completed()->count(),
            'failed_payments' => $query->failed()->count(),
            'success_rate' => self::calculateSuccessRate($query),
            'average_amount' => $query->avg('amount'),
        ];
    }

    /**
     * Calcular tasa de éxito
     */
    private static function calculateSuccessRate(Builder $query): float
    {
        $total = $query->count();
        $completed = $query->completed()->count();

        return $total > 0 ? ($completed / $total) * 100 : 0;
    }
}