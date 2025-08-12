<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Venue extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'address',
        'municipality_id',
        'latitude',
        'longitude',
        'capacity',
        'description',
        'venue_type',
        'venue_status',
        'is_verified',
    ];

    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function venueType()
    {
        return $this->belongsTo(VenueType::class);
    }
}
