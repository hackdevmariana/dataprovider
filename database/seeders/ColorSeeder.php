<?php

namespace Database\Seeders;

use App\Models\Color;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Colores primarios
        $this->createPrimaryColors();
        
        // Colores secundarios
        $this->createSecondaryColors();
        
        // Colores neutros
        $this->createNeutralColors();
        
        // Colores energéticos (verdes, azules)
        $this->createEnergyColors();
        
        // Colores corporativos
        $this->createCorporateColors();
    }

    /**
     * Crear colores primarios.
     */
    private function createPrimaryColors(): void
    {
        $primaryColors = [
            [
                'name' => 'Rojo Primario',
                'hex_code' => '#FF0000',
                'rgb_code' => 'rgb(255, 0, 0)',
                'hsl_code' => 'hsl(0, 100%, 50%)',
                'is_primary' => true,
                'description' => 'Rojo puro, color primario básico para identidades corporativas.',
            ],
            [
                'name' => 'Azul Primario',
                'hex_code' => '#0000FF',
                'rgb_code' => 'rgb(0, 0, 255)',
                'hsl_code' => 'hsl(240, 100%, 50%)',
                'is_primary' => true,
                'description' => 'Azul puro, color primario básico para identidades corporativas.',
            ],
            [
                'name' => 'Amarillo Primario',
                'hex_code' => '#FFFF00',
                'rgb_code' => 'rgb(255, 255, 0)',
                'hsl_code' => 'hsl(60, 100%, 50%)',
                'is_primary' => true,
                'description' => 'Amarillo puro, color primario básico para identidades corporativas.',
            ],
        ];

        foreach ($primaryColors as $colorData) {
            $colorData['slug'] = Str::slug($colorData['name']);
            Color::create($colorData);
        }
    }

    /**
     * Crear colores secundarios.
     */
    private function createSecondaryColors(): void
    {
        $secondaryColors = [
            [
                'name' => 'Verde',
                'hex_code' => '#00FF00',
                'rgb_code' => 'rgb(0, 255, 0)',
                'hsl_code' => 'hsl(120, 100%, 50%)',
                'is_primary' => false,
                'description' => 'Verde puro, ideal para temas ecológicos y energías renovables.',
            ],
            [
                'name' => 'Naranja',
                'hex_code' => '#FF8000',
                'rgb_code' => 'rgb(255, 128, 0)',
                'hsl_code' => 'hsl(30, 100%, 50%)',
                'is_primary' => false,
                'description' => 'Naranja vibrante, perfecto para llamar la atención.',
            ],
            [
                'name' => 'Púrpura',
                'hex_code' => '#8000FF',
                'rgb_code' => 'rgb(128, 0, 255)',
                'hsl_code' => 'hsl(270, 100%, 50%)',
                'is_primary' => false,
                'description' => 'Púrpura intenso, color distintivo para marcas premium.',
            ],
        ];

        foreach ($secondaryColors as $colorData) {
            $colorData['slug'] = Str::slug($colorData['name']);
            Color::create($colorData);
        }
    }

    /**
     * Crear colores neutros.
     */
    private function createNeutralColors(): void
    {
        $neutralColors = [
            [
                'name' => 'Negro',
                'hex_code' => '#000000',
                'rgb_code' => 'rgb(0, 0, 0)',
                'hsl_code' => 'hsl(0, 0%, 0%)',
                'is_primary' => false,
                'description' => 'Negro puro, esencial para textos y contrastes.',
            ],
            [
                'name' => 'Blanco',
                'hex_code' => '#FFFFFF',
                'rgb_code' => 'rgb(255, 255, 255)',
                'hsl_code' => 'hsl(0, 0%, 100%)',
                'is_primary' => false,
                'description' => 'Blanco puro, perfecto para fondos y espacios.',
            ],
            [
                'name' => 'Gris Oscuro',
                'hex_code' => '#333333',
                'rgb_code' => 'rgb(51, 51, 51)',
                'hsl_code' => 'hsl(0, 0%, 20%)',
                'is_primary' => false,
                'description' => 'Gris oscuro, elegante para textos secundarios.',
            ],
            [
                'name' => 'Gris Medio',
                'hex_code' => '#666666',
                'rgb_code' => 'rgb(102, 102, 102)',
                'hsl_code' => 'hsl(0, 0%, 40%)',
                'is_primary' => false,
                'description' => 'Gris medio, versátil para elementos secundarios.',
            ],
            [
                'name' => 'Gris Claro',
                'hex_code' => '#CCCCCC',
                'rgb_code' => 'rgb(204, 204, 204)',
                'hsl_code' => 'hsl(0, 0%, 80%)',
                'is_primary' => false,
                'description' => 'Gris claro, ideal para fondos sutiles.',
            ],
        ];

        foreach ($neutralColors as $colorData) {
            $colorData['slug'] = Str::slug($colorData['name']);
            Color::create($colorData);
        }
    }

    /**
     * Crear colores energéticos.
     */
    private function createEnergyColors(): void
    {
        $energyColors = [
            [
                'name' => 'Verde Energía',
                'hex_code' => '#00B050',
                'rgb_code' => 'rgb(0, 176, 80)',
                'hsl_code' => 'hsl(140, 100%, 35%)',
                'is_primary' => false,
                'description' => 'Verde energético, perfecto para energías renovables.',
            ],
            [
                'name' => 'Azul Solar',
                'hex_code' => '#0070C0',
                'rgb_code' => 'rgb(0, 112, 192)',
                'hsl_code' => 'hsl(200, 100%, 38%)',
                'is_primary' => false,
                'description' => 'Azul solar, ideal para tecnologías solares.',
            ],
            [
                'name' => 'Verde Sostenible',
                'hex_code' => '#2E8B57',
                'rgb_code' => 'rgb(46, 139, 87)',
                'hsl_code' => 'hsl(146, 50%, 36%)',
                'is_primary' => false,
                'description' => 'Verde sostenible, representa ecología y medio ambiente.',
            ],
            [
                'name' => 'Azul Eólico',
                'hex_code' => '#4169E1',
                'rgb_code' => 'rgb(65, 105, 225)',
                'hsl_code' => 'hsl(225, 73%, 57%)',
                'is_primary' => false,
                'description' => 'Azul eólico, perfecto para energía eólica.',
            ],
            [
                'name' => 'Amarillo Solar',
                'hex_code' => '#FFD700',
                'rgb_code' => 'rgb(255, 215, 0)',
                'hsl_code' => 'hsl(51, 100%, 50%)',
                'is_primary' => false,
                'description' => 'Amarillo solar, representa la energía del sol.',
            ],
        ];

        foreach ($energyColors as $colorData) {
            $colorData['slug'] = Str::slug($colorData['name']);
            Color::create($colorData);
        }
    }

    /**
     * Crear colores corporativos.
     */
    private function createCorporateColors(): void
    {
        $corporateColors = [
            [
                'name' => 'Azul Corporativo',
                'hex_code' => '#1E3A8A',
                'rgb_code' => 'rgb(30, 58, 138)',
                'hsl_code' => 'hsl(220, 64%, 33%)',
                'is_primary' => false,
                'description' => 'Azul corporativo profesional, ideal para empresas tecnológicas.',
            ],
            [
                'name' => 'Verde Corporativo',
                'hex_code' => '#059669',
                'rgb_code' => 'rgb(5, 150, 105)',
                'hsl_code' => 'hsl(160, 94%, 30%)',
                'is_primary' => false,
                'description' => 'Verde corporativo, perfecto para empresas sostenibles.',
            ],
            [
                'name' => 'Rojo Corporativo',
                'hex_code' => '#DC2626',
                'rgb_code' => 'rgb(220, 38, 38)',
                'hsl_code' => 'hsl(0, 84%, 51%)',
                'is_primary' => false,
                'description' => 'Rojo corporativo, ideal para marcas dinámicas.',
            ],
            [
                'name' => 'Púrpura Corporativo',
                'hex_code' => '#7C3AED',
                'rgb_code' => 'rgb(124, 58, 237)',
                'hsl_code' => 'hsl(262, 83%, 58%)',
                'is_primary' => false,
                'description' => 'Púrpura corporativo, perfecto para marcas creativas.',
            ],
            [
                'name' => 'Naranja Corporativo',
                'hex_code' => '#EA580C',
                'rgb_code' => 'rgb(234, 88, 12)',
                'hsl_code' => 'hsl(25, 90%, 48%)',
                'is_primary' => false,
                'description' => 'Naranja corporativo, ideal para marcas energéticas.',
            ],
        ];

        foreach ($corporateColors as $colorData) {
            $colorData['slug'] = Str::slug($colorData['name']);
            Color::create($colorData);
        }
    }
}