<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Award extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'awarded_by',
        'first_year_awarded',
        'category',
    ];

    public function awardWinners(): HasMany
    {
        return $this->hasMany(AwardWinner::class);
    }
}
