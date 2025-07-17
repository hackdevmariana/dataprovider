<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialAccount extends Model
{
    protected $fillable = [
        'person_id',
        'platform',
        'handle',
        'url',
    ];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}
