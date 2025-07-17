<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appearance extends Model
{
    protected $table = 'person_physical_profiles';

    protected $fillable = [
        'person_id',
        'height_cm',
        'weight_kg',
        'body_type',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }
}
