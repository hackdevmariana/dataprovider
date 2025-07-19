<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RelationshipType extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'reciprocal_slug',
        'category',
        'degree',
        'gender_specific',
        'description',
        'is_symmetrical',
    ];
}
