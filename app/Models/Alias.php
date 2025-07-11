<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alias extends Model
{
    protected $fillable = [
        'name',
        'type',
        'is_primary',
        'person_id',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function person(): BelongsTo {
        return $this->belongsTo(Person::class);
    }
}
