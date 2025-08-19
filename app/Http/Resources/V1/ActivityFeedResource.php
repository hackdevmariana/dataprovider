<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityFeedResource extends JsonResource
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
            'user' => new UserResource($this->whenLoaded('user')),
            'activity_type' => $this->activity_type,
            'activity_type_label' => $this->getActivityTypeLabel(),
            'description' => $this->getReadableDescription(),
            'summary' => $this->summary,
            
            // Datos específicos de la actividad
            'activity_data' => $this->activity_data,
            'related' => $this->when($this->related, function () {
                return [
                    'type' => class_basename($this->related_type),
                    'id' => $this->related_id,
                    'data' => $this->whenLoaded('related'),
                ];
            }),
            
            // Métricas energéticas
            'energy_metrics' => $this->when(
                $this->energy_amount_kwh || $this->cost_savings_eur || $this->co2_savings_kg,
                [
                    'energy_amount_kwh' => $this->energy_amount_kwh,
                    'cost_savings_eur' => $this->cost_savings_eur,
                    'co2_savings_kg' => $this->co2_savings_kg,
                    'investment_amount_eur' => $this->investment_amount_eur,
                ]
            ),
            
            // Configuración
            'visibility' => $this->visibility,
            'is_featured' => $this->is_featured,
            'is_milestone' => $this->is_milestone,
            'allow_interactions' => $this->allow_interactions,
            
            // Métricas de engagement
            'engagement' => [
                'score' => $this->engagement_score,
                'likes_count' => $this->likes_count,
                'loves_count' => $this->loves_count,
                'wow_count' => $this->wow_count,
                'comments_count' => $this->comments_count,
                'shares_count' => $this->shares_count,
                'bookmarks_count' => $this->bookmarks_count,
                'views_count' => $this->views_count,
                'total_reactions' => $this->likes_count + $this->loves_count + $this->wow_count,
            ],
            
            // Ubicación
            'location' => $this->when(
                $this->latitude && $this->longitude,
                [
                    'latitude' => $this->latitude,
                    'longitude' => $this->longitude,
                    'location_name' => $this->location_name,
                    'distance_km' => $this->when(isset($this->distance_km), $this->distance_km),
                ]
            ),
            
            // Información temporal
            'timing' => [
                'activity_occurred_at' => $this->activity_occurred_at,
                'is_real_time' => $this->is_real_time,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
            
            // Agrupación
            'grouping' => $this->when(
                $this->activity_group || $this->parent_activity_id,
                [
                    'activity_group' => $this->activity_group,
                    'parent_activity_id' => $this->parent_activity_id,
                    'has_children' => $this->whenLoaded('childActivities', function () {
                        return $this->childActivities->count() > 0;
                    }),
                ]
            ),
            
            // Algoritmo
            'algorithm' => [
                'relevance_score' => $this->relevance_score,
                'community_impact_score' => $this->community_impact_score,
                'boost_until' => $this->boost_until,
            ],
            
            // Estado y moderación
            'status' => $this->status,
            'moderation' => $this->when(
                $this->flags_count > 0 || $this->moderated_by,
                [
                    'flags_count' => $this->flags_count,
                    'flag_reasons' => $this->flag_reasons,
                    'moderated_by' => $this->whenLoaded('moderator', new UserResource($this->moderator)),
                    'moderated_at' => $this->moderated_at,
                ]
            ),
            
            // Interacciones (si están cargadas)
            'interactions' => $this->when(
                $this->relationLoaded('interactions'),
                function () {
                    return [
                        'recent' => SocialInteractionResource::collection(
                            $this->interactions->take(5)
                        ),
                        'summary' => $this->interactions->groupBy('interaction_type')
                            ->map(fn($group) => $group->count()),
                    ];
                }
            ),
            
            // Metadatos adicionales
            'meta' => [
                'can_interact' => $this->allow_interactions && $this->status === 'active',
                'is_own_activity' => $request->user() ? $this->user_id === $request->user()->id : false,
                'age_in_hours' => $this->created_at->diffInHours(now()),
                'emoji' => $this->getActivityEmoji(),
            ],
        ];
    }

    /**
     * Obtener etiqueta legible para el tipo de actividad
     */
    private function getActivityTypeLabel(): string
    {
        return match ($this->activity_type) {
            'energy_saved' => 'Energía Ahorrada',
            'solar_generated' => 'Energía Solar Generada',
            'achievement_unlocked' => 'Logro Desbloqueado',
            'project_funded' => 'Proyecto Financiado',
            'installation_completed' => 'Instalación Completada',
            'cooperative_joined' => 'Se Unió a Cooperativa',
            'roof_published' => 'Techo Publicado',
            'investment_made' => 'Inversión Realizada',
            'production_right_sold' => 'Derecho de Producción Vendido',
            'challenge_completed' => 'Desafío Completado',
            'milestone_reached' => 'Hito Alcanzado',
            'content_published' => 'Contenido Publicado',
            'expert_verified' => 'Experto Verificado',
            'review_published' => 'Reseña Publicada',
            'topic_created' => 'Tema Creado',
            'community_contribution' => 'Contribución Comunitaria',
            'carbon_milestone' => 'Hito de CO2',
            'efficiency_improvement' => 'Mejora de Eficiencia',
            'grid_contribution' => 'Contribución a Red',
            'sustainability_goal' => 'Meta de Sostenibilidad',
            default => 'Actividad',
        };
    }

    /**
     * Obtener emoji representativo para la actividad
     */
    private function getActivityEmoji(): string
    {
        return match ($this->activity_type) {
            'energy_saved' => '⚡',
            'solar_generated' => '☀️',
            'achievement_unlocked' => '🏆',
            'project_funded' => '💰',
            'installation_completed' => '🔧',
            'cooperative_joined' => '🤝',
            'roof_published' => '🏠',
            'investment_made' => '📈',
            'production_right_sold' => '💼',
            'challenge_completed' => '🎯',
            'milestone_reached' => '🎉',
            'content_published' => '📝',
            'expert_verified' => '✅',
            'review_published' => '⭐',
            'topic_created' => '💬',
            'community_contribution' => '👥',
            'carbon_milestone' => '🌱',
            'efficiency_improvement' => '📊',
            'grid_contribution' => '🔌',
            'sustainability_goal' => '🌍',
            default => '📊',
        };
    }
}
