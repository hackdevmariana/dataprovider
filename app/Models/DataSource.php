<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataSource extends Model
{
    protected $fillable = [
        'name',
        'url',
        'description',
        'official',
        'country_code',
    ];

    public function stats()
    {
        return $this->hasMany(Stat::class);
    }
}
