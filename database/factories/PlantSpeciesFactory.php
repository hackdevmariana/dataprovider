<?php

namespace Database\Factories;

use App\Models\PlantSpecies;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PlantSpecies>
 */
class PlantSpeciesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $plantTypes = ['tree', 'shrub', 'herb', 'grass', 'vine', 'palm', 'conifer'];
        $sizeCategories = ['small', 'medium', 'large', 'giant'];
        $climateZones = ['mediterráneo', 'atlántico', 'continental', 'subtropical', 'montano'];
        
        $plantType = $this->faker->randomElement($plantTypes);
        $isTree = $plantType === 'tree';
        
        // Generar datos realistas según el tipo
        $data = $this->generatePlantData($plantType);
        
        $sizeCategory = $data['size_category'];
        $heightRange = $this->getHeightRange($sizeCategory);
        $co2Absorption = $this->getCO2Absorption($plantType, $sizeCategory);
        
        return [
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'scientific_name' => $data['scientific_name'],
            'family' => $data['family'],
            'co2_absorption_kg_per_year' => $co2Absorption,
            'co2_absorption_min' => $co2Absorption * 0.7,
            'co2_absorption_max' => $co2Absorption * 1.3,
            'description' => $data['description'],
            'plant_type' => $plantType,
            'size_category' => $sizeCategory,
            'height_min' => $heightRange['min'],
            'height_max' => $heightRange['max'],
            'lifespan_years' => $this->getLifespan($plantType),
            'growth_rate_cm_year' => $this->getGrowthRate($plantType, $sizeCategory),
            'climate_zones' => $this->faker->randomElements($climateZones, $this->faker->numberBetween(1, 3)),
            'soil_types' => $this->faker->randomElement([
                'arcilloso, limoso',
                'arenoso, bien drenado',
                'calcáreo, seco',
                'húmedo, rico en materia orgánica',
                'cualquier tipo de suelo'
            ]),
            'water_needs_mm_year' => $this->faker->numberBetween(300, 1200),
            'drought_resistant' => $this->faker->boolean(30),
            'frost_resistant' => $this->faker->boolean(50),
            'is_endemic' => $this->faker->boolean(20),
            'is_invasive' => $this->faker->boolean(5),
            'suitable_for_reforestation' => $isTree ? $this->faker->boolean(80) : $this->faker->boolean(40),
            'suitable_for_urban' => $plantType === 'tree' ? $this->faker->boolean(60) : $this->faker->boolean(80),
            'flowering_season' => $this->faker->randomElement(['primavera', 'verano', 'otoño', 'invierno', 'primavera-verano']),
            'fruit_season' => $this->faker->randomElement(['verano', 'otoño', 'invierno', null]),
            'provides_food' => $this->faker->boolean(25),
            'provides_timber' => $isTree ? $this->faker->boolean(60) : false,
            'medicinal_use' => $this->faker->boolean(15),
            'planting_cost_eur' => $this->getPlantingCost($plantType, $sizeCategory),
            'maintenance_cost_eur_year' => $this->faker->randomFloat(2, 0.5, 5),
            'survival_rate_percent' => $this->faker->numberBetween(60, 95),
            'native_region_id' => null, // Se puede asignar manualmente
            'source' => $this->faker->randomElement(['manual', 'botanical_database', 'forest_service', 'university_research']),
            'source_url' => $this->faker->optional(0.6)->url(),
            'is_verified' => $this->faker->boolean(70),
            'verification_entity' => $this->faker->optional(0.7)->randomElement(['CSIC', 'Universidad', 'MITECO', 'Real Jardín Botánico']),
        ];
    }

    /**
     * Generate realistic plant data based on type.
     */
    private function generatePlantData($plantType)
    {
        $treesData = [
            ['name' => 'Encina', 'scientific_name' => 'Quercus ilex', 'family' => 'Fagaceae', 'size_category' => 'large'],
            ['name' => 'Roble', 'scientific_name' => 'Quercus robur', 'family' => 'Fagaceae', 'size_category' => 'large'],
            ['name' => 'Pino marítimo', 'scientific_name' => 'Pinus pinaster', 'family' => 'Pinaceae', 'size_category' => 'large'],
            ['name' => 'Alcornoque', 'scientific_name' => 'Quercus suber', 'family' => 'Fagaceae', 'size_category' => 'large'],
            ['name' => 'Olivo', 'scientific_name' => 'Olea europaea', 'family' => 'Oleaceae', 'size_category' => 'medium'],
            ['name' => 'Almendro', 'scientific_name' => 'Prunus dulcis', 'family' => 'Rosaceae', 'size_category' => 'medium'],
            ['name' => 'Ciprés', 'scientific_name' => 'Cupressus sempervirens', 'family' => 'Cupressaceae', 'size_category' => 'large'],
            ['name' => 'Eucalipto', 'scientific_name' => 'Eucalyptus globulus', 'family' => 'Myrtaceae', 'size_category' => 'giant'],
        ];

        $shrubsData = [
            ['name' => 'Romero', 'scientific_name' => 'Rosmarinus officinalis', 'family' => 'Lamiaceae', 'size_category' => 'small'],
            ['name' => 'Lavanda', 'scientific_name' => 'Lavandula angustifolia', 'family' => 'Lamiaceae', 'size_category' => 'small'],
            ['name' => 'Jara', 'scientific_name' => 'Cistus ladanifer', 'family' => 'Cistaceae', 'size_category' => 'medium'],
            ['name' => 'Retama', 'scientific_name' => 'Retama sphaerocarpa', 'family' => 'Fabaceae', 'size_category' => 'medium'],
        ];

        $herbsData = [
            ['name' => 'Tomillo', 'scientific_name' => 'Thymus vulgaris', 'family' => 'Lamiaceae', 'size_category' => 'small'],
            ['name' => 'Orégano', 'scientific_name' => 'Origanum vulgare', 'family' => 'Lamiaceae', 'size_category' => 'small'],
            ['name' => 'Salvia', 'scientific_name' => 'Salvia officinalis', 'family' => 'Lamiaceae', 'size_category' => 'small'],
        ];

        switch ($plantType) {
            case 'tree':
            case 'conifer':
                $data = $this->faker->randomElement($treesData);
                break;
            case 'shrub':
                $data = $this->faker->randomElement($shrubsData);
                break;
            case 'herb':
                $data = $this->faker->randomElement($herbsData);
                break;
            default:
                $data = [
                    'name' => $this->faker->word() . ' ' . $this->faker->randomElement(['común', 'silvestre', 'español']),
                    'scientific_name' => $this->faker->word() . ' ' . $this->faker->word(),
                    'family' => $this->faker->word() . 'aceae',
                    'size_category' => $this->faker->randomElement(['small', 'medium'])
                ];
        }

        $data['description'] = "Especie {$data['name']} ({$data['scientific_name']}) de la familia {$data['family']}, típica de la península ibérica.";
        
        return $data;
    }

    /**
     * Get height range based on size category.
     */
    private function getHeightRange($sizeCategory)
    {
        switch ($sizeCategory) {
            case 'small':
                return ['min' => 0.5, 'max' => 3];
            case 'medium':
                return ['min' => 3, 'max' => 12];
            case 'large':
                return ['min' => 12, 'max' => 25];
            case 'giant':
                return ['min' => 25, 'max' => 50];
            default:
                return ['min' => 1, 'max' => 8];
        }
    }

    /**
     * Get realistic CO2 absorption based on plant type and size.
     */
    private function getCO2Absorption($plantType, $sizeCategory)
    {
        $baseRates = [
            'tree' => ['small' => 5, 'medium' => 15, 'large' => 25, 'giant' => 40],
            'conifer' => ['small' => 8, 'medium' => 18, 'large' => 30, 'giant' => 45],
            'shrub' => ['small' => 2, 'medium' => 5, 'large' => 8, 'giant' => 12],
            'herb' => ['small' => 0.5, 'medium' => 1.5, 'large' => 3, 'giant' => 5],
            'grass' => ['small' => 0.3, 'medium' => 0.8, 'large' => 1.5, 'giant' => 2],
            'vine' => ['small' => 1, 'medium' => 3, 'large' => 5, 'giant' => 7],
            'palm' => ['small' => 3, 'medium' => 8, 'large' => 15, 'giant' => 25],
        ];

        $baseRate = $baseRates[$plantType][$sizeCategory] ?? 5;
        
        // Añadir variabilidad
        return round($baseRate * $this->faker->randomFloat(2, 0.8, 1.4), 2);
    }

    /**
     * Get lifespan based on plant type.
     */
    private function getLifespan($plantType)
    {
        switch ($plantType) {
            case 'tree':
            case 'conifer':
                return $this->faker->numberBetween(50, 300);
            case 'shrub':
                return $this->faker->numberBetween(15, 50);
            case 'herb':
                return $this->faker->numberBetween(1, 10);
            case 'palm':
                return $this->faker->numberBetween(30, 100);
            default:
                return $this->faker->numberBetween(5, 25);
        }
    }

    /**
     * Get growth rate based on plant type and size.
     */
    private function getGrowthRate($plantType, $sizeCategory)
    {
        $baseSpeeds = [
            'tree' => 40,
            'conifer' => 35,
            'shrub' => 25,
            'herb' => 15,
            'grass' => 10,
            'vine' => 80,
            'palm' => 20,
        ];

        $multipliers = [
            'small' => 0.6,
            'medium' => 1.0,
            'large' => 1.3,
            'giant' => 1.6,
        ];

        $baseSpeed = $baseSpeeds[$plantType] ?? 30;
        $multiplier = $multipliers[$sizeCategory] ?? 1.0;
        
        return round($baseSpeed * $multiplier * $this->faker->randomFloat(2, 0.7, 1.5));
    }

    /**
     * Get planting cost based on type and size.
     */
    private function getPlantingCost($plantType, $sizeCategory)
    {
        $baseCosts = [
            'tree' => ['small' => 3, 'medium' => 8, 'large' => 15, 'giant' => 25],
            'conifer' => ['small' => 4, 'medium' => 10, 'large' => 18, 'giant' => 30],
            'shrub' => ['small' => 1.5, 'medium' => 3, 'large' => 6, 'giant' => 10],
            'herb' => ['small' => 0.5, 'medium' => 1, 'large' => 2, 'giant' => 3],
            'palm' => ['small' => 20, 'medium' => 50, 'large' => 100, 'giant' => 200],
        ];

        $baseCost = $baseCosts[$plantType][$sizeCategory] ?? 5;
        
        return round($baseCost * $this->faker->randomFloat(2, 0.8, 1.3), 2);
    }

    /**
     * Tree species only.
     */
    public function tree(): static
    {
        return $this->state(fn (array $attributes) => [
            'plant_type' => 'tree',
            'suitable_for_reforestation' => true,
            'provides_timber' => $this->faker->boolean(70),
        ]);
    }

    /**
     * High CO2 absorption species.
     */
    public function highCO2(): static
    {
        return $this->state(fn (array $attributes) => [
            'co2_absorption_kg_per_year' => $this->faker->randomFloat(1, 20, 45),
            'size_category' => $this->faker->randomElement(['large', 'giant']),
        ]);
    }

    /**
     * Verified species.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => true,
            'verification_entity' => 'CSIC',
            'source' => 'university_research',
        ]);
    }

    /**
     * Native species.
     */
    public function native(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_endemic' => true,
            'is_invasive' => false,
            'suitable_for_reforestation' => true,
        ]);
    }

    /**
     * Drought resistant species.
     */
    public function droughtResistant(): static
    {
        return $this->state(fn (array $attributes) => [
            'drought_resistant' => true,
            'water_needs_mm_year' => $this->faker->numberBetween(200, 500),
        ]);
    }

    /**
     * Urban suitable species.
     */
    public function urban(): static
    {
        return $this->state(fn (array $attributes) => [
            'suitable_for_urban' => true,
            'size_category' => $this->faker->randomElement(['small', 'medium']),
            'is_invasive' => false,
        ]);
    }
}