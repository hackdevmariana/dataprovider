<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsArticle extends Model
{
    protected $fillable = [
        'title', 'slug', 'summary', 'content', 'source_url',
        'published_at', 'featured_start', 'featured_end',
        'media_outlet_id', 'author_id', 'municipality_id', 'language_id',
        'image_id', 'tag_id',
        'is_outstanding', 'is_verified', 'is_scraped', 'is_translated',
        'visibility', 'views_count', 'tags',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'featured_start' => 'datetime',
        'featured_end' => 'datetime',
        'is_outstanding' => 'boolean',
        'is_verified' => 'boolean',
        'is_scraped' => 'boolean',
        'is_translated' => 'boolean',
        'tags' => 'array',
    ];

    public function mediaOutlet()
    {
        return $this->belongsTo(MediaOutlet::class);
    }

    public function author()
    {
        return $this->belongsTo(Person::class, 'author_id');
    }

    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function image()
    {
        return $this->belongsTo(Image::class);
    }

    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }
}
