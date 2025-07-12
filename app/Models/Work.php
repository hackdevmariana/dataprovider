<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Work extends Model
{
    protected $fillable = [
        'title', 'slug', 'type', 'description', 'release_year',
        'person_id', 'genre', 'language_id', 'link_id'
    ];

    protected $casts = [
        'release_year' => 'integer',
    ];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function link()
    {
        return $this->belongsTo(Link::class);
    }
}
