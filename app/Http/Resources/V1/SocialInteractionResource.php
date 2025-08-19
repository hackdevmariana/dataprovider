<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SocialInteractionResource extends JsonResource
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
            'interaction_type' => $this->interaction_type,
            'interaction_label' => $this->getReadableDescription(),
            'emoji' => $this->getEmoji(),
            
            // Contenido de la interacción
            'interaction_note' => $this->interaction_note,
            'interaction_data' => $this->interaction_data,
            
            // Objeto interactuado
            'interactable' => $this->when($this->interactable, function () {
                return [
                    'type' => class_basename($this->interactable_type),
                    'id' => $this->interactable_id,
                    'data' => $this->whenLoaded('interactable'),
                ];
            }),
            
            // Configuración
            'is_public' => $this->is_public,
            'notify_author' => $this->notify_author,
            'show_in_activity' => $this->show_in_activity,
            
            // Métricas
            'engagement_weight' => $this->engagement_weight,
            'quality_score' => $this->quality_score,
            
            // Información contextual
            'context' => $this->when(
                $this->source || $this->device_type || ($this->latitude && $this->longitude),
                [
                    'source' => $this->source,
                    'device_type' => $this->device_type,
                    'location' => $this->when(
                        $this->latitude && $this->longitude,
                        [
                            'latitude' => $this->latitude,
                            'longitude' => $this->longitude,
                        ]
                    ),
                ]
            ),
            
            // Temporalidad
            'timing' => [
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
                'interaction_expires_at' => $this->interaction_expires_at,
                'is_temporary' => $this->is_temporary,
                'has_expired' => $this->hasExpired(),
            ],
            
            // Estado
            'status' => $this->status,
            'is_active' => $this->status === 'active',
            'is_positive' => $this->isPositive(),
            'is_negative' => $this->isNegative(),
            
            // Metadatos
            'meta' => [
                'can_withdraw' => $request->user() ? $this->user_id === $request->user()->id : false,
                'age_in_hours' => $this->created_at->diffInHours(now()),
                'interaction_category' => $this->getInteractionCategory(),
            ],
        ];
    }

    /**
     * Obtener categoría de la interacción
     */
    private function getInteractionCategory(): string
    {
        return match ($this->interaction_type) {
            'like', 'love', 'wow', 'celebrate' => 'reaction',
            'support' => 'endorsement',
            'share' => 'distribution',
            'bookmark', 'follow', 'subscribe' => 'curation',
            'report', 'hide', 'block' => 'moderation',
            default => 'other',
        };
    }
}
