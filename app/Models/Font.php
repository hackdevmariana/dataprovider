<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Font extends Model
{
    protected $fillable = [
        'name', 'family', 'style', 'weight', 'license', 'source_url', 'is_default',
    ];

    public function fontables()
    {
        return $this->morphedByMany(self::class, 'fontable');
    }
}
