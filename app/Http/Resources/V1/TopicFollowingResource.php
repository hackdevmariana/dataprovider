<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TopicFollowingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
            'topic' => [
                'id' => $this->topic->id,
                'name' => $this->topic->name,
                'slug' => $this->topic->slug,
                'description' => $this->topic->description,
                'is_active' => $this->topic->is_active,
            ],
            'follow_type' => $this->follow_type,
            'follow_type_label' => $this->getFollowTypeLabel(),
            'notifications_enabled' => $this->notifications_enabled,
            'notification_preferences' => $this->notification_preferences ?? [],
            'visit_count' => $this->visit_count ?? 0,
            'followed_at' => $this->followed_at,
            'last_visited_at' => $this->last_visited_at,
            'unread_posts_count' => $this->unread_posts_count ?? 0,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Get human-readable follow type label
     */
    private function getFollowTypeLabel(): string
    {
        return match ($this->follow_type) {
            'following' => 'Siguiendo',
            'watching' => 'Vigilando',
            'ignoring' => 'Ignorando',
            default => ucfirst($this->follow_type),
        };
    }
}