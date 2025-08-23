<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

/**
 * Modelo para el Santoral Católico.
 * 
 * Gestiona información completa sobre santos católicos, incluyendo
 * fechas de celebración, patronazgos, biografías y relaciones
 * con municipios y otros modelos del sistema.
 * 
 * @property int $id
 * @property string $name Nombre del santo
 * @property string|null $canonical_name Nombre canónico en latín
 * @property string $slug Slug único para URL
 * @property string|null $description Descripción breve del santo
 * @property string|null $biography Biografía completa del santo
 * @property \Carbon\Carbon|null $birth_date Fecha de nacimiento
 * @property \Carbon\Carbon|null $death_date Fecha de muerte/tránsito
 * @property \Carbon\Carbon|null $canonization_date Fecha de canonización
 * @property \Carbon\Carbon $feast_date Fecha de celebración litúrgica
 * @property \Carbon\Carbon|null $feast_date_optional Fecha alternativa de celebración
 * @property string $category Categoría del santo (martyr, confessor, virgin, etc.)
 * @property string $feast_type Tipo de celebración litúrgica
 * @property string|null $liturgical_color Color litúrgico de la celebración
 * @property string|null $patron_of Patrono de (oficios, lugares, causas)
 * @property bool $is_patron Es patrono de algún lugar o causa
 * @property array|null $patronages Lista de patronazgos específicos
 * @property string|null $specialties Especialidades o virtudes del santo
 * @property int|null $birth_place_id Lugar de nacimiento
 * @property int|null $death_place_id Lugar de muerte
 * @property int|null $municipality_id Municipio donde es patrono
 * @property string|null $region Región o territorio de influencia
 * @property string|null $country País de origen o influencia
 * @property string|null $liturgical_rank Rango litúrgico
 * @property string|null $prayers Oraciones asociadas al santo
 * @property string|null $hymns Himnos asociados al santo
 * @property array|null $attributes Atributos o símbolos del santo
 * @property bool $is_active Santo activo en el calendario
 * @property bool $is_universal Celebrado universalmente
 * @property bool $is_local Solo celebrado localmente
 * @property int $popularity_score Puntuación de popularidad
 * @property string|null $notes Notas adicionales
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * 
 * @property-read \App\Models\Municipality|null $municipality
 * @property-read \App\Models\Municipality|null $birthPlace
 * @property-read \App\Models\Municipality|null $deathPlace
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CalendarHoliday[] $calendarHolidays
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Anniversary[] $anniversaries
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Person[] $people
 */
class CatholicSaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'canonical_name',
        'slug',
        'description',
        'biography',
        'birth_date',
        'death_date',
        'canonization_date',
        'feast_date',
        'feast_date_optional',
        'category',
        'feast_type',
        'liturgical_color',
        'patron_of',
        'is_patron',
        'patronages',
        'specialties',
        'birth_place_id',
        'death_place_id',
        'municipality_id',
        'region',
        'country',
        'liturgical_rank',
        'prayers',
        'hymns',
        'attributes',
        'is_active',
        'is_universal',
        'is_local',
        'popularity_score',
        'notes',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'death_date' => 'date',
        'canonization_date' => 'date',
        'feast_date' => 'date',
        'feast_date_optional' => 'date',
        'is_patron' => 'boolean',
        'patronages' => 'array',
        'attributes' => 'array',
        'is_active' => 'boolean',
        'is_universal' => 'boolean',
        'is_local' => 'boolean',
        'popularity_score' => 'integer',
    ];

    /**
     * Municipio donde es patrono.
     */
    public function municipality(): BelongsTo
    {
        return $this->belongsTo(Municipality::class);
    }

    /**
     * Lugar de nacimiento.
     */
    public function birthPlace(): BelongsTo
    {
        return $this->belongsTo(Municipality::class, 'birth_place_id');
    }

    /**
     * Lugar de muerte.
     */
    public function deathPlace(): BelongsTo
    {
        return $this->belongsTo(Municipality::class, 'death_place_id');
    }

    /**
     * Festivos del calendario relacionados.
     */
    public function calendarHolidays(): HasMany
    {
        return $this->hasMany(CalendarHoliday::class, 'catholic_saint_id');
    }

    /**
     * Aniversarios relacionados.
     */
    public function anniversaries(): HasMany
    {
        return $this->hasMany(Anniversary::class, 'catholic_saint_id');
    }

    /**
     * Personas con este nombre de santo.
     */
    public function people(): HasMany
    {
        return $this->hasMany(Person::class, 'name_saint_id');
    }

    /**
     * Scope para santos activos.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para santos universales.
     */
    public function scopeUniversal($query)
    {
        return $query->where('is_universal', true);
    }

    /**
     * Scope para santos locales.
     */
    public function scopeLocal($query)
    {
        return $query->where('is_local', true);
    }

    /**
     * Scope para santos por categoría.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope para santos por tipo de fiesta.
     */
    public function scopeByFeastType($query, $feastType)
    {
        return $query->where('feast_type', $feastType);
    }

    /**
     * Scope para santos que celebran en una fecha específica.
     */
    public function scopeByFeastDate($query, $date)
    {
        return $query->where('feast_date', $date)
                    ->orWhere('feast_date_optional', $date);
    }

    /**
     * Scope para santos patronos.
     */
    public function scopePatrons($query)
    {
        return $query->where('is_patron', true);
    }

    /**
     * Scope para santos por municipio.
     */
    public function scopeByMunicipality($query, $municipalityId)
    {
        return $query->where('municipality_id', $municipalityId);
    }

    /**
     * Scope para santos por popularidad.
     */
    public function scopePopular($query, $minScore = 5)
    {
        return $query->where('popularity_score', '>=', $minScore);
    }

    /**
     * Obtener el color litúrgico en español.
     */
    public function getLiturgicalColorNameAttribute(): string
    {
        return match($this->liturgical_color) {
            'white' => 'Blanco',
            'red' => 'Rojo',
            'green' => 'Verde',
            'purple' => 'Morado',
            'pink' => 'Rosa',
            'gold' => 'Dorado',
            'black' => 'Negro',
            default => 'No especificado',
        };
    }

    /**
     * Obtener la categoría en español.
     */
    public function getCategoryNameAttribute(): string
    {
        return match($this->category) {
            'martyr' => 'Mártir',
            'confessor' => 'Confesor',
            'virgin' => 'Virgen',
            'virgin_martyr' => 'Virgen y Mártir',
            'bishop' => 'Obispo',
            'pope' => 'Papa',
            'religious' => 'Religioso/a',
            'lay_person' => 'Laico/a',
            'founder' => 'Fundador/a',
            'doctor' => 'Doctor de la Iglesia',
            'apostle' => 'Apóstol',
            'evangelist' => 'Evangelista',
            'prophet' => 'Profeta',
            'patriarch' => 'Patriarca',
            default => 'Otros',
        };
    }

    /**
     * Obtener el tipo de fiesta en español.
     */
    public function getFeastTypeNameAttribute(): string
    {
        return match($this->feast_type) {
            'solemnity' => 'Solemnidad',
            'feast' => 'Fiesta',
            'memorial' => 'Memoria',
            'optional_memorial' => 'Memoria Opcional',
            'commemoration' => 'Conmemoración',
            default => 'Memoria',
        };
    }

    /**
     * Verificar si es el santo del día.
     */
    public function isTodaySaint(): bool
    {
        $today = Carbon::today();
        return $this->feast_date->equalTo($today) || 
               ($this->feast_date_optional && $this->feast_date_optional->equalTo($today));
    }

    /**
     * Obtener la edad al morir (si se conocen ambas fechas).
     */
    public function getAgeAtDeathAttribute(): ?int
    {
        if ($this->birth_date && $this->death_date) {
            return $this->birth_date->diffInYears($this->death_date);
        }
        return null;
    }

    /**
     * Obtener el tiempo transcurrido desde la canonización.
     */
    public function getYearsSinceCanonizationAttribute(): ?int
    {
        if ($this->canonization_date) {
            return $this->canonization_date->diffInYears(Carbon::today());
        }
        return null;
    }

    /**
     * Verificar si es un santo reciente (canonizado en los últimos 100 años).
     */
    public function isRecentSaint(): bool
    {
        if ($this->canonization_date) {
            return $this->canonization_date->diffInYears(Carbon::today()) <= 100;
        }
        return false;
    }

    /**
     * Obtener el próximo aniversario de celebración.
     */
    public function getNextFeastDateAttribute(): Carbon
    {
        $today = Carbon::today();
        $nextFeast = Carbon::createFromDate($today->year, $this->feast_date->month, $this->feast_date->day);
        
        if ($nextFeast->lessThan($today)) {
            $nextFeast->addYear();
        }
        
        return $nextFeast;
    }

    /**
     * Obtener días hasta la próxima celebración.
     */
    public function getDaysUntilNextFeastAttribute(): int
    {
        return (int) Carbon::today()->diffInDays($this->next_feast_date, false);
    }
}
