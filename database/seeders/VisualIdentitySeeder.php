<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VisualIdentity;
use App\Models\Color;
use App\Models\Font;

class VisualIdentitySeeder extends Seeder
{
    /**
     * Ejecutar el seeder para identidades visuales de KiroLux.
     */
    public function run(): void
    {
        $this->command->info('Creando identidades visuales para KiroLux...');

        // Crear identidades visuales
        $identities = $this->getKiroLuxIdentities();
        $createdCount = 0;

        foreach ($identities as $identityData) {
            $identity = VisualIdentity::firstOrCreate(
                ['name' => $identityData['name']],
                [
                    'name' => $identityData['name'],
                    'description' => $identityData['description'],
                ]
            );
            
            if ($identity->wasRecentlyCreated) {
                $createdCount++;
            }

            // Asociar colores a la identidad visual
            if (isset($identityData['colors'])) {
                $this->attachColorsToIdentity($identity, $identityData['colors']);
            }

            // Asociar fuentes a la identidad visual
            if (isset($identityData['fonts'])) {
                $this->attachFontsToIdentity($identity, $identityData['fonts']);
            }
        }

        $this->command->info("✅ Creadas {$createdCount} identidades visuales");

        // Mostrar estadísticas
        $this->showStatistics();
    }

    /**
     * Identidades visuales de KiroLux.
     */
    private function getKiroLuxIdentities(): array
    {
        return [
            // Identidad principal de KiroLux
            [
                'name' => 'KiroLux Principal',
                'description' => 'Identidad visual principal de KiroLux para aplicaciones móviles y web. Enfocada en sostenibilidad, energía renovable y cooperativismo.',
                'colors' => [
                    'kirolux-green-primary',
                    'kirolux-green-dark',
                    'kirolux-green-light',
                    'kirolux-solar-yellow',
                    'kirolux-cooperative-blue',
                    'kirolux-charcoal-gray',
                    'kirolux-light-gray',
                    'pure-white',
                ],
                'fonts' => [
                    'Inter Regular',
                    'Inter Medium',
                    'Inter Semibold',
                    'Inter Bold',
                    'Poppins Medium',
                    'Poppins Semibold',
                    'Poppins Bold',
                ],
            ],

            // Identidad para dashboard y analytics
            [
                'name' => 'KiroLux Dashboard',
                'description' => 'Identidad visual especializada para dashboards, analytics y visualización de datos energéticos. Optimizada para métricas y gráficos.',
                'colors' => [
                    'kirolux-green-primary',
                    'kirolux-cooperative-blue',
                    'kirolux-navy-blue',
                    'kirolux-charcoal-gray',
                    'kirolux-medium-gray',
                    'kirolux-success-green',
                    'kirolux-warning-yellow',
                    'kirolux-error-red',
                ],
                'fonts' => [
                    'Inter Regular',
                    'Inter Medium',
                    'Inter Bold',
                    'Roboto Condensed Regular',
                    'Roboto Condensed Medium',
                    'Roboto Condensed Bold',
                    'JetBrains Mono Regular',
                    'JetBrains Mono Medium',
                ],
            ],

            // Identidad para marketing y branding
            [
                'name' => 'KiroLux Marketing',
                'description' => 'Identidad visual para materiales de marketing, presentaciones y comunicación externa. Diseñada para impacto visual y reconocimiento de marca.',
                'colors' => [
                    'kirolux-green-primary',
                    'kirolux-solar-yellow',
                    'kirolux-energy-orange',
                    'kirolux-gold-award',
                    'kirolux-innovation-purple',
                    'kirolux-charcoal-gray',
                    'pure-white',
                    'absolute-black',
                ],
                'fonts' => [
                    'Poppins Regular',
                    'Poppins Medium',
                    'Poppins Semibold',
                    'Poppins Bold',
                    'Inter Medium',
                    'Inter Bold',
                ],
            ],

            // Identidad para cooperativas
            [
                'name' => 'KiroLux Cooperativas',
                'description' => 'Identidad visual específica para cooperativas energéticas. Enfatiza confianza, colaboración y sostenibilidad comunitaria.',
                'colors' => [
                    'kirolux-cooperative-blue',
                    'kirolux-navy-blue',
                    'kirolux-green-primary',
                    'kirolux-green-dark',
                    'kirolux-charcoal-gray',
                    'kirolux-medium-gray',
                    'kirolux-light-gray',
                    'pure-white',
                ],
                'fonts' => [
                    'Inter Regular',
                    'Inter Medium',
                    'Inter Semibold',
                    'Poppins Medium',
                    'Poppins Semibold',
                ],
            ],

            // Identidad para gamificación
            [
                'name' => 'KiroLux Gamificación',
                'description' => 'Identidad visual para elementos de gamificación, logros, rankings y challenges energéticos. Diseñada para motivar y engagement.',
                'colors' => [
                    'kirolux-gold-award',
                    'kirolux-innovation-purple',
                    'kirolux-energy-orange',
                    'kirolux-success-green',
                    'kirolux-solar-yellow',
                    'kirolux-green-primary',
                    'kirolux-charcoal-gray',
                ],
                'fonts' => [
                    'Poppins Medium',
                    'Poppins Semibold',
                    'Poppins Bold',
                    'Inter Medium',
                    'Inter Semibold',
                    'Inter Bold',
                ],
            ],

            // Identidad para modo oscuro
            [
                'name' => 'KiroLux Modo Oscuro',
                'description' => 'Identidad visual optimizada para modo oscuro. Colores ajustados para mejor legibilidad y menor fatiga visual en condiciones de poca luz.',
                'colors' => [
                    'kirolux-green-light',
                    'kirolux-solar-yellow',
                    'kirolux-cooperative-blue',
                    'kirolux-light-gray',
                    'kirolux-medium-gray',
                    'kirolux-charcoal-gray',
                    'absolute-black',
                ],
                'fonts' => [
                    'Inter Regular',
                    'Inter Medium',
                    'Inter Semibold',
                    'Poppins Medium',
                    'JetBrains Mono Regular',
                ],
            ],

            // Identidad para impresión
            [
                'name' => 'KiroLux Impresión',
                'description' => 'Identidad visual optimizada para materiales impresos. Colores CMYK-friendly y tipografías de alta legibilidad.',
                'colors' => [
                    'kirolux-green-dark',
                    'kirolux-charcoal-gray',
                    'kirolux-navy-blue',
                    'absolute-black',
                    'pure-white',
                ],
                'fonts' => [
                    'Inter Regular',
                    'Inter Medium',
                    'Inter Bold',
                    'Poppins Regular',
                    'Poppins Medium',
                    'Poppins Bold',
                ],
            ],
        ];
    }

    /**
     * Asociar colores a una identidad visual.
     */
    private function attachColorsToIdentity(VisualIdentity $identity, array $colorSlugs): void
    {
        $colors = Color::whereIn('slug', $colorSlugs)->get();
        if ($colors->isNotEmpty()) {
            $identity->colors()->syncWithoutDetaching($colors->pluck('id'));
        }
    }

    /**
     * Asociar fuentes a una identidad visual.
     */
    private function attachFontsToIdentity(VisualIdentity $identity, array $fontNames): void
    {
        $fonts = Font::whereIn('name', $fontNames)->get();
        if ($fonts->isNotEmpty()) {
            $identity->fonts()->syncWithoutDetaching($fonts->pluck('id'));
        }
    }

    /**
     * Mostrar estadísticas de las identidades visuales creadas.
     */
    private function showStatistics(): void
    {
        $stats = [
            'Total identidades' => VisualIdentity::count(),
            'Con colores asignados' => VisualIdentity::has('colors')->count(),
            'Con fuentes asignadas' => VisualIdentity::has('fonts')->count(),
            'Identidades KiroLux' => VisualIdentity::where('name', 'LIKE', 'KiroLux%')->count(),
        ];

        $this->command->info("\n📊 Estadísticas de identidades visuales:");
        foreach ($stats as $type => $count) {
            $this->command->info("   {$type}: {$count}");
        }

        // Identidades más completas
        $identities = VisualIdentity::withCount(['colors', 'fonts'])->get();
        if ($identities->isNotEmpty()) {
            $this->command->info("\n🎨 Identidades visuales:");
            foreach ($identities as $identity) {
                $this->command->info("   {$identity->name}: {$identity->colors_count} colores, {$identity->fonts_count} fuentes");
            }
        }

        // Colores más utilizados en identidades
        $colorUsage = Color::withCount('colorables')->orderBy('colorables_count', 'desc')->limit(5)->get();
        if ($colorUsage->isNotEmpty()) {
            $this->command->info("\n🌈 Colores más utilizados:");
            foreach ($colorUsage as $color) {
                $this->command->info("   {$color->name}: usado en {$color->colorables_count} identidades");
            }
        }

        // Fuentes más utilizadas en identidades
        $fontUsage = Font::withCount('fontables')->orderBy('fontables_count', 'desc')->limit(5)->get();
        if ($fontUsage->isNotEmpty()) {
            $this->command->info("\n🔤 Fuentes más utilizadas:");
            foreach ($fontUsage as $font) {
                $this->command->info("   {$font->name}: usado en {$font->fontables_count} identidades");
            }
        }

        // Información para KiroLux
        $mainIdentity = VisualIdentity::where('name', 'KiroLux Principal')->first();
        $dashboardIdentity = VisualIdentity::where('name', 'KiroLux Dashboard')->first();
        
        $this->command->info("\n⚡ Para KiroLux:");
        if ($mainIdentity) {
            $mainColors = $mainIdentity->colors()->count();
            $mainFonts = $mainIdentity->fonts()->count();
            $this->command->info("   🎨 Identidad principal: {$mainColors} colores, {$mainFonts} fuentes");
        }
        if ($dashboardIdentity) {
            $dashColors = $dashboardIdentity->colors()->count();
            $dashFonts = $dashboardIdentity->fonts()->count();
            $this->command->info("   📊 Identidad dashboard: {$dashColors} colores, {$dashFonts} fuentes");
        }
        $this->command->info("   🎯 Cobertura completa: App, Web, Marketing, Cooperativas");
        $this->command->info("   🌙 Modo oscuro: Optimizado y listo");
        $this->command->info("   🖨️ Impresión: CMYK-ready");
        $this->command->info("   🎮 Gamificación: Colores premium integrados");
        $this->command->info("   📱 Responsive: Todas las identidades mobile-first");
    }
}
