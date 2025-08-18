<?php

namespace Database\Factories;

use App\Models\Cooperative;
use App\Models\Municipality;
use App\Models\Image;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Carbon\Carbon;

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
        $legalForms = ['Sociedad Cooperativa', 'Cooperativa de Trabajo Asociado', 'Cooperativa de Consumidores'];
        
        $type = $this->faker->randomElement($cooperativeTypes);
        
        // Nombres realistas según el tipo
        $nameTemplates = [
            'energy' => [
                'Cooperativa Energética {city}',
                'Energía Renovable {city} Coop',
                'Som Energia {city}',
                'Cooperativa Solar {city}',
                'Energías Verdes {city}',
            ],
            'housing' => [
                'Cooperativa de Viviendas {city}',
                'Habitacoop {city}',
                'Vivienda Solidaria {city}',
                'Cooperativa Residencial {city}',
            ],
            'agriculture' => [
                'Cooperativa Agrícola {city}',
                'Agricultores Unidos {city}',
                'Cooperativa Rural {city}',
                'Campo Verde {city}',
            ],
            'etc' => [
                'Cooperativa {city}',
                'Cooperativa de Servicios {city}',
                'Cooperativa Integral {city}',
            ],
        ];
        
        $city = $this->faker->randomElement([
            'Madrid', 'Barcelona', 'Valencia', 'Sevilla', 'Bilbao',
            'Zaragoza', 'Málaga', 'Murcia', 'Palma', 'Granada',
            'Vitoria', 'Santander', 'Toledo', 'Badajoz', 'Logroño'
        ]);
        
        $nameTemplate = $this->faker->randomElement($nameTemplates[$type]);
        $name = str_replace('{city}', $city, $nameTemplate);
        $slug = Str::slug($name);
        
        // Actividades principales según el tipo
        $mainActivities = [
            'energy' => [
                'Comercialización de energía renovable',
                'Gestión de instalaciones solares comunitarias',
                'Autoconsumo energético compartido',
                'Servicios energéticos sostenibles',
            ],
            'housing' => [
                'Promoción de vivienda cooperativa',
                'Cesión de uso de viviendas',
                'Rehabilitación de edificios',
                'Vivienda social colaborativa',
            ],
            'agriculture' => [
                'Producción agrícola ecológica',
                'Comercialización de productos locales',
                'Servicios agrarios cooperativos',
                'Agricultura sostenible',
            ],
            'etc' => [
                'Servicios cooperativos diversos',
                'Economía social y solidaria',
                'Servicios comunitarios',
                'Cooperación social',
            ],
        ];
        
        $mainActivity = $this->faker->randomElement($mainActivities[$type]);
        
        // Direcciones realistas españolas
        $addresses = [
            'Calle de la Cooperación, ' . $this->faker->numberBetween(1, 200) . ', ' . $city,
            'Avenida de la Sostenibilidad, ' . $this->faker->numberBetween(1, 100) . ', ' . $city,
            'Plaza de la Economía Social, ' . $this->faker->numberBetween(1, 50) . ', ' . $city,
            'Calle Verde, ' . $this->faker->numberBetween(1, 150) . ', ' . $city,
        ];
        
        // Coordenadas aproximadas de ciudades españolas
        $coordinates = [
            'Madrid' => ['lat' => 40.4168, 'lng' => -3.7038],
            'Barcelona' => ['lat' => 41.3851, 'lng' => 2.1734],
            'Valencia' => ['lat' => 39.4699, 'lng' => -0.3763],
            'Sevilla' => ['lat' => 37.3891, 'lng' => -5.9845],
            'Bilbao' => ['lat' => 43.2627, 'lng' => -2.9253],
        ];
        
        $coords = $coordinates[$city] ?? ['lat' => 40.4168, 'lng' => -3.7038];
        
        return [
            'name' => $name,
            'slug' => $slug,
            'legal_name' => $name . ' S.Coop.',
            'cooperative_type' => $type,
            'scope' => $this->faker->randomElement($scopes),
            'nif' => 'F' . $this->faker->numerify('########'),
            'founded_at' => $this->faker->dateTimeBetween('-20 years', '-1 year'),
            'phone' => '9' . $this->faker->randomElement(['00', '01', '02']) . ' ' . $this->faker->numerify('### ###'),
            'email' => 'info@' . Str::slug($name) . '.coop',
            'website' => 'https://www.' . Str::slug($name) . '.coop',
            'logo_url' => null,
            'municipality_id' => Municipality::first()?->id ?? Municipality::create([
                'name' => $city,
                'slug' => Str::slug($city),
                'province_id' => 1, // Placeholder
                'autonomous_community_id' => 1, // Placeholder
                'country_id' => 1, // Placeholder
            ])->id,
            'address' => $this->faker->randomElement($addresses),
            'latitude' => $coords['lat'] + $this->faker->randomFloat(4, -0.1, 0.1),
            'longitude' => $coords['lng'] + $this->faker->randomFloat(4, -0.1, 0.1),
            'description' => $this->generateDescription($type, $name),
            'number_of_members' => $this->faker->numberBetween(5, 500),
            'main_activity' => $mainActivity,
            'is_open_to_new_members' => $this->faker->boolean(70), // 70% abiertas a nuevos socios
            'source' => $this->faker->randomElement(['manual', 'api', 'import', 'web_scraping']),
            'has_energy_market_access' => $type === 'energy' ? $this->faker->boolean(60) : false,
            'legal_form' => $this->faker->randomElement($legalForms),
            'statutes_url' => $this->faker->boolean(40) ? 
                'https://www.' . Str::slug($name) . '.coop/estatutos.pdf' : 
                null,
            'accepts_new_installations' => $type === 'energy' ? $this->faker->boolean(50) : false,
            'image_id' => null, // Se puede asignar manualmente si existe el modelo Image
        ];
    }

    /**
     * Generate a realistic description based on type.
     */
    private function generateDescription($type, $name)
    {
        $descriptions = [
            'energy' => [
                "{$name} es una cooperativa energética comprometida con la transición hacia un modelo energético más sostenible y democrático. Ofrecemos servicios de comercialización de energía 100% renovable y gestión de instalaciones de autoconsumo compartido.",
                "Somos una cooperativa que promueve el acceso a la energía renovable de forma justa y participativa. Nuestros socios participan activamente en las decisiones sobre el suministro energético y contribuyen a un futuro más sostenible.",
                "Cooperativa especializada en energías renovables que ofrece servicios de suministro eléctrico, asesoramiento energético y desarrollo de proyectos de autoconsumo comunitario.",
            ],
            'housing' => [
                "{$name} es una cooperativa de vivienda que promueve el acceso a la vivienda digna a través del modelo de cesión de uso, garantizando la desmercantilización del suelo y la vivienda.",
                "Cooperativa dedicada a la promoción de vivienda cooperativa en régimen de cesión de uso, fomentando la vida comunitaria y la sostenibilidad habitacional.",
                "Trabajamos por el derecho a la vivienda a través de modelos cooperativos alternativos que priorizan el uso sobre la propiedad.",
            ],
            'agriculture' => [
                "{$name} es una cooperativa agrícola que agrupa a productores locales comprometidos con la agricultura ecológica y la comercialización directa de productos de proximidad.",
                "Cooperativa que promueve la agricultura sostenible y la soberanía alimentaria, conectando productores y consumidores de forma directa y justa.",
                "Agrupamos a agricultores y ganaderos de la zona para ofrecer productos ecológicos de calidad, fomentando prácticas agrarias respetuosas con el medio ambiente.",
            ],
            'etc' => [
                "{$name} es una cooperativa integral que ofrece diversos servicios basados en los principios de la economía social y solidaria.",
                "Cooperativa multisectorial que desarrolla proyectos de economía social, priorizando las personas y el bien común sobre el beneficio económico.",
                "Trabajamos desde los valores cooperativos para generar alternativas económicas más justas y sostenibles.",
            ],
        ];

        return $this->faker->randomElement($descriptions[$type] ?? $descriptions['etc']);
    }

    /**
     * Indicate that the cooperative is energy type.
     */
    public function energy(): static
    {
        return $this->state(fn (array $attributes) => [
            'cooperative_type' => 'energy',
            'has_energy_market_access' => $this->faker->boolean(80),
            'accepts_new_installations' => $this->faker->boolean(70),
        ]);
    }

    /**
     * Indicate that the cooperative is housing type.
     */
    public function housing(): static
    {
        return $this->state(fn (array $attributes) => [
            'cooperative_type' => 'housing',
            'has_energy_market_access' => false,
            'accepts_new_installations' => false,
        ]);
    }

    /**
     * Indicate that the cooperative is agricultural type.
     */
    public function agriculture(): static
    {
        return $this->state(fn (array $attributes) => [
            'cooperative_type' => 'agriculture',
            'has_energy_market_access' => false,
            'accepts_new_installations' => false,
        ]);
    }

    /**
     * Indicate that the cooperative is open to new members.
     */
    public function openToMembers(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_open_to_new_members' => true,
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
     * Indicate that the cooperative has energy market access.
     */
    public function withEnergyAccess(): static
    {
        return $this->state(fn (array $attributes) => [
            'has_energy_market_access' => true,
        ]);
    }

    /**
     * Indicate that the cooperative has local scope.
     */
    public function local(): static
    {
        return $this->state(fn (array $attributes) => [
            'scope' => 'local',
        ]);
    }

    /**
     * Indicate that the cooperative has regional scope.
     */
    public function regional(): static
    {
        return $this->state(fn (array $attributes) => [
            'scope' => 'regional',
        ]);
    }

    /**
     * Indicate that the cooperative has national scope.
     */
    public function national(): static
    {
        return $this->state(fn (array $attributes) => [
            'scope' => 'national',
        ]);
    }
}