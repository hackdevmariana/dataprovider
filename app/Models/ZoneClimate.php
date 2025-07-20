<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZoneClimate extends Model
{
    protected $fillable = [
        'name', 'avg_kwh_per_kw_year', 'description',
    ];
}