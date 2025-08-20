<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CooperativePostResource extends JsonResource
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
            'cooperative' => [
                'id' => $this->cooperative->id,
                'name' => $this->cooperative->name,
                'slug' => $this->cooperative->slug,
            ],
            'author' => [
                'id' => $this->author->id,
                'name' => $this->author->name,
            ],
            'title' => $this->title,
            'content' => $this->content,
            'excerpt' => $this->getExcerpt(),
            'post_type' => $this->post_type,
            'post_type_label' => $this->getPostTypeLabel(),
            'status' => $this->status,
            'status_label' => $this->getStatusLabel(),
            'visibility' => $this->visibility,
            'visibility_label' => $this->getVisibilityLabel(),
            'comments_enabled' => $this->comments_enabled,
            'is_pinned' => $this->is_pinned,
            'is_featured' => $this->is_featured,
            'views_count' => $this->views_count ?? 0,
            'comments_count' => $this->comments_count ?? 0,
            'likes_count' => $this->likes_count ?? 0,
            'attachments' => $this->attachments ?? [],
            'metadata' => $this->metadata ?? [],
            'published_at' => $this->published_at,
            'expires_at' => $this->expires_at,
            'pinned_until' => $this->pinned_until,
            'is_published' => $this->isPublished(),
            'is_draft' => $this->isDraft(),
            'is_archived' => $this->isArchived(),
            'is_expired' => $this->isExpired(),
            'is_currently_pinned' => $this->isCurrentlyPinned(),
            'reading_time_minutes' => $this->getReadingTimeMinutes(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Get post excerpt
     */
    private function getExcerpt(int $length = 150): string
    {
        $content = strip_tags($this->content);
        
        if (strlen($content) <= $length) {
            return $content;
        }

        return substr($content, 0, $length) . '...';
    }

    /**
     * Get human-readable post type label
     */
    private function getPostTypeLabel(): string
    {
        return match ($this->post_type) {
            'announcement' => 'Anuncio',
            'news' => 'Noticia',
            'event' => 'Evento',
            'discussion' => 'Discusión',
            'update' => 'Actualización',
            default => ucfirst($this->post_type),
        };
    }

    /**
     * Get human-readable status label
     */
    private function getStatusLabel(): string
    {
        return match ($this->status) {
            'draft' => 'Borrador',
            'published' => 'Publicado',
            'archived' => 'Archivado',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get human-readable visibility label
     */
    private function getVisibilityLabel(): string
    {
        return match ($this->visibility) {
            'public' => 'Público',
            'members_only' => 'Solo Miembros',
            'board_only' => 'Solo Junta Directiva',
            default => ucfirst($this->visibility),
        };
    }

    /**
     * Check if post is published
     */
    private function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /**
     * Check if post is draft
     */
    private function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Check if post is archived
     */
    private function isArchived(): bool
    {
        return $this->status === 'archived';
    }

    /**
     * Check if post is expired
     */
    private function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if post is currently pinned
     */
    private function isCurrentlyPinned(): bool
    {
        if (!$this->is_pinned) {
            return false;
        }

        // Si no hay fecha límite, está fijado indefinidamente
        if (!$this->pinned_until) {
            return true;
        }

        // Verificar si la fecha límite no ha pasado
        return $this->pinned_until->isFuture();
    }

    /**
     * Calculate estimated reading time in minutes
     */
    private function getReadingTimeMinutes(): int
    {
        $wordCount = str_word_count(strip_tags($this->content));
        $wordsPerMinute = 200; // Average reading speed
        
        return max(1, ceil($wordCount / $wordsPerMinute));
    }
}