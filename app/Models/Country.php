<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Country
 *
 * Represents a country.
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $iso_alpha2
 * @property string $iso_alpha3
 * @property string $iso_numeric
 * @property string|null $demonym
 * @property string|null $official_language
 * @property string|null $currency_code
 * @property string|null $phone_code
 * @property float|null $latitude
 * @property float|null $longitude
 * @property string|null $flag_url
 * @property int|null $population
 * @property float|null $gdp_usd
 * @property string|null $region_group
 * @property float|null $area_km2
 * @property float|null $altitude_m
 * @property int|null $timezone_id
 *
 * @property-read Timezone $timezone
 * @property-read \Illuminate\Database\Eloquent\Collection|Language[] $languages
 * @property-read \Illuminate\Database\Eloquent\Collection|Region[] $regions
 */
class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'iso_alpha2',
        'iso_alpha3',
        'iso_numeric',
        'demonym',
        'official_language',
        'currency_code',
        'phone_code',
        'latitude',
        'longitude',
        'flag_url',
        'population',
        'gdp_usd',
        'region_group',
        'area_km2',
        'altitude_m',
        'timezone_id',
    ];

    public function timezone()
    {
        return $this->belongsTo(Timezone::class);
    }

    public function languages()
    {
        return $this->belongsToMany(Language::class)->withPivot('is_official');
    }

    public function regions()
    {
        return $this->hasMany(Region::class);
    }
}
