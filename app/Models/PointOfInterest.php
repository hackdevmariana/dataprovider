<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PointOfInterest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'address',
        'type',
        'latitude',
        'longitude',
        'municipality_id',
        'source',
        'description',
        'is_cultural_center',
        'is_energy_installation',
        'is_cooperative_office',
        'opening_hours',
    ];

    protected $casts = [
        'opening_hours' => 'array',
        'is_cultural_center' => 'boolean',
        'is_energy_installation' => 'boolean',
        'is_cooperative_office' => 'boolean',
    ];

    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'point_of_interest_tag');
    }
}
