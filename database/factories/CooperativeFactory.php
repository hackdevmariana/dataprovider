<?php

namespace Database\Factories;

use App\Models\Cooperative;
use App\Models\Municipality;
use App\Models\DataSource;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cooperative>
 */
class CooperativeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cooperativeTypes = ['energy', 'housing', 'agriculture', 'etc'];
        $scopes = ['local', 'regional', 'national'];
        $sources = ['manual', 'cnmc'];
        
        $name = $this->faker->company() . ' ' . $this->faker->randomElement(['Cooperativa', 'Coop', 'Sociedad Cooperativa']);
        $cooperativeType = $this->faker->randomElement($cooperativeTypes);
        $scope = $this->faker->randomElement($scopes);
        
        // Generar actividades principales basadas en el tipo
        $mainActivities = match($cooperativeType) {
            'energy' => [
                'Generación de energía renovable',
                'Comercialización de energía',
                'Gestión de instalaciones fotovoltaicas',
                'Autoconsumo colectivo',
                'Eficiencia energética',
                'Mercado energético local'
            ],
            'housing' => [
                'Construcción de viviendas cooperativas',
                'Gestión de viviendas sociales',
                'Rehabilitación energética',
                'Vivienda colaborativa',
                'Desarrollo urbano sostenible'
            ],
            'agriculture' => [
                'Producción agrícola ecológica',
                'Comercialización de productos',
                'Gestión de tierras comunales',
                'Tecnología agrícola',
                'Cooperativismo agrario'
            ],
            'etc' => [
                'Servicios generales',
                'Actividades diversas',
                'Cooperativismo integral',
                'Servicios comunitarios'
            ]
        };

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'legal_name' => $this->faker->optional(0.8)->company() . ' S. Coop.',
            'cooperative_type' => $cooperativeType,
            'scope' => $scope,
            'nif' => $this->faker->optional(0.9)->numerify('G########'),
            'founded_at' => $this->faker->dateTimeBetween('-50 years', '-1 year'),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->companyEmail(),
            'website' => $this->faker->url(),
            'logo_url' => $this->faker->optional(0.6)->imageUrl(200, 200, 'business'),
            'image_id' => null, // Se puede asociar después
            'municipality_id' => Municipality::factory(),
            'address' => $this->faker->streetAddress(),
            'latitude' => $this->faker->latitude(35, 44), // España
            'longitude' => $this->faker->longitude(-9, 4), // España
            'description' => $this->faker->optional(0.8)->paragraph(3),
            'number_of_members' => $this->faker->numberBetween(5, 500),
            'main_activity' => $this->faker->randomElement($mainActivities),
            'is_open_to_new_members' => $this->faker->boolean(70),
            'source' => $this->faker->randomElement($sources),
            'data_source_id' => $this->faker->optional(0.3)->randomElement(DataSource::pluck('id')->toArray()),
            'has_energy_market_access' => $cooperativeType === 'energy' ? $this->faker->boolean(60) : $this->faker->boolean(10),
            'legal_form' => $this->faker->randomElement([
                'Sociedad Cooperativa',
                'Sociedad Cooperativa Andaluza',
                'Sociedad Cooperativa de Trabajo Asociado',
                'Cooperativa de Servicios',
                'Cooperativa de Consumo'
            ]),
            'statutes_url' => $this->faker->optional(0.4)->url(),
            'accepts_new_installations' => $cooperativeType === 'energy' ? $this->faker->boolean(80) : $this->faker->boolean(20),
        ];
    }

    /**
     * Indicate that the cooperative is an energy cooperative.
     */
    public function energy(): static
    {
        return $this->state(fn (array $attributes) => [
            'cooperative_type' => 'energy',
            'main_activity' => $this->faker->randomElement([
                'Generación de energía renovable',
                'Comercialización de energía',
                'Gestión de instalaciones fotovoltaicas',
                'Autoconsumo colectivo'
            ]),
            'has_energy_market_access' => $this->faker->boolean(70),
            'accepts_new_installations' => $this->faker->boolean(85),
        ]);
    }

    /**
     * Indicate that the cooperative is a housing cooperative.
     */
    public function housing(): static
    {
        return $this->state(fn (array $attributes) => [
            'cooperative_type' => 'housing',
            'main_activity' => $this->faker->randomElement([
                'Construcción de viviendas cooperativas',
                'Gestión de viviendas sociales',
                'Rehabilitación energética',
                'Vivienda colaborativa'
            ]),
            'has_energy_market_access' => false,
            'accepts_new_installations' => $this->faker->boolean(30),
        ]);
    }

    /**
     * Indicate that the cooperative is an agriculture cooperative.
     */
    public function agriculture(): static
    {
        return $this->state(fn (array $attributes) => [
            'cooperative_type' => 'agriculture',
            'main_activity' => $this->faker->randomElement([
                'Producción agrícola ecológica',
                'Comercialización de productos',
                'Gestión de tierras comunales',
                'Tecnología agrícola'
            ]),
            'has_energy_market_access' => $this->faker->boolean(20),
            'accepts_new_installations' => $this->faker->boolean(40),
        ]);
    }

    /**
     * Indicate that the cooperative is open to new members.
     */
    public function openToNewMembers(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_open_to_new_members' => true,
        ]);
    }

    /**
     * Indicate that the cooperative has energy market access.
     */
    public function withEnergyMarketAccess(): static
    {
        return $this->state(fn (array $attributes) => [
            'has_energy_market_access' => true,
        ]);
    }

    /**
     * Indicate that the cooperative accepts new installations.
     */
    public function acceptingInstallations(): static
    {
        return $this->state(fn (array $attributes) => [
            'accepts_new_installations' => true,
        ]);
    }

    /**
     * Indicate that the cooperative is local scope.
     */
    public function local(): static
    {
        return $this->state(fn (array $attributes) => [
            'scope' => 'local',
        ]);
    }

    /**
     * Indicate that the cooperative is regional scope.
     */
    public function regional(): static
    {
        return $this->state(fn (array $attributes) => [
            'scope' => 'regional',
        ]);
    }

    /**
     * Indicate that the cooperative is national scope.
     */
    public function national(): static
    {
        return $this->state(fn (array $attributes) => [
            'scope' => 'national',
        ]);
    }
}