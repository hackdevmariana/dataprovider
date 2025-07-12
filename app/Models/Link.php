<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Link extends Model
{
    protected $fillable = [
        'url',
        'label',
        'related_type',
        'related_id',
        'type',
        'is_primary',
        'opens_in_new_tab',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'opens_in_new_tab' => 'boolean',
    ];

    public function related(): MorphTo
    {
        return $this->morphTo();
    }
}
