<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarbonSavingLog extends Model
{
    protected $fillable = [
        'user_id', 'cooperative_id', 'kw_installed', 'production_kwh',
        'co2_saved_kg', 'date_range_start', 'date_range_end',
        'estimation_source', 'carbon_saving_method', 'created_by_system',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function cooperative() { return $this->belongsTo(Cooperative::class); }
    public function equivalences() {
        return $this->belongsToMany(CarbonEquivalence::class, 'carbon_equivalence_log')
                    ->withPivot('quantity_equivalent')->withTimestamps();
    }
}