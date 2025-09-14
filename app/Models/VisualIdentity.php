<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisualIdentity extends Model
{
    protected $fillable = ['name', 'description'];

    public function colors()
    {
        return $this->morphToMany(Color::class, 'colorable')
                    ->withPivot(['usage', 'is_primary', 'sort_order'])
                    ->withTimestamps();
    }

    public function fonts()
    {
        return $this->morphToMany(Font::class, 'fontable')
                    ->withPivot(['usage', 'is_primary', 'sort_order'])
                    ->withTimestamps();
    }
}
