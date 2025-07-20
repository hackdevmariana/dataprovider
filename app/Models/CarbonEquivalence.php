<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarbonEquivalence extends Model
{
    protected $fillable = [
        'name', 'slug', 'co2_kg_equivalent', 'description',
        'category', 'efficiency_ratio', 'loss_factor',
    ];

    public function savingLogs() {
        return $this->belongsToMany(CarbonSavingLog::class, 'carbon_equivalence_log')
                    ->withPivot('quantity_equivalent')->withTimestamps();
    }
}
