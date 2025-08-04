<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'tag_type',
        'is_searchable',
    ];

    /**
     * Relación específica con PointOfInterest (no polimórfica)
     */
    public function pointOfInterests()
    {
        return $this->belongsToMany(PointOfInterest::class, 'point_of_interest_tag');
    }

    /**
     * Relaciones polimórficas con otros modelos
     */
    public function events()
    {
        return $this->morphedByMany(Event::class, 'taggable');
    }

    public function works()
    {
        return $this->morphedByMany(Work::class, 'taggable');
    }

    public function people()
    {
        return $this->morphedByMany(Person::class, 'taggable');
    }

    public function anniversaries()
    {
        return $this->morphedByMany(Anniversary::class, 'taggable');
    }

    public function images()
    {
        return $this->morphedByMany(Image::class, 'taggable');
    }
}
