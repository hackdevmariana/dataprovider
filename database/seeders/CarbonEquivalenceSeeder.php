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
            ['name' => 'Electricidad - Solar', 'co2_kg_equivalent' => 0.04, 'category' => 'energy', 'unit' => 'kWh', 'source' => 'IPCC', 'is_verified' => true],
            ['name' => 'Electricidad - Eólica', 'co2_kg_equivalent' => 0.02, 'category' => 'energy', 'unit' => 'kWh', 'source' => 'IPCC', 'is_verified' => true],
            ['name' => 'Gas Natural', 'co2_kg_equivalent' => 2.03, 'category' => 'energy', 'unit' => 'm³', 'source' => 'MITECO', 'is_verified' => true],
        ];

        // Transporte
        $transportData = [
            ['name' => 'Coche - Gasolina', 'co2_kg_equivalent' => 0.12, 'category' => 'transport', 'unit' => 'km', 'source' => 'MITECO', 'is_verified' => true],
            ['name' => 'Coche - Eléctrico', 'co2_kg_equivalent' => 0.05, 'category' => 'transport', 'unit' => 'km', 'source' => 'MITECO', 'is_verified' => true],
            ['name' => 'Tren AVE', 'co2_kg_equivalent' => 0.03, 'category' => 'transport', 'unit' => 'km', 'source' => 'Renfe', 'is_verified' => true],
            ['name' => 'Avión Doméstico', 'co2_kg_equivalent' => 0.25, 'category' => 'transport', 'unit' => 'km', 'source' => 'ICAO', 'is_verified' => true],
        ];

        // Alimentación
        $foodData = [
            ['name' => 'Carne de Res', 'co2_kg_equivalent' => 27.0, 'category' => 'food', 'unit' => 'kg', 'source' => 'FAO', 'is_verified' => true],
            ['name' => 'Pollo', 'co2_kg_equivalent' => 6.9, 'category' => 'food', 'unit' => 'kg', 'source' => 'FAO', 'is_verified' => true],
            ['name' => 'Leche', 'co2_kg_equivalent' => 3.2, 'category' => 'food', 'unit' => 'litro', 'source' => 'FAO', 'is_verified' => true],
            ['name' => 'Arroz', 'co2_kg_equivalent' => 2.7, 'category' => 'food', 'unit' => 'kg', 'source' => 'FAO', 'is_verified' => true],
        ];

        $allData = array_merge($energyData, $transportData, $foodData);

        foreach ($allData as $data) {
            $data['slug'] = \Str::slug($data['name']);
            $data['description'] = "Factor de emisión para {$data['name']}";
            $data['calculation_method'] = 'LCA';
            $data['verification_entity'] = $data['source'];
            $data['last_updated'] = now();

            CarbonEquivalence::firstOrCreate(['slug' => $data['slug']], $data);
        }

        // Crear datos adicionales con factory (comentado por duplicados)
        // CarbonEquivalence::factory(8)->create();

        $this->command->info('Carbon Equivalence seeder completed: ' . CarbonEquivalence::count() . ' equivalences created.');
    }
}