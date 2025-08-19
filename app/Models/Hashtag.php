<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Sistema de hashtags inteligente con trending y categorización.
 * 
 * Gestiona hashtags con auto-categorización, trending automático
 * y sugerencias inteligentes basadas en contenido.
 */
class Hashtag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'icon',
        'category',
        'usage_count',
        'posts_count',
        'followers_count',
        'trending_score',
        'is_trending',
        'is_verified',
        'is_blocked',
        'created_by',
        'related_hashtags',
        'synonyms',
        'auto_suggest',
    ];

    protected $casts = [
        'trending_score' => 'decimal:2',
        'is_trending' => 'boolean',
        'is_verified' => 'boolean',
        'is_blocked' => 'boolean',
        'related_hashtags' => 'array',
        'synonyms' => 'array',
        'auto_suggest' => 'boolean',
    ];

    /**
     * Usuario que creó el hashtag.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Contenidos que usan este hashtag.
     */
    public function contentHashtags(): HasMany
    {
        return $this->hasMany(ContentHashtag::class);
    }

    /**
     * Crear o encontrar hashtag por nombre.
     */
    public static function findOrCreateByName(string $name, ?User $creator = null): static
    {
        $cleanName = static::cleanHashtagName($name);
        $slug = Str::slug($cleanName);

        $hashtag = static::where('slug', $slug)->first();

        if (!$hashtag) {
            $hashtag = static::create([
                'name' => $cleanName,
                'slug' => $slug,
                'category' => static::detectCategory($cleanName),
                'created_by' => $creator?->id,
            ]);
        }

        return $hashtag;
    }

    /**
     * Limpiar nombre del hashtag.
     */
    public static function cleanHashtagName(string $name): string
    {
        // Remover # si existe, convertir a lowercase, limpiar caracteres especiales
        $clean = ltrim($name, '#');
        $clean = strtolower($clean);
        $clean = preg_replace('/[^a-z0-9\s]/', '', $clean);
        $clean = preg_replace('/\s+/', '', $clean); // Sin espacios
        
        return $clean;
    }

    /**
     * Detectar categoría automáticamente basada en el nombre.
     */
    public static function detectCategory(string $name): string
    {
        $keywords = [
            'technology' => ['solar', 'panel', 'inversor', 'bateria', 'smart', 'tech', 'iot'],
            'legislation' => ['rd244', 'ley', 'normativa', 'regulacion', 'legal', 'decreto'],
            'financing' => ['subvencion', 'ayuda', 'credito', 'financiacion', 'inversion'],
            'installation' => ['instalacion', 'montaje', 'mantenimiento', 'reparacion'],
            'cooperative' => ['cooperativa', 'comunidad', 'colectivo', 'compartido'],
            'market' => ['precio', 'mercado', 'tarifa', 'factura', 'ahorro'],
            'sustainability' => ['sostenible', 'verde', 'eco', 'medioambiente', 'co2'],
            'location' => ['madrid', 'barcelona', 'sevilla', 'valencia', 'andalucia'],
        ];

        foreach ($keywords as $category => $terms) {
            foreach ($terms as $term) {
                if (str_contains($name, $term)) {
                    return $category;
                }
            }
        }

        return 'general';
    }

    /**
     * Incrementar uso del hashtag.
     */
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
        $this->updateTrendingScore();
    }

    /**
     * Incrementar contador de posts.
     */
    public function incrementPosts(): void
    {
        $this->increment('posts_count');
        $this->updateTrendingScore();
    }

    /**
     * Actualizar score de trending.
     */
    public function updateTrendingScore(): void
    {
        // Algoritmo: uso reciente + posts + engagement
        $recentUsage = $this->contentHashtags()
                           ->where('created_at', '>=', now()->subDays(7))
                           ->count();
        
        $score = ($recentUsage * 10) + ($this->posts_count * 2) + $this->usage_count;
        
        $this->update([
            'trending_score' => $score,
            'is_trending' => $score > 50, // Umbral para trending
        ]);
    }

    /**
     * Obtener hashtags trending.
     */
    public static function getTrending(int $limit = 10)
    {
        return static::where('is_trending', true)
                    ->where('is_blocked', false)
                    ->orderBy('trending_score', 'desc')
                    ->limit($limit)
                    ->get();
    }

    /**
     * Obtener hashtags relacionados.
     */
    public function getRelated(int $limit = 5)
    {
        // Si tiene hashtags relacionados definidos manualmente
        if ($this->related_hashtags) {
            return static::whereIn('slug', $this->related_hashtags)
                        ->where('is_blocked', false)
                        ->limit($limit)
                        ->get();
        }

        // Si no, buscar por categoría similar
        return static::where('category', $this->category)
                    ->where('id', '!=', $this->id)
                    ->where('is_blocked', false)
                    ->orderBy('usage_count', 'desc')
                    ->limit($limit)
                    ->get();
    }

    /**
     * Buscar hashtags por término.
     */
    public static function search(string $term, int $limit = 10)
    {
        return static::where('name', 'like', "%{$term}%")
                    ->where('is_blocked', false)
                    ->where('auto_suggest', true)
                    ->orderBy('usage_count', 'desc')
                    ->limit($limit)
                    ->get();
    }

    /**
     * Obtener hashtags por categoría.
     */
    public static function getByCategory(string $category, int $limit = 20)
    {
        return static::where('category', $category)
                    ->where('is_blocked', false)
                    ->orderBy('usage_count', 'desc')
                    ->limit($limit)
                    ->get();
    }

    /**
     * Extraer hashtags de un texto.
     */
    public static function extractFromText(string $text): array
    {
        preg_match_all('/#([a-zA-Z0-9_]+)/', $text, $matches);
        return array_unique($matches[1]);
    }
}

