<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PlantSpecies;

class PlantSpeciesSeeder extends Seeder
{
    public function run(): void
    {
        // Árboles autóctonos españoles
        $treesData = [
            ['name' => 'Encina', 'scientific_name' => 'Quercus ilex', 'family' => 'Fagaceae', 
             'co2_absorption_kg_per_year' => 22.5, 'plant_type' => 'tree', 'size_category' => 'large', 
             'height_min' => 8, 'height_max' => 25, 'drought_resistant' => true, 'is_endemic' => true, 
             'suitable_for_reforestation' => true, 'provides_timber' => true, 'planting_cost_eur' => 8],
            ['name' => 'Roble común', 'scientific_name' => 'Quercus robur', 'family' => 'Fagaceae',
             'co2_absorption_kg_per_year' => 28.0, 'plant_type' => 'tree', 'size_category' => 'large',
             'height_min' => 15, 'height_max' => 40, 'frost_resistant' => true, 'is_endemic' => true,
             'suitable_for_reforestation' => true, 'provides_timber' => true, 'planting_cost_eur' => 12],
            ['name' => 'Pino marítimo', 'scientific_name' => 'Pinus pinaster', 'family' => 'Pinaceae',
             'co2_absorption_kg_per_year' => 30.0, 'plant_type' => 'conifer', 'size_category' => 'large',
             'height_min' => 20, 'height_max' => 35, 'drought_resistant' => true, 'is_endemic' => true,
             'suitable_for_reforestation' => true, 'provides_timber' => true, 'planting_cost_eur' => 6],
            ['name' => 'Olivo', 'scientific_name' => 'Olea europaea', 'family' => 'Oleaceae',
             'co2_absorption_kg_per_year' => 12.0, 'plant_type' => 'tree', 'size_category' => 'medium',
             'height_min' => 4, 'height_max' => 12, 'drought_resistant' => true, 'provides_food' => true,
             'suitable_for_urban' => true, 'planting_cost_eur' => 25],
        ];

        $shrubsData = [
            ['name' => 'Romero', 'scientific_name' => 'Rosmarinus officinalis', 'family' => 'Lamiaceae',
             'co2_absorption_kg_per_year' => 3.5, 'plant_type' => 'shrub', 'size_category' => 'small',
             'height_min' => 0.5, 'height_max' => 2, 'drought_resistant' => true, 'medicinal_use' => true,
             'suitable_for_urban' => true, 'planting_cost_eur' => 2],
            ['name' => 'Lavanda', 'scientific_name' => 'Lavandula angustifolia', 'family' => 'Lamiaceae',
             'co2_absorption_kg_per_year' => 2.8, 'plant_type' => 'shrub', 'size_category' => 'small',
             'height_min' => 0.3, 'height_max' => 1, 'drought_resistant' => true, 'medicinal_use' => true,
             'suitable_for_urban' => true, 'planting_cost_eur' => 3],
        ];

        $allSpecies = array_merge($treesData, $shrubsData);

        foreach ($allSpecies as $species) {
            $species['slug'] = \Str::slug($species['name']);
            $co2Base = $species['co2_absorption_kg_per_year'];
            $species['co2_absorption_min'] = round($co2Base * 0.7, 1);
            $species['co2_absorption_max'] = round($co2Base * 1.3, 1);
            $species['description'] = "Especie {$species['name']} típica de España";
            $species['lifespan_years'] = 100;
            $species['growth_rate_cm_year'] = 30;
            $species['climate_zones'] = ['mediterráneo'];
            $species['soil_types'] = 'cualquier tipo de suelo';
            $species['water_needs_mm_year'] = 500;
            $species['survival_rate_percent'] = 85;
            $species['maintenance_cost_eur_year'] = 1.0;
            $species['source'] = 'manual';
            $species['is_verified'] = true;
            $species['verification_entity'] = 'MITECO';

            PlantSpecies::firstOrCreate(['slug' => $species['slug']], $species);
        }

        // Crear especies adicionales con factory (comentado por duplicados)
        // PlantSpecies::factory(8)->create();

        $this->command->info('Plant Species seeder completed: ' . PlantSpecies::count() . ' species created.');
    }
}