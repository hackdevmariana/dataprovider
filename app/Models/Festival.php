<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Festival extends Model
{
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
        // You can define a relation via events or directly
        return $this->hasManyThrough(Artist::class, Event::class);
    }
}
