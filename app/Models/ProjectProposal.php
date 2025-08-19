<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Sistema de propuestas de proyectos colaborativos.
 * 
 * Permite a los usuarios proponer proyectos energéticos,
 * buscar financiación colaborativa y gestionar su ejecución.
 */
class ProjectProposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'proposer_id',
        'cooperative_id',
        'title',
        'slug',
        'description',
        'summary',
        'objectives',
        'benefits',
        'project_type',
        'scale',
        'municipality_id',
        'specific_location',
        'latitude',
        'longitude',
        'estimated_power_kw',
        'estimated_annual_production_kwh',
        'technical_specifications',
        'total_investment_required',
        'investment_raised',
        'min_investment_per_participant',
        'max_investment_per_participant',
        'max_participants',
        'current_participants',
        'estimated_roi_percentage',
        'payback_period_years',
        'estimated_annual_savings',
        'financial_projections',
        'funding_deadline',
        'project_start_date',
        'expected_completion_date',
        'estimated_duration_months',
        'project_milestones',
        'documents',
        'images',
        'technical_reports',
        'has_permits',
        'permits_status',
        'is_technically_validated',
        'technical_validator_id',
        'technical_validation_date',
        'status',
        'status_notes',
        'reviewed_by',
        'reviewed_at',
        'views_count',
        'likes_count',
        'comments_count',
        'shares_count',
        'bookmarks_count',
        'engagement_score',
        'is_public',
        'is_featured',
        'allow_comments',
        'allow_investments',
        'notify_updates',
    ];

    protected $casts = [
        'objectives' => 'array',
        'benefits' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'estimated_power_kw' => 'decimal:2',
        'estimated_annual_production_kwh' => 'decimal:2',
        'technical_specifications' => 'array',
        'total_investment_required' => 'decimal:2',
        'investment_raised' => 'decimal:2',
        'min_investment_per_participant' => 'decimal:2',
        'max_investment_per_participant' => 'decimal:2',
        'estimated_roi_percentage' => 'decimal:2',
        'estimated_annual_savings' => 'decimal:2',
        'financial_projections' => 'array',
        'funding_deadline' => 'date',
        'project_start_date' => 'date',
        'expected_completion_date' => 'date',
        'project_milestones' => 'array',
        'documents' => 'array',
        'images' => 'array',
        'technical_reports' => 'array',
        'permits_status' => 'array',
        'has_permits' => 'boolean',
        'is_technically_validated' => 'boolean',
        'technical_validation_date' => 'datetime',
        'reviewed_at' => 'datetime',
        'engagement_score' => 'decimal:2',
        'is_public' => 'boolean',
        'is_featured' => 'boolean',
        'allow_comments' => 'boolean',
        'allow_investments' => 'boolean',
        'notify_updates' => 'boolean',
    ];

    /**
     * Usuario que propone el proyecto.
     */
    public function proposer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'proposer_id');
    }

    /**
     * Cooperativa asociada al proyecto.
     */
    public function cooperative(): BelongsTo
    {
        return $this->belongsTo(Cooperative::class);
    }

    /**
     * Municipio donde se ubicará el proyecto.
     */
    public function municipality(): BelongsTo
    {
        return $this->belongsTo(Municipality::class);
    }

    /**
     * Usuario que validó técnicamente el proyecto.
     */
    public function technicalValidator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technical_validator_id');
    }

    /**
     * Usuario que revisó el proyecto.
     */
    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Inversiones en el proyecto.
     */
    public function investments(): HasMany
    {
        return $this->hasMany(ProjectInvestment::class);
    }

    /**
     * Actualizaciones del proyecto.
     */
    public function updates(): HasMany
    {
        return $this->hasMany(ProjectUpdate::class);
    }

    /**
     * Derechos de producción asociados.
     */
    public function productionRights(): HasMany
    {
        return $this->hasMany(ProductionRight::class);
    }

    /**
     * Boot del modelo.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($project) {
            if (empty($project->slug)) {
                $project->slug = static::generateUniqueSlug($project->title);
            }
        });
    }

    /**
     * Generar slug único.
     */
    public static function generateUniqueSlug(string $title): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Verificar si el proyecto está abierto a inversiones.
     */
    public function isOpenForInvestment(): bool
    {
        return $this->status === 'funding' &&
               $this->allow_investments &&
               $this->funding_deadline >= now()->toDateString() &&
               $this->investment_raised < $this->total_investment_required;
    }

    /**
     * Calcular porcentaje de financiación.
     */
    public function getFundingPercentage(): float
    {
        if ($this->total_investment_required <= 0) {
            return 0;
        }

        return round(($this->investment_raised / $this->total_investment_required) * 100, 2);
    }

    /**
     * Obtener inversión restante necesaria.
     */
    public function getRemainingInvestment(): float
    {
        return max(0, $this->total_investment_required - $this->investment_raised);
    }

    /**
     * Verificar si el proyecto está completamente financiado.
     */
    public function isFullyFunded(): bool
    {
        return $this->investment_raised >= $this->total_investment_required;
    }

    /**
     * Añadir nueva inversión.
     */
    public function addInvestment(User $investor, float $amount, array $details = []): ProjectInvestment
    {
        $investment = $this->investments()->create([
            'investor_id' => $investor->id,
            'investment_amount' => $amount,
            'investment_type' => $details['type'] ?? 'monetary',
            'investment_details' => $details['details'] ?? null,
            'investment_description' => $details['description'] ?? null,
            'expected_return_percentage' => $details['return_percentage'] ?? null,
            'investment_term_years' => $details['term_years'] ?? null,
            'return_frequency' => $details['return_frequency'] ?? null,
        ]);

        // Actualizar contadores del proyecto
        $this->increment('investment_raised', $amount);
        $this->increment('current_participants');
        $this->updateEngagementScore();

        // Cambiar estado si está completamente financiado
        if ($this->isFullyFunded()) {
            $this->update(['status' => 'funded']);
        }

        return $investment;
    }

    /**
     * Crear actualización del proyecto.
     */
    public function addUpdate(User $author, array $data): ProjectUpdate
    {
        return $this->updates()->create(array_merge($data, [
            'author_id' => $author->id,
        ]));
    }

    /**
     * Incrementar vistas.
     */
    public function incrementViews(): void
    {
        $this->increment('views_count');
        $this->updateEngagementScore();
    }

    /**
     * Incrementar likes.
     */
    public function incrementLikes(): void
    {
        $this->increment('likes_count');
        $this->updateEngagementScore();
    }

    /**
     * Actualizar score de engagement.
     */
    public function updateEngagementScore(): void
    {
        $score = ($this->views_count * 0.1) +
                ($this->likes_count * 2) +
                ($this->comments_count * 3) +
                ($this->shares_count * 5) +
                ($this->bookmarks_count * 4) +
                ($this->current_participants * 10) +
                ($this->getFundingPercentage() * 0.5);

        $this->update(['engagement_score' => $score]);
    }

    /**
     * Validar técnicamente el proyecto.
     */
    public function validateTechnically(User $validator, array $validationData = []): void
    {
        $this->update([
            'is_technically_validated' => true,
            'technical_validator_id' => $validator->id,
            'technical_validation_date' => now(),
            'technical_specifications' => array_merge(
                $this->technical_specifications ?? [],
                $validationData
            ),
        ]);
    }

    /**
     * Aprobar proyecto.
     */
    public function approve(User $reviewer, string $notes = null): void
    {
        $this->update([
            'status' => 'approved',
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'status_notes' => $notes,
        ]);
    }

    /**
     * Rechazar proyecto.
     */
    public function reject(User $reviewer, string $notes): void
    {
        $this->update([
            'status' => 'rejected',
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'status_notes' => $notes,
        ]);
    }

    /**
     * Abrir proyecto para financiación.
     */
    public function openForFunding(): void
    {
        $this->update([
            'status' => 'funding',
            'allow_investments' => true,
        ]);
    }

    /**
     * Iniciar ejecución del proyecto.
     */
    public function startExecution(): void
    {
        $this->update([
            'status' => 'in_progress',
            'project_start_date' => now()->toDateString(),
            'allow_investments' => false,
        ]);
    }

    /**
     * Completar proyecto.
     */
    public function complete(): void
    {
        $this->update([
            'status' => 'completed',
            'expected_completion_date' => now()->toDateString(),
        ]);
    }

    /**
     * Obtener proyectos destacados.
     */
    public static function getFeatured(int $limit = 10)
    {
        return static::where('is_featured', true)
                    ->where('is_public', true)
                    ->where('status', 'funding')
                    ->orderBy('engagement_score', 'desc')
                    ->limit($limit)
                    ->get();
    }

    /**
     * Obtener proyectos por estado.
     */
    public static function getByStatus(string $status, int $limit = 20)
    {
        return static::where('status', $status)
                    ->where('is_public', true)
                    ->orderBy('created_at', 'desc')
                    ->limit($limit)
                    ->get();
    }

    /**
     * Buscar proyectos.
     */
    public static function search(string $term, array $filters = [], int $limit = 20)
    {
        $query = static::where('is_public', true)
                      ->where(function ($q) use ($term) {
                          $q->where('title', 'like', "%{$term}%")
                            ->orWhere('description', 'like', "%{$term}%")
                            ->orWhere('summary', 'like', "%{$term}%");
                      });

        if (isset($filters['project_type'])) {
            $query->where('project_type', $filters['project_type']);
        }

        if (isset($filters['scale'])) {
            $query->where('scale', $filters['scale']);
        }

        if (isset($filters['municipality_id'])) {
            $query->where('municipality_id', $filters['municipality_id']);
        }

        if (isset($filters['min_investment'])) {
            $query->where('min_investment_per_participant', '>=', $filters['min_investment']);
        }

        if (isset($filters['max_investment'])) {
            $query->where('max_investment_per_participant', '<=', $filters['max_investment']);
        }

        return $query->orderBy('engagement_score', 'desc')
                    ->limit($limit)
                    ->get();
    }

    /**
     * Obtener proyectos cercanos a una ubicación.
     */
    public static function getNearby(float $latitude, float $longitude, float $radiusKm = 50, int $limit = 10)
    {
        // Cálculo básico de distancia usando fórmula haversine simplificada
        $latRange = $radiusKm / 111; // Aproximadamente 111 km por grado de latitud
        $lngRange = $radiusKm / (111 * cos(deg2rad($latitude)));

        return static::where('is_public', true)
                    ->where('status', 'funding')
                    ->whereBetween('latitude', [$latitude - $latRange, $latitude + $latRange])
                    ->whereBetween('longitude', [$longitude - $lngRange, $longitude + $lngRange])
                    ->orderBy('engagement_score', 'desc')
                    ->limit($limit)
                    ->get();
    }
}
