<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Sistema de endorsements/validaciones de habilidades estilo LinkedIn.
 * 
 * Permite a los usuarios validar las habilidades y conocimientos
 * de otros usuarios con contexto y métricas de confianza.
 */
class UserEndorsement extends Model
{
    use HasFactory;

    protected $fillable = [
        'endorser_id',
        'endorsed_id',
        'skill_category',
        'specific_skill',
        'endorsement_text',
        'skill_rating',
        'relationship_context',
        'project_context',
        'collaboration_duration_months',
        'is_verified',
        'trust_score',
        'helpful_votes',
        'total_votes',
        'is_public',
        'show_on_profile',
        'notify_endorsed',
        'is_mutual',
        'reciprocal_endorsement_id',
        'status',
        'disputed_by',
        'dispute_reason',
        'disputed_at',
    ];

    protected $casts = [
        'skill_rating' => 'decimal:1',
        'trust_score' => 'decimal:2',
        'is_verified' => 'boolean',
        'is_public' => 'boolean',
        'show_on_profile' => 'boolean',
        'notify_endorsed' => 'boolean',
        'is_mutual' => 'boolean',
        'disputed_at' => 'datetime',
    ];

    /**
     * Usuario que hace el endorsement.
     */
    public function endorser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'endorser_id');
    }

    /**
     * Usuario que recibe el endorsement.
     */
    public function endorsed(): BelongsTo
    {
        return $this->belongsTo(User::class, 'endorsed_id');
    }

    /**
     * Usuario que disputa el endorsement.
     */
    public function disputedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disputed_by');
    }

    /**
     * Endorsement recíproco.
     */
    public function reciprocal(): BelongsTo
    {
        return $this->belongsTo(static::class, 'reciprocal_endorsement_id');
    }

    /**
     * Obtener endorsements de un usuario por categoría.
     */
    public static function getForUserByCategory(User $user, string $category)
    {
        return static::where('endorsed_id', $user->id)
                    ->where('skill_category', $category)
                    ->where('status', 'active')
                    ->where('is_public', true)
                    ->with('endorser')
                    ->orderBy('skill_rating', 'desc')
                    ->orderBy('helpful_votes', 'desc')
                    ->get();
    }

    /**
     * Obtener resumen de habilidades de un usuario.
     */
    public static function getSkillsSummary(User $user): array
    {
        $endorsements = static::where('endorsed_id', $user->id)
                             ->where('status', 'active')
                             ->where('is_public', true)
                             ->get();

        $summary = [];

        foreach ($endorsements as $endorsement) {
            $category = $endorsement->skill_category;
            
            if (!isset($summary[$category])) {
                $summary[$category] = [
                    'count' => 0,
                    'average_rating' => 0,
                    'total_helpful_votes' => 0,
                    'top_endorsers' => [],
                ];
            }

            $summary[$category]['count']++;
            $summary[$category]['total_helpful_votes'] += $endorsement->helpful_votes;
            
            if ($endorsement->skill_rating) {
                $summary[$category]['ratings'][] = $endorsement->skill_rating;
            }

            $summary[$category]['top_endorsers'][] = [
                'endorser' => $endorsement->endorser,
                'rating' => $endorsement->skill_rating,
                'helpful_votes' => $endorsement->helpful_votes,
            ];
        }

        // Calcular promedios y ordenar
        foreach ($summary as $category => &$data) {
            if (!empty($data['ratings'])) {
                $data['average_rating'] = round(array_sum($data['ratings']) / count($data['ratings']), 1);
                unset($data['ratings']);
            }

            // Ordenar top endorsers por rating y votos útiles
            usort($data['top_endorsers'], function ($a, $b) {
                if ($a['rating'] !== $b['rating']) {
                    return $b['rating'] <=> $a['rating'];
                }
                return $b['helpful_votes'] <=> $a['helpful_votes'];
            });

            $data['top_endorsers'] = array_slice($data['top_endorsers'], 0, 5);
        }

        return $summary;
    }

    /**
     * Votar como útil.
     */
    public function voteHelpful(): void
    {
        $this->increment('helpful_votes');
        $this->increment('total_votes');
        $this->updateTrustScore();
    }

    /**
     * Votar como no útil.
     */
    public function voteNotHelpful(): void
    {
        $this->increment('total_votes');
        $this->updateTrustScore();
    }

    /**
     * Actualizar score de confianza.
     */
    public function updateTrustScore(): void
    {
        if ($this->total_votes === 0) {
            return;
        }

        $helpfulRatio = ($this->helpful_votes / $this->total_votes) * 100;
        $this->update(['trust_score' => round($helpfulRatio, 2)]);
    }

    /**
     * Crear endorsement mutuo.
     */
    public function createMutual(array $data): static
    {
        $mutual = static::create([
            'endorser_id' => $this->endorsed_id,
            'endorsed_id' => $this->endorser_id,
            'skill_category' => $data['skill_category'],
            'specific_skill' => $data['specific_skill'] ?? null,
            'endorsement_text' => $data['endorsement_text'] ?? null,
            'skill_rating' => $data['skill_rating'] ?? null,
            'relationship_context' => $this->relationship_context,
            'project_context' => $this->project_context,
            'collaboration_duration_months' => $this->collaboration_duration_months,
            'is_mutual' => true,
            'reciprocal_endorsement_id' => $this->id,
        ]);

        $this->update([
            'is_mutual' => true,
            'reciprocal_endorsement_id' => $mutual->id,
        ]);

        return $mutual;
    }

    /**
     * Disputar endorsement.
     */
    public function dispute(User $user, string $reason): void
    {
        $this->update([
            'status' => 'disputed',
            'disputed_by' => $user->id,
            'dispute_reason' => $reason,
            'disputed_at' => now(),
        ]);
    }

    /**
     * Resolver disputa.
     */
    public function resolveDispute(string $resolution): void
    {
        $status = $resolution === 'approve' ? 'active' : 'rejected';
        
        $this->update([
            'status' => $status,
            'disputed_by' => null,
            'dispute_reason' => null,
            'disputed_at' => null,
        ]);
    }

    /**
     * Obtener endorsements más valorados por categoría.
     */
    public static function getTopByCategory(string $category, int $limit = 10)
    {
        return static::where('skill_category', $category)
                    ->where('status', 'active')
                    ->where('is_public', true)
                    ->where('is_verified', true)
                    ->orderBy('skill_rating', 'desc')
                    ->orderBy('helpful_votes', 'desc')
                    ->with(['endorser', 'endorsed'])
                    ->limit($limit)
                    ->get();
    }

    /**
     * Verificar si ya existe endorsement.
     */
    public static function exists(User $endorser, User $endorsed, string $skillCategory, string $specificSkill = null): bool
    {
        $query = static::where('endorser_id', $endorser->id)
                      ->where('endorsed_id', $endorsed->id)
                      ->where('skill_category', $skillCategory);

        if ($specificSkill) {
            $query->where('specific_skill', $specificSkill);
        }

        return $query->exists();
    }
}
