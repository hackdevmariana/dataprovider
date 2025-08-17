<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class AutonomousCommunity
 *
 * Represents an autonomous community (Spain).
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $code
 * @property int|null $country_id
 * @property float|null $latitude
 * @property float|null $longitude
 * @property float|null $area_km2
 * @property float|null $altitude_m
 * @property int|null $timezone_id
 *
 * @property-read Country $country
 * @property-read Timezone $timezone
 * @property-read \Illuminate\Database\Eloquent\Collection|Province[] $provinces
 * @property-read \Illuminate\Database\Eloquent\Collection|Region[] $regions
 */
class AutonomousCommunity extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'code',
        'country_id',
        'latitude',
        'longitude',
        'area_km2',
        'altitude_m',
        'timezone_id',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function timezone()
    {
        return $this->belongsTo(Timezone::class);
    }

    public function provinces()
    {
        return $this->hasMany(Province::class);
    }
    public function regions()
    {
        return $this->hasMany(Region::class);
    }

    public function municipalities()
    {
        return $this->hasMany(Municipality::class);
    }
}
