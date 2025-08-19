<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Voto en contenido (upvote/downvote estilo Reddit/StackOverflow).
 * 
 * Sistema de votación para posts, comentarios, proyectos, etc.
 * con peso basado en reputación del votante.
 */
class ContentVote extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'votable_type',
        'votable_id',
        'vote_type',
        'vote_weight',
        'reason',
        'is_helpful_vote',
        'metadata',
        'is_valid',
        'validated_by',
    ];

    protected $casts = [
        'is_helpful_vote' => 'boolean',
        'metadata' => 'array',
        'is_valid' => 'boolean',
    ];

    /**
     * Usuario que emite el voto.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Contenido votado (polimórfico).
     */
    public function votable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Usuario que validó el voto.
     */
    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    /**
     * Procesar el voto y actualizar reputación.
     */
    public function processVote(): void
    {
        if (!$this->is_valid) return;

        // Actualizar contadores en el contenido votado
        if ($this->votable) {
            if ($this->vote_type === 'upvote') {
                $this->votable->incrementLikes();
                
                // Añadir reputación al autor del contenido
                if (method_exists($this->votable, 'author')) {
                    $author = $this->votable->author;
                    $points = $this->votable instanceof TopicPost ? 5 : 2;
                    
                    $author->reputation?->addReputation(
                        $this->votable instanceof TopicPost ? 'question_upvoted' : 'answer_upvoted',
                        $points,
                        $this->getContentCategory(),
                        $this->getTopicId(),
                        $this->votable,
                        $this->user
                    );
                }
            } else {
                $this->votable->decrementLikes();
                
                // Penalizar reputación del autor
                if (method_exists($this->votable, 'author')) {
                    $author = $this->votable->author;
                    $points = $this->votable instanceof TopicPost ? -2 : -1;
                    
                    $author->reputation?->addReputation(
                        $this->votable instanceof TopicPost ? 'question_downvoted' : 'answer_downvoted',
                        $points,
                        $this->getContentCategory(),
                        $this->getTopicId(),
                        $this->votable,
                        $this->user,
                        $this->reason
                    );
                }
            }
        }
    }

    /**
     * Obtener categoría del contenido para reputación.
     */
    protected function getContentCategory(): ?string
    {
        if ($this->votable instanceof TopicPost) {
            return $this->votable->topic->category;
        }
        
        if ($this->votable instanceof TopicComment) {
            return $this->votable->post->topic->category;
        }

        return null;
    }

    /**
     * Obtener ID del tema para reputación.
     */
    protected function getTopicId(): ?int
    {
        if ($this->votable instanceof TopicPost) {
            return $this->votable->topic_id;
        }
        
        if ($this->votable instanceof TopicComment) {
            return $this->votable->post->topic_id;
        }

        return null;
    }
}

