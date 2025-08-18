<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Especies vegetales para reforestación y compensación de CO2.
 * 
 * Catálogo de plantas y árboles con información sobre su capacidad
 * de absorción de CO2, características de crecimiento, y utilidad
 * para proyectos de compensación ambiental.
 * 
 * @property int $id
 * @property string $name Nombre común de la especie
 * @property string $slug Slug único para URLs
 * @property string|null $scientific_name Nombre científico
 * @property string|null $family Familia botánica
 * @property float $co2_absorption_kg_per_year CO2 absorbido por año (kg)
 * @property float|null $co2_absorption_min Absorción mínima (kg/año)
 * @property float|null $co2_absorption_max Absorción máxima (kg/año)
 * @property string|null $description Descripción de la especie
 * @property string $plant_type Tipo: tree, shrub, herb, grass, etc
 * @property string $size_category Categoría: small, medium, large, giant
 * @property float|null $height_min Altura mínima (metros)
 * @property float|null $height_max Altura máxima (metros)
 * @property int|null $lifespan_years Esperanza de vida (años)
 * @property int|null $growth_rate_cm_year Velocidad crecimiento (cm/año)
 * @property string|null $climate_zones Zonas climáticas (JSON)
 * @property string|null $soil_types Tipos de suelo preferidos
 * @property float|null $water_needs_mm_year Necesidades hídricas (mm/año)
 * @property bool $drought_resistant Resistente a sequía
 * @property bool $frost_resistant Resistente a heladas
 * @property bool $is_endemic Si es especie endémica
 * @property bool $is_invasive Si es especie invasiva
 * @property bool $suitable_for_reforestation Si es apta para reforestación
 * @property bool $suitable_for_urban Si es apta para zonas urbanas
 * @property string|null $flowering_season Época de floración
 * @property string|null $fruit_season Época de fructificación
 * @property bool $provides_food Si proporciona alimento
 * @property bool $provides_timber Si proporciona madera
 * @property bool $medicinal_use Si tiene uso medicinal
 * @property float|null $planting_cost_eur Coste de plantación (euros)
 * @property float|null $maintenance_cost_eur_year Coste mantenimiento anual
 * @property int|null $survival_rate_percent Tasa de supervivencia (%)
 * @property int|null $image_id Imagen de la especie
 * @property int|null $native_region_id Región nativa
 * @property string $source Fuente de los datos
 * @property string|null $source_url URL de la fuente
 * @property bool $is_verified Si está verificado científicamente
 * @property string|null $verification_entity Entidad verificadora
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * 
 * @property-read \App\Models\Image|null $image
 * @property-read \App\Models\Region|null $nativeRegion
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PlantationProject[] $plantationProjects
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Municipality[] $suitableMunicipalities
 */
class PlantSpecies extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'scientific_name',
        'family',
        'co2_absorption_kg_per_year',
        'co2_absorption_min',
        'co2_absorption_max',
        'description',
        'plant_type',
        'size_category',
        'height_min',
        'height_max',
        'lifespan_years',
        'growth_rate_cm_year',
        'climate_zones',
        'soil_types',
        'water_needs_mm_year',
        'drought_resistant',
        'frost_resistant',
        'is_endemic',
        'is_invasive',
        'suitable_for_reforestation',
        'suitable_for_urban',
        'flowering_season',
        'fruit_season',
        'provides_food',
        'provides_timber',
        'medicinal_use',
        'planting_cost_eur',
        'maintenance_cost_eur_year',
        'survival_rate_percent',
        'image_id',
        'native_region_id',
        'source',
        'source_url',
        'is_verified',
        'verification_entity',
    ];

    protected $casts = [
        'co2_absorption_kg_per_year' => 'float',
        'co2_absorption_min' => 'float',
        'co2_absorption_max' => 'float',
        'height_min' => 'float',
        'height_max' => 'float',
        'lifespan_years' => 'integer',
        'growth_rate_cm_year' => 'integer',
        'climate_zones' => 'array',
        'water_needs_mm_year' => 'float',
        'drought_resistant' => 'boolean',
        'frost_resistant' => 'boolean',
        'is_endemic' => 'boolean',
        'is_invasive' => 'boolean',
        'suitable_for_reforestation' => 'boolean',
        'suitable_for_urban' => 'boolean',
        'provides_food' => 'boolean',
        'provides_timber' => 'boolean',
        'medicinal_use' => 'boolean',
        'planting_cost_eur' => 'float',
        'maintenance_cost_eur_year' => 'float',
        'survival_rate_percent' => 'integer',
        'is_verified' => 'boolean',
    ];

    /**
     * Imagen de la especie.
     */
    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class);
    }

    /**
     * Región nativa de la especie.
     */
    public function nativeRegion(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * Proyectos de plantación que usan esta especie.
     */
    public function plantationProjects(): HasMany
    {
        return $this->hasMany(PlantationProject::class);
    }

    /**
     * Municipios donde es adecuada para plantar.
     */
    public function suitableMunicipalities(): BelongsToMany
    {
        return $this->belongsToMany(Municipality::class, 'plant_species_municipality')
                    ->withPivot(['suitability_score', 'recommended_season'])
                    ->withTimestamps();
    }

    /**
     * Scope para árboles.
     */
    public function scopeTrees($query)
    {
        return $query->where('plant_type', 'tree');
    }

    /**
     * Scope para especies aptas para reforestación.
     */
    public function scopeForReforestation($query)
    {
        return $query->where('suitable_for_reforestation', true);
    }

    /**
     * Scope para especies urbanas.
     */
    public function scopeForUrban($query)
    {
        return $query->where('suitable_for_urban', true);
    }

    /**
     * Scope para especies nativas.
     */
    public function scopeNative($query, $regionId = null)
    {
        $q = $query->where('is_endemic', true);
        
        if ($regionId) {
            $q->where('native_region_id', $regionId);
        }
        
        return $q;
    }

    /**
     * Scope para especies resistentes a sequía.
     */
    public function scopeDroughtResistant($query)
    {
        return $query->where('drought_resistant', true);
    }

    /**
     * Scope para especies de alta absorción CO2.
     */
    public function scopeHighCO2Absorption($query, $minKg = 20)
    {
        return $query->where('co2_absorption_kg_per_year', '>=', $minKg);
    }

    /**
     * Scope para especies de crecimiento rápido.
     */
    public function scopeFastGrowing($query, $minCmYear = 50)
    {
        return $query->where('growth_rate_cm_year', '>=', $minCmYear);
    }

    /**
     * Scope para especies verificadas.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Calcular CO2 absorbido en un período.
     */
    public function calculateCO2Absorption($years)
    {
        return $this->co2_absorption_kg_per_year * $years;
    }

    /**
     * Obtener el costo total de plantación y mantenimiento.
     */
    public function calculateTotalCost($years, $quantity = 1)
    {
        $plantingCost = ($this->planting_cost_eur ?? 2) * $quantity;
        $maintenanceCost = ($this->maintenance_cost_eur_year ?? 0.5) * $years * $quantity;
        
        return round($plantingCost + $maintenanceCost, 2);
    }

    /**
     * Obtener tipo de planta en español.
     */
    public function getPlantTypeNameAttribute()
    {
        $types = [
            'tree' => 'Árbol',
            'shrub' => 'Arbusto',
            'herb' => 'Hierba',
            'grass' => 'Pasto',
            'vine' => 'Enredadera',
            'palm' => 'Palmera',
            'conifer' => 'Conífera',
            'fern' => 'Helecho',
            'succulent' => 'Suculenta',
            'bamboo' => 'Bambú',
        ];

        return $types[$this->plant_type] ?? 'Desconocido';
    }

    /**
     * Obtener categoría de tamaño en español.
     */
    public function getSizeCategoryNameAttribute()
    {
        $sizes = [
            'small' => 'Pequeño',
            'medium' => 'Mediano',
            'large' => 'Grande',
            'giant' => 'Gigante',
        ];

        return $sizes[$this->size_category] ?? 'Desconocido';
    }

    /**
     * Obtener nivel de absorción de CO2.
     */
    public function getCO2AbsorptionLevelAttribute()
    {
        $absorption = $this->co2_absorption_kg_per_year;
        
        if ($absorption < 5) return 'bajo';
        elseif ($absorption < 15) return 'medio';
        elseif ($absorption < 30) return 'alto';
        else return 'muy_alto';
    }

    /**
     * Obtener color para visualización.
     */
    public function getCO2AbsorptionColorAttribute()
    {
        $colors = [
            'bajo' => '#ef4444',      // Rojo
            'medio' => '#f97316',     // Naranja
            'alto' => '#eab308',      // Amarillo
            'muy_alto' => '#22c55e',  // Verde
        ];

        return $colors[$this->co2_absorption_level] ?? '#6b7280';
    }

    /**
     * Verificar si es adecuada para clima específico.
     */
    public function isSuitableForClimate($climateType)
    {
        if (!$this->climate_zones) {
            return false;
        }

        return in_array($climateType, $this->climate_zones);
    }

    /**
     * Obtener beneficios adicionales.
     */
    public function getAdditionalBenefitsAttribute()
    {
        $benefits = [];
        
        if ($this->provides_food) $benefits[] = 'Produce alimento';
        if ($this->provides_timber) $benefits[] = 'Proporciona madera';
        if ($this->medicinal_use) $benefits[] = 'Uso medicinal';
        if ($this->drought_resistant) $benefits[] = 'Resistente a sequía';
        if ($this->frost_resistant) $benefits[] = 'Resistente a heladas';
        
        return $benefits;
    }

    /**
     * Obtener eficiencia de absorción CO2 por euro invertido.
     */
    public function getCO2EfficiencyAttribute()
    {
        $cost = $this->planting_cost_eur ?? 2;
        return round($this->co2_absorption_kg_per_year / $cost, 2);
    }

    /**
     * Obtener puntuación de idoneidad para reforestación.
     */
    public function getReforestationScoreAttribute()
    {
        $score = 0;
        
        // Absorción CO2 (max 40 puntos)
        $score += min(40, $this->co2_absorption_kg_per_year * 2);
        
        // Supervivencia (max 20 puntos)
        $score += ($this->survival_rate_percent ?? 70) * 0.2;
        
        // Crecimiento rápido (max 15 puntos)
        $score += min(15, ($this->growth_rate_cm_year ?? 30) * 0.3);
        
        // Resistencia (max 15 puntos)
        if ($this->drought_resistant) $score += 7.5;
        if ($this->frost_resistant) $score += 7.5;
        
        // Beneficios adicionales (max 10 puntos)
        $score += count($this->additional_benefits) * 2.5;
        
        return round($score, 1);
    }

    /**
     * Obtener recomendación por tipo de proyecto.
     */
    public function getProjectRecommendationAttribute()
    {
        if ($this->co2_absorption_kg_per_year > 25 && $this->suitable_for_reforestation) {
            return 'Excelente para compensación CO2';
        }
        
        if ($this->suitable_for_urban && $this->size_category === 'medium') {
            return 'Ideal para ciudades verdes';
        }
        
        if ($this->provides_food && $this->co2_absorption_kg_per_year > 10) {
            return 'Perfecto para agroforestación';
        }
        
        if ($this->drought_resistant && $this->is_endemic) {
            return 'Recomendado para restauración ecológica';
        }
        
        return 'Apto para proyectos generales';
    }
}