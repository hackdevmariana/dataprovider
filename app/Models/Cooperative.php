<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Cooperativa energética o de otro tipo.
 * 
 * Representa una cooperativa que puede ofrecer servicios energéticos,
 * gestionar instalaciones de autoconsumo, o realizar otras actividades cooperativas.
 * 
 * @property int $id
 * @property string $name Nombre de la cooperativa
 * @property string $slug Slug único para URLs
 * @property string|null $legal_name Nombre legal/social
 * @property string $cooperative_type Tipo: energy, housing, agriculture, etc
 * @property string $scope Ámbito: local, regional, national
 * @property string|null $nif NIF/CIF de la cooperativa
 * @property \Carbon\Carbon|null $founded_at Fecha de fundación
 * @property string $phone Teléfono de contacto
 * @property string $email Email de contacto
 * @property string $website Web oficial
 * @property string|null $logo_url URL del logo
 * @property int|null $image_id Imagen asociada
 * @property int $municipality_id Municipio donde se ubica
 * @property string $address Dirección física
 * @property float|null $latitude Latitud para geolocalización
 * @property float|null $longitude Longitud para geolocalización
 * @property string|null $description Descripción de la cooperativa
 * @property int|null $number_of_members Número de socios
 * @property string $main_activity Actividad principal
 * @property bool $is_open_to_new_members Si acepta nuevos socios
 * @property string $source Fuente de los datos
 * @property int|null $data_source_id Fuente de datos estructurada
 * @property bool $has_energy_market_access Si tiene acceso al mercado energético
 * @property string|null $legal_form Forma jurídica específica
 * @property string|null $statutes_url URL de los estatutos
 * @property bool $accepts_new_installations Si acepta nuevas instalaciones
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * 
 * @property-read \App\Models\Municipality $municipality Municipio
 * @property-read \App\Models\Image|null $image Imagen
 * @property-read \App\Models\DataSource|null $dataSource Fuente de datos
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CooperativeUserMember[] $userMemberships Membresías
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users Usuarios socios
 */
class Cooperative extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'legal_name',
        'cooperative_type',
        'scope',
        'nif',
        'founded_at',
        'phone',
        'email',
        'website',
        'logo_url',
        'image_id',
        'municipality_id',
        'address',
        'latitude',
        'longitude',
        'description',
        'number_of_members',
        'main_activity',
        'is_open_to_new_members',
        'source',
        'data_source_id',
        'has_energy_market_access',
        'legal_form',
        'statutes_url',
        'accepts_new_installations',
    ];

    protected $casts = [
        'founded_at' => 'date',
        'latitude' => 'float',
        'longitude' => 'float',
        'is_open_to_new_members' => 'boolean',
        'has_energy_market_access' => 'boolean',
        'accepts_new_installations' => 'boolean',
    ];

    public function municipality(): BelongsTo
    {
        return $this->belongsTo(Municipality::class);
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class);
    }

    public function dataSource(): BelongsTo
    {
        return $this->belongsTo(DataSource::class);
    }



    public function userMemberships()
    {
        return $this->hasMany(\App\Models\CooperativeUserMember::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\User::class, 'cooperative_user_members')
            ->withPivot(['role', 'joined_at', 'is_active'])
            ->withTimestamps();
    }

    /**
     * Scope para filtrar por tipo de cooperativa.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('cooperative_type', $type);
    }

    /**
     * Scope para cooperativas energéticas.
     */
    public function scopeEnergy($query)
    {
        return $query->where('cooperative_type', 'energy');
    }

    /**
     * Scope para filtrar por ámbito.
     */
    public function scopeOfScope($query, $scope)
    {
        return $query->where('scope', $scope);
    }

    /**
     * Scope para cooperativas que aceptan nuevos socios.
     */
    public function scopeOpenToNewMembers($query)
    {
        return $query->where('is_open_to_new_members', true);
    }

    /**
     * Scope para cooperativas con acceso al mercado energético.
     */
    public function scopeWithEnergyMarketAccess($query)
    {
        return $query->where('has_energy_market_access', true);
    }

    /**
     * Scope para cooperativas que aceptan nuevas instalaciones.
     */
    public function scopeAcceptingInstallations($query)
    {
        return $query->where('accepts_new_installations', true);
    }

    /**
     * Scope para filtrar por municipio.
     */
    public function scopeInMunicipality($query, $municipalityId)
    {
        return $query->where('municipality_id', $municipalityId);
    }

    /**
     * Obtener el nombre del tipo de cooperativa en español.
     */
    public function getCooperativeTypeNameAttribute()
    {
        $types = [
            'energy' => 'Energética',
            'housing' => 'Vivienda',
            'agriculture' => 'Agrícola',
            'etc' => 'Otros',
        ];

        return $types[$this->cooperative_type] ?? 'Desconocido';
    }

    /**
     * Obtener el nombre del ámbito en español.
     */
    public function getScopeNameAttribute()
    {
        $scopes = [
            'local' => 'Local',
            'regional' => 'Regional',
            'national' => 'Nacional',
        ];

        return $scopes[$this->scope] ?? 'Desconocido';
    }

    /**
     * Verificar si la cooperativa está activa para nuevos proyectos.
     */
    public function getIsActiveForProjectsAttribute()
    {
        return $this->is_open_to_new_members && $this->accepts_new_installations;
    }

    /**
     * Obtener el número de años desde la fundación.
     */
    public function getYearsSinceFoundedAttribute()
    {
        if (!$this->founded_at) {
            return null;
        }

        return $this->founded_at->diffInYears(now());
    }

    /**
     * Obtener resumen de contacto.
     */
    public function getContactSummaryAttribute()
    {
        return [
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,
            'address' => $this->address,
        ];
    }

    /**
     * Obtener socios activos.
     */
    public function getActiveMembersAttribute()
    {
        return $this->users()->wherePivot('is_active', true)->count();
    }
}
