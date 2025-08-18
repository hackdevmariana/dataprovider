<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Font extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'family', 'style', 'weight', 'license', 'source_url', 'is_default',
    ];

    protected $casts = [
        'weight' => 'integer',
        'is_default' => 'boolean',
    ];

    /**
     * Entidades que usan esta fuente.
     */
    public function visualIdentities()
    {
        return $this->morphedByMany(VisualIdentity::class, 'fontable')
                    ->withPivot(['usage', 'is_primary', 'sort_order'])
                    ->withTimestamps();
    }

    public function festivals()
    {
        return $this->morphedByMany(Festival::class, 'fontable')
                    ->withPivot(['usage', 'is_primary', 'sort_order'])
                    ->withTimestamps();
    }

    public function cooperatives()
    {
        return $this->morphedByMany(Cooperative::class, 'fontable')
                    ->withPivot(['usage', 'is_primary', 'sort_order'])
                    ->withTimestamps();
    }

    public function events()
    {
        return $this->morphedByMany(Event::class, 'fontable')
                    ->withPivot(['usage', 'is_primary', 'sort_order'])
                    ->withTimestamps();
    }

    public function artists()
    {
        return $this->morphedByMany(Artist::class, 'fontable')
                    ->withPivot(['usage', 'is_primary', 'sort_order'])
                    ->withTimestamps();
    }

    /**
     * Obtener la fuente como CSS font-family.
     */
    public function toCssFontFamily(): string
    {
        return "'{$this->family}', {$this->getFallbackFonts()}";
    }

    /**
     * Obtener fuentes de respaldo según el tipo.
     */
    private function getFallbackFonts(): string
    {
        $sansSerifFonts = ['Inter', 'Roboto', 'Open Sans', 'Lato', 'Montserrat'];
        $serifFonts = ['Playfair Display', 'Merriweather', 'Lora', 'Source Serif Pro'];
        $monospacefonts = ['Fira Code', 'Source Code Pro', 'Monaco', 'Consolas'];

        if (in_array($this->family, $monospacefonts)) {
            return 'monospace';
        } elseif (in_array($this->family, $serifFonts)) {
            return 'serif';
        } else {
            return 'sans-serif';
        }
    }

    /**
     * Obtener el CSS completo de la fuente.
     */
    public function toCss(): string
    {
        $css = "font-family: {$this->toCssFontFamily()};";
        
        if ($this->weight) {
            $css .= " font-weight: {$this->weight};";
        }
        
        if ($this->style && $this->style !== 'Regular') {
            $css .= " font-style: " . strtolower($this->style) . ";";
        }

        return $css;
    }

    /**
     * Verificar si es una fuente web.
     */
    public function isWebFont(): bool
    {
        return str_contains($this->source_url, 'fonts.googleapis.com') || 
               str_contains($this->source_url, 'fonts.adobe.com');
    }
}
