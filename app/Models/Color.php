<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Color extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'hex_code', 'rgb_code', 'hsl_code',
        'is_primary', 'description',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    /**
     * Entidades que usan este color.
     */
    public function visualIdentities()
    {
        return $this->morphedByMany(VisualIdentity::class, 'colorable')
                    ->withPivot(['usage', 'is_primary', 'sort_order'])
                    ->withTimestamps();
    }

    public function festivals()
    {
        return $this->morphedByMany(Festival::class, 'colorable')
                    ->withPivot(['usage', 'is_primary', 'sort_order'])
                    ->withTimestamps();
    }

    public function cooperatives()
    {
        return $this->morphedByMany(Cooperative::class, 'colorable')
                    ->withPivot(['usage', 'is_primary', 'sort_order'])
                    ->withTimestamps();
    }

    public function events()
    {
        return $this->morphedByMany(Event::class, 'colorable')
                    ->withPivot(['usage', 'is_primary', 'sort_order'])
                    ->withTimestamps();
    }

    public function artists()
    {
        return $this->morphedByMany(Artist::class, 'colorable')
                    ->withPivot(['usage', 'is_primary', 'sort_order'])
                    ->withTimestamps();
    }

    /**
     * Obtener el color como array RGB.
     */
    public function getRgbArray(): array
    {
        $hex = ltrim($this->hex_code, '#');
        return [
            'r' => hexdec(substr($hex, 0, 2)),
            'g' => hexdec(substr($hex, 2, 2)),
            'b' => hexdec(substr($hex, 4, 2)),
        ];
    }

    /**
     * Obtener el color como string CSS.
     */
    public function toCss(): string
    {
        return $this->hex_code;
    }

    /**
     * Verificar si el color es oscuro.
     */
    public function isDark(): bool
    {
        $rgb = $this->getRgbArray();
        $brightness = ($rgb['r'] * 299 + $rgb['g'] * 587 + $rgb['b'] * 114) / 1000;
        return $brightness < 128;
    }
}
