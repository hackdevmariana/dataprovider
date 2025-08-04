<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'start_datetime',
        'end_datetime',
        'venue_id',
        'event_type_id',
        'festival_id',
        'language_id',
        'timezone_id',
        'municipality_id',
        'point_of_interest_id',
        'work_id',
        'price',
        'is_free',
        'audience_size_estimate',
        'source_url',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'is_free' => 'boolean',
        'price' => 'decimal:2',
        'audience_size_estimate' => 'integer',
    ];

    // Relaciones
    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    public function eventType()
    {
        return $this->belongsTo(EventType::class);
    }

    public function festival()
    {
        return $this->belongsTo(Festival::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function timezone()
    {
        return $this->belongsTo(Timezone::class);
    }

    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

    public function pointOfInterest()
    {
        return $this->belongsTo(PointOfInterest::class);
    }

    public function work()
    {
        return $this->belongsTo(Work::class);
    }



    public function artists()
    {
        return $this->belongsToMany(Artist::class, 'artist_event')->withPivot('role')->withTimestamps();
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
