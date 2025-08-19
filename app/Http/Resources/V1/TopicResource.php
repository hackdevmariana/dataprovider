<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TopicResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $request->user();
        
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'rules' => $this->when($user && $this->isMember($user), $this->rules),
            'welcome_message' => $this->welcome_message,
            
            // Apariencia
            'appearance' => [
                'icon' => $this->icon ?: $this->getDefaultIcon(),
                'color' => $this->color,
                'banner_image' => $this->banner_image,
                'avatar_image' => $this->avatar_image,
            ],
            
            // Categorización
            'category' => $this->category,
            'category_label' => $this->getCategoryLabel(),
            'tags' => $this->tags ?? [],
            
            // Configuración
            'visibility' => $this->visibility,
            'join_policy' => $this->join_policy,
            'post_permission' => $this->post_permission,
            'comment_permission' => $this->comment_permission,
            
            // Funcionalidades habilitadas
            'features' => [
                'allow_polls' => $this->allow_polls,
                'allow_images' => $this->allow_images,
                'allow_videos' => $this->allow_videos,
                'allow_links' => $this->allow_links,
                'allow_files' => $this->allow_files,
                'enable_wiki' => $this->enable_wiki,
                'enable_events' => $this->enable_events,
                'enable_marketplace' => $this->enable_marketplace,
            ],
            
            // Estadísticas principales
            'stats' => [
                'members_count' => $this->members_count ?? 0,
                'posts_count' => $this->posts_count ?? 0,
                'comments_count' => $this->comments_count ?? 0,
            ],
            
            // Métricas de actividad
            'activity' => [
                'activity_score' => $this->activity_score ?? 0,
                'last_activity_at' => $this->last_activity_at,
                'last_post_at' => $this->last_post_at,
            ],
            
            // Estados
            'status' => [
                'is_active' => $this->is_active ?? true,
                'is_featured' => $this->is_featured ?? false,
            ],
            
            // Información del creador
            'creator' => new UserResource($this->whenLoaded('creator')),
            
            // Información de membresía del usuario actual
            'user_membership' => $this->when($user, function () use ($user) {
                $membership = $this->memberships()
                                  ->where('user_id', $user->id)
                                  ->first();
                
                if (!$membership) {
                    return [
                        'is_member' => false,
                        'can_join' => $this->join_policy !== 'closed',
                        'can_view' => $this->canBeViewedBy($user),
                        'can_post' => false,
                        'can_comment' => false,
                    ];
                }
                
                return [
                    'is_member' => true,
                    'role' => $membership->role,
                    'role_label' => $membership->getRoleLabel(),
                    'status' => $membership->status,
                    'joined_at' => $membership->joined_at,
                    'reputation_score' => $membership->reputation_score,
                    'posts_count' => $membership->posts_count,
                    'comments_count' => $membership->comments_count,
                    'can_post' => $this->canPostBy($user),
                    'can_comment' => $this->canCommentBy($user),
                    'can_moderate' => $membership->isModerator(),
                ];
            }),
            
            // Permisos del usuario actual
            'permissions' => $this->when($user, [
                'can_view' => $this->canBeViewedBy($user),
                'can_join' => !$this->isMember($user) && $this->join_policy !== 'closed',
                'can_post' => $this->canPostBy($user),
                'can_comment' => $this->canCommentBy($user),
                'can_moderate' => $user ? $this->isModerator($user) : false,
                'can_edit' => $user ? ($this->creator_id === $user->id || $this->isModerator($user)) : false,
            ]),
            
            // Temas relacionados
            'related_topics' => $this->when($this->related_topics, $this->related_topics),
            
            // Información temporal
            'timing' => [
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
                'days_since_creation' => $this->days_since_creation,
                'peak_activity_at' => $this->peak_activity_at,
                'peak_members_count' => $this->peak_members_count,
            ],
            
            // SEO y metadatos
            'seo' => $this->when($this->meta_title || $this->meta_description, [
                'meta_title' => $this->meta_title,
                'meta_description' => $this->meta_description,
            ]),
            
            // Estadísticas detalladas (solo para miembros)
            'detailed_stats' => $this->when(
                $user && $this->isMember($user),
                function () {
                    return $this->getStats();
                }
            ),
            
            // Metadatos adicionales
            'meta' => [
                'url' => "/topics/{$this->slug}",
                'api_url' => "/api/v1/topics/{$this->id}",
                'posts_url' => "/api/v1/topics/{$this->id}/posts",
                'members_url' => "/api/v1/topics/{$this->id}/members",
                'join_url' => "/api/v1/topics/{$this->id}/join",
                'age_in_days' => $this->created_at->diffInDays(now()),
                'is_new' => $this->created_at->isAfter(now()->subDays(7)),
                'activity_level' => $this->getActivityLevel(),
                'size_category' => $this->getSizeCategory(),
            ],
        ];
    }

    /**
     * Obtener etiqueta del nivel de dificultad
     */
    private function getDifficultyLabel(): string
    {
        return match ($this->difficulty_level) {
            'beginner' => 'Principiante',
            'intermediate' => 'Intermedio', 
            'advanced' => 'Avanzado',
            'expert' => 'Experto',
            default => 'Principiante',
        };
    }

    /**
     * Obtener nivel de actividad
     */
    private function getActivityLevel(): string
    {
        $score = $this->activity_score;
        
        return match (true) {
            $score >= 200 => 'very_high',
            $score >= 100 => 'high',
            $score >= 50 => 'medium',
            $score >= 20 => 'low',
            default => 'very_low',
        };
    }

    /**
     * Obtener categoría de tamaño
     */
    private function getSizeCategory(): string
    {
        $members = $this->members_count;
        
        return match (true) {
            $members >= 1000 => 'large',
            $members >= 100 => 'medium',
            $members >= 10 => 'small',
            default => 'tiny',
        };
    }
}