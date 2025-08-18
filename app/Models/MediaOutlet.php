<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Medios de comunicación para gestión de fuentes mediáticas.
 * 
 * Sistema completo de gestión de medios de comunicación con
 * clasificación por tipo, alcance, especialización temática
 * y métricas de influencia y credibilidad.
 * 
 * @property int $id
 * @property string $name Nombre del medio
 * @property string $slug Slug único para URL
 * @property string $type Tipo de medio
 * @property string $media_category Categoría mediática
 * @property string|null $description Descripción del medio
 * @property string|null $website URL del sitio web
 * @property string|null $rss_feed URL del feed RSS
 * @property string|null $headquarters_location Ubicación sede
 * @property int|null $municipality_id Municipio sede
 * @property string|null $coverage_scope Alcance de cobertura
 * @property array|null $languages Idiomas de publicación
 * @property int|null $circulation Tirada/audiencia
 * @property string|null $circulation_type Tipo de circulación
 * @property int|null $founding_year Año de fundación
 * @property string|null $owner_company Empresa propietaria
 * @property string|null $political_leaning Orientación política
 * @property array|null $specializations Especializaciones temáticas
 * @property bool $is_digital_native Si es nativo digital
 * @property bool $is_verified Medio verificado
 * @property bool $is_active Medio activo
 * @property bool $covers_sustainability Si cubre sostenibilidad
 * @property float|null $credibility_score Puntuación credibilidad
 * @property float|null $influence_score Puntuación influencia
 * @property float|null $sustainability_focus Enfoque sostenibilidad
 * @property int $articles_count Número de artículos
 * @property int $monthly_pageviews Visitas mensuales
 * @property int $social_media_followers Seguidores redes sociales
 * @property array|null $social_media_handles Cuentas redes sociales
 * @property string|null $contact_email Email de contacto
 * @property string|null $press_contact_name Nombre contacto prensa
 * @property string|null $press_contact_email Email contacto prensa
 * @property string|null $press_contact_phone Teléfono contacto prensa
 * @property array|null $editorial_team Equipo editorial
 * @property string|null $content_licensing Licencias contenido
 * @property bool $allows_reprints Si permite reimpresiones
 * @property array|null $api_access Acceso a API
 * @property string|null $logo_url URL del logo
 * @property \Carbon\Carbon|null $last_scraped_at Último scraping
 * @property \Carbon\Carbon|null $verified_at Fecha verificación
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * 
 * @property-read \App\Models\Municipality|null $municipality
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\MediaContact[] $contacts
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\NewsArticle[] $newsArticles
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tag[] $specializedTags
 */
class MediaOutlet extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'media_category',
        'description',
        'website',
        'rss_feed',
        'headquarters_location',
        'municipality_id',
        'coverage_scope',
        'languages',
        'circulation',
        'circulation_type',
        'founding_year',
        'owner_company',
        'political_leaning',
        'specializations',
        'is_digital_native',
        'is_verified',
        'is_active',
        'covers_sustainability',
        'credibility_score',
        'influence_score',
        'sustainability_focus',
        'articles_count',
        'monthly_pageviews',
        'social_media_followers',
        'social_media_handles',
        'contact_email',
        'press_contact_name',
        'press_contact_email',
        'press_contact_phone',
        'editorial_team',
        'content_licensing',
        'allows_reprints',
        'api_access',
        'logo_url',
        'last_scraped_at',
        'verified_at',
    ];

    protected $casts = [
        'languages' => 'array',
        'circulation' => 'integer',
        'founding_year' => 'integer',
        'specializations' => 'array',
        'is_digital_native' => 'boolean',
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
        'covers_sustainability' => 'boolean',
        'credibility_score' => 'float',
        'influence_score' => 'float',
        'sustainability_focus' => 'float',
        'articles_count' => 'integer',
        'monthly_pageviews' => 'integer',
        'social_media_followers' => 'integer',
        'social_media_handles' => 'array',
        'editorial_team' => 'array',
        'allows_reprints' => 'boolean',
        'api_access' => 'array',
        'last_scraped_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    /**
     * Municipio donde tiene sede el medio.
     */
    public function municipality(): BelongsTo
    {
        return $this->belongsTo(Municipality::class);
    }

    /**
     * Contactos de prensa del medio.
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(MediaContact::class);
    }

    /**
     * Artículos publicados por este medio.
     */
    public function newsArticles(): HasMany
    {
        return $this->hasMany(NewsArticle::class);
    }

    /**
     * Tags especializados del medio.
     */
    public function specializedTags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'media_outlet_tag')
                    ->withTimestamps();
    }

    /**
     * Scope para medios activos.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para medios verificados.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope para medios por tipo.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope para medios especializados en sostenibilidad.
     */
    public function scopeSustainabilityFocused($query)
    {
        return $query->where('covers_sustainability', true)
                    ->orWhere('sustainability_focus', '>=', 0.5);
    }

    /**
     * Scope para medios con alta credibilidad.
     */
    public function scopeHighCredibility($query, $minScore = 7.0)
    {
        return $query->where('credibility_score', '>=', $minScore);
    }

    /**
     * Scope para medios influyentes.
     */
    public function scopeInfluential($query, $minScore = 7.0)
    {
        return $query->where('influence_score', '>=', $minScore);
    }

    /**
     * Scope para medios nativos digitales.
     */
    public function scopeDigitalNative($query)
    {
        return $query->where('is_digital_native', true);
    }

    /**
     * Scope para medios locales.
     */
    public function scopeLocal($query)
    {
        return $query->where('coverage_scope', 'local');
    }

    /**
     * Scope para medios nacionales.
     */
    public function scopeNational($query)
    {
        return $query->where('coverage_scope', 'nacional');
    }

    /**
     * Calcular puntuación de credibilidad.
     */
    public function calculateCredibilityScore()
    {
        $score = 5.0; // Base neutral
        
        // Años de existencia (máximo +2 puntos)
        if ($this->founding_year) {
            $yearsActive = date('Y') - $this->founding_year;
            $score += min(2, $yearsActive / 25);
        }
        
        // Verificación (+1 punto)
        if ($this->is_verified) {
            $score += 1;
        }
        
        // Transparencia contactos (+0.5 puntos)
        if ($this->press_contact_email && $this->press_contact_name) {
            $score += 0.5;
        }
        
        // Engagement alto (+1 punto)
        if ($this->monthly_pageviews > 100000) {
            $score += 1;
        }
        
        // Múltiples especializaciones (+0.5 puntos)
        if ($this->specializations && count($this->specializations) >= 3) {
            $score += 0.5;
        }
        
        $this->credibility_score = round(min(10, $score), 1);
        return $this->credibility_score;
    }

    /**
     * Calcular puntuación de influencia.
     */
    public function calculateInfluenceScore()
    {
        $score = 0;
        
        // Audiencia mensual
        $pageviews = $this->monthly_pageviews ?? 0;
        if ($pageviews > 1000000) $score += 4;
        elseif ($pageviews > 500000) $score += 3;
        elseif ($pageviews > 100000) $score += 2;
        elseif ($pageviews > 10000) $score += 1;
        
        // Seguidores redes sociales
        $followers = $this->social_media_followers ?? 0;
        if ($followers > 500000) $score += 3;
        elseif ($followers > 100000) $score += 2;
        elseif ($followers > 10000) $score += 1;
        
        // Número de artículos (actividad)
        if ($this->articles_count > 1000) $score += 2;
        elseif ($this->articles_count > 100) $score += 1;
        
        // Alcance nacional vs local
        if ($this->coverage_scope === 'nacional' || $this->coverage_scope === 'internacional') {
            $score += 1;
        }
        
        $this->influence_score = round(min(10, $score), 1);
        return $this->influence_score;
    }

    /**
     * Analizar enfoque en sostenibilidad.
     */
    public function analyzeSustainabilityFocus()
    {
        $score = 0;
        
        // Especialización explícita
        if ($this->covers_sustainability) {
            $score += 0.5;
        }
        
        // Especializaciones relacionadas
        if ($this->specializations) {
            $sustainabilityTopics = ['medio_ambiente', 'energia', 'sostenibilidad', 'cambio_climatico', 'tecnologia_verde'];
            $matches = array_intersect($this->specializations, $sustainabilityTopics);
            $score += count($matches) * 0.2;
        }
        
        // Análisis de artículos (requiere artículos existentes)
        $sustainabilityArticles = $this->newsArticles()
                                      ->whereNotNull('sustainability_topics')
                                      ->count();
        
        if ($this->articles_count > 0) {
            $sustainabilityRatio = $sustainabilityArticles / $this->articles_count;
            $score += $sustainabilityRatio * 0.5;
        }
        
        $this->sustainability_focus = round(min(1, $score), 2);
        return $this->sustainability_focus;
    }

    /**
     * Obtener artículos recientes.
     */
    public function getRecentArticles($days = 7, $limit = 10)
    {
        return $this->newsArticles()
                   ->published()
                   ->where('published_at', '>', now()->subDays($days))
                   ->orderBy('published_at', 'desc')
                   ->limit($limit)
                   ->get();
    }

    /**
     * Obtener artículos más populares.
     */
    public function getPopularArticles($limit = 10)
    {
        return $this->newsArticles()
                   ->published()
                   ->orderBy('views_count', 'desc')
                   ->limit($limit)
                   ->get();
    }

    /**
     * Obtener artículos sobre sostenibilidad.
     */
    public function getSustainabilityArticles($limit = 10)
    {
        return $this->newsArticles()
                   ->published()
                   ->sustainability()
                   ->orderBy('published_at', 'desc')
                   ->limit($limit)
                   ->get();
    }

    /**
     * Obtener contacto principal de prensa.
     */
    public function getPrimaryPressContactAttribute()
    {
        return [
            'name' => $this->press_contact_name,
            'email' => $this->press_contact_email,
            'phone' => $this->press_contact_phone,
        ];
    }

    /**
     * Obtener perfil de audiencia.
     */
    public function getAudienceProfileAttribute()
    {
        return [
            'monthly_pageviews' => $this->monthly_pageviews,
            'social_followers' => $this->social_media_followers,
            'articles_published' => $this->articles_count,
            'circulation' => $this->circulation,
            'circulation_type' => $this->circulation_type,
        ];
    }

    /**
     * Obtener información de especialización.
     */
    public function getSpecializationInfoAttribute()
    {
        return [
            'primary_topics' => $this->specializations,
            'covers_sustainability' => $this->covers_sustainability,
            'sustainability_focus' => $this->sustainability_focus,
            'sustainability_articles_count' => $this->newsArticles()->sustainability()->count(),
        ];
    }

    /**
     * Obtener métricas de calidad.
     */
    public function getQualityMetricsAttribute()
    {
        return [
            'credibility_score' => $this->credibility_score,
            'influence_score' => $this->influence_score,
            'is_verified' => $this->is_verified,
            'years_active' => $this->founding_year ? (date('Y') - $this->founding_year) : null,
            'avg_article_engagement' => $this->getAverageArticleEngagement(),
        ];
    }

    /**
     * Calcular engagement promedio de artículos.
     */
    private function getAverageArticleEngagement()
    {
        $articles = $this->newsArticles()->published()->get();
        
        if ($articles->isEmpty()) {
            return 0;
        }
        
        $totalEngagement = $articles->sum(function($article) {
            return $article->views_count + $article->shares_count + $article->comments_count;
        });
        
        return round($totalEngagement / $articles->count(), 2);
    }

    /**
     * Verificar si es medio de referencia.
     */
    public function getIsReferenceMediaAttribute()
    {
        return $this->is_verified && 
               $this->credibility_score >= 8.0 && 
               $this->influence_score >= 7.0;
    }

    /**
     * Obtener tipo de medio en español.
     */
    public function getTypeNameAttribute()
    {
        $types = [
            'newspaper' => 'Periódico',
            'magazine' => 'Revista',
            'tv' => 'Televisión',
            'radio' => 'Radio',
            'digital' => 'Medio Digital',
            'news_agency' => 'Agencia de Noticias',
            'blog' => 'Blog',
            'podcast' => 'Podcast',
        ];

        return $types[$this->type] ?? 'Desconocido';
    }

    /**
     * Obtener alcance en español.
     */
    public function getCoverageScopeNameAttribute()
    {
        $scopes = [
            'local' => 'Local',
            'regional' => 'Regional',
            'nacional' => 'Nacional',
            'internacional' => 'Internacional',
        ];

        return $scopes[$this->coverage_scope] ?? 'No especificado';
    }
}
