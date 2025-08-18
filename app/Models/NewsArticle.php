<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Carbon\Carbon;

/**
 * Artículos de noticias para gestión de contenido mediático.
 * 
 * Sistema completo de gestión de noticias con soporte para múltiples
 * idiomas, categorización, geolocalización y análisis de engagement.
 * Incluye funcionalidades avanzadas para medios sostenibles y ambientales.
 * 
 * @property int $id
 * @property string $title Título del artículo
 * @property string $slug Slug único para URL
 * @property string|null $summary Resumen/entradilla del artículo
 * @property string $content Contenido completo del artículo
 * @property string|null $excerpt Extracto automático
 * @property string|null $source_url URL original del artículo
 * @property string|null $original_title Título original (si traducido)
 * @property \Carbon\Carbon|null $published_at Fecha de publicación
 * @property \Carbon\Carbon|null $featured_start Inicio destacado
 * @property \Carbon\Carbon|null $featured_end Fin destacado
 * @property int|null $media_outlet_id Medio de comunicación
 * @property int|null $author_id Autor del artículo
 * @property int|null $municipality_id Municipio relacionado
 * @property int|null $language_id Idioma del artículo
 * @property int|null $image_id Imagen principal
 * @property string $category Categoría del artículo
 * @property string $topic_focus Enfoque temático específico
 * @property string $article_type Tipo de artículo
 * @property bool $is_outstanding Artículo destacado
 * @property bool $is_verified Artículo verificado
 * @property bool $is_scraped Obtenido por scraping
 * @property bool $is_translated Artículo traducido
 * @property bool $is_breaking_news Noticia de última hora
 * @property bool $is_evergreen Contenido perenne
 * @property string $visibility Visibilidad del artículo
 * @property string $status Estado del artículo
 * @property int $views_count Número de visualizaciones
 * @property int $shares_count Número de compartidos
 * @property int $comments_count Número de comentarios
 * @property float|null $reading_time_minutes Tiempo estimado lectura
 * @property int|null $word_count Número de palabras
 * @property float|null $sentiment_score Puntuación de sentimiento
 * @property string|null $sentiment_label Etiqueta de sentimiento
 * @property array|null $keywords Palabras clave extraídas
 * @property array|null $entities Entidades nombradas
 * @property array|null $sustainability_topics Temas de sostenibilidad
 * @property float|null $environmental_impact_score Puntuación impacto ambiental
 * @property array|null $related_co2_data Datos CO2 relacionados
 * @property string|null $geo_scope Alcance geográfico
 * @property float|null $latitude Latitud del contenido
 * @property float|null $longitude Longitud del contenido
 * @property string|null $seo_title Título SEO
 * @property string|null $seo_description Descripción SEO
 * @property array|null $social_media_meta Metadatos redes sociales
 * @property \Carbon\Carbon|null $scraped_at Fecha de scraping
 * @property \Carbon\Carbon|null $last_engagement_at Último engagement
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * 
 * @property-read \App\Models\MediaOutlet|null $mediaOutlet
 * @property-read \App\Models\Person|null $author
 * @property-read \App\Models\Municipality|null $municipality
 * @property-read \App\Models\Language|null $language
 * @property-read \App\Models\Image|null $image
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tag[] $tags
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserGeneratedContent[] $userComments
 */
class NewsArticle extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'summary',
        'content',
        'excerpt',
        'source_url',
        'original_title',
        'published_at',
        'featured_start',
        'featured_end',
        'media_outlet_id',
        'author_id',
        'municipality_id',
        'language_id',
        'image_id',
        'category',
        'topic_focus',
        'article_type',
        'is_outstanding',
        'is_verified',
        'is_scraped',
        'is_translated',
        'is_breaking_news',
        'is_evergreen',
        'visibility',
        'status',
        'views_count',
        'shares_count',
        'comments_count',
        'reading_time_minutes',
        'word_count',
        'sentiment_score',
        'sentiment_label',
        'keywords',
        'entities',
        'sustainability_topics',
        'environmental_impact_score',
        'related_co2_data',
        'geo_scope',
        'latitude',
        'longitude',
        'seo_title',
        'seo_description',
        'social_media_meta',
        'scraped_at',
        'last_engagement_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'featured_start' => 'datetime',
        'featured_end' => 'datetime',
        'is_outstanding' => 'boolean',
        'is_verified' => 'boolean',
        'is_scraped' => 'boolean',
        'is_translated' => 'boolean',
        'is_breaking_news' => 'boolean',
        'is_evergreen' => 'boolean',
        'views_count' => 'integer',
        'shares_count' => 'integer',
        'comments_count' => 'integer',
        'reading_time_minutes' => 'float',
        'word_count' => 'integer',
        'sentiment_score' => 'float',
        'keywords' => 'array',
        'entities' => 'array',
        'sustainability_topics' => 'array',
        'environmental_impact_score' => 'float',
        'related_co2_data' => 'array',
        'latitude' => 'float',
        'longitude' => 'float',
        'social_media_meta' => 'array',
        'scraped_at' => 'datetime',
        'last_engagement_at' => 'datetime',
    ];

    /**
     * Medio de comunicación que publicó el artículo.
     */
    public function mediaOutlet(): BelongsTo
    {
        return $this->belongsTo(MediaOutlet::class);
    }

    /**
     * Autor del artículo.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'author_id');
    }

    /**
     * Municipio relacionado con el artículo.
     */
    public function municipality(): BelongsTo
    {
        return $this->belongsTo(Municipality::class);
    }

    /**
     * Idioma del artículo.
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    /**
     * Imagen principal del artículo.
     */
    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class);
    }

    /**
     * Tags asociados al artículo.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'news_article_tag')
                    ->withTimestamps();
    }

    /**
     * Comentarios de usuarios.
     */
    public function userComments(): HasMany
    {
        return $this->hasMany(UserGeneratedContent::class, 'related_id')
                    ->where('related_type', self::class)
                    ->where('content_type', 'comment');
    }

    /**
     * Contenido generado por usuarios relacionado.
     */
    public function userContent(): MorphMany
    {
        return $this->morphMany(UserGeneratedContent::class, 'related');
    }

    /**
     * Scope para artículos publicados.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->where('published_at', '<=', now());
    }

    /**
     * Scope para artículos destacados.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_outstanding', true)
                    ->where(function($q) {
                        $q->whereNull('featured_start')
                          ->orWhere('featured_start', '<=', now());
                    })
                    ->where(function($q) {
                        $q->whereNull('featured_end')
                          ->orWhere('featured_end', '>=', now());
                    });
    }

    /**
     * Scope para noticias de última hora.
     */
    public function scopeBreaking($query)
    {
        return $query->where('is_breaking_news', true)
                    ->where('published_at', '>', now()->subHours(24));
    }

    /**
     * Scope para artículos por categoría.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope para artículos sostenibles.
     */
    public function scopeSustainability($query)
    {
        return $query->whereIn('category', ['sostenibilidad', 'medio_ambiente', 'energia'])
                    ->orWhereNotNull('sustainability_topics')
                    ->orWhereNotNull('environmental_impact_score');
    }

    /**
     * Scope para artículos con alto engagement.
     */
    public function scopeHighEngagement($query, $minViews = 1000)
    {
        return $query->where('views_count', '>=', $minViews)
                    ->orderBy('views_count', 'desc');
    }

    /**
     * Scope para búsqueda de texto.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('title', 'LIKE', "%{$search}%")
              ->orWhere('summary', 'LIKE', "%{$search}%")
              ->orWhere('content', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Scope para artículos cerca de ubicación.
     */
    public function scopeNearLocation($query, $lat, $lng, $radiusKm = 50)
    {
        return $query->whereNotNull('latitude')
                    ->whereNotNull('longitude')
                    ->whereRaw(
                        '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) <= ?',
                        [$lat, $lng, $lat, $radiusKm]
                    );
    }

    /**
     * Incrementar contador de visualizaciones.
     */
    public function incrementViews()
    {
        $this->increment('views_count');
        $this->update(['last_engagement_at' => now()]);
    }

    /**
     * Incrementar contador de compartidos.
     */
    public function incrementShares()
    {
        $this->increment('shares_count');
        $this->update(['last_engagement_at' => now()]);
    }

    /**
     * Calcular tiempo de lectura automáticamente.
     */
    public function calculateReadingTime()
    {
        if (!$this->content) {
            return 0;
        }

        $wordCount = str_word_count(strip_tags($this->content));
        $this->word_count = $wordCount;
        
        // Promedio 250 palabras por minuto
        $readingTime = ceil($wordCount / 250);
        $this->reading_time_minutes = max(1, $readingTime);
        
        return $this->reading_time_minutes;
    }

    /**
     * Generar extracto automático.
     */
    public function generateExcerpt($length = 160)
    {
        if ($this->summary) {
            return substr($this->summary, 0, $length);
        }

        $content = strip_tags($this->content);
        $excerpt = substr($content, 0, $length);
        
        // Cortar en la última palabra completa
        $lastSpace = strrpos($excerpt, ' ');
        if ($lastSpace !== false) {
            $excerpt = substr($excerpt, 0, $lastSpace);
        }
        
        return $excerpt . '...';
    }

    /**
     * Analizar temas de sostenibilidad en el contenido.
     */
    public function analyzeSustainabilityTopics()
    {
        $sustainabilityKeywords = [
            'energia_renovable' => ['solar', 'eólica', 'renovable', 'fotovoltaica', 'biomasa'],
            'cambio_climatico' => ['cambio climático', 'calentamiento global', 'emisiones', 'CO2'],
            'economia_circular' => ['reciclaje', 'reutilización', 'economía circular', 'residuos'],
            'biodiversidad' => ['biodiversidad', 'ecosistema', 'especies', 'fauna', 'flora'],
            'transporte_sostenible' => ['vehículo eléctrico', 'transporte público', 'bicicleta', 'movilidad'],
            'agricultura_sostenible' => ['agricultura ecológica', 'orgánico', 'permacultura', 'agroecología'],
        ];

        $content = strtolower($this->title . ' ' . $this->summary . ' ' . $this->content);
        $foundTopics = [];

        foreach ($sustainabilityKeywords as $topic => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($content, strtolower($keyword))) {
                    $foundTopics[] = $topic;
                    break;
                }
            }
        }

        $this->sustainability_topics = array_unique($foundTopics);
        return $this->sustainability_topics;
    }

    /**
     * Calcular puntuación de impacto ambiental.
     */
    public function calculateEnvironmentalImpact()
    {
        $score = 0;
        
        // Puntuación base por categoría
        $categoryScores = [
            'sostenibilidad' => 10,
            'medio_ambiente' => 10,
            'energia' => 8,
            'tecnologia' => 5,
            'economia' => 3,
            'politica' => 3,
        ];

        $score += $categoryScores[$this->category] ?? 0;

        // Puntuación por temas de sostenibilidad
        if ($this->sustainability_topics) {
            $score += count($this->sustainability_topics) * 2;
        }

        // Puntuación por engagement (indica relevancia)
        if ($this->views_count > 1000) $score += 2;
        if ($this->shares_count > 50) $score += 2;

        // Puntuación por verificación
        if ($this->is_verified) $score += 1;

        $this->environmental_impact_score = min(10, $score);
        return $this->environmental_impact_score;
    }

    /**
     * Obtener artículos relacionados.
     */
    public function getRelatedArticles($limit = 5)
    {
        $query = self::published()
                    ->where('id', '!=', $this->id);

        // Prioridad: misma categoría
        if ($this->category) {
            $query->where('category', $this->category);
        }

        // Si tiene temas de sostenibilidad, buscar similares
        if ($this->sustainability_topics) {
            $query->orWhere(function($q) {
                foreach ($this->sustainability_topics as $topic) {
                    $q->orWhereJsonContains('sustainability_topics', $topic);
                }
            });
        }

        // Si tiene municipio, incluir noticias locales
        if ($this->municipality_id) {
            $query->orWhere('municipality_id', $this->municipality_id);
        }

        return $query->orderBy('published_at', 'desc')
                    ->limit($limit)
                    ->get();
    }

    /**
     * Obtener nivel de sentimiento.
     */
    public function getSentimentLevelAttribute()
    {
        if (!$this->sentiment_score) {
            return 'neutral';
        }

        if ($this->sentiment_score >= 0.5) return 'positivo';
        elseif ($this->sentiment_score <= -0.5) return 'negativo';
        else return 'neutral';
    }

    /**
     * Verificar si está actualmente destacado.
     */
    public function getIsCurrentlyFeaturedAttribute()
    {
        if (!$this->is_outstanding) {
            return false;
        }

        $now = now();
        
        $startOk = !$this->featured_start || $this->featured_start <= $now;
        $endOk = !$this->featured_end || $this->featured_end >= $now;
        
        return $startOk && $endOk;
    }

    /**
     * Obtener URL del artículo.
     */
    public function getUrlAttribute()
    {
        return "/noticias/{$this->slug}";
    }

    /**
     * Obtener datos para redes sociales.
     */
    public function getSocialShareDataAttribute()
    {
        return [
            'title' => $this->seo_title ?: $this->title,
            'description' => $this->seo_description ?: $this->generateExcerpt(160),
            'image' => $this->image?->url,
            'url' => $this->url,
        ];
    }

    /**
     * Obtener engagement rate.
     */
    public function getEngagementRateAttribute()
    {
        if ($this->views_count == 0) {
            return 0;
        }

        $totalEngagement = $this->shares_count + $this->comments_count;
        return round(($totalEngagement / $this->views_count) * 100, 2);
    }
}
