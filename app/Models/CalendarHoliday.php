<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CalendarHoliday extends Model
{
    use HasFactory;
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

    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }
}
