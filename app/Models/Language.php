<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Language extends Model
{
    use HasFactory;

    protected $fillable = [
        'language',
        'slug',
        'native_name',
        'iso_639_1',
        'iso_639_2',
        'rtl',
    ];

    public function countries()
    {
        return $this->belongsToMany(Country::class)->withPivot('is_official');
    }
}
