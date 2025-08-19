<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CooperativePost extends Model
{
    use HasFactory;

    protected $fillable = [
        'cooperative_id',
        'author_id',
        'title',
        'content',
        'post_type',
        'status',
        'visibility',
        'attachments',
        'metadata',
        'comments_enabled',
        'is_pinned',
        'is_featured',
        'views_count',
        'likes_count',
        'comments_count',
        'published_at',
        'pinned_until',
    ];

    protected $casts = [
        'attachments' => 'array',
        'metadata' => 'array',
        'comments_enabled' => 'boolean',
        'is_pinned' => 'boolean',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
        'pinned_until' => 'datetime',
    ];

    /**
     * Get the cooperative this post belongs to
     */
    public function cooperative(): BelongsTo
    {
        return $this->belongsTo(Cooperative::class);
    }

    /**
     * Get the author of this post
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Publish the post
     */
    public function publish(): void
    {
        $this->update([
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    /**
     * Pin the post
     */
    public function pin(\DateTime $until = null): void
    {
        $this->update([
            'is_pinned' => true,
            'pinned_until' => $until,
        ]);
    }

    /**
     * Unpin the post
     */
    public function unpin(): void
    {
        $this->update([
            'is_pinned' => false,
            'pinned_until' => null,
        ]);
    }

    /**
     * Increment view count
     */
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    /**
     * Check if post is currently pinned
     */
    public function isPinned(): bool
    {
        return $this->is_pinned && 
               (!$this->pinned_until || $this->pinned_until->isFuture());
    }

    /**
     * Scope for published posts
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->whereNotNull('published_at');
    }

    /**
     * Scope for public posts
     */
    public function scopePublic($query)
    {
        return $query->where('visibility', 'public');
    }

    /**
     * Scope for pinned posts
     */
    public function scopePinned($query)
    {
        return $query->where('is_pinned', true)
                    ->where(function ($q) {
                        $q->whereNull('pinned_until')
                          ->orWhere('pinned_until', '>', now());
                    });
    }

    /**
     * Scope by post type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('post_type', $type);
    }
}