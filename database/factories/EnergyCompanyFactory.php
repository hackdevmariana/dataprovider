<?php

namespace Database\Factories;

use App\Models\EnergyCompany;
use App\Models\Municipality;
use App\Models\Image;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EnergyCompany>
 */
class EnergyCompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $companyTypes = ['comercializadora', 'distribuidora', 'mixta', 'cooperativa'];
        $coverageScopes = ['local', 'regional', 'nacional'];
        
        $name = $this->faker->randomElement([
            'Energía Verde',
            'Electricidad Sostenible',
            'Potencia Renovable',
            'Suministro Eléctrico',
            'Energías del Futuro',
            'Electricidad Limpia',
            'Poder Energético',
            'Renovables Ibéricas',
            'Electricidad Inteligente',
            'Energía Ecológica',
        ]) . ' ' . $this->faker->randomElement(['SA', 'SL', 'Coop', 'SCoop']);
        
        $slug = Str::slug($name);
        
        // Datos de contacto realistas españoles
        $phoneCustomer = '9' . $this->faker->randomElement(['00', '01', '02']) . ' ' . 
                        $this->faker->numerify('### ###');
        
        $phoneCommercial = $this->faker->boolean(70) ? 
            '9' . $this->faker->randomElement(['00', '01', '02']) . ' ' . $this->faker->numerify('### ###') : 
            null;
        
        $website = 'https://www.' . strtolower(str_replace(' ', '', explode(' ', $name)[0])) . 
                  $this->faker->randomElement(['.es', '.com', '.net']);
        
        $emailCustomer = 'clientes@' . parse_url($website, PHP_URL_HOST);
        $emailCommercial = $this->faker->boolean(60) ? 
            'comercial@' . parse_url($website, PHP_URL_HOST) : 
            null;
        
        // Ofertas destacadas típicas del mercado español
        $highlightedOffers = [
            'Tarifa indexada al PVPC con descuento',
            'Precio fijo 12 meses sin sorpresas',
            'Energía 100% renovable certificada',
            'Tarifa plana sin permanencia',
            'Descuentos en franjas horarias',
            'Gestión 100% digital',
            'Autoconsumo + comercialización',
            'App inteligente para optimizar consumo',
            null, // Algunas empresas sin oferta destacada
        ];
        
        // Direcciones típicas españolas
        $addresses = [
            'Calle de Alcalá, ' . $this->faker->numberBetween(1, 500) . ', 28027 Madrid',
            'Avinguda del Paral·lel, ' . $this->faker->numberBetween(1, 300) . ', 08015 Barcelona',
            'Calle Colón, ' . $this->faker->numberBetween(1, 100) . ', 46004 Valencia',
            'Avenida de la Constitución, ' . $this->faker->numberBetween(1, 50) . ', 41004 Sevilla',
            'Plaza Euskadi, ' . $this->faker->numberBetween(1, 20) . ', 48009 Bilbao',
            'Rúa Real, ' . $this->faker->numberBetween(1, 200) . ', 15003 A Coruña',
        ];
        
        // Coordenadas aproximadas de ciudades españolas principales
        $locations = [
            ['lat' => 40.4168, 'lng' => -3.7038], // Madrid
            ['lat' => 41.3851, 'lng' => 2.1734],  // Barcelona
            ['lat' => 39.4699, 'lng' => -0.3763], // Valencia
            ['lat' => 37.3891, 'lng' => -5.9845], // Sevilla
            ['lat' => 43.2627, 'lng' => -2.9253], // Bilbao
            ['lat' => 43.3623, 'lng' => -8.4115], // A Coruña
        ];
        
        $location = $this->faker->randomElement($locations);
        
        return [
            'name' => $name,
            'slug' => $slug,
            'website' => $website,
            'phone_customer' => $phoneCustomer,
            'phone_commercial' => $phoneCommercial,
            'email_customer' => $emailCustomer,
            'email_commercial' => $emailCommercial,
            'highlighted_offer' => $this->faker->randomElement($highlightedOffers),
            'cnmc_id' => $this->faker->boolean(80) ? 
                $this->faker->numerify('####') : 
                null,
            'logo_url' => null, // Se puede añadir URLs de logos si es necesario
            'company_type' => $this->faker->randomElement($companyTypes),
            'address' => $this->faker->randomElement($addresses),
            'latitude' => $location['lat'] + $this->faker->randomFloat(4, -0.1, 0.1),
            'longitude' => $location['lng'] + $this->faker->randomFloat(4, -0.1, 0.1),
            'coverage_scope' => $this->faker->randomElement($coverageScopes),
            'municipality_id' => null, // Se puede añadir municipios si es necesario
            'image_id' => null, // Se puede añadir imágenes si es necesario
        ];
    }

    /**
     * Indicate that the company is a commercializer.
     */
    public function commercializer(): static
    {
        return $this->state(fn (array $attributes) => [
            'company_type' => 'comercializadora',
            'cnmc_id' => $this->faker->numerify('####'),
            'highlighted_offer' => $this->faker->randomElement([
                'Tarifa indexada al PVPC con descuento',
                'Precio fijo 12 meses sin sorpresas',
                'Tarifa plana sin permanencia',
            ]),
        ]);
    }

    /**
     * Indicate that the company is a distributor.
     */
    public function distributor(): static
    {
        return $this->state(fn (array $attributes) => [
            'company_type' => 'distribuidora',
            'highlighted_offer' => null, // Distribuidoras no tienen ofertas comerciales
            'email_commercial' => null,
            'phone_commercial' => null,
        ]);
    }

    /**
     * Indicate that the company is a cooperative.
     */
    public function cooperative(): static
    {
        return $this->state(function (array $attributes) {
            $name = $this->faker->randomElement([
                'Som Energia',
                'Goiener',
                'Zencer',
                'Energética Coop',
                'Cooperativa Verde',
                'Renovables Unidos',
            ]);
            
            return [
                'name' => $name,
                'slug' => Str::slug($name),
                'company_type' => 'cooperativa',
                'cnmc_id' => $this->faker->numerify('04##'),
                'highlighted_offer' => $this->faker->randomElement([
                    '100% energía renovable certificada',
                    'Cooperativa digital con app móvil',
                    'Energía verde del País Vasco',
                    'Gestión democrática y transparente',
                ]),
                'coverage_scope' => 'nacional',
            ];
        });
    }

    /**
     * Indicate that the company has national coverage.
     */
    public function national(): static
    {
        return $this->state(fn (array $attributes) => [
            'coverage_scope' => 'nacional',
        ]);
    }

    /**
     * Indicate that the company has regional coverage.
     */
    public function regional(): static
    {
        return $this->state(fn (array $attributes) => [
            'coverage_scope' => 'regional',
        ]);
    }

    /**
     * Indicate that the company has local coverage.
     */
    public function local(): static
    {
        return $this->state(fn (array $attributes) => [
            'coverage_scope' => 'local',
        ]);
    }

    /**
     * Indicate that the company is in Madrid.
     */
    public function inMadrid(): static
    {
        return $this->state(fn (array $attributes) => [
            'address' => 'Calle de Alcalá, ' . $this->faker->numberBetween(1, 500) . ', 28027 Madrid',
            'latitude' => 40.4168 + $this->faker->randomFloat(4, -0.05, 0.05),
            'longitude' => -3.7038 + $this->faker->randomFloat(4, -0.05, 0.05),
        ]);
    }

    /**
     * Indicate that the company is in Barcelona.
     */
    public function inBarcelona(): static
    {
        return $this->state(fn (array $attributes) => [
            'address' => 'Avinguda del Paral·lel, ' . $this->faker->numberBetween(1, 300) . ', 08015 Barcelona',
            'latitude' => 41.3851 + $this->faker->randomFloat(4, -0.05, 0.05),
            'longitude' => 2.1734 + $this->faker->randomFloat(4, -0.05, 0.05),
        ]);
    }
}