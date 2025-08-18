<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Contenido generado por usuarios para engagement y participación.
 * 
 * Sistema completo de gestión de contenido generado por usuarios
 * incluyendo comentarios, reseñas, reportes, preguntas, respuestas
 * y otros tipos de participación con moderación avanzada.
 * 
 * @property int $id
 * @property int|null $user_id Usuario que creó el contenido
 * @property string $related_type Tipo del modelo relacionado
 * @property int $related_id ID del modelo relacionado
 * @property string $content_type Tipo de contenido
 * @property string $content Contenido principal
 * @property string|null $title Título del contenido
 * @property string|null $excerpt Extracto del contenido
 * @property string $language Idioma del contenido
 * @property string $visibility Visibilidad del contenido
 * @property string $status Estado del contenido
 * @property int|null $parent_id Contenido padre (para respuestas)
 * @property float|null $rating Calificación (1-5)
 * @property array|null $metadata Metadatos adicionales
 * @property array|null $media_attachments Archivos adjuntos
 * @property string|null $user_name Nombre del usuario (si anónimo)
 * @property string|null $user_email Email del usuario (si anónimo)
 * @property string|null $user_ip IP del usuario
 * @property string|null $user_agent User agent del navegador
 * @property bool $is_anonymous Si es contenido anónimo
 * @property bool $is_verified Si está verificado
 * @property bool $is_featured Si está destacado
 * @property bool $is_spam Si es marcado como spam
 * @property bool $needs_moderation Si necesita moderación
 * @property int $likes_count Número de likes
 * @property int $dislikes_count Número de dislikes
 * @property int $replies_count Número de respuestas
 * @property int $reports_count Número de reportes
 * @property float|null $sentiment_score Puntuación de sentimiento
 * @property string|null $sentiment_label Etiqueta de sentimiento
 * @property array|null $moderation_notes Notas de moderación
 * @property array|null $auto_tags Tags automáticos
 * @property int|null $moderator_id Moderador que revisó
 * @property string|null $location_name Ubicación mencionada
 * @property float|null $latitude Latitud si tiene ubicación
 * @property float|null $longitude Longitud si tiene ubicación
 * @property \Carbon\Carbon|null $published_at Fecha de publicación
 * @property \Carbon\Carbon|null $moderated_at Fecha de moderación
 * @property \Carbon\Carbon|null $featured_until Destacado hasta
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * 
 * @property-read \App\Models\User|null $user
 * @property-read \Illuminate\Database\Eloquent\Model $related
 * @property-read \App\Models\UserGeneratedContent|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserGeneratedContent[] $replies
 * @property-read \App\Models\User|null $moderator
 */
class UserGeneratedContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'related_type',
        'related_id',
        'content_type',
        'content',
        'title',
        'excerpt',
        'language',
        'visibility',
        'status',
        'parent_id',
        'rating',
        'metadata',
        'media_attachments',
        'user_name',
        'user_email',
        'user_ip',
        'user_agent',
        'is_anonymous',
        'is_verified',
        'is_featured',
        'is_spam',
        'needs_moderation',
        'likes_count',
        'dislikes_count',
        'replies_count',
        'reports_count',
        'sentiment_score',
        'sentiment_label',
        'moderation_notes',
        'auto_tags',
        'moderator_id',
        'location_name',
        'latitude',
        'longitude',
        'published_at',
        'moderated_at',
        'featured_until',
    ];

    protected $casts = [
        'rating' => 'float',
        'metadata' => 'array',
        'media_attachments' => 'array',
        'is_anonymous' => 'boolean',
        'is_verified' => 'boolean',
        'is_featured' => 'boolean',
        'is_spam' => 'boolean',
        'needs_moderation' => 'boolean',
        'likes_count' => 'integer',
        'dislikes_count' => 'integer',
        'replies_count' => 'integer',
        'reports_count' => 'integer',
        'sentiment_score' => 'float',
        'moderation_notes' => 'array',
        'auto_tags' => 'array',
        'latitude' => 'float',
        'longitude' => 'float',
        'published_at' => 'datetime',
        'moderated_at' => 'datetime',
        'featured_until' => 'datetime',
    ];

    /**
     * Usuario que creó el contenido.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Modelo relacionado (polimórfico).
     */
    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Contenido padre (para respuestas).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Respuestas a este contenido.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
                    ->orderBy('created_at', 'asc');
    }

    /**
     * Moderador que revisó el contenido.
     */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }

    /**
     * Scope para contenido publicado.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope para contenido público.
     */
    public function scopePublic($query)
    {
        return $query->where('status', '!=', 'rejected');
    }

    /**
     * Scope para contenido por tipo.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('content_type', $type);
    }

    /**
     * Scope para comentarios.
     */
    public function scopeComments($query)
    {
        return $query->where('content_type', 'comment');
    }

    /**
     * Scope para reseñas.
     */
    public function scopeReviews($query)
    {
        return $query->where('content_type', 'review');
    }

    /**
     * Scope para reportes.
     */
    public function scopeReports($query)
    {
        return $query->where('content_type', 'report');
    }

    /**
     * Scope para preguntas.
     */
    public function scopeQuestions($query)
    {
        return $query->where('content_type', 'question');
    }

    /**
     * Scope para contenido verificado.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope para contenido destacado.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)
                    ->where(function($q) {
                        $q->whereNull('featured_until')
                          ->orWhere('featured_until', '>=', now());
                    });
    }

    /**
     * Scope para contenido que necesita moderación.
     */
    public function scopeNeedsModeration($query)
    {
        return $query->where('needs_moderation', true)
                    ->where('status', 'pending');
    }

    /**
     * Scope para contenido sin spam.
     */
    public function scopeNotSpam($query)
    {
        return $query->where('is_spam', false);
    }

    /**
     * Scope para contenido con alta puntuación.
     */
    public function scopeHighRated($query, $minRating = 4.0)
    {
        return $query->where('rating', '>=', $minRating);
    }

    /**
     * Scope para contenido con muchos likes.
     */
    public function scopePopular($query, $minLikes = 10)
    {
        return $query->where('likes_count', '>=', $minLikes)
                    ->orderBy('likes_count', 'desc');
    }

    /**
     * Scope para búsqueda de texto.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('content', 'LIKE', "%{$search}%")
              ->orWhere('title', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Scope para contenido reciente.
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope para contenido principal (no respuestas).
     */
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Incrementar contador de likes.
     */
    public function incrementLikes()
    {
        $this->increment('likes_count');
        return $this;
    }

    /**
     * Incrementar contador de dislikes.
     */
    public function incrementDislikes()
    {
        $this->increment('dislikes_count');
        return $this;
    }

    /**
     * Marcar como spam.
     */
    public function markAsSpam($moderatorId = null)
    {
        $this->update([
            'is_spam' => true,
            'status' => 'rejected',
            'moderator_id' => $moderatorId,
            'moderated_at' => now(),
        ]);

        return $this;
    }

    /**
     * Aprobar contenido.
     */
    public function approve($moderatorId = null)
    {
        $this->update([
            'status' => 'published',
            'needs_moderation' => false,
            'moderator_id' => $moderatorId,
            'moderated_at' => now(),
            'published_at' => now(),
        ]);

        return $this;
    }

    /**
     * Rechazar contenido.
     */
    public function reject($moderatorId = null, $reason = null)
    {
        $notes = $this->moderation_notes ?? [];
        if ($reason) {
            $notes[] = [
                'action' => 'rejected',
                'reason' => $reason,
                'moderator_id' => $moderatorId,
                'timestamp' => now()->toISOString(),
            ];
        }

        $this->update([
            'status' => 'rejected',
            'needs_moderation' => false,
            'moderator_id' => $moderatorId,
            'moderated_at' => now(),
            'moderation_notes' => $notes,
        ]);

        return $this;
    }

    /**
     * Destacar contenido.
     */
    public function feature($until = null)
    {
        $this->update([
            'is_featured' => true,
            'featured_until' => $until,
        ]);

        return $this;
    }

    /**
     * Analizar sentimiento del contenido.
     */
    public function analyzeSentiment()
    {
        // Análisis básico de sentimiento basado en palabras clave
        $content = strtolower($this->content . ' ' . ($this->title ?? ''));
        
        $positiveWords = ['excelente', 'genial', 'fantástico', 'bueno', 'increíble', 'perfecto', 'recomiendo', 'me gusta'];
        $negativeWords = ['malo', 'terrible', 'horrible', 'pésimo', 'no recomiendo', 'no me gusta', 'awful', 'problema'];
        
        $positiveCount = 0;
        $negativeCount = 0;
        
        foreach ($positiveWords as $word) {
            $positiveCount += substr_count($content, $word);
        }
        
        foreach ($negativeWords as $word) {
            $negativeCount += substr_count($content, $word);
        }
        
        $totalWords = $positiveCount + $negativeCount;
        
        if ($totalWords === 0) {
            $score = 0;
            $label = 'neutral';
        } else {
            $score = ($positiveCount - $negativeCount) / max($totalWords, 1);
            
            if ($score >= 0.3) {
                $label = 'positivo';
            } elseif ($score <= -0.3) {
                $label = 'negativo';
            } else {
                $label = 'neutral';
            }
        }
        
        $this->update([
            'sentiment_score' => round($score, 2),
            'sentiment_label' => $label,
        ]);
        
        return $this;
    }

    /**
     * Generar tags automáticos.
     */
    public function generateAutoTags()
    {
        $content = strtolower($this->content . ' ' . ($this->title ?? ''));
        $tags = [];
        
        // Tags de sostenibilidad
        $sustainabilityKeywords = [
            'energia_renovable' => ['solar', 'eólica', 'renovable'],
            'medio_ambiente' => ['contaminación', 'reciclaje', 'naturaleza'],
            'cambio_climatico' => ['clima', 'co2', 'emisiones'],
            'transporte_sostenible' => ['bicicleta', 'transporte público', 'eléctrico'],
        ];
        
        foreach ($sustainabilityKeywords as $tag => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($content, $keyword)) {
                    $tags[] = $tag;
                    break;
                }
            }
        }
        
        // Tags de sentimiento
        if ($this->sentiment_label) {
            $tags[] = 'sentimiento_' . $this->sentiment_label;
        }
        
        // Tags de tipo de contenido
        if ($this->rating) {
            $tags[] = 'con_calificacion';
            if ($this->rating >= 4) {
                $tags[] = 'alta_calificacion';
            }
        }
        
        $this->update(['auto_tags' => array_unique($tags)]);
        
        return $this;
    }

    /**
     * Verificar si necesita moderación automática.
     */
    public function shouldNeedModeration()
    {
        $needsModeration = false;
        
        // Contenido con palabras sospechosas
        $suspiciousWords = ['spam', 'viagra', 'casino', 'lotería', 'dinero fácil'];
        $content = strtolower($this->content);
        
        foreach ($suspiciousWords as $word) {
            if (str_contains($content, $word)) {
                $needsModeration = true;
                break;
            }
        }
        
        // Contenido muy negativo
        if ($this->sentiment_score && $this->sentiment_score <= -0.7) {
            $needsModeration = true;
        }
        
        // Usuarios anónimos siempre necesitan moderación
        if ($this->is_anonymous) {
            $needsModeration = true;
        }
        
        // Contenido con múltiples enlaces
        if (substr_count($this->content, 'http') > 2) {
            $needsModeration = true;
        }
        
        $this->update(['needs_moderation' => $needsModeration]);
        
        return $needsModeration;
    }

    /**
     * Obtener información del autor.
     */
    public function getAuthorInfoAttribute()
    {
        if ($this->user) {
            return [
                'name' => $this->user->name,
                'email' => $this->user->email,
                'is_registered' => true,
                'avatar' => $this->user->avatar ?? null,
            ];
        }
        
        return [
            'name' => $this->user_name,
            'email' => $this->user_email,
            'is_registered' => false,
            'avatar' => null,
        ];
    }

    /**
     * Obtener métricas de engagement.
     */
    public function getEngagementMetricsAttribute()
    {
        $totalVotes = $this->likes_count + $this->dislikes_count;
        
        return [
            'likes' => $this->likes_count,
            'dislikes' => $this->dislikes_count,
            'replies' => $this->replies_count,
            'reports' => $this->reports_count,
            'total_votes' => $totalVotes,
            'approval_rate' => $totalVotes > 0 ? round(($this->likes_count / $totalVotes) * 100, 1) : 0,
            'engagement_score' => $this->calculateEngagementScore(),
        ];
    }

    /**
     * Obtener estado del contenido.
     */
    public function getContentStatusAttribute()
    {
        return [
            'status' => $this->status,
            'visibility' => $this->visibility,
            'is_published' => $this->status === 'published',
            'is_featured' => $this->is_featured && (!$this->featured_until || $this->featured_until >= now()),
            'is_verified' => $this->is_verified,
            'needs_moderation' => $this->needs_moderation,
            'is_spam' => $this->is_spam,
            'moderated_at' => $this->moderated_at?->diffForHumans(),
        ];
    }

    /**
     * Calcular puntuación de engagement.
     */
    private function calculateEngagementScore()
    {
        $score = 0;
        
        // Puntos por likes (peso mayor)
        $score += $this->likes_count * 2;
        
        // Puntos por respuestas (indica conversación)
        $score += $this->replies_count * 3;
        
        // Penalización por dislikes
        $score -= $this->dislikes_count * 1;
        
        // Penalización por reportes
        $score -= $this->reports_count * 5;
        
        // Bonificación por verificación
        if ($this->is_verified) {
            $score += 5;
        }
        
        // Bonificación por calificación alta
        if ($this->rating && $this->rating >= 4) {
            $score += 3;
        }
        
        return max(0, $score);
    }

    /**
     * Obtener tipo de contenido en español.
     */
    public function getContentTypeNameAttribute()
    {
        $types = [
            'comment' => 'Comentario',
            'review' => 'Reseña',
            'question' => 'Pregunta',
            'answer' => 'Respuesta',
            'report' => 'Reporte',
            'suggestion' => 'Sugerencia',
            'testimonial' => 'Testimonio',
            'complaint' => 'Queja',
            'compliment' => 'Elogio',
        ];

        return $types[$this->content_type] ?? 'Desconocido';
    }

    /**
     * Obtener URL del contenido relacionado.
     */
    public function getRelatedUrlAttribute()
    {
        if ($this->related_type === 'App\Models\NewsArticle' && $this->related) {
            return "/noticias/{$this->related->slug}";
        }
        
        // Agregar más tipos según sea necesario
        return null;
    }
}
