<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource para transformar contactos de medios en respuestas JSON.
 */
class MediaContactResource extends JsonResource
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
            'contact_name' => $this->contact_name,
            'job_title' => $this->job_title,
            'department' => $this->department,
            
            // Tipo y categoría
            'type' => $this->type,
            'type_name' => $this->type_name,
            'is_freelancer' => $this->is_freelancer,
            
            // Información de contacto
            'contact_info' => [
                'phone' => $this->phone,
                'mobile_phone' => $this->mobile_phone,
                'email' => $this->email,
                'secondary_email' => $this->secondary_email,
                'preferred_method' => $this->preferred_contact_method,
                'recommended_method' => $this->getRecommendedContactMethod(),
            ],
            
            // Especialización
            'professional_profile' => [
                'specializations' => $this->specializations ?? [],
                'coverage_areas' => $this->coverage_areas ?? [],
                'language_preference' => $this->language_preference,
                'bio' => $this->bio,
            ],
            
            // Preferencias de contenido
            'content_preferences' => [
                'accepts_press_releases' => $this->accepts_press_releases,
                'accepts_interviews' => $this->accepts_interviews,
                'accepts_events_invitations' => $this->accepts_events_invitations,
            ],
            
            // Estado y prioridad
            'status' => [
                'is_active' => $this->is_active,
                'is_verified' => $this->is_verified,
                'priority_level' => $this->priority_level,
                'priority_level_name' => $this->priority_level_name,
                'verification_status' => $this->verification_status,
            ],
            
            // Disponibilidad
            'availability' => [
                'schedule' => $this->availability_schedule ?? [],
                'is_available_now' => $this->isAvailableNow(),
            ],
            
            // Métricas de interacción
            'interaction_metrics' => [
                'contacts_count' => $this->contacts_count,
                'successful_contacts' => $this->successful_contacts,
                'response_rate' => $this->response_rate,
                'last_contacted_at' => $this->when($this->last_contacted_at, $this->last_contacted_at->diffForHumans()),
                'last_response_at' => $this->when($this->last_response_at, $this->last_response_at->diffForHumans()),
                'avg_response_time' => $this->calculateAverageResponseTime(),
            ],
            
            // Redes sociales y información adicional
            'social_media_profiles' => $this->social_media_profiles ?? [],
            'recent_articles' => $this->recent_articles ?? [],
            'notes' => $this->when($request->user()?->can('view-internal-notes'), $this->notes),
            
            // Fechas
            'verified_at' => $this->when($this->verified_at, $this->verified_at->toISOString()),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            
            // Relaciones
            'media_outlet' => $this->whenLoaded('mediaOutlet', function() {
                return [
                    'id' => $this->mediaOutlet->id,
                    'name' => $this->mediaOutlet->name,
                    'type' => $this->mediaOutlet->type,
                    'type_name' => $this->mediaOutlet->type_name,
                    'coverage_scope' => $this->mediaOutlet->coverage_scope,
                    'credibility_score' => $this->mediaOutlet->credibility_score,
                    'is_verified' => $this->mediaOutlet->is_verified,
                    'covers_sustainability' => $this->mediaOutlet->covers_sustainability,
                ];
            }),
            
            // Historial de interacciones (solo en vista detalle)
            'recent_interactions' => $this->when(
                $request->routeIs('*.show') && isset($this->recent_interactions),
                $this->recent_interactions
            ),
        ];
    }
}