<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Work
 *
 * Represents a work (book, film, etc.) associated with a person.
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $type
 * @property string|null $description
 * @property int|null $release_year
 * @property int|null $person_id
 * @property string|null $genre
 * @property int|null $language_id
 * @property int|null $link_id
 *
 * @property-read Person $person
 * @property-read Language $language
 * @property-read Link $link
 */
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
