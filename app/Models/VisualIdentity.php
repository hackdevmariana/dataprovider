<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisualIdentity extends Model
{
    protected $fillable = ['name', 'description'];

    public function colors()
    {
        return $this->morphToMany(Color::class, 'colorable');
    }

    public function fonts()
    {
        return $this->morphToMany(Font::class, 'fontable');
    }
}
