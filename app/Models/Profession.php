<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Profession
 *
 * Represents a profession or occupation.
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $category
 * @property bool $is_public_facing
 */
class Profession extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'category',
        'is_public_facing',
    ];

    protected $casts = [
        'is_public_facing' => 'boolean',
    ];

    /**
     * Personas que ejercen esta profesión.
     */
    public function people()
    {
        return $this->belongsToMany(Person::class, 'person_profession')
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
     * Personas que ejercen actualmente esta profesión.
     */
    public function currentPeople()
    {
        return $this->belongsToMany(Person::class, 'person_profession')
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
     * Personas para las que esta es su profesión principal.
     */
    public function primaryPeople()
    {
        return $this->belongsToMany(Person::class, 'person_profession')
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
}
