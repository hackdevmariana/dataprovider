<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Region
 *
 * Represents a region within a country.
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int|null $province_id
 * @property int|null $autonomous_community_id
 * @property int|null $country_id
 * @property float|null $latitude
 * @property float|null $longitude
 * @property float|null $area_km2
 * @property float|null $altitude_m
 * @property int|null $timezone_id
 *
 * @property-read Province $province
 * @property-read AutonomousCommunity $autonomousCommunity
 * @property-read Country $country
 * @property-read Timezone $timezone
 */
class Region extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'province_id',
        'autonomous_community_id',
        'country_id',
        'latitude',
        'longitude',
        'area_km2',
        'altitude_m',
        'timezone_id',
    ];

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function autonomousCommunity()
    {
        return $this->belongsTo(AutonomousCommunity::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function timezone()
    {
        return $this->belongsTo(Timezone::class);
    }
}
