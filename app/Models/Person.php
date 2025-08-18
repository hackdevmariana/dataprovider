<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Person
 *
 * Represents a person with biographical and relational data.
 *
 * @property int $id
 * @property string $name
 * @property string|null $birth_name
 * @property string $slug
 * @property \Carbon\Carbon|null $birth_date
 * @property \Carbon\Carbon|null $death_date
 * @property string|null $birth_place
 * @property string|null $death_place
 * @property int|null $nationality_id
 * @property int|null $language_id
 * @property int|null $image_id
 * @property string|null $gender
 * @property string|null $official_website
 * @property string|null $wikidata_id
 * @property string|null $wikipedia_url
 * @property string|null $notable_for
 * @property string|null $occupation_summary
 * @property array|null $social_handles
 * @property bool $is_influencer
 * @property int|null $search_boost
 * @property string|null $short_bio
 * @property string|null $long_bio
 * @property string|null $source_url
 * @property \Carbon\Carbon|null $last_updated_from_source
 *
 * @property-read Country $nationality
 * @property-read Language $language
 * @property-read Image $image
 * @property-read \Illuminate\Database\Eloquent\Collection|Alias[] $aliases
 * @property-read \Illuminate\Database\Eloquent\Collection|Tag[] $tags
 * @property-read Appearance|null $appearance
 */
class Person extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'birth_name',
        'slug',
        'birth_date',
        'death_date',
        'birth_place',
        'death_place',
        'nationality_id',
        'language_id',
        'image_id',
        'gender',
        'official_website',
        'wikidata_id',
        'wikipedia_url',
        'notable_for',
        'occupation_summary',
        'social_handles',
        'is_influencer',
        'search_boost',
        'short_bio',
        'long_bio',
        'source_url',
        'last_updated_from_source',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'death_date' => 'date',
        'is_influencer' => 'boolean',
        'social_handles' => 'array',
        'last_updated_from_source' => 'datetime',
    ];

    public function nationality(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'nationality_id');
    }
    public function aliases()
    {
        return $this->hasMany(Alias::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class);
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
    public function appearance()
    {
        return $this->hasOne(Appearance::class, 'person_id');
    }

    /**
     * Profesiones de la persona.
     */
    public function professions()
    {
        return $this->belongsToMany(Profession::class, 'person_profession')
                    ->withPivot([
                        'start_year',
                        'end_year',
                        'is_primary',
                        'is_current',
                        'notes'
                    ])
                    ->withTimestamps();
    }

    /**
     * Profesión principal actual.
     */
    public function primaryProfession()
    {
        return $this->belongsToMany(Profession::class, 'person_profession')
                    ->wherePivot('is_primary', true)
                    ->wherePivot('is_current', true)
                    ->withPivot([
                        'start_year',
                        'end_year',
                        'is_primary',
                        'is_current',
                        'notes'
                    ])
                    ->withTimestamps();
    }

    /**
     * Obras en las que ha participado.
     */
    public function works()
    {
        return $this->belongsToMany(Work::class, 'person_work')
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
     * Obras donde es protagonista.
     */
    public function leadingWorks()
    {
        return $this->belongsToMany(Work::class, 'person_work')
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
     * Premios ganados.
     */
    public function awards()
    {
        return $this->hasMany(AwardWinner::class);
    }

    /**
     * Miembros de la familia.
     */
    public function familyMembers()
    {
        return $this->hasMany(FamilyMember::class);
    }

    /**
     * Links relacionados.
     */
    public function links()
    {
        return $this->morphMany(Link::class, 'related');
    }

    /**
     * Cuentas sociales.
     */
    public function socialAccounts()
    {
        return $this->hasMany(SocialAccount::class);
    }
}
