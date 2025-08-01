<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
}
