<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SocialAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'person_id',
        'platform',
        'handle',
        'url',
        'followers_count',
        'verified',
        'is_public',
    ];

    protected $casts = [
        'verified' => 'boolean',
        'is_public' => 'boolean',
    ];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}
