<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Sistema de contenido patrocinado y publicidad nativa.
 * 
 * Gestiona campañas publicitarias con targeting avanzado,
 * diferentes modelos de pricing y métricas detalladas.
 */
class SponsoredContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'sponsor_id',
        'sponsorable_type',
        'sponsorable_id',
        'campaign_name',
        'campaign_description',
        'content_type',
        'target_audience',
        'target_topics',
        'target_locations',
        'target_demographics',
        'ad_label',
        'call_to_action',
        'destination_url',
        'creative_assets',
        'pricing_model',
        'bid_amount',
        'daily_budget',
        'total_budget',
        'spent_amount',
        'start_date',
        'end_date',
        'schedule_config',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'impressions',
        'clicks',
        'conversions',
        'ctr',
        'conversion_rate',
        'engagement_rate',
        'show_sponsor_info',
        'allow_user_feedback',
        'disclosure_text',
    ];

    protected $casts = [
        'target_audience' => 'array',
        'target_topics' => 'array',
        'target_locations' => 'array',
        'target_demographics' => 'array',
        'creative_assets' => 'array',
        'schedule_config' => 'array',
        'disclosure_text' => 'array',
        'bid_amount' => 'decimal:4',
        'daily_budget' => 'decimal:2',
        'total_budget' => 'decimal:2',
        'spent_amount' => 'decimal:2',
        'ctr' => 'decimal:2',
        'conversion_rate' => 'decimal:2',
        'engagement_rate' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'reviewed_at' => 'datetime',
        'show_sponsor_info' => 'boolean',
        'allow_user_feedback' => 'boolean',
    ];

    /**
     * Usuario/empresa patrocinador.
     */
    public function sponsor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sponsor_id');
    }

    /**
     * Contenido patrocinado (polimórfico).
     */
    public function sponsorable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Usuario que revisó la campaña.
     */
    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Verificar si la campaña está activa.
     */
    public function isActive(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        $now = now();
        
        if ($this->start_date > $now) {
            return false;
        }

        if ($this->end_date && $this->end_date < $now) {
            return false;
        }

        if ($this->daily_budget && $this->getDailySpent() >= $this->daily_budget) {
            return false;
        }

        if ($this->total_budget && $this->spent_amount >= $this->total_budget) {
            return false;
        }

        return true;
    }

    /**
     * Obtener gasto diario actual.
     */
    public function getDailySpent(): float
    {
        // Aquí iría la lógica para calcular el gasto del día actual
        // Por simplicidad, asumimos que se calcula desde otra tabla de transacciones
        return 0.0;
    }

    /**
     * Registrar impresión.
     */
    public function recordImpression(): void
    {
        $this->increment('impressions');
        $this->updateMetrics();
    }

    /**
     * Registrar click.
     */
    public function recordClick(): void
    {
        $this->increment('clicks');
        $this->updateMetrics();

        // Cobrar por click si es modelo CPC
        if ($this->pricing_model === 'cpc') {
            $this->chargeBid();
        }
    }

    /**
     * Registrar conversión.
     */
    public function recordConversion(): void
    {
        $this->increment('conversions');
        $this->updateMetrics();

        // Cobrar por acción si es modelo CPA
        if ($this->pricing_model === 'cpa') {
            $this->chargeBid();
        }
    }

    /**
     * Actualizar métricas calculadas.
     */
    public function updateMetrics(): void
    {
        $ctr = $this->impressions > 0 ? ($this->clicks / $this->impressions) * 100 : 0;
        $conversionRate = $this->clicks > 0 ? ($this->conversions / $this->clicks) * 100 : 0;

        $this->update([
            'ctr' => round($ctr, 2),
            'conversion_rate' => round($conversionRate, 2),
        ]);
    }

    /**
     * Cobrar bid amount.
     */
    protected function chargeBid(): void
    {
        $newSpent = $this->spent_amount + $this->bid_amount;
        $this->update(['spent_amount' => $newSpent]);

        // Verificar si se agotó el presupuesto
        if ($this->total_budget && $newSpent >= $this->total_budget) {
            $this->update(['status' => 'completed']);
        }
    }

    /**
     * Verificar si el contenido debe mostrarse al usuario.
     */
    public function shouldShowToUser(User $user): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        // Verificar targeting de audiencia
        if ($this->target_audience && !$this->matchesAudience($user)) {
            return false;
        }

        // Verificar targeting demográfico
        if ($this->target_demographics && !$this->matchesDemographics($user)) {
            return false;
        }

        // Verificar targeting de ubicación
        if ($this->target_locations && !$this->matchesLocation($user)) {
            return false;
        }

        return true;
    }

    /**
     * Verificar si coincide con audiencia objetivo.
     */
    protected function matchesAudience(User $user): bool
    {
        // Lógica de matching de audiencia
        return true;
    }

    /**
     * Verificar si coincide con demografía objetivo.
     */
    protected function matchesDemographics(User $user): bool
    {
        // Lógica de matching demográfico
        return true;
    }

    /**
     * Verificar si coincide con ubicación objetivo.
     */
    protected function matchesLocation(User $user): bool
    {
        // Lógica de matching de ubicación
        return true;
    }

    /**
     * Obtener campañas activas para mostrar.
     */
    public static function getActiveCampaigns(string $contentType = null, int $limit = 10)
    {
        $query = static::where('status', 'active')
                      ->where('start_date', '<=', now())
                      ->where(function ($q) {
                          $q->whereNull('end_date')
                            ->orWhere('end_date', '>', now());
                      });

        if ($contentType) {
            $query->where('content_type', $contentType);
        }

        return $query->orderBy('bid_amount', 'desc')
                    ->limit($limit)
                    ->get();
    }

    /**
     * Aprobar campaña.
     */
    public function approve(User $reviewer, string $notes = null): void
    {
        $this->update([
            'status' => 'approved',
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'review_notes' => $notes,
        ]);
    }

    /**
     * Rechazar campaña.
     */
    public function reject(User $reviewer, string $notes): void
    {
        $this->update([
            'status' => 'rejected',
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'review_notes' => $notes,
        ]);
    }
}
