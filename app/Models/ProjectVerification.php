<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ProjectVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_proposal_id',
        'requested_by',
        'verified_by',
        'type',
        'status',
        'fee',
        'currency',
        'verification_criteria',
        'documents_required',
        'documents_provided',
        'verification_results',
        'verification_notes',
        'rejection_reason',
        'score',
        'requested_at',
        'reviewed_at',
        'verified_at',
        'expires_at',
        'is_public',
        'certificate_number',
    ];

    protected $casts = [
        'fee' => 'decimal:2',
        'verification_criteria' => 'array',
        'documents_required' => 'array',
        'documents_provided' => 'array',
        'verification_results' => 'array',
        'requested_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'verified_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_public' => 'boolean',
    ];

    // Relaciones

    /**
     * Proyecto que se está verificando
     */
    public function projectProposal(): BelongsTo
    {
        return $this->belongsTo(ProjectProposal::class);
    }

    /**
     * Usuario que solicita la verificación
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * Usuario verificador
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Pagos relacionados con esta verificación
     */
    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    // Scopes

    /**
     * Verificaciones solicitadas
     */
    public function scopeRequested(Builder $query): Builder
    {
        return $query->where('status', 'requested');
    }

    /**
     * Verificaciones en revisión
     */
    public function scopeInReview(Builder $query): Builder
    {
        return $query->where('status', 'in_review');
    }

    /**
     * Verificaciones aprobadas
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    /**
     * Verificaciones rechazadas
     */
    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Verificaciones expiradas
     */
    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('status', 'expired')
                    ->orWhere('expires_at', '<=', now());
    }

    /**
     * Verificaciones por tipo
     */
    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Verificaciones públicas
     */
    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }

    /**
     * Verificaciones próximas a expirar
     */
    public function scopeExpiringSoon(Builder $query, int $days = 30): Builder
    {
        return $query->where('status', 'approved')
                    ->whereBetween('expires_at', [now(), now()->addDays($days)]);
    }

    // Métodos auxiliares

    /**
     * Verificar si está aprobada
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved' && 
               ($this->expires_at === null || $this->expires_at->isFuture());
    }

    /**
     * Verificar si está expirada
     */
    public function isExpired(): bool
    {
        return $this->status === 'expired' || 
               ($this->expires_at && $this->expires_at->isPast());
    }

    /**
     * Verificar si está en revisión
     */
    public function isInReview(): bool
    {
        return $this->status === 'in_review';
    }

    /**
     * Iniciar proceso de revisión
     */
    public function startReview(User $verifier): bool
    {
        return $this->update([
            'status' => 'in_review',
            'verified_by' => $verifier->id,
            'reviewed_at' => now(),
        ]);
    }

    /**
     * Aprobar verificación
     */
    public function approve(array $results, string $notes = null, int $score = null): bool
    {
        $certificateNumber = $this->generateCertificateNumber();
        $expiresAt = $this->calculateExpirationDate();

        return $this->update([
            'status' => 'approved',
            'verification_results' => $results,
            'verification_notes' => $notes,
            'score' => $score,
            'verified_at' => now(),
            'expires_at' => $expiresAt,
            'certificate_number' => $certificateNumber,
        ]);
    }

    /**
     * Rechazar verificación
     */
    public function reject(string $reason, string $notes = null): bool
    {
        return $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'verification_notes' => $notes,
            'verified_at' => now(),
        ]);
    }

    /**
     * Generar número de certificado único
     */
    private function generateCertificateNumber(): string
    {
        $prefix = strtoupper(substr($this->type, 0, 2));
        $year = now()->year;
        $sequence = str_pad($this->id, 6, '0', STR_PAD_LEFT);
        
        return "{$prefix}-{$year}-{$sequence}";
    }

    /**
     * Calcular fecha de expiración según el tipo
     */
    private function calculateExpirationDate(): ?\Carbon\Carbon
    {
        return match ($this->type) {
            'basic' => now()->addYear(),
            'advanced' => now()->addYears(2),
            'professional' => now()->addYears(3),
            'enterprise' => now()->addYears(5),
            default => now()->addYear(),
        };
    }

    /**
     * Obtener precio de verificación según el tipo
     */
    public static function getFeeByType(string $type): float
    {
        return match ($type) {
            'basic' => 199.00,
            'advanced' => 499.00,
            'professional' => 999.00,
            'enterprise' => 1999.00,
            default => 199.00,
        };
    }

    /**
     * Obtener criterios de verificación por defecto
     */
    public static function getDefaultCriteria(string $type): array
    {
        $baseCriteria = [
            'project_feasibility',
            'financial_viability',
            'legal_compliance',
            'technical_specifications',
        ];

        return match ($type) {
            'basic' => $baseCriteria,
            'advanced' => array_merge($baseCriteria, [
                'environmental_impact',
                'risk_assessment',
                'timeline_analysis',
            ]),
            'professional' => array_merge($baseCriteria, [
                'environmental_impact',
                'risk_assessment',
                'timeline_analysis',
                'market_analysis',
                'competitive_advantage',
                'scalability_potential',
            ]),
            'enterprise' => array_merge($baseCriteria, [
                'environmental_impact',
                'risk_assessment',
                'timeline_analysis',
                'market_analysis',
                'competitive_advantage',
                'scalability_potential',
                'regulatory_approval',
                'insurance_coverage',
                'stakeholder_analysis',
            ]),
            default => $baseCriteria,
        };
    }

    /**
     * Obtener documentos requeridos por defecto
     */
    public static function getDefaultDocuments(string $type): array
    {
        $baseDocuments = [
            'project_description',
            'business_plan',
            'financial_projections',
            'technical_drawings',
        ];

        return match ($type) {
            'basic' => $baseDocuments,
            'advanced' => array_merge($baseDocuments, [
                'environmental_study',
                'risk_analysis',
                'permits_licenses',
            ]),
            'professional' => array_merge($baseDocuments, [
                'environmental_study',
                'risk_analysis',
                'permits_licenses',
                'market_research',
                'competitive_analysis',
                'insurance_documentation',
            ]),
            'enterprise' => array_merge($baseDocuments, [
                'environmental_study',
                'risk_analysis',
                'permits_licenses',
                'market_research',
                'competitive_analysis',
                'insurance_documentation',
                'regulatory_approvals',
                'stakeholder_agreements',
                'audit_reports',
            ]),
            default => $baseDocuments,
        };
    }

    /**
     * Obtener etiqueta del tipo
     */
    public function getTypeLabel(): string
    {
        return match ($this->type) {
            'basic' => 'Básica',
            'advanced' => 'Avanzada',
            'professional' => 'Profesional',
            'enterprise' => 'Enterprise',
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
            'in_review' => 'En revisión',
            'approved' => 'Aprobada',
            'rejected' => 'Rechazada',
            'expired' => 'Expirada',
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
            'in_review' => 'orange',
            'approved' => 'green',
            'rejected' => 'red',
            'expired' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Obtener badge de verificación
     */
    public function getBadge(): array
    {
        if (!$this->isApproved()) {
            return [];
        }

        return [
            'type' => $this->type,
            'label' => $this->getTypeLabel(),
            'score' => $this->score,
            'certificate' => $this->certificate_number,
            'verified_at' => $this->verified_at->format('d/m/Y'),
            'expires_at' => $this->expires_at?->format('d/m/Y'),
        ];
    }

    /**
     * Días hasta expiración
     */
    public function getDaysUntilExpiration(): int
    {
        if (!$this->expires_at) {
            return -1;
        }

        return now()->diffInDays($this->expires_at, false);
    }

    // Eventos del modelo

    protected static function booted()
    {
        static::creating(function (ProjectVerification $verification) {
            if (empty($verification->requested_at)) {
                $verification->requested_at = now();
            }
            
            if (empty($verification->fee)) {
                $verification->fee = self::getFeeByType($verification->type);
            }
            
            if (empty($verification->verification_criteria)) {
                $verification->verification_criteria = self::getDefaultCriteria($verification->type);
            }
            
            if (empty($verification->documents_required)) {
                $verification->documents_required = self::getDefaultDocuments($verification->type);
            }
        });
    }
}