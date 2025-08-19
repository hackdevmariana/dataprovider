<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Builder;

class TopicComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic_post_id', 'user_id', 'parent_id', 'body', 'excerpt',
        'depth', 'thread_path', 'sort_order', 'children_count',
        'comment_type', 'is_best_answer', 'is_pinned',
        'upvotes_count', 'downvotes_count', 'score', 'replies_count',
        'quality_score', 'status', 'images', 'attachments', 'links',
        'mentioned_users', 'tags', 'last_activity_at'
    ];

    protected $casts = [
        'images' => 'array',
        'attachments' => 'array',
        'links' => 'array',
        'mentioned_users' => 'array',
        'tags' => 'array',
        'quality_score' => 'decimal:2',
        'is_best_answer' => 'boolean',
        'is_pinned' => 'boolean',
        'last_activity_at' => 'datetime',
    ];

    // Relaciones básicas
    public function topicPost(): BelongsTo
    {
        return $this->belongsTo(TopicPost::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(TopicComment::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(TopicComment::class, 'parent_id');
    }

    public function socialInteractions(): MorphMany
    {
        return $this->morphMany(SocialInteraction::class, 'interactable');
    }

    // Scopes principales
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopeRootComments(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    public function scopeBestAnswers(Builder $query): Builder
    {
        return $query->where('is_best_answer', true);
    }

    public function scopeOrderByScore(Builder $query): Builder
    {
        return $query->orderByDesc('score')->orderBy('created_at');
    }

    // Métodos auxiliares
    public function calculateScore(): int
    {
        return $this->upvotes_count - $this->downvotes_count;
    }

    public function updateScore(): void
    {
        $this->update(['score' => $this->calculateScore()]);
    }

    public function markAsBestAnswer(): void
    {
        // Desmarcar otras respuestas como mejor respuesta
        $this->topicPost->comments()
             ->where('is_best_answer', true)
             ->update(['is_best_answer' => false]);
        
        $this->update(['is_best_answer' => true]);
    }

    public function canBeViewedBy(?User $user = null): bool
    {
        if ($this->status !== 'published') {
            return $user && ($this->user_id === $user->id || $this->topicPost->topic->isModerator($user));
        }
        return $this->topicPost->canBeViewedBy($user);
    }

    // Eventos del modelo
    protected static function booted()
    {
        static::created(function (TopicComment $comment) {
            if ($comment->status === 'published') {
                $comment->topicPost->increment('comments_count');
                $comment->topicPost->update(['last_comment_at' => $comment->created_at]);
                
                if ($comment->parent_id) {
                    $comment->parent->increment('children_count');
                }
            }
        });
    }
}