<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource para transformar contenido generado por usuarios en respuestas JSON.
 */
class UserGeneratedContentResource extends JsonResource
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
            'title' => $this->title,
            'content' => $this->content,
            'excerpt' => $this->excerpt,
            
            // Tipo y categoría
            'content_type' => $this->content_type,
            'content_type_name' => $this->content_type_name,
            'language' => $this->language,
            'rating' => $this->rating,
            
            // Estado y visibilidad
            'status' => [
                'status' => $this->status,
                'visibility' => $this->visibility,
                'is_published' => $this->status === 'published',
                'is_featured' => $this->is_featured,
                'is_verified' => $this->is_verified,
                'is_spam' => $this->is_spam,
                'needs_moderation' => $this->needs_moderation,
            ],
            
            // Información del autor
            'author' => [
                'user_id' => $this->user_id,
                'name' => $this->user?->name ?? $this->user_name,
                'email' => $this->when($request->user()?->can('view-user-emails'), $this->user?->email ?? $this->user_email),
                'is_registered' => !$this->is_anonymous,
                'is_anonymous' => $this->is_anonymous,
                'avatar' => $this->user?->avatar,
            ],
            
            // Métricas de engagement
            'engagement' => [
                'likes_count' => $this->likes_count,
                'dislikes_count' => $this->dislikes_count,
                'replies_count' => $this->replies_count,
                'reports_count' => $this->reports_count,
                'total_votes' => $this->likes_count + $this->dislikes_count,
                'approval_rate' => $this->engagement_metrics['approval_rate'] ?? 0,
                'engagement_score' => $this->calculateEngagementScore(),
            ],
            
            // Análisis de contenido
            'sentiment' => [
                'score' => $this->sentiment_score,
                'label' => $this->sentiment_label,
            ],
            
            // Tags y categorización automática
            'auto_tags' => $this->auto_tags ?? [],
            'metadata' => $this->metadata ?? [],
            'media_attachments' => $this->media_attachments ?? [],
            
            // Jerarquía (para comentarios anidados)
            'parent_id' => $this->parent_id,
            'hierarchy' => [
                'is_top_level' => is_null($this->parent_id),
                'has_replies' => $this->replies_count > 0,
            ],
            
            // Geolocalización (si está disponible)
            'location' => $this->when($this->latitude && $this->longitude, [
                'location_name' => $this->location_name,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
            ]),
            
            // Fechas
            'published_at' => $this->when($this->published_at, $this->published_at->toISOString()),
            'moderated_at' => $this->when($this->moderated_at, $this->moderated_at->toISOString()),
            'featured_until' => $this->when($this->featured_until, $this->featured_until->toISOString()),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            
            // Información de moderación (solo para moderadores)
            'moderation' => $this->when($request->user()?->can('moderate-content'), [
                'moderator_id' => $this->moderator_id,
                'moderation_notes' => $this->moderation_notes ?? [],
                'user_ip' => $this->user_ip,
                'user_agent' => $this->user_agent,
            ]),
            
            // Relaciones
            'user' => $this->whenLoaded('user', function() {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'avatar' => $this->user->avatar,
                ];
            }),
            
            'related' => $this->whenLoaded('related', function() {
                // Información básica del contenido relacionado
                return [
                    'type' => $this->related_type,
                    'id' => $this->related_id,
                    'title' => $this->related->title ?? $this->related->name ?? null,
                    'url' => $this->related_url,
                ];
            }),
            
            'parent' => $this->whenLoaded('parent', function() {
                return [
                    'id' => $this->parent->id,
                    'content_type' => $this->parent->content_type,
                    'author_name' => $this->parent->user?->name ?? $this->parent->user_name,
                    'excerpt' => substr(strip_tags($this->parent->content), 0, 100) . '...',
                ];
            }),
            
            'replies' => $this->whenLoaded('replies', function() {
                return $this->replies->map(function($reply) {
                    return [
                        'id' => $reply->id,
                        'author_name' => $reply->user?->name ?? $reply->user_name,
                        'content' => $reply->content,
                        'likes_count' => $reply->likes_count,
                        'created_at' => $reply->created_at->toISOString(),
                    ];
                });
            }),
            
            'moderator' => $this->whenLoaded('moderator', function() {
                return [
                    'id' => $this->moderator->id,
                    'name' => $this->moderator->name,
                ];
            }),
        ];
    }
}