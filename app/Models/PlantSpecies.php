<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlantSpecies extends Model
{
    protected $fillable = [
        'name', 'slug', 'co2_absorption_kg_per_year', 'description',
        'image_id', 'native_region_id', 'absorption_range_kg_year', 'is_endemic',
    ];

    public function image()
    {
        return $this->belongsTo(Image::class);
    }

    public function nativeRegion()
    {
        return $this->belongsTo(Region::class);
    }
}