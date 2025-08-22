<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonProfession extends Model
{
    protected $table = 'person_profession';

    protected $fillable = [
        'person_id',
        'profession_id',
        'start_year',
        'end_year',
        'is_primary',
        'is_current',
        'notes',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_current' => 'boolean',
        'start_year' => 'integer',
        'end_year' => 'integer',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function profession(): BelongsTo
    {
        return $this->belongsTo(Profession::class);
    }
}
