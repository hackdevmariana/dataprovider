<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Color;

class ColorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colors = [
            // Colores primarios
            [
                'name' => 'Azul Primario',
                'slug' => 'azul-primario',
                'hex_code' => '#0066CC',
                'rgb_code' => 'rgb(0, 102, 204)',
                'hsl_code' => 'hsl(210, 100%, 40%)',
                'is_primary' => true,
                'description' => 'Color azul principal para la identidad corporativa',
            ],
            [
                'name' => 'Verde Energía',
                'slug' => 'verde-energia',
                'hex_code' => '#00CC66',
                'rgb_code' => 'rgb(0, 204, 102)',
                'hsl_code' => 'hsl(150, 100%, 40%)',
                'is_primary' => true,
                'description' => 'Verde representativo de energías renovables',
            ],
            [
                'name' => 'Naranja Solar',
                'slug' => 'naranja-solar',
                'hex_code' => '#FF6600',
                'rgb_code' => 'rgb(255, 102, 0)',
                'hsl_code' => 'hsl(24, 100%, 50%)',
                'is_primary' => true,
                'description' => 'Naranja que representa la energía solar',
            ],

            // Colores secundarios
            [
                'name' => 'Azul Claro',
                'slug' => 'azul-claro',
                'hex_code' => '#66B3FF',
                'rgb_code' => 'rgb(102, 179, 255)',
                'hsl_code' => 'hsl(210, 100%, 70%)',
                'is_primary' => false,
                'description' => 'Variación clara del azul primario',
            ],
            [
                'name' => 'Verde Claro',
                'slug' => 'verde-claro',
                'hex_code' => '#66FF99',
                'rgb_code' => 'rgb(102, 255, 153)',
                'hsl_code' => 'hsl(150, 100%, 70%)',
                'is_primary' => false,
                'description' => 'Variación clara del verde energía',
            ],
            [
                'name' => 'Naranja Claro',
                'slug' => 'naranja-claro',
                'hex_code' => '#FF9966',
                'rgb_code' => 'rgb(255, 153, 102)',
                'hsl_code' => 'hsl(24, 100%, 70%)',
                'is_primary' => false,
                'description' => 'Variación clara del naranja solar',
            ],

            // Colores de acento
            [
                'name' => 'Amarillo Energía',
                'slug' => 'amarillo-energia',
                'hex_code' => '#FFCC00',
                'rgb_code' => 'rgb(255, 204, 0)',
                'hsl_code' => 'hsl(48, 100%, 50%)',
                'is_primary' => false,
                'description' => 'Amarillo para destacar elementos energéticos',
            ],
            [
                'name' => 'Rojo Alerta',
                'slug' => 'rojo-alerta',
                'hex_code' => '#CC0000',
                'rgb_code' => 'rgb(204, 0, 0)',
                'hsl_code' => 'hsl(0, 100%, 40%)',
                'is_primary' => false,
                'description' => 'Rojo para alertas y advertencias',
            ],
            [
                'name' => 'Morado Innovación',
                'slug' => 'morado-innovacion',
                'hex_code' => '#6600CC',
                'rgb_code' => 'rgb(102, 0, 204)',
                'hsl_code' => 'hsl(270, 100%, 40%)',
                'is_primary' => false,
                'description' => 'Morado para elementos de innovación tecnológica',
            ],

            // Colores neutros
            [
                'name' => 'Gris Oscuro',
                'slug' => 'gris-oscuro',
                'hex_code' => '#333333',
                'rgb_code' => 'rgb(51, 51, 51)',
                'hsl_code' => 'hsl(0, 0%, 20%)',
                'is_primary' => false,
                'description' => 'Gris oscuro para textos principales',
            ],
            [
                'name' => 'Gris Medio',
                'slug' => 'gris-medio',
                'hex_code' => '#666666',
                'rgb_code' => 'rgb(102, 102, 102)',
                'hsl_code' => 'hsl(0, 0%, 40%)',
                'is_primary' => false,
                'description' => 'Gris medio para textos secundarios',
            ],
            [
                'name' => 'Gris Claro',
                'slug' => 'gris-claro',
                'hex_code' => '#CCCCCC',
                'rgb_code' => 'rgb(204, 204, 204)',
                'hsl_code' => 'hsl(0, 0%, 80%)',
                'is_primary' => false,
                'description' => 'Gris claro para fondos y bordes',
            ],
            [
                'name' => 'Blanco',
                'slug' => 'blanco',
                'hex_code' => '#FFFFFF',
                'rgb_code' => 'rgb(255, 255, 255)',
                'hsl_code' => 'hsl(0, 0%, 100%)',
                'is_primary' => false,
                'description' => 'Blanco para fondos principales',
            ],

            // Colores específicos de energías renovables
            [
                'name' => 'Azul Eólico',
                'slug' => 'azul-eolico',
                'hex_code' => '#0066FF',
                'rgb_code' => 'rgb(0, 102, 255)',
                'hsl_code' => 'hsl(220, 100%, 50%)',
                'is_primary' => false,
                'description' => 'Azul específico para energía eólica',
            ],
            [
                'name' => 'Verde Hidroeléctrico',
                'slug' => 'verde-hidroelectrico',
                'hex_code' => '#00AA55',
                'rgb_code' => 'rgb(0, 170, 85)',
                'hsl_code' => 'hsl(150, 100%, 33%)',
                'is_primary' => false,
                'description' => 'Verde específico para energía hidroeléctrica',
            ],
            [
                'name' => 'Marrón Biomasa',
                'slug' => 'marron-biomasa',
                'hex_code' => '#8B4513',
                'rgb_code' => 'rgb(139, 69, 19)',
                'hsl_code' => 'hsl(25, 76%, 31%)',
                'is_primary' => false,
                'description' => 'Marrón específico para biomasa',
            ],
            [
                'name' => 'Azul Geotérmico',
                'slug' => 'azul-geotermico',
                'hex_code' => '#4169E1',
                'rgb_code' => 'rgb(65, 105, 225)',
                'hsl_code' => 'hsl(225, 73%, 57%)',
                'is_primary' => false,
                'description' => 'Azul específico para energía geotérmica',
            ],

            // Colores de estado
            [
                'name' => 'Verde Éxito',
                'slug' => 'verde-exito',
                'hex_code' => '#28A745',
                'rgb_code' => 'rgb(40, 167, 69)',
                'hsl_code' => 'hsl(134, 61%, 41%)',
                'is_primary' => false,
                'description' => 'Verde para indicar éxito o aprobación',
            ],
            [
                'name' => 'Amarillo Advertencia',
                'slug' => 'amarillo-advertencia',
                'hex_code' => '#FFC107',
                'rgb_code' => 'rgb(255, 193, 7)',
                'hsl_code' => 'hsl(45, 100%, 51%)',
                'is_primary' => false,
                'description' => 'Amarillo para advertencias',
            ],
            [
                'name' => 'Rojo Error',
                'slug' => 'rojo-error',
                'hex_code' => '#DC3545',
                'rgb_code' => 'rgb(220, 53, 69)',
                'hsl_code' => 'hsl(354, 70%, 54%)',
                'is_primary' => false,
                'description' => 'Rojo para errores o rechazos',
            ],
            [
                'name' => 'Azul Información',
                'slug' => 'azul-informacion',
                'hex_code' => '#17A2B8',
                'rgb_code' => 'rgb(23, 162, 184)',
                'hsl_code' => 'hsl(188, 78%, 41%)',
                'is_primary' => false,
                'description' => 'Azul para información general',
            ],

            // Colores de gradiente
            [
                'name' => 'Gradiente Solar',
                'slug' => 'gradiente-solar',
                'hex_code' => '#FF6B35',
                'rgb_code' => 'rgb(255, 107, 53)',
                'hsl_code' => 'hsl(15, 100%, 60%)',
                'is_primary' => false,
                'description' => 'Color para gradientes solares',
            ],
            [
                'name' => 'Gradiente Eólico',
                'slug' => 'gradiente-eolico',
                'hex_code' => '#4A90E2',
                'rgb_code' => 'rgb(74, 144, 226)',
                'hsl_code' => 'hsl(210, 73%, 59%)',
                'is_primary' => false,
                'description' => 'Color para gradientes eólicos',
            ],
            [
                'name' => 'Gradiente Hidroeléctrico',
                'slug' => 'gradiente-hidroelectrico',
                'hex_code' => '#50C878',
                'rgb_code' => 'rgb(80, 200, 120)',
                'hsl_code' => 'hsl(140, 52%, 55%)',
                'is_primary' => false,
                'description' => 'Color para gradientes hidroeléctricos',
            ],

            // Colores de accesibilidad
            [
                'name' => 'Azul Accesible',
                'slug' => 'azul-accesible',
                'hex_code' => '#0056B3',
                'rgb_code' => 'rgb(0, 86, 179)',
                'hsl_code' => 'hsl(210, 100%, 35%)',
                'is_primary' => false,
                'description' => 'Azul con alto contraste para accesibilidad',
            ],
            [
                'name' => 'Verde Accesible',
                'slug' => 'verde-accesible',
                'hex_code' => '#006600',
                'rgb_code' => 'rgb(0, 102, 0)',
                'hsl_code' => 'hsl(120, 100%, 20%)',
                'is_primary' => false,
                'description' => 'Verde con alto contraste para accesibilidad',
            ],
            [
                'name' => 'Rojo Accesible',
                'slug' => 'rojo-accesible',
                'hex_code' => '#B30000',
                'rgb_code' => 'rgb(179, 0, 0)',
                'hsl_code' => 'hsl(0, 100%, 35%)',
                'is_primary' => false,
                'description' => 'Rojo con alto contraste para accesibilidad',
            ],

            // Colores de marca
            [
                'name' => 'Azul Corporativo',
                'slug' => 'azul-corporativo',
                'hex_code' => '#1E3A8A',
                'rgb_code' => 'rgb(30, 58, 138)',
                'hsl_code' => 'hsl(220, 64%, 33%)',
                'is_primary' => false,
                'description' => 'Azul corporativo principal',
            ],
            [
                'name' => 'Verde Corporativo',
                'slug' => 'verde-corporativo',
                'hex_code' => '#059669',
                'rgb_code' => 'rgb(5, 150, 105)',
                'hsl_code' => 'hsl(160, 94%, 30%)',
                'is_primary' => false,
                'description' => 'Verde corporativo principal',
            ],
            [
                'name' => 'Naranja Corporativo',
                'slug' => 'naranja-corporativo',
                'hex_code' => '#EA580C',
                'rgb_code' => 'rgb(234, 88, 12)',
                'hsl_code' => 'hsl(20, 90%, 48%)',
                'is_primary' => false,
                'description' => 'Naranja corporativo principal',
            ],
        ];

        foreach ($colors as $color) {
            Color::firstOrCreate(
                ['slug' => $color['slug']], // Condición de búsqueda
                $color // Datos a crear si no existe
            );
        }

        $this->command->info('Se han creado ' . count($colors) . ' colores.');
    }
}

