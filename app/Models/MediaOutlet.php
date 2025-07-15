<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MediaOutlet extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'website',
        'headquarters_location',
        'municipality_id',
        'language',
        'circulation',
        'founding_year',
    ];

    protected $casts = [
        'circulation' => 'integer',
        'founding_year' => 'integer',
    ];

    // Relaciones

    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

    public function contacts()
    {
        return $this->hasMany(MediaContact::class);
    }

    public function newsArticles()
    {
        return $this->hasMany(NewsArticle::class);
    }
}
