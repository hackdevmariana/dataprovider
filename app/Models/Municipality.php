<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Municipality extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'ine_code',
        'postal_code',
        'population',
        'mayor_name',
        'mayor_salary',
        'latitude',
        'longitude',
        'area_km2',
        'altitude_m',
        'is_capital',
        'tourism_info',
        'region_id',
        'province_id',
        'autonomous_community_id',
        'country_id',
        'timezone_id',
    ];

    public function timezone()
    {
        return $this->belongsTo(Timezone::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

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

    public function pointsOfInterest()
    {
        return $this->hasMany(PointOfInterest::class);
    }
}
