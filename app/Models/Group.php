<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = ['name', 'description'];

    public function members()
    {
        return $this->morphToMany(Artist::class, 'memberable'); // or belongsToMany if no morph
    }

    public function artists()
    {
        return $this->belongsToMany(Artist::class, 'artist_group_member')->withPivot('joined_at', 'left_at')->withTimestamps();
    }
}
