<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Sistema de reputación de usuario estilo StackOverflow.
 * 
 * Gestiona la reputación global y por categorías de cada usuario,
 * incluyendo métricas de contribución, engagement y liderazgo.
 * 
 * @property int $id
 * @property int $user_id Usuario
 * @property int $total_reputation Reputación total
 * @property array|null $category_reputation Reputación por categoría
 * @property array|null $topic_reputation Reputación por tema
 * @property int $helpful_answers Respuestas útiles
 * @property int $accepted_solutions Soluciones aceptadas
 * @property int $quality_posts Posts de calidad
 * @property int $verified_contributions Contribuciones verificadas
 * @property int $upvotes_received Upvotes recibidos
 * @property int $downvotes_received Downvotes recibidos
 * @property float $upvote_ratio Ratio de upvotes
 * @property int $topics_created Temas creados
 * @property int $successful_projects Proyectos exitosos
 * @property int $mentorship_points Puntos de mentoría
 * @property int $warnings_received Advertencias recibidas
 * @property int $content_removed Contenido eliminado
 * @property bool $is_suspended Si está suspendido
 * @property \Carbon\Carbon|null $suspended_until Hasta cuándo suspendido
 * @property int|null $global_rank Ranking global
 * @property array|null $category_ranks Rankings por categoría
 * @property int|null $monthly_rank Ranking mensual
 * @property bool $is_verified_professional Si es profesional verificado
 * @property array|null $professional_credentials Credenciales profesionales
 * @property array|null $expertise_areas Áreas de expertise
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * 
 * @property-read \App\Models\User $user Usuario
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ReputationTransaction[] $transactions Transacciones
 */
class UserReputation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_reputation',
        'category_reputation',
        'topic_reputation',
        'helpful_answers',
        'accepted_solutions',
        'quality_posts',
        'verified_contributions',
        'upvotes_received',
        'downvotes_received',
        'upvote_ratio',
        'topics_created',
        'successful_projects',
        'mentorship_points',
        'warnings_received',
        'content_removed',
        'is_suspended',
        'suspended_until',
        'global_rank',
        'category_ranks',
        'monthly_rank',
        'is_verified_professional',
        'professional_credentials',
        'expertise_areas',
    ];

    protected $casts = [
        'category_reputation' => 'array',
        'topic_reputation' => 'array',
        'upvote_ratio' => 'decimal:2',
        'is_suspended' => 'boolean',
        'suspended_until' => 'datetime',
        'category_ranks' => 'array',
        'is_verified_professional' => 'boolean',
        'professional_credentials' => 'array',
        'expertise_areas' => 'array',
    ];

    /**
     * Usuario propietario de la reputación.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Transacciones de reputación.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(ReputationTransaction::class, 'user_id', 'user_id');
    }

    /**
     * Añadir reputación por una acción específica.
     */
    public function addReputation(
        string $actionType, 
        int $points, 
        ?string $category = null,
        ?int $topicId = null,
        ?Model $relatedModel = null,
        ?User $triggeredBy = null,
        ?string $description = null
    ): ReputationTransaction {
        // Crear transacción
        $transaction = ReputationTransaction::create([
            'user_id' => $this->user_id,
            'action_type' => $actionType,
            'reputation_change' => $points,
            'category' => $category,
            'topic_id' => $topicId,
            'related_type' => $relatedModel ? get_class($relatedModel) : null,
            'related_id' => $relatedModel?->id,
            'triggered_by' => $triggeredBy?->id,
            'description' => $description,
        ]);

        // Actualizar reputación total
        $this->increment('total_reputation', $points);

        // Actualizar reputación por categoría si aplica
        if ($category) {
            $categoryRep = $this->category_reputation ?? [];
            $categoryRep[$category] = ($categoryRep[$category] ?? 0) + $points;
            $this->update(['category_reputation' => $categoryRep]);
        }

        // Actualizar reputación por tema si aplica
        if ($topicId) {
            $topicRep = $this->topic_reputation ?? [];
            $topicRep[$topicId] = ($topicRep[$topicId] ?? 0) + $points;
            $this->update(['topic_reputation' => $topicRep]);
        }

        // Actualizar métricas específicas según el tipo de acción
        $this->updateMetricsForAction($actionType, $points > 0);

        // Recalcular ranking
        $this->updateRanking();

        return $transaction;
    }

    /**
     * Actualizar métricas específicas según el tipo de acción.
     */
    protected function updateMetricsForAction(string $actionType, bool $isPositive): void
    {
        switch ($actionType) {
            case 'answer_accepted':
                if ($isPositive) $this->increment('accepted_solutions');
                break;
            case 'answer_upvoted':
            case 'question_upvoted':
                if ($isPositive) $this->increment('upvotes_received');
                break;
            case 'answer_downvoted':
            case 'question_downvoted':
                if (!$isPositive) $this->increment('downvotes_received');
                break;
            case 'helpful_comment':
                if ($isPositive) $this->increment('helpful_answers');
                break;
            case 'tutorial_featured':
                if ($isPositive) $this->increment('quality_posts');
                break;
            case 'project_completed':
                if ($isPositive) $this->increment('successful_projects');
                break;
            case 'expert_verification':
                if ($isPositive) $this->update(['is_verified_professional' => true]);
                break;
            case 'rule_violation':
            case 'spam_detected':
                if (!$isPositive) $this->increment('warnings_received');
                break;
            case 'answer_deleted':
                if (!$isPositive) $this->increment('content_removed');
                break;
        }

        // Actualizar ratio de upvotes
        $this->updateUpvoteRatio();
    }

    /**
     * Actualizar ratio de upvotes vs downvotes.
     */
    public function updateUpvoteRatio(): void
    {
        $total = $this->upvotes_received + $this->downvotes_received;
        $ratio = $total > 0 ? ($this->upvotes_received / $total) * 100 : 0;
        $this->update(['upvote_ratio' => $ratio]);
    }

    /**
     * Actualizar ranking global del usuario.
     */
    public function updateRanking(): void
    {
        $rank = UserReputation::where('total_reputation', '>', $this->total_reputation)->count() + 1;
        $this->update(['global_rank' => $rank]);
    }

    /**
     * Obtener nivel de reputación del usuario.
     */
    public function getReputationLevel(): string
    {
        if ($this->total_reputation >= 10000) return 'expert';
        if ($this->total_reputation >= 5000) return 'leader';
        if ($this->total_reputation >= 1000) return 'advanced';
        if ($this->total_reputation >= 500) return 'intermediate';
        if ($this->total_reputation >= 100) return 'contributor';
        
        return 'novice';
    }

    /**
     * Verificar si el usuario puede realizar una acción específica.
     */
    public function canPerformAction(string $action): bool
    {
        // Definir requisitos de reputación para diferentes acciones
        $requirements = [
            'vote_up' => 15,
            'vote_down' => 125,
            'comment' => 50,
            'create_topic' => 500,
            'edit_posts' => 2000,
            'close_questions' => 3000,
            'moderate' => 5000,
        ];

        return $this->total_reputation >= ($requirements[$action] ?? 0);
    }

    /**
     * Obtener privilegios del usuario basados en reputación.
     */
    public function getPrivileges(): array
    {
        $privileges = [];
        
        if ($this->total_reputation >= 15) $privileges[] = 'vote_up';
        if ($this->total_reputation >= 50) $privileges[] = 'comment';
        if ($this->total_reputation >= 125) $privileges[] = 'vote_down';
        if ($this->total_reputation >= 500) $privileges[] = 'create_topic';
        if ($this->total_reputation >= 1000) $privileges[] = 'edit_wiki_posts';
        if ($this->total_reputation >= 2000) $privileges[] = 'edit_others_posts';
        if ($this->total_reputation >= 3000) $privileges[] = 'close_questions';
        if ($this->total_reputation >= 5000) $privileges[] = 'moderate_content';
        if ($this->total_reputation >= 10000) $privileges[] = 'access_moderation_tools';

        return $privileges;
    }

    /**
     * Suspender usuario por tiempo determinado.
     */
    public function suspend(\DateTimeInterface $until, string $reason): void
    {
        $this->update([
            'is_suspended' => true,
            'suspended_until' => $until,
        ]);

        // Crear transacción de penalización
        $this->addReputation('rule_violation', -50, null, null, null, null, $reason);
    }

    /**
     * Levantar suspensión del usuario.
     */
    public function unsuspend(): void
    {
        $this->update([
            'is_suspended' => false,
            'suspended_until' => null,
        ]);
    }

    /**
     * Verificar si el usuario está suspendido actualmente.
     */
    public function isSuspended(): bool
    {
        if (!$this->is_suspended) return false;
        
        if ($this->suspended_until && $this->suspended_until->isPast()) {
            $this->unsuspend();
            return false;
        }

        return true;
    }

    /**
     * Obtener reputación en una categoría específica.
     */
    public function getCategoryReputation(string $category): int
    {
        return $this->category_reputation[$category] ?? 0;
    }

    /**
     * Obtener reputación en un tema específico.
     */
    public function getTopicReputation(int $topicId): int
    {
        return $this->topic_reputation[$topicId] ?? 0;
    }

    /**
     * Obtener top categorías por reputación.
     */
    public function getTopCategories(int $limit = 5): array
    {
        if (!$this->category_reputation) return [];
        
        arsort($this->category_reputation);
        return array_slice($this->category_reputation, 0, $limit, true);
    }
}

