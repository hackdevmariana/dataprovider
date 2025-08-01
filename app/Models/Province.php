<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
