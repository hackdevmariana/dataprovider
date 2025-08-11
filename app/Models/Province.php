<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Province
 *
 * Represents a province.
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $ine_code
 * @property int|null $autonomous_community_id
 * @property int|null $country_id
 * @property float|null $latitude
 * @property float|null $longitude
 * @property float|null $area_km2
 * @property float|null $altitude_m
 * @property int|null $timezone_id
 *
 * @property-read AutonomousCommunity $autonomousCommunity
 * @property-read Country $country
 * @property-read \Illuminate\Database\Eloquent\Collection|Region[] $regions
 * @property-read Timezone $timezone
 * @property-read \Illuminate\Database\Eloquent\Collection|Municipality[] $municipalities
 */
class Province extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'ine_code',
        'autonomous_community_id',
        'country_id',
        'latitude',
        'longitude',
        'area_km2',
        'altitude_m',
        'timezone_id',
    ];

    public function autonomousCommunity()
    {
        return $this->belongsTo(AutonomousCommunity::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function regions()
    {
        return $this->hasMany(Region::class);
    }

    public function timezone()
    {
        return $this->belongsTo(Timezone::class);
    }

    public function municipalities()
    {
        return $this->hasMany(Municipality::class);
    }
}
