<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Color;

class ColorSeeder extends Seeder
{
    /**
     * Ejecutar el seeder para colores de KiroLux.
     */
    public function run(): void
    {
        $this->command->info('Creando paleta de colores para KiroLux...');

        // Crear paleta de colores para KiroLux
        $kiroluxColors = $this->getKiroLuxColorPalette();
        $createdCount = 0;

        foreach ($kiroluxColors as $colorData) {
            $color = Color::firstOrCreate(
                ['slug' => $colorData['slug']],
                [
                    'name' => $colorData['name'],
                    'slug' => $colorData['slug'],
                    'hex_code' => $colorData['hex_code'],
                    'rgb_code' => $colorData['rgb_code'],
                    'hsl_code' => $colorData['hsl_code'],
                    'is_primary' => $colorData['is_primary'],
                    'description' => $colorData['description'],
                ]
            );
            
            if ($color->wasRecentlyCreated) {
                $createdCount++;
            }
        }

        $this->command->info("✅ Creados {$createdCount} colores de la paleta KiroLux");

        // Crear colores adicionales del sistema
        $systemColors = $this->getSystemColors();
        foreach ($systemColors as $colorData) {
            $color = Color::firstOrCreate(
                ['slug' => $colorData['slug']],
                $colorData
            );
            
            if ($color->wasRecentlyCreated) {
                $createdCount++;
            }
        }

        $this->command->info("✅ Total creados: {$createdCount} colores");

        // Mostrar estadísticas
        $this->showStatistics();
    }

    /**
     * Paleta de colores oficial de KiroLux.
     */
    private function getKiroLuxColorPalette(): array
    {
        return [
            // Colores primarios - Energía y sostenibilidad
            [
                'name' => 'KiroLux Verde Primario',
                'slug' => 'kirolux-green-primary',
                'hex_code' => '#22C55E',
                'rgb_code' => 'rgb(34, 197, 94)',
                'hsl_code' => 'hsl(142, 71%, 45%)',
                'is_primary' => true,
                'description' => 'Color verde principal de KiroLux, representa energía renovable y sostenibilidad.',
            ],
            [
                'name' => 'KiroLux Verde Oscuro',
                'slug' => 'kirolux-green-dark',
                'hex_code' => '#16A34A',
                'rgb_code' => 'rgb(22, 163, 74)',
                'hsl_code' => 'hsl(142, 76%, 36%)',
                'is_primary' => false,
                'description' => 'Verde oscuro para elementos de contraste y texto sobre fondos claros.',
            ],
            [
                'name' => 'KiroLux Verde Claro',
                'slug' => 'kirolux-green-light',
                'hex_code' => '#4ADE80',
                'rgb_code' => 'rgb(74, 222, 128)',
                'hsl_code' => 'hsl(142, 69%, 58%)',
                'is_primary' => false,
                'description' => 'Verde claro para fondos suaves y elementos secundarios.',
            ],

            // Colores secundarios - Energía solar
            [
                'name' => 'KiroLux Amarillo Solar',
                'slug' => 'kirolux-solar-yellow',
                'hex_code' => '#FCD34D',
                'rgb_code' => 'rgb(252, 211, 77)',
                'hsl_code' => 'hsl(45, 96%, 65%)',
                'is_primary' => false,
                'description' => 'Amarillo que representa la energía solar y la luz del sol.',
            ],
            [
                'name' => 'KiroLux Naranja Energético',
                'slug' => 'kirolux-energy-orange',
                'hex_code' => '#FB923C',
                'rgb_code' => 'rgb(251, 146, 60)',
                'hsl_code' => 'hsl(27, 96%, 61%)',
                'is_primary' => false,
                'description' => 'Naranja vibrante para indicar actividad energética y alertas.',
            ],

            // Colores de soporte - Cooperativismo
            [
                'name' => 'KiroLux Azul Cooperativo',
                'slug' => 'kirolux-cooperative-blue',
                'hex_code' => '#3B82F6',
                'rgb_code' => 'rgb(59, 130, 246)',
                'hsl_code' => 'hsl(221, 91%, 60%)',
                'is_primary' => false,
                'description' => 'Azul que representa confianza, cooperación y tecnología.',
            ],
            [
                'name' => 'KiroLux Azul Marino',
                'slug' => 'kirolux-navy-blue',
                'hex_code' => '#1E40AF',
                'rgb_code' => 'rgb(30, 64, 175)',
                'hsl_code' => 'hsl(226, 83%, 40%)',
                'is_primary' => false,
                'description' => 'Azul marino para elementos de navegación y headers.',
            ],

            // Colores neutros - Interface
            [
                'name' => 'KiroLux Gris Carbón',
                'slug' => 'kirolux-charcoal-gray',
                'hex_code' => '#374151',
                'rgb_code' => 'rgb(55, 65, 81)',
                'hsl_code' => 'hsl(220, 19%, 27%)',
                'is_primary' => false,
                'description' => 'Gris oscuro para textos principales y elementos de contraste.',
            ],
            [
                'name' => 'KiroLux Gris Medio',
                'slug' => 'kirolux-medium-gray',
                'hex_code' => '#6B7280',
                'rgb_code' => 'rgb(107, 114, 128)',
                'hsl_code' => 'hsl(220, 9%, 46%)',
                'is_primary' => false,
                'description' => 'Gris medio para textos secundarios y elementos de soporte.',
            ],
            [
                'name' => 'KiroLux Gris Claro',
                'slug' => 'kirolux-light-gray',
                'hex_code' => '#F3F4F6',
                'rgb_code' => 'rgb(243, 244, 246)',
                'hsl_code' => 'hsl(220, 14%, 96%)',
                'is_primary' => false,
                'description' => 'Gris muy claro para fondos y separadores sutiles.',
            ],

            // Colores de estado - Alertas y notificaciones
            [
                'name' => 'KiroLux Éxito Verde',
                'slug' => 'kirolux-success-green',
                'hex_code' => '#10B981',
                'rgb_code' => 'rgb(16, 185, 129)',
                'hsl_code' => 'hsl(158, 84%, 39%)',
                'is_primary' => false,
                'description' => 'Verde para mensajes de éxito y confirmaciones positivas.',
            ],
            [
                'name' => 'KiroLux Advertencia Amarilla',
                'slug' => 'kirolux-warning-yellow',
                'hex_code' => '#F59E0B',
                'rgb_code' => 'rgb(245, 158, 11)',
                'hsl_code' => 'hsl(38, 92%, 50%)',
                'is_primary' => false,
                'description' => 'Amarillo para advertencias y mensajes informativos.',
            ],
            [
                'name' => 'KiroLux Error Rojo',
                'slug' => 'kirolux-error-red',
                'hex_code' => '#EF4444',
                'rgb_code' => 'rgb(239, 68, 68)',
                'hsl_code' => 'hsl(0, 84%, 60%)',
                'is_primary' => false,
                'description' => 'Rojo para errores y mensajes críticos.',
            ],

            // Colores especiales - Gamificación
            [
                'name' => 'KiroLux Oro Premio',
                'slug' => 'kirolux-gold-award',
                'hex_code' => '#D97706',
                'rgb_code' => 'rgb(217, 119, 6)',
                'hsl_code' => 'hsl(32, 95%, 44%)',
                'is_primary' => false,
                'description' => 'Dorado para logros, premios y elementos premium.',
            ],
            [
                'name' => 'KiroLux Púrpura Innovación',
                'slug' => 'kirolux-innovation-purple',
                'hex_code' => '#8B5CF6',
                'rgb_code' => 'rgb(139, 92, 246)',
                'hsl_code' => 'hsl(258, 90%, 66%)',
                'is_primary' => false,
                'description' => 'Púrpura para elementos de innovación y características avanzadas.',
            ],
        ];
    }

    /**
     * Colores adicionales del sistema.
     */
    private function getSystemColors(): array
    {
        return [
            // Colores básicos del sistema
            [
                'name' => 'Blanco Puro',
                'slug' => 'pure-white',
                'hex_code' => '#FFFFFF',
                'rgb_code' => 'rgb(255, 255, 255)',
                'hsl_code' => 'hsl(0, 0%, 100%)',
                'is_primary' => false,
                'description' => 'Blanco puro para fondos principales.',
            ],
            [
                'name' => 'Negro Absoluto',
                'slug' => 'absolute-black',
                'hex_code' => '#000000',
                'rgb_code' => 'rgb(0, 0, 0)',
                'hsl_code' => 'hsl(0, 0%, 0%)',
                'is_primary' => false,
                'description' => 'Negro absoluto para contrastes máximos.',
            ],
            [
                'name' => 'Transparente',
                'slug' => 'transparent',
                'hex_code' => '#00000000',
                'rgb_code' => 'rgba(0, 0, 0, 0)',
                'hsl_code' => 'hsla(0, 0%, 0%, 0)',
                'is_primary' => false,
                'description' => 'Color transparente para overlays y efectos.',
            ],
        ];
    }

    /**
     * Mostrar estadísticas de los colores creados.
     */
    private function showStatistics(): void
    {
        $stats = [
            'Total colores' => Color::count(),
            'Colores primarios' => Color::where('is_primary', true)->count(),
            'Colores KiroLux' => Color::where('slug', 'LIKE', 'kirolux-%')->count(),
            'Colores de estado' => Color::where('slug', 'LIKE', '%-green')
                                        ->orWhere('slug', 'LIKE', '%-yellow')
                                        ->orWhere('slug', 'LIKE', '%-red')
                                        ->count(),
            'Colores neutros' => Color::where('slug', 'LIKE', '%-gray')->count(),
            'Colores especiales' => Color::where('slug', 'LIKE', '%-gold')
                                         ->orWhere('slug', 'LIKE', '%-purple')
                                         ->count(),
        ];

        $this->command->info("\n📊 Estadísticas de colores:");
        foreach ($stats as $type => $count) {
            $this->command->info("   {$type}: {$count}");
        }

        // Paleta principal
        $primaryColors = Color::where('is_primary', true)->get();
        if ($primaryColors->isNotEmpty()) {
            $this->command->info("\n🎨 Colores primarios:");
            foreach ($primaryColors as $color) {
                $this->command->info("   {$color->name}: {$color->hex_code}");
            }
        }

        // Colores más representativos de KiroLux
        $brandColors = Color::where('slug', 'LIKE', 'kirolux-%')
                           ->whereIn('slug', [
                               'kirolux-green-primary',
                               'kirolux-solar-yellow', 
                               'kirolux-cooperative-blue'
                           ])
                           ->get();

        if ($brandColors->isNotEmpty()) {
            $this->command->info("\n🌟 Colores marca KiroLux:");
            foreach ($brandColors as $color) {
                $this->command->info("   {$color->name}: {$color->hex_code}");
            }
        }

        // Información para KiroLux
        $totalKirolux = Color::where('slug', 'LIKE', 'kirolux-%')->count();
        $this->command->info("\n⚡ Para KiroLux:");
        $this->command->info("   🎨 Paleta completa: {$totalKirolux} colores de marca");
        $this->command->info("   💚 Enfoque sostenible: Verde como color principal");
        $this->command->info("   ☀️ Energía solar: Amarillo y naranja integrados");
        $this->command->info("   🤝 Cooperativismo: Azul para confianza");
        $this->command->info("   🎯 Sistema completo: Estados, neutros y especiales");
        $this->command->info("   📱 Mobile-ready: Paleta optimizada para apps");
    }
}
