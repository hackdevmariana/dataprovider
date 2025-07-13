<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    protected $fillable = [
        'name', 'slug', 'hex_code', 'rgb_code', 'hsl_code',
        'is_primary', 'description',
    ];

    public function colorables()
    {
        return $this->morphedByMany(self::class, 'colorable');
    }
}
