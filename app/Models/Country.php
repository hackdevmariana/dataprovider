<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
}
