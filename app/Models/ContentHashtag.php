<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Relación polimórfica entre hashtags y contenido.
 * 
 * Permite asociar hashtags a cualquier tipo de contenido
 * con métricas de relevancia y engagement.
 */
class ContentHashtag extends Model
{
    use HasFactory;

    protected $fillable = [
        'hashtag_id',
        'hashtaggable_type',
        'hashtaggable_id',
        'added_by',
        'clicks_count',
        'relevance_score',
        'is_auto_generated',
        'confidence_score',
    ];

    protected $casts = [
        'relevance_score' => 'decimal:2',
        'is_auto_generated' => 'boolean',
        'confidence_score' => 'decimal:2',
    ];

    /**
     * Hashtag asociado.
     */
    public function hashtag(): BelongsTo
    {
        return $this->belongsTo(Hashtag::class);
    }

    /**
     * Contenido que tiene el hashtag (polimórfico).
     */
    public function hashtaggable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Usuario que añadió el hashtag.
     */
    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    /**
     * Incrementar clicks en el hashtag.
     */
    public function incrementClicks(): void
    {
        $this->increment('clicks_count');
        $this->hashtag->incrementUsage();
    }

    /**
     * Asociar hashtags a contenido desde texto.
     */
    public static function attachHashtagsFromText(Model $content, string $text, User $user): array
    {
        $hashtagNames = Hashtag::extractFromText($text);
        $attached = [];

        foreach ($hashtagNames as $name) {
            $hashtag = Hashtag::findOrCreateByName($name, $user);
            
            $contentHashtag = static::firstOrCreate([
                'hashtag_id' => $hashtag->id,
                'hashtaggable_type' => get_class($content),
                'hashtaggable_id' => $content->id,
            ], [
                'added_by' => $user->id,
                'relevance_score' => 100,
                'is_auto_generated' => true,
            ]);

            $attached[] = $contentHashtag;
        }

        return $attached;
    }

    /**
     * Obtener hashtags más relevantes para un contenido.
     */
    public static function getMostRelevantForContent(Model $content, int $limit = 10)
    {
        return static::where('hashtaggable_type', get_class($content))
                    ->where('hashtaggable_id', $content->id)
                    ->with('hashtag')
                    ->orderBy('relevance_score', 'desc')
                    ->limit($limit)
                    ->get();
    }
}
