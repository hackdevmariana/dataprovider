<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonWork extends Model
{
    protected $table = 'person_work';

    protected $fillable = [
        'person_id',
        'work_id',
        'role',
        'character_name',
        'credited_as',
        'billing_order',
        'contribution_pct',
        'is_primary',
        'notes',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'billing_order' => 'integer',
        'contribution_pct' => 'decimal:2',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function work(): BelongsTo
    {
        return $this->belongsTo(Work::class);
    }
}
