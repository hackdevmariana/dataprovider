<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserGeneratedContent extends Model
{
    protected $fillable = [
        'user_id',
        'related_type',
        'related_id',
        'content_type',
        'content',
        'language',
        'visibility',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function related()
    {
        return $this->morphTo();
    }
}
