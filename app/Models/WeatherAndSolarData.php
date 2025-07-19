<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeatherAndSolarData extends Model
{
    protected $fillable = [
        'datetime', 'location', 'temperature', 'humidity', 'cloud_coverage',
        'solar_irradiance', 'wind_speed', 'precipitation',
    ];

    protected $casts = [
        'datetime' => 'datetime',
        'temperature' => 'float',
        'humidity' => 'float',
        'cloud_coverage' => 'float',
        'solar_irradiance' => 'float',
        'wind_speed' => 'float',
        'precipitation' => 'float',
    ];
}
