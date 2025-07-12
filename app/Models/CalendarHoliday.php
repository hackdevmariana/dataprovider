<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalendarHoliday extends Model
{
    protected $fillable = [
        'name',
        'date',
        'slug',
        'municipality_id',
    ];

    public function locations()
    {
        return $this->hasMany(CalendarHolidayLocation::class);
    }
}
