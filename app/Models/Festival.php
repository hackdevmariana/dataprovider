<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Festival extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'slug', 'description', 'month', 'usual_days',
        'recurring', 'location_id', 'logo_url', 'color_theme'
    ];

    public function location()
    {
        return $this->belongsTo(Municipality::class, 'location_id');
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'festival_tag');
    }

    public function artists()
    {
        // Obtener artistas a través de los eventos y la tabla pivote artist_event
        return Artist::whereHas('events', function($q) {
            $q->where('festival_id', $this->id);
        });
    }
}
