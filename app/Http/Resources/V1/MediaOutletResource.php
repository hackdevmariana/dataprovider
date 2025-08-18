<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource para transformar medios de comunicación en respuestas JSON.
 */
class MediaOutletResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            
            // Información básica del medio
            'type' => $this->type,
            'type_name' => $this->type_name,
            'media_category' => $this->media_category,
            'website' => $this->website,
            'rss_feed' => $this->rss_feed,
            'logo_url' => $this->logo_url,
            
            // Ubicación y cobertura
            'headquarters_location' => $this->headquarters_location,
            'coverage_scope' => $this->coverage_scope,
            'coverage_scope_name' => $this->coverage_scope_name,
            'languages' => $this->languages ?? [],
            
            // Información corporativa
            'founding_year' => $this->founding_year,
            'owner_company' => $this->owner_company,
            'political_leaning' => $this->political_leaning,
            'specializations' => $this->specializations ?? [],
            
            // Características del medio
            'is_digital_native' => $this->is_digital_native,
            'is_verified' => $this->is_verified,
            'is_active' => $this->is_active,
            'covers_sustainability' => $this->covers_sustainability,
            'is_reference_media' => $this->is_reference_media,
            
            // Métricas de calidad
            'quality_metrics' => [
                'credibility_score' => $this->credibility_score,
                'influence_score' => $this->influence_score,
                'sustainability_focus' => $this->sustainability_focus,
                'years_active' => $this->founding_year ? (date('Y') - $this->founding_year) : null,
            ],
            
            // Métricas de audiencia
            'audience_profile' => [
                'articles_count' => $this->articles_count,
                'monthly_pageviews' => $this->monthly_pageviews,
                'social_media_followers' => $this->social_media_followers,
                'circulation' => $this->circulation,
                'circulation_type' => $this->circulation_type,
            ],
            
            // Contacto y prensa
            'contact_info' => [
                'contact_email' => $this->contact_email,
                'press_contact' => [
                    'name' => $this->press_contact_name,
                    'email' => $this->press_contact_email,
                    'phone' => $this->press_contact_phone,
                ],
            ],
            
            // Redes sociales
            'social_media' => [
                'handles' => $this->social_media_handles ?? [],
                'followers_count' => $this->social_media_followers,
            ],
            
            // Información adicional
            'editorial_team' => $this->editorial_team ?? [],
            'content_licensing' => $this->content_licensing,
            'allows_reprints' => $this->allows_reprints,
            'api_access' => $this->api_access ?? [],
            
            // Fechas importantes
            'last_scraped_at' => $this->when($this->last_scraped_at, $this->last_scraped_at->toISOString()),
            'verified_at' => $this->when($this->verified_at, $this->verified_at->toISOString()),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            
            // Relaciones
            'municipality' => $this->whenLoaded('municipality', function() {
                return [
                    'id' => $this->municipality->id,
                    'name' => $this->municipality->name,
                    'province' => $this->municipality->province?->name,
                    'autonomous_community' => $this->municipality->province?->autonomousCommunity?->name,
                ];
            }),
            
            'contacts' => $this->whenLoaded('contacts', function() {
                return $this->contacts->map(function($contact) {
                    return [
                        'id' => $contact->id,
                        'contact_name' => $contact->contact_name,
                        'type' => $contact->type,
                        'type_name' => $contact->type_name,
                        'job_title' => $contact->job_title,
                        'email' => $contact->email,
                        'phone' => $contact->phone,
                        'priority_level' => $contact->priority_level,
                        'is_active' => $contact->is_active,
                        'response_rate' => $contact->response_rate,
                    ];
                });
            }),
            
            'specialized_tags' => $this->whenLoaded('specializedTags', function() {
                return $this->specializedTags->map(function($tag) {
                    return [
                        'id' => $tag->id,
                        'name' => $tag->name,
                        'slug' => $tag->slug,
                        'tag_type' => $tag->tag_type,
                    ];
                });
            }),
            
            // Artículos (solo en vista detalle)
            'recent_articles' => $this->when(
                $request->routeIs('*.show') && isset($this->recent_articles),
                $this->recent_articles
            ),
            
            'popular_articles' => $this->when(
                $request->routeIs('*.show') && isset($this->popular_articles),
                $this->popular_articles
            ),
            
            'sustainability_articles' => $this->when(
                $request->routeIs('*.show') && isset($this->sustainability_articles),
                $this->sustainability_articles
            ),
        ];
    }
}