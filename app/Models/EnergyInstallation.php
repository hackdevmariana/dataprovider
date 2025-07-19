<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnergyInstallation extends Model
{
    protected $fillable = [
        'name', 'type', 'capacity_kw', 'location', 'owner_id', 'commissioned_at',
    ];

    protected $casts = [
        'capacity_kw' => 'float',
        'commissioned_at' => 'datetime',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
