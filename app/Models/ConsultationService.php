<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Builder;

class ConsultationService extends Model
{
    use HasFactory;

    protected $fillable = [
        'consultant_id',
        'client_id',
        'title',
        'description',
        'type',
        'format',
        'status',
        'hourly_rate',
        'fixed_price',
        'total_amount',
        'currency',
        'estimated_hours',
        'actual_hours',
        'requested_at',
        'accepted_at',
        'started_at',
        'completed_at',
        'deadline',
        'requirements',
        'deliverables',
        'milestones',
        'client_notes',
        'consultant_notes',
        'client_rating',
        'consultant_rating',
        'client_review',
        'consultant_review',
        'platform_commission',
        'is_featured',
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'fixed_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'platform_commission' => 'decimal:4',
        'requirements' => 'array',
        'deliverables' => 'array',
        'milestones' => 'array',
        'requested_at' => 'datetime',
        'accepted_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'deadline' => 'datetime',
        'is_featured' => 'boolean',
    ];

    // Relaciones

    /**
     * Usuario consultor
     */
    public function consultant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'consultant_id');
    }

    /**
     * Usuario cliente
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Pagos relacionados con esta consultoría
     */
    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    // Scopes

    /**
     * Consultas solicitadas
     */
    public function scopeRequested(Builder $query): Builder
    {
        return $query->where('status', 'requested');
    }

    /**
     * Consultas aceptadas
     */
    public function scopeAccepted(Builder $query): Builder
    {
        return $query->where('status', 'accepted');
    }

    /**
     * Consultas en progreso
     */
    public function scopeInProgress(Builder $query): Builder
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Consultas completadas
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    /**
     * Consultas por tipo
     */
    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Consultas por formato
     */
    public function scopeByFormat(Builder $query, string $format): Builder
    {
        return $query->where('format', $format);
    }

    /**
     * Consultas destacadas
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Consultas vencidas
     */
    public function scopeOverdue(Builder $query): Builder
    {
        return $query->whereIn('status', ['accepted', 'in_progress'])
                    ->where('deadline', '<', now());
    }

    /**
     * Consultas por consultor
     */
    public function scopeByConsultant(Builder $query, int $consultantId): Builder
    {
        return $query->where('consultant_id', $consultantId);
    }

    /**
     * Consultas por cliente
     */
    public function scopeByClient(Builder $query, int $clientId): Builder
    {
        return $query->where('client_id', $clientId);
    }

    // Métodos auxiliares

    /**
     * Verificar si está completada
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Verificar si está vencida
     */
    public function isOverdue(): bool
    {
        return in_array($this->status, ['accepted', 'in_progress']) && 
               $this->deadline && 
               $this->deadline->isPast();
    }

    /**
     * Aceptar consulta
     */
    public function accept(array $terms = []): bool
    {
        return $this->update([
            'status' => 'accepted',
            'accepted_at' => now(),
            'consultant_notes' => $terms['notes'] ?? null,
            'total_amount' => $terms['total_amount'] ?? $this->calculateTotalAmount(),
        ]);
    }

    /**
     * Iniciar consulta
     */
    public function start(): bool
    {
        return $this->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);
    }

    /**
     * Completar consulta
     */
    public function complete(array $deliverables = []): bool
    {
        return $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'deliverables' => array_merge($this->deliverables ?? [], $deliverables),
        ]);
    }

    /**
     * Cancelar consulta
     */
    public function cancel(string $reason = null): bool
    {
        $notes = $reason ? "Cancelada: {$reason}" : 'Consulta cancelada';
        
        return $this->update([
            'status' => 'cancelled',
            'consultant_notes' => $notes,
        ]);
    }

    /**
     * Calcular cantidad total
     */
    public function calculateTotalAmount(): float
    {
        if ($this->fixed_price) {
            return $this->fixed_price;
        }

        if ($this->hourly_rate && $this->estimated_hours) {
            return $this->hourly_rate * $this->estimated_hours;
        }

        return 0;
    }

    /**
     * Calcular comisión de la plataforma
     */
    public function calculatePlatformCommission(): float
    {
        return $this->total_amount * $this->platform_commission;
    }

    /**
     * Calcular cantidad neta para el consultor
     */
    public function calculateNetAmount(): float
    {
        return $this->total_amount - $this->calculatePlatformCommission();
    }

    /**
     * Añadir rating del cliente
     */
    public function rateByClient(int $rating, string $review = null): bool
    {
        return $this->update([
            'client_rating' => $rating,
            'client_review' => $review,
        ]);
    }

    /**
     * Añadir rating del consultor
     */
    public function rateByConsultant(int $rating, string $review = null): bool
    {
        return $this->update([
            'consultant_rating' => $rating,
            'consultant_review' => $review,
        ]);
    }

    /**
     * Obtener rating promedio
     */
    public function getAverageRating(): float
    {
        $ratings = array_filter([$this->client_rating, $this->consultant_rating]);
        
        if (empty($ratings)) {
            return 0;
        }

        return array_sum($ratings) / count($ratings);
    }

    /**
     * Actualizar horas trabajadas
     */
    public function updateActualHours(int $hours): bool
    {
        $newTotal = ($this->actual_hours ?? 0) + $hours;
        
        return $this->update([
            'actual_hours' => $newTotal,
            'total_amount' => $this->hourly_rate ? $this->hourly_rate * $newTotal : $this->total_amount,
        ]);
    }

    /**
     * Obtener etiqueta del tipo
     */
    public function getTypeLabel(): string
    {
        return match ($this->type) {
            'technical' => 'Técnica',
            'legal' => 'Legal',
            'financial' => 'Financiera',
            'installation' => 'Instalación',
            'maintenance' => 'Mantenimiento',
            'custom' => 'Personalizada',
            default => 'Desconocido',
        };
    }

    /**
     * Obtener etiqueta del formato
     */
    public function getFormatLabel(): string
    {
        return match ($this->format) {
            'online' => 'Online',
            'onsite' => 'Presencial',
            'hybrid' => 'Híbrido',
            'document_review' => 'Revisión documentos',
            'phone_call' => 'Llamada telefónica',
            default => 'Desconocido',
        };
    }

    /**
     * Obtener etiqueta del estado
     */
    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'requested' => 'Solicitada',
            'accepted' => 'Aceptada',
            'in_progress' => 'En progreso',
            'completed' => 'Completada',
            'cancelled' => 'Cancelada',
            'disputed' => 'En disputa',
            default => 'Desconocido',
        };
    }

    /**
     * Obtener color del estado
     */
    public function getStatusColor(): string
    {
        return match ($this->status) {
            'requested' => 'blue',
            'accepted' => 'green',
            'in_progress' => 'orange',
            'completed' => 'green',
            'cancelled' => 'red',
            'disputed' => 'purple',
            default => 'gray',
        };
    }

    /**
     * Obtener precio formateado
     */
    public function getFormattedPrice(): string
    {
        if ($this->fixed_price) {
            return '€' . number_format($this->fixed_price, 2, ',', '.');
        }

        if ($this->hourly_rate) {
            return '€' . number_format($this->hourly_rate, 2, ',', '.') . '/hora';
        }

        return 'A convenir';
    }

    /**
     * Días hasta deadline
     */
    public function getDaysUntilDeadline(): int
    {
        if (!$this->deadline) {
            return -1;
        }

        return now()->diffInDays($this->deadline, false);
    }

    /**
     * Progreso de la consulta (0-100%)
     */
    public function getProgress(): int
    {
        return match ($this->status) {
            'requested' => 10,
            'accepted' => 25,
            'in_progress' => 75,
            'completed' => 100,
            'cancelled' => 0,
            default => 0,
        };
    }

    /**
     * Obtener estadísticas del consultor
     */
    public static function getConsultantStats(int $consultantId): array
    {
        $consultations = self::where('consultant_id', $consultantId);

        return [
            'total_consultations' => $consultations->count(),
            'completed_consultations' => $consultations->completed()->count(),
            'average_rating' => $consultations->whereNotNull('client_rating')->avg('client_rating'),
            'total_earnings' => $consultations->completed()->sum('total_amount'),
            'response_rate' => self::calculateResponseRate($consultantId),
            'completion_rate' => self::calculateCompletionRate($consultantId),
        ];
    }

    /**
     * Calcular tasa de respuesta del consultor
     */
    private static function calculateResponseRate(int $consultantId): float
    {
        $total = self::where('consultant_id', $consultantId)->count();
        $responded = self::where('consultant_id', $consultantId)
                         ->whereIn('status', ['accepted', 'in_progress', 'completed'])
                         ->count();

        return $total > 0 ? ($responded / $total) * 100 : 0;
    }

    /**
     * Calcular tasa de completación del consultor
     */
    private static function calculateCompletionRate(int $consultantId): float
    {
        $accepted = self::where('consultant_id', $consultantId)
                        ->whereIn('status', ['accepted', 'in_progress', 'completed'])
                        ->count();
        $completed = self::where('consultant_id', $consultantId)->completed()->count();

        return $accepted > 0 ? ($completed / $accepted) * 100 : 0;
    }
}