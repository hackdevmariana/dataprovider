<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TagGroup extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];
}
