<?php

namespace Database\Factories;

use App\Models\EnergyInstallation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EnergyInstallation>
 */
class EnergyInstallationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['solar', 'wind', 'hydro', 'biomass', 'other'];
        $type = $this->faker->randomElement($types);
        
        // Capacidades típicas por tipo de instalación
        $capacityRanges = [
            'solar' => [3, 10],      // Residencial: 3-10 kW
            'wind' => [10, 100],     // Mini-eólica: 10-100 kW
            'hydro' => [5, 50],      // Mini-hidráulica: 5-50 kW
            'biomass' => [20, 200],  // Biomasa: 20-200 kW
            'other' => [5, 50],      // Otras: 5-50 kW
        ];
        
        [$minCapacity, $maxCapacity] = $capacityRanges[$type];
        
        // Nombres típicos por tipo
        $nameTemplates = [
            'solar' => [
                'Instalación Solar {location}',
                'Planta Fotovoltaica {location}',
                'Autoconsumo Solar {location}',
                'Placas Solares {location}',
            ],
            'wind' => [
                'Aerogenerador {location}',
                'Mini-Eólica {location}',
                'Instalación Eólica {location}',
            ],
            'hydro' => [
                'Mini-Hidráulica {location}',
                'Turbina Hidráulica {location}',
                'Aprovechamiento Hidráulico {location}',
            ],
            'biomass' => [
                'Planta de Biomasa {location}',
                'Generador de Biomasa {location}',
                'Instalación de Biomasa {location}',
            ],
            'other' => [
                'Instalación Energética {location}',
                'Sistema de Autoconsumo {location}',
                'Generación Renovable {location}',
            ],
        ];
        
        $location = $this->faker->randomElement([
            'Madrid', 'Barcelona', 'Valencia', 'Sevilla', 'Zaragoza',
            'Málaga', 'Murcia', 'Palma', 'Las Palmas', 'Bilbao',
            'Alicante', 'Córdoba', 'Valladolid', 'Vigo', 'Gijón',
            'Vitoria', 'Granada', 'Oviedo', 'Tarrasa', 'Badalona'
        ]);
        
        $nameTemplate = $this->faker->randomElement($nameTemplates[$type]);
        $name = str_replace('{location}', $location, $nameTemplate);
        
        // Determinar si está comisionada (80% de probabilidad)
        $isCommissioned = $this->faker->boolean(80);
        $commissionedAt = null;
        
        if ($isCommissioned) {
            // Instalaciones comisionadas entre hace 5 años y hace 1 mes
            $commissionedAt = $this->faker->dateTimeBetween('-5 years', '-1 month');
        } else {
            // 50% sin fecha (planificación) o fecha futura (construcción)
            if ($this->faker->boolean(50)) {
                $commissionedAt = $this->faker->dateTimeBetween('+1 month', '+1 year');
            }
        }

        return [
            'name' => $name,
            'type' => $type,
            'capacity_kw' => $this->faker->randomFloat(2, $minCapacity, $maxCapacity),
            'location' => $location . ', ' . $this->faker->randomElement([
                'España', 'Comunidad de Madrid', 'Cataluña', 'Andalucía',
                'Valencia', 'Aragón', 'Castilla y León', 'País Vasco'
            ]),
            'owner_id' => User::factory(),
            'commissioned_at' => $commissionedAt,
        ];
    }

    /**
     * Indicate that the installation is solar type.
     */
    public function solar(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'solar',
            'capacity_kw' => $this->faker->randomFloat(2, 3, 10),
        ]);
    }

    /**
     * Indicate that the installation is wind type.
     */
    public function wind(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'wind',
            'capacity_kw' => $this->faker->randomFloat(2, 10, 100),
        ]);
    }

    /**
     * Indicate that the installation is commissioned.
     */
    public function commissioned(): static
    {
        return $this->state(fn (array $attributes) => [
            'commissioned_at' => $this->faker->dateTimeBetween('-5 years', '-1 month'),
        ]);
    }

    /**
     * Indicate that the installation is in development.
     */
    public function inDevelopment(): static
    {
        return $this->state(fn (array $attributes) => [
            'commissioned_at' => $this->faker->boolean(50) 
                ? null 
                : $this->faker->dateTimeBetween('+1 month', '+1 year'),
        ]);
    }

    /**
     * Indicate that the installation has high capacity.
     */
    public function highCapacity(): static
    {
        return $this->state(fn (array $attributes) => [
            'capacity_kw' => $this->faker->randomFloat(2, 50, 200),
        ]);
    }
}