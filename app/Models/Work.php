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

    /**
     * Personas que han participado en esta obra.
     */
    public function people()
    {
        return $this->belongsToMany(Person::class, 'person_work')
                    ->withPivot([
                        'role',
                        'character_name',
                        'credited_as',
                        'billing_order',
                        'contribution_pct',
                        'is_primary',
                        'notes'
                    ])
                    ->withTimestamps();
    }

    /**
     * Protagonistas de la obra.
     */
    public function leads()
    {
        return $this->belongsToMany(Person::class, 'person_work')
                    ->wherePivot('is_primary', true)
                    ->withPivot([
                        'role',
                        'character_name',
                        'credited_as',
                        'billing_order'
                    ])
                    ->orderByPivot('billing_order')
                    ->withTimestamps();
    }

    /**
     * Cast completo ordenado por importancia.
     */
    public function cast()
    {
        return $this->belongsToMany(Person::class, 'person_work')
                    ->wherePivot('role', 'actor')
                    ->withPivot([
                        'character_name',
                        'credited_as',
                        'billing_order'
                    ])
                    ->orderByPivot('billing_order')
                    ->withTimestamps();
    }

    /**
     * Directores de la obra.
     */
    public function directors()
    {
        return $this->belongsToMany(Person::class, 'person_work')
                    ->wherePivot('role', 'director')
                    ->withPivot([
                        'credited_as',
                        'contribution_pct'
                    ])
                    ->withTimestamps();
    }

    /**
     * Autores/escritores de la obra.
     */
    public function writers()
    {
        return $this->belongsToMany(Person::class, 'person_work')
                    ->wherePivotIn('role', ['author', 'writer', 'guionista', 'escritor'])
                    ->withPivot([
                        'credited_as',
                        'contribution_pct'
                    ])
                    ->withTimestamps();
    }

    /**
     * Tags relacionados con la obra.
     */
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    /**
     * Links relacionados con la obra.
     */
    public function links()
    {
        return $this->morphMany(Link::class, 'related');
    }

    /**
     * Eventos relacionados con la obra (estrenos, presentaciones, etc.).
     */
    public function events()
    {
        return $this->hasMany(Event::class);
    }
}
