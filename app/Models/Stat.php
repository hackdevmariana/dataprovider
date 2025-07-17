<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stat extends Model
{
    protected $fillable = [
        'subject_type',
        'subject_id',
        'value',
        'unit',
        'source_id',
        'recorded_at',
        'note',
    ];

    public function subject()
    {
        return $this->morphTo();
    }

    public function source()
    {
        return $this->belongsTo(DataSource::class, 'source_id');
    }
}
