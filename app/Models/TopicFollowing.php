<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TopicFollowing extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'topic_id',
        'follow_type',
        'notifications_enabled',
        'notification_preferences',
        'followed_at',
        'last_visited_at',
        'visit_count',
    ];

    protected $casts = [
        'notification_preferences' => 'array',
        'notifications_enabled' => 'boolean',
        'followed_at' => 'datetime',
        'last_visited_at' => 'datetime',
    ];

    /**
     * Get the user following the topic
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the topic being followed
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    /**
     * Update visit information
     */
    public function recordVisit(): void
    {
        $this->increment('visit_count');
        $this->update(['last_visited_at' => now()]);
    }

    /**
     * Check if user wants notifications for specific event
     */
    public function wantsNotificationFor(string $eventType): bool
    {
        if (!$this->notifications_enabled) {
            return false;
        }

        $preferences = $this->notification_preferences ?? [];
        return $preferences[$eventType] ?? true;
    }

    /**
     * Scope by follow type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('follow_type', $type);
    }

    /**
     * Scope for active followers (following or watching)
     */
    public function scopeActive($query)
    {
        return $query->whereIn('follow_type', ['following', 'watching']);
    }
}