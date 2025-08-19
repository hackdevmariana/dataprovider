<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Sistema de reviews y ratings estilo Google/Quora.
 * 
 * Permite a los usuarios hacer reviews detalladas de servicios,
 * productos y experiencias con verificación y respuestas del proveedor.
 */
class UserReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'reviewer_id',
        'reviewable_type',
        'reviewable_id',
        'overall_rating',
        'detailed_ratings',
        'title',
        'content',
        'pros',
        'cons',
        'images',
        'attachments',
        'service_type',
        'service_date',
        'service_cost',
        'service_location',
        'service_duration_days',
        'is_verified_purchase',
        'verification_code',
        'verified_at',
        'verified_by',
        'would_recommend',
        'recommendation_level',
        'helpful_votes',
        'not_helpful_votes',
        'total_votes',
        'helpfulness_ratio',
        'views_count',
        'provider_response',
        'provider_responded_at',
        'provider_responder_id',
        'status',
        'flags_count',
        'flag_reasons',
        'moderated_by',
        'moderated_at',
        'moderation_notes',
        'is_anonymous',
        'show_service_cost',
        'allow_contact',
    ];

    protected $casts = [
        'overall_rating' => 'decimal:1',
        'detailed_ratings' => 'array',
        'pros' => 'array',
        'cons' => 'array',
        'images' => 'array',
        'attachments' => 'array',
        'service_date' => 'date',
        'service_cost' => 'decimal:2',
        'is_verified_purchase' => 'boolean',
        'verified_at' => 'datetime',
        'would_recommend' => 'boolean',
        'helpfulness_ratio' => 'decimal:2',
        'provider_responded_at' => 'datetime',
        'flag_reasons' => 'array',
        'moderated_at' => 'datetime',
        'is_anonymous' => 'boolean',
        'show_service_cost' => 'boolean',
        'allow_contact' => 'boolean',
    ];

    /**
     * Usuario que hace la review.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    /**
     * Entidad reviewada (polimórfico).
     */
    public function reviewable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Usuario que verificó la review.
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Usuario del proveedor que respondió.
     */
    public function providerResponder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provider_responder_id');
    }

    /**
     * Usuario que moderó la review.
     */
    public function moderatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    /**
     * Obtener reviews de una entidad.
     */
    public static function getForEntity(Model $entity, array $filters = [])
    {
        $query = static::where('reviewable_type', get_class($entity))
                      ->where('reviewable_id', $entity->id)
                      ->where('status', 'published');

        if (isset($filters['service_type'])) {
            $query->where('service_type', $filters['service_type']);
        }

        if (isset($filters['verified_only']) && $filters['verified_only']) {
            $query->where('is_verified_purchase', true);
        }

        if (isset($filters['min_rating'])) {
            $query->where('overall_rating', '>=', $filters['min_rating']);
        }

        if (isset($filters['max_rating'])) {
            $query->where('overall_rating', '<=', $filters['max_rating']);
        }

        $sortBy = $filters['sort_by'] ?? 'helpful';
        
        switch ($sortBy) {
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'rating_high':
                $query->orderBy('overall_rating', 'desc');
                break;
            case 'rating_low':
                $query->orderBy('overall_rating', 'asc');
                break;
            case 'helpful':
            default:
                $query->orderBy('helpful_votes', 'desc')
                      ->orderBy('helpfulness_ratio', 'desc');
                break;
        }

        return $query->with(['reviewer', 'providerResponder'])
                    ->paginate($filters['per_page'] ?? 10);
    }

    /**
     * Obtener estadísticas de reviews para una entidad.
     */
    public static function getStatsForEntity(Model $entity): array
    {
        $reviews = static::where('reviewable_type', get_class($entity))
                        ->where('reviewable_id', $entity->id)
                        ->where('status', 'published');

        $total = $reviews->count();
        $averageRating = $reviews->avg('overall_rating') ?? 0;
        
        $ratingDistribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $count = $reviews->where('overall_rating', '>=', $i)
                           ->where('overall_rating', '<', $i + 1)
                           ->count();
            $ratingDistribution[$i] = [
                'count' => $count,
                'percentage' => $total > 0 ? round(($count / $total) * 100, 1) : 0,
            ];
        }

        $verifiedCount = $reviews->where('is_verified_purchase', true)->count();
        $recommendationCount = $reviews->where('would_recommend', true)->count();

        return [
            'total_reviews' => $total,
            'average_rating' => round($averageRating, 1),
            'rating_distribution' => $ratingDistribution,
            'verified_reviews' => $verifiedCount,
            'verified_percentage' => $total > 0 ? round(($verifiedCount / $total) * 100, 1) : 0,
            'recommendation_count' => $recommendationCount,
            'recommendation_percentage' => $total > 0 ? round(($recommendationCount / $total) * 100, 1) : 0,
        ];
    }

    /**
     * Votar como útil.
     */
    public function voteHelpful(): void
    {
        $this->increment('helpful_votes');
        $this->increment('total_votes');
        $this->updateHelpfulnessRatio();
    }

    /**
     * Votar como no útil.
     */
    public function voteNotHelpful(): void
    {
        $this->increment('not_helpful_votes');
        $this->increment('total_votes');
        $this->updateHelpfulnessRatio();
    }

    /**
     * Actualizar ratio de utilidad.
     */
    public function updateHelpfulnessRatio(): void
    {
        if ($this->total_votes === 0) {
            return;
        }

        $ratio = ($this->helpful_votes / $this->total_votes) * 100;
        $this->update(['helpfulness_ratio' => round($ratio, 2)]);
    }

    /**
     * Incrementar vistas.
     */
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    /**
     * Añadir respuesta del proveedor.
     */
    public function addProviderResponse(string $response, User $responder): void
    {
        $this->update([
            'provider_response' => $response,
            'provider_responded_at' => now(),
            'provider_responder_id' => $responder->id,
        ]);
    }

    /**
     * Reportar review.
     */
    public function flag(string $reason): void
    {
        $currentReasons = $this->flag_reasons ?? [];
        $currentReasons[] = [
            'reason' => $reason,
            'reported_at' => now()->toISOString(),
        ];

        $this->update([
            'flags_count' => $this->flags_count + 1,
            'flag_reasons' => $currentReasons,
            'status' => $this->flags_count >= 3 ? 'flagged' : $this->status,
        ]);
    }

    /**
     * Moderar review.
     */
    public function moderate(User $moderator, string $action, string $notes = null): void
    {
        $status = match($action) {
            'approve' => 'published',
            'hide' => 'hidden',
            'reject' => 'rejected',
            default => $this->status,
        };

        $this->update([
            'status' => $status,
            'moderated_by' => $moderator->id,
            'moderated_at' => now(),
            'moderation_notes' => $notes,
            'flags_count' => 0, // Reset flags after moderation
            'flag_reasons' => null,
        ]);
    }

    /**
     * Verificar compra/servicio.
     */
    public function verify(User $verifier, string $verificationCode = null): void
    {
        $this->update([
            'is_verified_purchase' => true,
            'verified_by' => $verifier->id,
            'verified_at' => now(),
            'verification_code' => $verificationCode,
        ]);
    }

    /**
     * Obtener reviews destacadas.
     */
    public static function getFeatured(int $limit = 10)
    {
        return static::where('status', 'published')
                    ->where('is_verified_purchase', true)
                    ->where('overall_rating', '>=', 4)
                    ->where('helpful_votes', '>=', 5)
                    ->orderBy('helpfulness_ratio', 'desc')
                    ->orderBy('helpful_votes', 'desc')
                    ->with(['reviewer', 'reviewable'])
                    ->limit($limit)
                    ->get();
    }

    /**
     * Obtener reviews recientes.
     */
    public static function getRecent(int $limit = 20)
    {
        return static::where('status', 'published')
                    ->orderBy('created_at', 'desc')
                    ->with(['reviewer', 'reviewable'])
                    ->limit($limit)
                    ->get();
    }

    /**
     * Buscar reviews por término.
     */
    public static function search(string $term, int $limit = 20)
    {
        return static::where('status', 'published')
                    ->where(function ($query) use ($term) {
                        $query->where('title', 'like', "%{$term}%")
                              ->orWhere('content', 'like', "%{$term}%");
                    })
                    ->orderBy('helpfulness_ratio', 'desc')
                    ->with(['reviewer', 'reviewable'])
                    ->limit($limit)
                    ->get();
    }
}
