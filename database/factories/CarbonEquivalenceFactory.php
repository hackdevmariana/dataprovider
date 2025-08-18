<?php

namespace Database\Factories;

use App\Models\CarbonEquivalence;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CarbonEquivalence>
 */
class CarbonEquivalenceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = ['energy', 'transport', 'food', 'construction', 'industry', 'waste', 'agriculture', 'other'];
        $category = $this->faker->randomElement($categories);
        
        // Generar datos realistas según la categoría
        $data = $this->generateCategoryData($category);
        
        return [
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'co2_kg_equivalent' => $data['co2'],
            'description' => $data['desc'],
            'category' => $category,
            'unit' => $data['unit'],
            'efficiency_ratio' => $this->faker->optional(0.3)->randomFloat(2, 0.8, 1.2),
            'loss_factor' => $this->faker->optional(0.2)->randomFloat(3, 0, 0.1),
            'calculation_method' => $this->faker->randomElement(['LCA', 'IPCC', 'ISO14067', 'GHG_Protocol', 'Manual']),
            'calculation_params' => $this->faker->optional(0.4)->randomElements([
                'scope_1' => true,
                'scope_2' => true,
                'scope_3' => false,
                'region' => 'EU',
                'methodology' => 'cradle_to_gate'
            ]),
            'source' => $this->faker->randomElement(['IPCC', 'DEFRA', 'EPA', 'IEA', 'Manual', 'Scientific_Study']),
            'source_url' => $this->faker->optional(0.6)->url(),
            'is_verified' => $this->faker->boolean(70),
            'verification_entity' => $this->faker->optional(0.7)->randomElement(['IPCC', 'DEFRA', 'EPA', 'ISO', 'Carbon_Trust']),
            'last_updated' => $this->faker->dateTimeBetween('-2 years', 'now'),
        ];
    }

    /**
     * Generate category-specific data.
     */
    private function generateCategoryData($category)
    {
        switch ($category) {
            case 'energy':
                return $this->generateEnergyData();
            case 'transport':
                return $this->generateTransportData();
            case 'food':
                return $this->generateFoodData();
            case 'construction':
                return $this->generateConstructionData();
            case 'industry':
                return $this->generateIndustryData();
            case 'waste':
                return $this->generateWasteData();
            case 'agriculture':
                return $this->generateAgricultureData();
            default:
                return $this->generateOtherData();
        }
    }

    private function generateEnergyData()
    {
        $energySources = [
            ['name' => 'Electricidad - Carbón', 'co2' => 0.82, 'unit' => 'kWh', 'desc' => 'Electricidad generada por centrales de carbón'],
            ['name' => 'Electricidad - Gas Natural', 'co2' => 0.35, 'unit' => 'kWh', 'desc' => 'Electricidad generada por centrales de gas natural'],
            ['name' => 'Electricidad - Solar Fotovoltaica', 'co2' => 0.04, 'unit' => 'kWh', 'desc' => 'Electricidad generada por paneles solares'],
            ['name' => 'Electricidad - Eólica', 'co2' => 0.02, 'unit' => 'kWh', 'desc' => 'Electricidad generada por aerogeneradores'],
            ['name' => 'Electricidad - Nuclear', 'co2' => 0.012, 'unit' => 'kWh', 'desc' => 'Electricidad generada por centrales nucleares'],
            ['name' => 'Electricidad - Hidroeléctrica', 'co2' => 0.024, 'unit' => 'kWh', 'desc' => 'Electricidad generada por centrales hidroeléctricas'],
            ['name' => 'Gas Natural - Combustión', 'co2' => 2.03, 'unit' => 'm³', 'desc' => 'Combustión directa de gas natural'],
            ['name' => 'Gasoil - Calefacción', 'co2' => 2.52, 'unit' => 'litro', 'desc' => 'Combustión de gasoil para calefacción'],
        ];
        
        return $this->faker->randomElement($energySources);
    }

    private function generateTransportData()
    {
        $transportModes = [
            ['name' => 'Coche - Gasolina', 'co2' => 0.12, 'unit' => 'km', 'desc' => 'Vehículo particular de gasolina'],
            ['name' => 'Coche - Diésel', 'co2' => 0.11, 'unit' => 'km', 'desc' => 'Vehículo particular diésel'],
            ['name' => 'Coche - Eléctrico', 'co2' => 0.05, 'unit' => 'km', 'desc' => 'Vehículo eléctrico (mix energético EU)'],
            ['name' => 'Autobús Urbano', 'co2' => 0.08, 'unit' => 'km', 'desc' => 'Transporte público urbano'],
            ['name' => 'Metro', 'co2' => 0.03, 'unit' => 'km', 'desc' => 'Metro o tren ligero'],
            ['name' => 'Tren - Media Distancia', 'co2' => 0.04, 'unit' => 'km', 'desc' => 'Tren convencional'],
            ['name' => 'Tren - Alta Velocidad', 'co2' => 0.03, 'unit' => 'km', 'desc' => 'AVE y trenes de alta velocidad'],
            ['name' => 'Avión - Vuelo Doméstico', 'co2' => 0.25, 'unit' => 'km', 'desc' => 'Vuelos nacionales'],
            ['name' => 'Avión - Vuelo Internacional', 'co2' => 0.15, 'unit' => 'km', 'desc' => 'Vuelos internacionales'],
            ['name' => 'Moto - Gasolina', 'co2' => 0.09, 'unit' => 'km', 'desc' => 'Motocicleta de gasolina'],
        ];
        
        return $this->faker->randomElement($transportModes);
    }

    private function generateFoodData()
    {
        $foodItems = [
            ['name' => 'Carne de Res', 'co2' => 27, 'unit' => 'kg', 'desc' => 'Carne de vacuno, incluyendo producción y procesado'],
            ['name' => 'Carne de Cerdo', 'co2' => 7.2, 'unit' => 'kg', 'desc' => 'Carne de cerdo, incluyendo producción y procesado'],
            ['name' => 'Pollo', 'co2' => 6.9, 'unit' => 'kg', 'desc' => 'Carne de pollo, incluyendo producción y procesado'],
            ['name' => 'Pescado - Salmón', 'co2' => 11.9, 'unit' => 'kg', 'desc' => 'Salmón de piscifactoría'],
            ['name' => 'Pescado - Atún', 'co2' => 6.1, 'unit' => 'kg', 'desc' => 'Atún de pesca'],
            ['name' => 'Leche de Vaca', 'co2' => 3.2, 'unit' => 'litro', 'desc' => 'Leche de vaca pasteurizada'],
            ['name' => 'Queso', 'co2' => 13.5, 'unit' => 'kg', 'desc' => 'Queso curado medio'],
            ['name' => 'Huevos', 'co2' => 4.2, 'unit' => 'kg', 'desc' => 'Huevos de gallina'],
            ['name' => 'Arroz', 'co2' => 2.7, 'unit' => 'kg', 'desc' => 'Arroz blanco'],
            ['name' => 'Trigo - Pan', 'co2' => 1.2, 'unit' => 'kg', 'desc' => 'Pan de trigo'],
            ['name' => 'Patatas', 'co2' => 0.3, 'unit' => 'kg', 'desc' => 'Patatas frescas'],
            ['name' => 'Tomates', 'co2' => 1.4, 'unit' => 'kg', 'desc' => 'Tomates frescos'],
            ['name' => 'Manzanas', 'co2' => 0.4, 'unit' => 'kg', 'desc' => 'Manzanas frescas'],
        ];
        
        return $this->faker->randomElement($foodItems);
    }

    private function generateConstructionData()
    {
        $materials = [
            ['name' => 'Cemento Portland', 'co2' => 0.82, 'unit' => 'kg', 'desc' => 'Cemento Portland ordinario'],
            ['name' => 'Acero - Estructural', 'co2' => 2.3, 'unit' => 'kg', 'desc' => 'Acero para construcción'],
            ['name' => 'Aluminio', 'co2' => 11.5, 'unit' => 'kg', 'desc' => 'Aluminio primario'],
            ['name' => 'Ladrillo', 'co2' => 0.24, 'unit' => 'kg', 'desc' => 'Ladrillo cerámico'],
            ['name' => 'Madera - Tablero', 'co2' => 0.72, 'unit' => 'm²', 'desc' => 'Tablero de madera contrachapada'],
            ['name' => 'Vidrio', 'co2' => 0.85, 'unit' => 'kg', 'desc' => 'Vidrio flotado'],
            ['name' => 'Yeso', 'co2' => 0.12, 'unit' => 'kg', 'desc' => 'Yeso para construcción'],
        ];
        
        return $this->faker->randomElement($materials);
    }

    private function generateIndustryData()
    {
        $processes = [
            ['name' => 'Plástico - PET', 'co2' => 3.4, 'unit' => 'kg', 'desc' => 'Producción de PET'],
            ['name' => 'Papel - Reciclado', 'co2' => 0.9, 'unit' => 'kg', 'desc' => 'Papel reciclado'],
            ['name' => 'Papel - Virgen', 'co2' => 1.8, 'unit' => 'kg', 'desc' => 'Papel de fibra virgen'],
            ['name' => 'Textil - Algodón', 'co2' => 8.5, 'unit' => 'kg', 'desc' => 'Producción de algodón'],
            ['name' => 'Textil - Poliéster', 'co2' => 9.5, 'unit' => 'kg', 'desc' => 'Producción de poliéster'],
        ];
        
        return $this->faker->randomElement($processes);
    }

    private function generateWasteData()
    {
        $wasteTypes = [
            ['name' => 'Residuo Orgánico - Compostaje', 'co2' => 0.1, 'unit' => 'kg', 'desc' => 'Compostaje de residuos orgánicos'],
            ['name' => 'Residuo Orgánico - Vertedero', 'co2' => 0.5, 'unit' => 'kg', 'desc' => 'Deposición en vertedero'],
            ['name' => 'Papel - Reciclaje', 'co2' => -0.7, 'unit' => 'kg', 'desc' => 'Beneficio del reciclaje'],
            ['name' => 'Plástico - Incineración', 'co2' => 2.1, 'unit' => 'kg', 'desc' => 'Incineración de plásticos'],
        ];
        
        return $this->faker->randomElement($wasteTypes);
    }

    private function generateAgricultureData()
    {
        $agriculture = [
            ['name' => 'Fertilizante - Urea', 'co2' => 1.4, 'unit' => 'kg', 'desc' => 'Producción y aplicación de urea'],
            ['name' => 'Fertilizante - NPK', 'co2' => 2.1, 'unit' => 'kg', 'desc' => 'Fertilizante compuesto NPK'],
            ['name' => 'Tractor Agrícola', 'co2' => 0.8, 'unit' => 'hora', 'desc' => 'Uso de maquinaria agrícola'],
        ];
        
        return $this->faker->randomElement($agriculture);
    }

    private function generateOtherData()
    {
        $others = [
            ['name' => 'Servidor Web - Cloud', 'co2' => 0.005, 'unit' => 'hora', 'desc' => 'Servidor virtual en la nube'],
            ['name' => 'Smartphone - Uso', 'co2' => 0.012, 'unit' => 'día', 'desc' => 'Uso diario de smartphone'],
            ['name' => 'Lavadora - Ciclo', 'co2' => 0.6, 'unit' => 'ciclo', 'desc' => 'Ciclo completo de lavadora'],
        ];
        
        return $this->faker->randomElement($others);
    }

    /**
     * Indicate that the equivalence is energy-related.
     */
    public function energy(): static
    {
        return $this->state(fn (array $attributes) => array_merge(
            $attributes,
            $this->generateEnergyData(),
            ['category' => 'energy']
        ));
    }

    /**
     * Indicate that the equivalence is transport-related.
     */
    public function transport(): static
    {
        return $this->state(fn (array $attributes) => array_merge(
            $attributes,
            $this->generateTransportData(),
            ['category' => 'transport']
        ));
    }

    /**
     * Indicate that the equivalence is food-related.
     */
    public function food(): static
    {
        return $this->state(fn (array $attributes) => array_merge(
            $attributes,
            $this->generateFoodData(),
            ['category' => 'food']
        ));
    }

    /**
     * Indicate that the equivalence is verified.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => true,
            'verification_entity' => 'IPCC',
            'source' => 'IPCC',
        ]);
    }

    /**
     * Indicate that the equivalence has low CO2 impact.
     */
    public function lowImpact(): static
    {
        return $this->state(fn (array $attributes) => [
            'co2_kg_equivalent' => $this->faker->randomFloat(3, 0.001, 0.999),
        ]);
    }

    /**
     * Indicate that the equivalence has high CO2 impact.
     */
    public function highImpact(): static
    {
        return $this->state(fn (array $attributes) => [
            'co2_kg_equivalent' => $this->faker->randomFloat(1, 10, 100),
        ]);
    }
}