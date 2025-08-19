<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Builder;

class ProjectCommission extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_proposal_id',
        'user_id',
        'type',
        'amount',
        'rate',
        'base_amount',
        'currency',
        'status',
        'due_date',
        'paid_at',
        'payment_method',
        'transaction_id',
        'description',
        'calculation_details',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'rate' => 'decimal:4',
        'base_amount' => 'decimal:2',
        'calculation_details' => 'array',
        'due_date' => 'datetime',
        'paid_at' => 'datetime',
    ];

    // Relaciones

    /**
     * Proyecto asociado
     */
    public function projectProposal(): BelongsTo
    {
        return $this->belongsTo(ProjectProposal::class);
    }

    /**
     * Usuario que debe pagar la comisión
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Pagos relacionados con esta comisión
     */
    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    // Scopes

    /**
     * Comisiones pendientes
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /**
     * Comisiones pagadas
     */
    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', 'paid');
    }

    /**
     * Comisiones vencidas
     */
    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('status', 'pending')
                    ->where('due_date', '<', now());
    }

    /**
     * Comisiones por tipo
     */
    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Comisiones próximas a vencer
     */
    public function scopeDueSoon(Builder $query, int $days = 7): Builder
    {
        return $query->where('status', 'pending')
                    ->whereBetween('due_date', [now(), now()->addDays($days)]);
    }

    // Métodos auxiliares

    /**
     * Verificar si la comisión está vencida
     */
    public function isOverdue(): bool
    {
        return $this->status === 'pending' && $this->due_date->isPast();
    }

    /**
     * Verificar si está pagada
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Marcar como pagada
     */
    public function markAsPaid(string $paymentMethod = null, string $transactionId = null): bool
    {
        return $this->update([
            'status' => 'paid',
            'paid_at' => now(),
            'payment_method' => $paymentMethod,
            'transaction_id' => $transactionId,
        ]);
    }

    /**
     * Calcular comisión automáticamente
     */
    public static function calculateCommission(float $baseAmount, float $rate): float
    {
        return $baseAmount * $rate;
    }

    /**
     * Crear comisión de éxito para un proyecto
     */
    public static function createSuccessFee(ProjectProposal $project, User $user): self
    {
        $rate = $user->subscriptionPlan?->commission_rate ?? 0.05; // 5% por defecto
        $baseAmount = $project->target_amount;
        $amount = self::calculateCommission($baseAmount, $rate);

        return self::create([
            'project_proposal_id' => $project->id,
            'user_id' => $user->id,
            'type' => 'success_fee',
            'amount' => $amount,
            'rate' => $rate,
            'base_amount' => $baseAmount,
            'status' => 'pending',
            'due_date' => now()->addDays(30),
            'description' => "Comisión de éxito por proyecto: {$project->title}",
            'calculation_details' => [
                'base_amount' => $baseAmount,
                'rate' => $rate,
                'calculation' => "{$baseAmount} × {$rate} = {$amount}",
                'project_title' => $project->title,
                'calculated_at' => now()->toISOString(),
            ],
        ]);
    }

    /**
     * Obtener etiqueta del tipo
     */
    public function getTypeLabel(): string
    {
        return match ($this->type) {
            'success_fee' => 'Comisión de éxito',
            'listing_fee' => 'Tarifa de listado',
            'verification_fee' => 'Tarifa de verificación',
            'premium_fee' => 'Tarifa premium',
            default => 'Desconocido',
        };
    }

    /**
     * Obtener etiqueta del estado
     */
    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'Pendiente',
            'paid' => 'Pagada',
            'waived' => 'Exonerada',
            'disputed' => 'En disputa',
            'refunded' => 'Reembolsada',
            default => 'Desconocido',
        };
    }

    /**
     * Obtener color del estado
     */
    public function getStatusColor(): string
    {
        return match ($this->status) {
            'pending' => $this->isOverdue() ? 'red' : 'orange',
            'paid' => 'green',
            'waived' => 'blue',
            'disputed' => 'purple',
            'refunded' => 'gray',
            default => 'gray',
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
     * Obtener días hasta vencimiento
     */
    public function getDaysUntilDue(): int
    {
        return now()->diffInDays($this->due_date, false);
    }

    /**
     * Obtener porcentaje formateado
     */
    public function getFormattedRate(): string
    {
        return number_format($this->rate * 100, 2) . '%';
    }
}