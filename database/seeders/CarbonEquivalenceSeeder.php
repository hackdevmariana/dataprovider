<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CarbonEquivalence;

class CarbonEquivalenceSeeder extends Seeder
{
    public function run(): void
    {
        // Equivalencias energéticas oficiales de España
        $energyData = [
            ['name' => 'Electricidad - Mix Español', 'co2_kg_equivalent' => 0.233, 'category' => 'energy', 'unit' => 'kWh', 'source' => 'REE', 'is_verified' => true],
            ['name' => 'Electricidad - Solar Fotovoltaica', 'co2_kg_equivalent' => 0.04, 'category' => 'energy', 'unit' => 'kWh', 'source' => 'IPCC', 'is_verified' => true],
            ['name' => 'Electricidad - Eólica', 'co2_kg_equivalent' => 0.02, 'category' => 'energy', 'unit' => 'kWh', 'source' => 'IPCC', 'is_verified' => true],
            ['name' => 'Electricidad - Hidroeléctrica', 'co2_kg_equivalent' => 0.01, 'category' => 'energy', 'unit' => 'kWh', 'source' => 'IPCC', 'is_verified' => true],
            ['name' => 'Electricidad - Nuclear', 'co2_kg_equivalent' => 0.012, 'category' => 'energy', 'unit' => 'kWh', 'source' => 'IPCC', 'is_verified' => true],
            ['name' => 'Electricidad - Carbón', 'co2_kg_equivalent' => 0.82, 'category' => 'energy', 'unit' => 'kWh', 'source' => 'IPCC', 'is_verified' => true],
            ['name' => 'Gas Natural', 'co2_kg_equivalent' => 2.03, 'category' => 'energy', 'unit' => 'm³', 'source' => 'MITECO', 'is_verified' => true],
            ['name' => 'Gasóleo C', 'co2_kg_equivalent' => 2.68, 'category' => 'energy', 'unit' => 'kg', 'source' => 'MITECO', 'is_verified' => true],
            ['name' => 'Propano', 'co2_kg_equivalent' => 2.95, 'category' => 'energy', 'unit' => 'kg', 'source' => 'MITECO', 'is_verified' => true],
        ];

        // Transporte
        $transportData = [
            ['name' => 'Coche - Gasolina', 'co2_kg_equivalent' => 0.12, 'category' => 'transport', 'unit' => 'km', 'source' => 'MITECO', 'is_verified' => true],
            ['name' => 'Coche - Diésel', 'co2_kg_equivalent' => 0.11, 'category' => 'transport', 'unit' => 'km', 'source' => 'MITECO', 'is_verified' => true],
            ['name' => 'Coche - Eléctrico', 'co2_kg_equivalent' => 0.05, 'category' => 'transport', 'unit' => 'km', 'source' => 'MITECO', 'is_verified' => true],
            ['name' => 'Coche - Híbrido', 'co2_kg_equivalent' => 0.08, 'category' => 'transport', 'unit' => 'km', 'source' => 'MITECO', 'is_verified' => true],
            ['name' => 'Motocicleta', 'co2_kg_equivalent' => 0.08, 'category' => 'transport', 'unit' => 'km', 'source' => 'MITECO', 'is_verified' => true],
            ['name' => 'Autobús Urbano', 'co2_kg_equivalent' => 0.08, 'category' => 'transport', 'unit' => 'km', 'source' => 'MITECO', 'is_verified' => true],
            ['name' => 'Tren AVE', 'co2_kg_equivalent' => 0.03, 'category' => 'transport', 'unit' => 'km', 'source' => 'Renfe', 'is_verified' => true],
            ['name' => 'Tren Regional', 'co2_kg_equivalent' => 0.04, 'category' => 'transport', 'unit' => 'km', 'source' => 'Renfe', 'is_verified' => true],
            ['name' => 'Avión Doméstico', 'co2_kg_equivalent' => 0.25, 'category' => 'transport', 'unit' => 'km', 'source' => 'ICAO', 'is_verified' => true],
            ['name' => 'Avión Internacional', 'co2_kg_equivalent' => 0.28, 'category' => 'transport', 'unit' => 'km', 'source' => 'ICAO', 'is_verified' => true],
            ['name' => 'Barco - Ferry', 'co2_kg_equivalent' => 0.15, 'category' => 'transport', 'unit' => 'km', 'source' => 'IMO', 'is_verified' => true],
        ];

        // Alimentación
        $foodData = [
            ['name' => 'Carne de Res', 'co2_kg_equivalent' => 27.0, 'category' => 'food', 'unit' => 'kg', 'source' => 'FAO', 'is_verified' => true],
            ['name' => 'Carne de Cordero', 'co2_kg_equivalent' => 39.2, 'category' => 'food', 'unit' => 'kg', 'source' => 'FAO', 'is_verified' => true],
            ['name' => 'Carne de Cerdo', 'co2_kg_equivalent' => 12.1, 'category' => 'food', 'unit' => 'kg', 'source' => 'FAO', 'is_verified' => true],
            ['name' => 'Pollo', 'co2_kg_equivalent' => 6.9, 'category' => 'food', 'unit' => 'kg', 'source' => 'FAO', 'is_verified' => true],
            ['name' => 'Pavo', 'co2_kg_equivalent' => 10.9, 'category' => 'food', 'unit' => 'kg', 'source' => 'FAO', 'is_verified' => true],
            ['name' => 'Leche de Vaca', 'co2_kg_equivalent' => 3.2, 'category' => 'food', 'unit' => 'litro', 'source' => 'FAO', 'is_verified' => true],
            ['name' => 'Queso', 'co2_kg_equivalent' => 13.5, 'category' => 'food', 'unit' => 'kg', 'source' => 'FAO', 'is_verified' => true],
            ['name' => 'Huevos', 'co2_kg_equivalent' => 4.2, 'category' => 'food', 'unit' => 'kg', 'source' => 'FAO', 'is_verified' => true],
            ['name' => 'Arroz', 'co2_kg_equivalent' => 2.7, 'category' => 'food', 'unit' => 'kg', 'source' => 'FAO', 'is_verified' => true],
            ['name' => 'Trigo', 'co2_kg_equivalent' => 1.4, 'category' => 'food', 'unit' => 'kg', 'source' => 'FAO', 'is_verified' => true],
            ['name' => 'Maíz', 'co2_kg_equivalent' => 1.0, 'category' => 'food', 'unit' => 'kg', 'source' => 'FAO', 'is_verified' => true],
            ['name' => 'Patatas', 'co2_kg_equivalent' => 0.46, 'category' => 'food', 'unit' => 'kg', 'source' => 'FAO', 'is_verified' => true],
            ['name' => 'Tomates', 'co2_kg_equivalent' => 2.0, 'category' => 'food', 'unit' => 'kg', 'source' => 'FAO', 'is_verified' => true],
            ['name' => 'Plátanos', 'co2_kg_equivalent' => 0.8, 'category' => 'food', 'unit' => 'kg', 'source' => 'FAO', 'is_verified' => true],
            ['name' => 'Manzanas', 'co2_kg_equivalent' => 0.4, 'category' => 'food', 'unit' => 'kg', 'source' => 'FAO', 'is_verified' => true],
            ['name' => 'Naranjas', 'co2_kg_equivalent' => 0.3, 'category' => 'food', 'unit' => 'kg', 'source' => 'FAO', 'is_verified' => true],
            ['name' => 'Café', 'co2_kg_equivalent' => 16.5, 'category' => 'food', 'unit' => 'kg', 'source' => 'FAO', 'is_verified' => true],
            ['name' => 'Chocolate', 'co2_kg_equivalent' => 19.0, 'category' => 'food', 'unit' => 'kg', 'source' => 'FAO', 'is_verified' => true],
        ];

        // Construcción y Materiales
        $constructionData = [
            ['name' => 'Cemento', 'co2_kg_equivalent' => 0.9, 'category' => 'construction', 'unit' => 'kg', 'source' => 'CEMBUREAU', 'is_verified' => true],
            ['name' => 'Acero', 'co2_kg_equivalent' => 1.85, 'category' => 'construction', 'unit' => 'kg', 'source' => 'World Steel', 'is_verified' => true],
            ['name' => 'Aluminio', 'co2_kg_equivalent' => 8.24, 'category' => 'construction', 'unit' => 'kg', 'source' => 'IAI', 'is_verified' => true],
            ['name' => 'Vidrio', 'co2_kg_equivalent' => 0.85, 'category' => 'construction', 'unit' => 'kg', 'source' => 'Glass Europe', 'is_verified' => true],
            ['name' => 'Madera', 'co2_kg_equivalent' => 0.3, 'category' => 'construction', 'unit' => 'kg', 'source' => 'FSC', 'is_verified' => true],
            ['name' => 'Ladrillo', 'co2_kg_equivalent' => 0.24, 'category' => 'construction', 'unit' => 'kg', 'source' => 'TBE', 'is_verified' => true],
        ];

        // Residuos
        $wasteData = [
            ['name' => 'Residuos Orgánicos - Compostaje', 'co2_kg_equivalent' => -0.5, 'category' => 'waste', 'unit' => 'kg', 'source' => 'MITECO', 'is_verified' => true],
            ['name' => 'Residuos Orgánicos - Vertedero', 'co2_kg_equivalent' => 0.4, 'category' => 'waste', 'unit' => 'kg', 'source' => 'MITECO', 'is_verified' => true],
            ['name' => 'Residuos Plásticos - Reciclaje', 'co2_kg_equivalent' => -0.8, 'category' => 'waste', 'unit' => 'kg', 'source' => 'MITECO', 'is_verified' => true],
            ['name' => 'Residuos Plásticos - Incineración', 'co2_kg_equivalent' => 2.5, 'category' => 'waste', 'unit' => 'kg', 'source' => 'MITECO', 'is_verified' => true],
            ['name' => 'Residuos Papel - Reciclaje', 'co2_kg_equivalent' => -0.3, 'category' => 'waste', 'unit' => 'kg', 'source' => 'MITECO', 'is_verified' => true],
            ['name' => 'Residuos Papel - Vertedero', 'co2_kg_equivalent' => 0.1, 'category' => 'waste', 'unit' => 'kg', 'source' => 'MITECO', 'is_verified' => true],
        ];

        // Agricultura
        $agricultureData = [
            ['name' => 'Fertilizante Nitrogenado', 'co2_kg_equivalent' => 5.4, 'category' => 'agriculture', 'unit' => 'kg', 'source' => 'IFA', 'is_verified' => true],
            ['name' => 'Fertilizante Fosfórico', 'co2_kg_equivalent' => 1.2, 'category' => 'agriculture', 'unit' => 'kg', 'source' => 'IFA', 'is_verified' => true],
            ['name' => 'Pesticida', 'co2_kg_equivalent' => 8.0, 'category' => 'agriculture', 'unit' => 'kg', 'source' => 'CropLife', 'is_verified' => true],
            ['name' => 'Riego por Goteo', 'co2_kg_equivalent' => 0.05, 'category' => 'agriculture', 'unit' => 'm³', 'source' => 'FAO', 'is_verified' => true],
            ['name' => 'Riego por Aspersión', 'co2_kg_equivalent' => 0.08, 'category' => 'agriculture', 'unit' => 'm³', 'source' => 'FAO', 'is_verified' => true],
        ];

        $allData = array_merge($energyData, $transportData, $foodData, $constructionData, $wasteData, $agricultureData);

        foreach ($allData as $data) {
            $data['slug'] = \Str::slug($data['name']);
            $data['description'] = "Factor de emisión de CO2 equivalente para {$data['name']}. Datos oficiales de {$data['source']}.";
            $data['calculation_method'] = 'LCA';
            $data['verification_entity'] = $data['source'];
            $data['last_updated'] = now();
            
            // Agregar algunos factores de eficiencia y pérdida aleatorios
            if (rand(0, 1)) {
                $data['efficiency_ratio'] = round(rand(80, 120) / 100, 3);
            }
            if (rand(0, 1)) {
                $data['loss_factor'] = round(rand(1, 15) / 100, 3);
            }

            CarbonEquivalence::firstOrCreate(['slug' => $data['slug']], $data);
        }

        $this->command->info('Carbon Equivalence seeder completed: ' . CarbonEquivalence::count() . ' equivalences created.');
    }
}