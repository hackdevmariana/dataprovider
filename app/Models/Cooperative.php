<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cooperative extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'legal_name',
        'cooperative_type',
        'scope',
        'nif',
        'founded_at',
        'phone',
        'email',
        'website',
        'logo_url',
        'image_id',
        'municipality_id',
        'address',
        'latitude',
        'longitude',
        'description',
        'number_of_members',
        'main_activity',
        'is_open_to_new_members',
        'source',
        'data_source_id',
        'has_energy_market_access',
        'legal_form',
        'statutes_url',
        'accepts_new_installations',
    ];

    protected $casts = [
        'founded_at' => 'date',
        'latitude' => 'float',
        'longitude' => 'float',
        'is_open_to_new_members' => 'boolean',
        'has_energy_market_access' => 'boolean',
        'accepts_new_installations' => 'boolean',
    ];

    public function municipality(): BelongsTo
    {
        return $this->belongsTo(Municipality::class);
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class);
    }

    public function dataSource(): BelongsTo
    {
        return $this->belongsTo(DataSource::class);
    }
}
