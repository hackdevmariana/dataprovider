<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VenueType extends Model
{
    protected $fillable = ['name', 'slug', 'description'];

    public function venues()
    {
        return $this->hasMany(Venue::class);
    }
}
