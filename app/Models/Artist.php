<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Artist extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'slug', 'description', 'birth_date', 'genre', 'person_id',
        'stage_name', 'group_name', 'active_years_start', 'active_years_end',
        'bio', 'photo', 'social_links', 'language_id'
    ];

    protected $casts = [
        'social_links' => 'array',
        'birth_date' => 'date',
        'active_years_start' => 'integer',
        'active_years_end' => 'integer',
    ];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'artist_event')->withPivot('role')->withTimestamps();
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'artist_group_member')->withPivot('joined_at', 'left_at')->withTimestamps();
    }
}
