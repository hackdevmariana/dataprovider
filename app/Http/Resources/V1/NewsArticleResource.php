<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource para transformar artículos de noticias en respuestas JSON.
 */
class NewsArticleResource extends JsonResource
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
            'slug' => $this->slug,
            'summary' => $this->summary,
            'excerpt' => $this->when($this->excerpt, $this->excerpt),
            'content' => $this->when($request->routeIs('*.show'), $this->content),
            'original_title' => $this->when($this->original_title, $this->original_title),
            
            // Metadata del artículo
            'category' => $this->category,
            'topic_focus' => $this->topic_focus,
            'article_type' => $this->article_type,
            'language' => $this->language?->code ?? 'es',
            
            // Estado y visibilidad
            'status' => $this->status,
            'visibility' => $this->visibility,
            'is_outstanding' => $this->is_outstanding,
            'is_verified' => $this->is_verified,
            'is_scraped' => $this->is_scraped,
            'is_translated' => $this->is_translated,
            'is_breaking_news' => $this->is_breaking_news,
            'is_evergreen' => $this->is_evergreen,
            'is_currently_featured' => $this->is_currently_featured,
            
            // Fechas
            'published_at' => $this->published_at?->toISOString(),
            'featured_start' => $this->when($this->featured_start, $this->featured_start->toISOString()),
            'featured_end' => $this->when($this->featured_end, $this->featured_end->toISOString()),
            'scraped_at' => $this->when($this->scraped_at, $this->scraped_at->toISOString()),
            'last_engagement_at' => $this->when($this->last_engagement_at, $this->last_engagement_at->toISOString()),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            
            // Métricas de engagement
            'engagement' => [
                'views_count' => $this->views_count,
                'shares_count' => $this->shares_count,
                'comments_count' => $this->comments_count,
                'engagement_rate' => $this->engagement_rate,
            ],
            
            // Análisis de contenido
            'reading_time_minutes' => $this->reading_time_minutes,
            'word_count' => $this->word_count,
            'sentiment' => [
                'score' => $this->sentiment_score,
                'label' => $this->sentiment_label,
                'level' => $this->sentiment_level,
            ],
            
            // Sostenibilidad y medio ambiente
            'sustainability' => [
                'topics' => $this->sustainability_topics ?? [],
                'environmental_impact_score' => $this->environmental_impact_score,
                'related_co2_data' => $this->related_co2_data ?? [],
            ],
            
            // Metadatos estructurados
            'keywords' => $this->keywords ?? [],
            'entities' => $this->entities ?? [],
            
            // Geolocalización
            'location' => $this->when($this->latitude && $this->longitude, [
                'geo_scope' => $this->geo_scope,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'municipality' => $this->whenLoaded('municipality', function() {
                    return [
                        'id' => $this->municipality->id,
                        'name' => $this->municipality->name,
                        'province' => $this->municipality->province?->name,
                    ];
                }),
            ]),
            
            // SEO y redes sociales
            'seo' => $this->when($request->routeIs('*.show'), [
                'title' => $this->seo_title,
                'description' => $this->seo_description,
                'social_media_meta' => $this->social_media_meta ?? [],
                'social_share_data' => $this->social_share_data,
            ]),
            
            // Relaciones
            'author' => $this->whenLoaded('author', function() {
                return [
                    'id' => $this->author->id,
                    'name' => $this->author->name,
                    'profession' => $this->author->profession?->name,
                ];
            }),
            
            'media_outlet' => $this->whenLoaded('mediaOutlet', function() {
                return [
                    'id' => $this->mediaOutlet->id,
                    'name' => $this->mediaOutlet->name,
                    'type' => $this->mediaOutlet->type,
                    'type_name' => $this->mediaOutlet->type_name,
                    'credibility_score' => $this->mediaOutlet->credibility_score,
                    'influence_score' => $this->mediaOutlet->influence_score,
                    'is_verified' => $this->mediaOutlet->is_verified,
                    'covers_sustainability' => $this->mediaOutlet->covers_sustainability,
                ];
            }),
            
            'image' => $this->whenLoaded('image', function() {
                return [
                    'id' => $this->image->id,
                    'url' => $this->image->url,
                    'alt_text' => $this->image->alt_text,
                ];
            }),
            
            'tags' => $this->whenLoaded('tags', function() {
                return $this->tags->map(function($tag) {
                    return [
                        'id' => $tag->id,
                        'name' => $tag->name,
                        'slug' => $tag->slug,
                        'tag_type' => $tag->tag_type,
                    ];
                });
            }),
            
            // URL del artículo
            'url' => $this->url,
            
            // Artículos relacionados (solo en vista detalle)
            'related_articles' => $this->when(
                $request->routeIs('*.show') && isset($this->related_articles),
                $this->related_articles
            ),
        ];
    }
}