<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Group extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'slug', 'description', 'genre', 'formed_at', 'disbanded_at', 'active_status',
        'website', 'social_media', 'contact_email', 'management_company',
        'origin_country', 'origin_city', 'current_location', 'municipality_id',
        'record_label', 'albums_count', 'songs_count', 'awards', 'certifications',
        'biography', 'image_id', 'tags', 'search_boost',
        'official_fan_club', 'is_verified', 'is_featured', 'source', 'metadata'
    ];

    protected $casts = [
        'formed_at' => 'date',
        'disbanded_at' => 'date',
        'social_media' => 'array',
        'awards' => 'array',
        'certifications' => 'array',
        'tags' => 'array',
        'metadata' => 'array',
        'is_verified' => 'boolean',
        'is_featured' => 'boolean',
        'albums_count' => 'integer',
        'songs_count' => 'integer',
        'search_boost' => 'integer',
    ];

    public function members()
    {
        return $this->morphToMany(Artist::class, 'memberable'); // or belongsToMany if no morph
    }

    public function artists()
    {
        return $this->belongsToMany(Artist::class, 'artist_group_member')->withPivot('joined_at', 'left_at')->withTimestamps();
    }

    /**
     * Municipio de origen del grupo.
     */
    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

    /**
     * Imagen principal del grupo.
     */
    public function image()
    {
        return $this->belongsTo(Image::class);
    }

    /**
     * Obtener el estado actual del grupo en español.
     */
    public function getActiveStatusLabelAttribute(): string
    {
        return match ($this->active_status) {
            'active' => 'Activo',
            'inactive' => 'Inactivo',
            'disbanded' => 'Disuelto',
            'on_hiatus' => 'En Pausa',
            default => 'Desconocido',
        };
    }

    /**
     * Verificar si el grupo está activo.
     */
    public function isActive(): bool
    {
        return $this->active_status === 'active';
    }

    /**
     * Verificar si el grupo está disuelto.
     */
    public function isDisbanded(): bool
    {
        return $this->active_status === 'disbanded';
    }

    /**
     * Obtener la edad del grupo en años.
     */
    public function getAgeAttribute(): ?int
    {
        if (!$this->formed_at) {
            return null;
        }

        $endDate = $this->disbanded_at ?? now();
        return $this->formed_at->diffInYears($endDate);
    }

    /**
     * Obtener la duración de actividad del grupo.
     */
    public function getActivityDurationAttribute(): ?string
    {
        if (!$this->formed_at) {
            return null;
        }

        $endDate = $this->disbanded_at ?? now();
        $years = $this->formed_at->diffInYears($endDate);
        $months = $this->formed_at->diffInMonths($endDate) % 12;

        if ($years > 0 && $months > 0) {
            return "{$years} años y {$months} meses";
        } elseif ($years > 0) {
            return "{$years} años";
        } else {
            return "{$months} meses";
        }
    }

    /**
     * Obtener redes sociales como array asociativo.
     */
    public function getSocialMediaLinksAttribute(): array
    {
        if (!$this->social_media) {
            return [];
        }

        $links = [];
        foreach ($this->social_media as $platform => $url) {
            if (filter_var($url, FILTER_VALIDATE_URL)) {
                $links[$platform] = $url;
            }
        }

        return $links;
    }

    /**
     * Obtener premios como texto legible.
     */
    public function getAwardsTextAttribute(): string
    {
        if (!$this->awards || empty($this->awards)) {
            return 'Sin premios registrados';
        }

        return implode(', ', $this->awards);
    }

    /**
     * Obtener certificaciones como texto legible.
     */
    public function getCertificationsTextAttribute(): string
    {
        if (!$this->certifications || empty($this->certifications)) {
            return 'Sin certificaciones';
        }

        $text = [];
        foreach ($this->certifications as $album => $certifications) {
            $text[] = "{$album}: " . implode(', ', $certifications);
        }

        return implode('; ', $text);
    }

    /**
     * Scope para grupos activos.
     */
    public function scopeActive($query)
    {
        return $query->where('active_status', 'active');
    }

    /**
     * Scope para grupos por género.
     */
    public function scopeByGenre($query, $genre)
    {
        return $query->where('genre', $genre);
    }

    /**
     * Scope para grupos por país de origen.
     */
    public function scopeByOriginCountry($query, $country)
    {
        return $query->where('origin_country', $country);
    }

    /**
     * Scope para grupos verificados.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope para grupos destacados.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope para búsqueda de texto.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('description', 'LIKE', "%{$search}%")
              ->orWhere('biography', 'LIKE', "%{$search}%")
              ->orWhere('genre', 'LIKE', "%{$search}%")
              ->orWhere('origin_country', 'LIKE', "%{$search}%")
              ->orWhere('origin_city', 'LIKE', "%{$search}%");
        });
    }
}
