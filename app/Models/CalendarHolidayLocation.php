<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalendarHolidayLocation extends Model
{
    protected $fillable = [
        'calendar_holiday_id',
        'municipality_id',
        'province_id',
        'autonomous_community_id',
        'country_id',
    ];

    public function holiday(): BelongsTo
    {
        return $this->belongsTo(CalendarHoliday::class, 'calendar_holiday_id');
    }

    public function municipality(): BelongsTo
    {
        return $this->belongsTo(Municipality::class);
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function autonomousCommunity(): BelongsTo
    {
        return $this->belongsTo(AutonomousCommunity::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
