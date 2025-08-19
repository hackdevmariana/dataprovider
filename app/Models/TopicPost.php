<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class TopicPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic_id', 'user_id', 'title', 'slug', 'body', 'excerpt', 'summary',
        'post_type', 'is_pinned', 'is_locked', 'is_featured', 'is_announcement',
        'allow_comments', 'images', 'videos', 'attachments', 'links',
        'views_count', 'upvotes_count', 'downvotes_count', 'score', 'comments_count',
        'quality_score', 'trending_score', 'hot_score', 'status', 'tags',
        'latitude', 'longitude', 'location_name', 'last_activity_at'
    ];

    protected $casts = [
        'images' => 'array',
        'videos' => 'array', 
        'attachments' => 'array',
        'links' => 'array',
        'tags' => 'array',
        'quality_score' => 'decimal:2',
        'trending_score' => 'decimal:2',
        'hot_score' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_pinned' => 'boolean',
        'is_locked' => 'boolean',
        'is_featured' => 'boolean',
        'is_announcement' => 'boolean',
        'allow_comments' => 'boolean',
        'last_activity_at' => 'datetime',
    ];

    // Relaciones básicas
    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TopicComment::class);
    }

    public function publishedComments(): HasMany
    {
        return $this->comments()->where('status', 'published');
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

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeTrending(Builder $query): Builder
    {
        return $query->orderByDesc('trending_score');
    }

    public function scopeHot(Builder $query): Builder
    {
        return $query->orderByDesc('hot_score')->orderByDesc('score');
    }

    // Métodos auxiliares principales
    public static function generateUniqueSlug(string $title, int $topicId): string
    {
        $slug = Str::slug($title);
        $count = 0;
        $originalSlug = $slug;
        
        while (static::where('slug', $slug)->where('topic_id', $topicId)->exists()) {
            $count++;
            $slug = $originalSlug . '-' . $count;
        }
        
        return $slug;
    }

    public function canBeViewedBy(?User $user = null): bool
    {
        if ($this->status !== 'published') {
            return $user && ($this->user_id === $user->id || $this->topic->isModerator($user));
        }
        return $this->topic->canBeViewedBy($user);
    }

    public function incrementViews(?User $user = null): void
    {
        $this->increment('views_count');
        $this->touch('last_activity_at');
    }

    public function calculateScore(): int
    {
        return $this->upvotes_count - $this->downvotes_count;
    }

    public function updateScore(): void
    {
        $this->update(['score' => $this->calculateScore()]);
    }

    // Eventos del modelo
    protected static function booted()
    {
        static::creating(function (TopicPost $post) {
            if (empty($post->slug)) {
                $post->slug = static::generateUniqueSlug($post->title, $post->topic_id);
            }
        });

        static::created(function (TopicPost $post) {
            if ($post->status === 'published') {
                $post->topic->increment('posts_count');
            }
        });
    }
}