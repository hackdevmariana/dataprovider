<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Person extends Model
{
    protected $fillable = [
        'name',
        'birth_name',
        'slug',
        'birth_date',
        'death_date',
        'birth_place',
        'death_place',
        'nationality_id',
        'language_id',
        'image_id',
        'gender',
        'official_website',
        'wikidata_id',
        'wikipedia_url',
        'notable_for',
        'occupation_summary',
        'social_handles',
        'is_influencer',
        'search_boost',
        'short_bio',
        'long_bio',
        'source_url',
        'last_updated_from_source',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'death_date' => 'date',
        'is_influencer' => 'boolean',
        'social_handles' => 'array',
        'last_updated_from_source' => 'datetime',
    ];

    public function nationality(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'nationality_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class);
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
