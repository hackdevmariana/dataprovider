<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserFollowResource extends JsonResource
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
            'follower' => new UserResource($this->whenLoaded('follower')),
            'following' => new UserResource($this->whenLoaded('following')),
            
            // Configuración de seguimiento
            'follow_type' => $this->follow_type,
            'follow_type_label' => $this->getFollowTypeLabel(),
            'follow_reason' => $this->follow_reason,
            'interests' => $this->interests,
            'tags' => $this->tags,
            
            // Configuración de notificaciones
            'notifications' => [
                'frequency' => $this->notification_frequency,
                'frequency_label' => $this->getNotificationFrequencyLabel(),
                'notify_new_activity' => $this->notify_new_activity,
                'notify_achievements' => $this->notify_achievements,
                'notify_projects' => $this->notify_projects,
                'notify_investments' => $this->notify_investments,
                'notify_milestones' => $this->notify_milestones,
                'notify_content' => $this->notify_content,
            ],
            
            // Configuración de feed
            'feed_settings' => [
                'show_in_main_feed' => $this->show_in_main_feed,
                'prioritize_in_feed' => $this->prioritize_in_feed,
                'feed_weight' => $this->feed_weight,
                'calculated_weight' => $this->getFeedWeight(),
                'minimum_relevance_score' => $this->minimum_relevance_score,
                'content_filters' => $this->content_filters,
                'activity_filters' => $this->activity_filters,
            ],
            
            // Reciprocidad
            'relationship' => [
                'is_mutual' => $this->is_mutual,
                'mutual_since' => $this->mutual_since,
                'status' => $this->status,
                'status_label' => $this->getStatusLabel(),
            ],
            
            // Métricas de engagement
            'engagement' => [
                'score' => $this->engagement_score,
                'interactions_count' => $this->interactions_count,
                'content_views' => $this->content_views,
                'last_interaction_at' => $this->last_interaction_at,
                'last_seen_activity_at' => $this->last_seen_activity_at,
            ],
            
            // Información temporal
            'timing' => [
                'followed_at' => $this->followed_at,
                'days_following' => $this->days_following,
                'status_changed_at' => $this->status_changed_at,
                'status_reason' => $this->status_reason,
            ],
            
            // Configuración de privacidad
            'privacy' => [
                'is_public' => $this->is_public,
                'show_to_followed' => $this->show_to_followed,
                'allow_followed_to_see_activity' => $this->allow_followed_to_see_activity,
            ],
            
            // Algoritmo
            'algorithm' => [
                'relevance_decay_rate' => $this->relevance_decay_rate,
                'algorithm_preferences' => $this->algorithm_preferences,
            ],
            
            // Metadatos
            'meta' => [
                'can_configure' => $request->user() ? $this->follower_id === $request->user()->id : false,
                'can_unfollow' => $request->user() ? $this->follower_id === $request->user()->id : false,
                'follow_duration_days' => $this->followed_at ? $this->followed_at->diffInDays(now()) : 0,
                'is_recent_follow' => $this->followed_at ? $this->followed_at->isAfter(now()->subDays(7)) : false,
                'is_long_term_follow' => $this->followed_at ? $this->followed_at->isBefore(now()->subMonths(6)) : false,
                'engagement_level' => $this->getEngagementLevel(),
            ],
        ];
    }

    /**
     * Obtener etiqueta del tipo de seguimiento
     */
    private function getFollowTypeLabel(): string
    {
        return match ($this->follow_type) {
            'general' => 'General',
            'expertise' => 'Por Expertise',
            'projects' => 'Solo Proyectos',
            'achievements' => 'Solo Logros',
            'energy_activity' => 'Actividad Energética',
            'installations' => 'Instalaciones',
            'investments' => 'Inversiones',
            'content' => 'Contenido',
            'community' => 'Actividad Comunitaria',
            default => 'General',
        };
    }

    /**
     * Obtener etiqueta de la frecuencia de notificaciones
     */
    private function getNotificationFrequencyLabel(): string
    {
        return match ($this->notification_frequency) {
            'instant' => 'Instantáneo',
            'daily_digest' => 'Resumen Diario',
            'weekly_digest' => 'Resumen Semanal',
            'monthly_digest' => 'Resumen Mensual',
            'never' => 'Nunca',
            default => 'Instantáneo',
        };
    }

    /**
     * Obtener etiqueta del estado
     */
    private function getStatusLabel(): string
    {
        return match ($this->status) {
            'active' => 'Activo',
            'paused' => 'Pausado',
            'muted' => 'Silenciado',
            'blocked' => 'Bloqueado',
            'requested' => 'Solicitado',
            'rejected' => 'Rechazado',
            default => 'Activo',
        };
    }

    /**
     * Obtener nivel de engagement
     */
    private function getEngagementLevel(): string
    {
        $score = $this->engagement_score;
        
        return match (true) {
            $score >= 80 => 'very_high',
            $score >= 60 => 'high',
            $score >= 40 => 'medium',
            $score >= 20 => 'low',
            default => 'very_low',
        };
    }
}
