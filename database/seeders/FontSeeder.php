<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Font;

class FontSeeder extends Seeder
{
    /**
     * Ejecutar el seeder para fuentes de KiroLux.
     */
    public function run(): void
    {
        $this->command->info('Creando tipografías para KiroLux...');

        // Crear fuentes principales para KiroLux
        $kiroluxFonts = $this->getKiroLuxFonts();
        $createdCount = 0;

        foreach ($kiroluxFonts as $fontData) {
            $font = Font::firstOrCreate(
                ['name' => $fontData['name']],
                [
                    'name' => $fontData['name'],
                    'family' => $fontData['family'],
                    'style' => $fontData['style'],
                    'weight' => $fontData['weight'],
                    'license' => $fontData['license'],
                    'source_url' => $fontData['source_url'],
                    'is_default' => $fontData['is_default'],
                ]
            );
            
            if ($font->wasRecentlyCreated) {
                $createdCount++;
            }
        }

        $this->command->info("✅ Creadas {$createdCount} fuentes para KiroLux");

        // Mostrar estadísticas
        $this->showStatistics();
    }

    /**
     * Fuentes optimizadas para KiroLux.
     */
    private function getKiroLuxFonts(): array
    {
        return [
            // Fuente principal - Sans-serif moderna
            [
                'name' => 'Inter Regular',
                'family' => 'Inter',
                'style' => 'normal',
                'weight' => '400',
                'license' => 'Open Font License',
                'source_url' => 'https://fonts.google.com/specimen/Inter',
                'is_default' => true,
            ],
            [
                'name' => 'Inter Medium',
                'family' => 'Inter',
                'style' => 'normal',
                'weight' => '500',
                'license' => 'Open Font License',
                'source_url' => 'https://fonts.google.com/specimen/Inter',
                'is_default' => false,
            ],
            [
                'name' => 'Inter Semibold',
                'family' => 'Inter',
                'style' => 'normal',
                'weight' => '600',
                'license' => 'Open Font License',
                'source_url' => 'https://fonts.google.com/specimen/Inter',
                'is_default' => false,
            ],
            [
                'name' => 'Inter Bold',
                'family' => 'Inter',
                'style' => 'normal',
                'weight' => '700',
                'license' => 'Open Font License',
                'source_url' => 'https://fonts.google.com/specimen/Inter',
                'is_default' => false,
            ],

            // Fuente secundaria - Para títulos y branding
            [
                'name' => 'Poppins Regular',
                'family' => 'Poppins',
                'style' => 'normal',
                'weight' => '400',
                'license' => 'Open Font License',
                'source_url' => 'https://fonts.google.com/specimen/Poppins',
                'is_default' => false,
            ],
            [
                'name' => 'Poppins Medium',
                'family' => 'Poppins',
                'style' => 'normal',
                'weight' => '500',
                'license' => 'Open Font License',
                'source_url' => 'https://fonts.google.com/specimen/Poppins',
                'is_default' => false,
            ],
            [
                'name' => 'Poppins Semibold',
                'family' => 'Poppins',
                'style' => 'normal',
                'weight' => '600',
                'license' => 'Open Font License',
                'source_url' => 'https://fonts.google.com/specimen/Poppins',
                'is_default' => false,
            ],
            [
                'name' => 'Poppins Bold',
                'family' => 'Poppins',
                'style' => 'normal',
                'weight' => '700',
                'license' => 'Open Font License',
                'source_url' => 'https://fonts.google.com/specimen/Poppins',
                'is_default' => false,
            ],

            // Fuente monospace - Para datos técnicos
            [
                'name' => 'JetBrains Mono Regular',
                'family' => 'JetBrains Mono',
                'style' => 'normal',
                'weight' => '400',
                'license' => 'Open Font License',
                'source_url' => 'https://fonts.google.com/specimen/JetBrains+Mono',
                'is_default' => false,
            ],
            [
                'name' => 'JetBrains Mono Medium',
                'family' => 'JetBrains Mono',
                'style' => 'normal',
                'weight' => '500',
                'license' => 'Open Font License',
                'source_url' => 'https://fonts.google.com/specimen/JetBrains+Mono',
                'is_default' => false,
            ],
            [
                'name' => 'JetBrains Mono Bold',
                'family' => 'JetBrains Mono',
                'style' => 'normal',
                'weight' => '700',
                'license' => 'Open Font License',
                'source_url' => 'https://fonts.google.com/specimen/JetBrains+Mono',
                'is_default' => false,
            ],

            // Fuente display - Para números grandes y métricas
            [
                'name' => 'Roboto Condensed Regular',
                'family' => 'Roboto Condensed',
                'style' => 'normal',
                'weight' => '400',
                'license' => 'Apache License',
                'source_url' => 'https://fonts.google.com/specimen/Roboto+Condensed',
                'is_default' => false,
            ],
            [
                'name' => 'Roboto Condensed Medium',
                'family' => 'Roboto Condensed',
                'style' => 'normal',
                'weight' => '500',
                'license' => 'Apache License',
                'source_url' => 'https://fonts.google.com/specimen/Roboto+Condensed',
                'is_default' => false,
            ],
            [
                'name' => 'Roboto Condensed Bold',
                'family' => 'Roboto Condensed',
                'style' => 'normal',
                'weight' => '700',
                'license' => 'Apache License',
                'source_url' => 'https://fonts.google.com/specimen/Roboto+Condensed',
                'is_default' => false,
            ],

            // Fuentes de sistema - Fallback
            [
                'name' => 'System UI Regular',
                'family' => 'system-ui',
                'style' => 'normal',
                'weight' => '400',
                'license' => 'System Font',
                'source_url' => 'system://fonts',
                'is_default' => false,
            ],
            [
                'name' => 'SF Pro Display Regular',
                'family' => 'SF Pro Display',
                'style' => 'normal',
                'weight' => '400',
                'license' => 'Apple System Font',
                'source_url' => 'https://developer.apple.com/fonts/',
                'is_default' => false,
            ],
            [
                'name' => 'Roboto Regular',
                'family' => 'Roboto',
                'style' => 'normal',
                'weight' => '400',
                'license' => 'Apache License',
                'source_url' => 'https://fonts.google.com/specimen/Roboto',
                'is_default' => false,
            ],

            // Fuente para iconos - Si se necesita
            [
                'name' => 'Material Icons Regular',
                'family' => 'Material Icons',
                'style' => 'normal',
                'weight' => '400',
                'license' => 'Apache License',
                'source_url' => 'https://fonts.google.com/icons',
                'is_default' => false,
            ],
            [
                'name' => 'Heroicons Outline',
                'family' => 'Heroicons',
                'style' => 'outline',
                'weight' => '400',
                'license' => 'MIT License',
                'source_url' => 'https://heroicons.com/',
                'is_default' => false,
            ],
            [
                'name' => 'Heroicons Solid',
                'family' => 'Heroicons',
                'style' => 'solid',
                'weight' => '400',
                'license' => 'MIT License',
                'source_url' => 'https://heroicons.com/',
                'is_default' => false,
            ],
        ];
    }

    /**
     * Mostrar estadísticas de las fuentes creadas.
     */
    private function showStatistics(): void
    {
        $stats = [
            'Total fuentes' => Font::count(),
            'Fuentes por defecto' => Font::where('is_default', true)->count(),
            'Familia Inter' => Font::where('family', 'Inter')->count(),
            'Familia Poppins' => Font::where('family', 'Poppins')->count(),
            'Fuentes monospace' => Font::where('family', 'JetBrains Mono')->count(),
            'Fuentes display' => Font::where('family', 'Roboto Condensed')->count(),
            'Fuentes de sistema' => Font::whereIn('family', ['system-ui', 'SF Pro Display', 'Roboto'])->count(),
            'Fuentes de iconos' => Font::whereIn('family', ['Material Icons', 'Heroicons'])->count(),
        ];

        $this->command->info("\n📊 Estadísticas de fuentes:");
        foreach ($stats as $type => $count) {
            $this->command->info("   {$type}: {$count}");
        }

        // Familias de fuentes
        $families = Font::selectRaw('family, COUNT(*) as count')
                       ->groupBy('family')
                       ->orderBy('count', 'desc')
                       ->get();

        if ($families->isNotEmpty()) {
            $this->command->info("\n🔤 Familias de fuentes:");
            foreach ($families as $family) {
                $this->command->info("   {$family->family}: {$family->count} variantes");
            }
        }

        // Pesos disponibles
        $weights = Font::selectRaw('weight, COUNT(*) as count')
                      ->groupBy('weight')
                      ->orderBy('weight', 'asc')
                      ->get();

        if ($weights->isNotEmpty()) {
            $this->command->info("\n⚖️ Pesos disponibles:");
            foreach ($weights as $weight) {
                $weightName = $this->getWeightName($weight->weight);
                $this->command->info("   {$weight->weight} ({$weightName}): {$weight->count} fuentes");
            }
        }

        // Licencias
        $licenses = Font::selectRaw('license, COUNT(*) as count')
                       ->groupBy('license')
                       ->orderBy('count', 'desc')
                       ->get();

        if ($licenses->isNotEmpty()) {
            $this->command->info("\n📜 Licencias:");
            foreach ($licenses as $license) {
                $this->command->info("   {$license->license}: {$license->count} fuentes");
            }
        }

        // Información para KiroLux
        $defaultFont = Font::where('is_default', true)->first();
        $interFonts = Font::where('family', 'Inter')->count();
        $poppinsFonts = Font::where('family', 'Poppins')->count();
        
        $this->command->info("\n⚡ Para KiroLux:");
        $this->command->info("   🔤 Fuente principal: " . ($defaultFont ? $defaultFont->name : 'No definida'));
        $this->command->info("   📱 Inter (UI): {$interFonts} variantes - Óptima para interfaces");
        $this->command->info("   🎨 Poppins (Branding): {$poppinsFonts} variantes - Para títulos");
        $this->command->info("   💻 JetBrains Mono: Para datos técnicos y código");
        $this->command->info("   📊 Roboto Condensed: Para métricas y números grandes");
        $this->command->info("   🌍 Soporte multiidioma: Completo");
        $this->command->info("   📱 Mobile-optimized: Todas las fuentes");
        $this->command->info("   ⚡ Performance: Google Fonts CDN");
    }

    /**
     * Obtener el nombre legible del peso de fuente.
     */
    private function getWeightName(string $weight): string
    {
        $weights = [
            '100' => 'Thin',
            '200' => 'Extra Light',
            '300' => 'Light',
            '400' => 'Regular',
            '500' => 'Medium',
            '600' => 'Semibold',
            '700' => 'Bold',
            '800' => 'Extra Bold',
            '900' => 'Black',
        ];

        return $weights[$weight] ?? 'Unknown';
    }
}
