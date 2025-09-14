<?php

namespace Database\Seeders;

use App\Models\VisualIdentity;
use App\Models\Color;
use App\Models\Font;
use Illuminate\Database\Seeder;

class VisualIdentitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear identidades visuales para diferentes tipos de proyectos
        $this->createEnergyProjectIdentities();
        $this->createCorporateIdentities();
        $this->createFestivalIdentities();
    }

    /**
     * Crear identidades para proyectos energéticos.
     */
    private function createEnergyProjectIdentities(): void
    {
        // Identidad para proyecto solar
        $solarIdentity = VisualIdentity::create([
            'name' => 'Proyecto Solar Comunitario',
            'description' => 'Identidad visual para proyecto de energía solar comunitaria',
        ]);

        // Asociar colores energéticos
        $energyColors = Color::whereIn('name', [
            'Verde Energía', 'Azul Solar', 'Amarillo Solar', 'Verde Sostenible'
        ])->get();

        foreach ($energyColors as $color) {
            $solarIdentity->colors()->attach($color->id, [
                'usage' => $color->name === 'Verde Energía' ? 'primary' : 'secondary',
                'is_primary' => $color->name === 'Verde Energía',
                'sort_order' => $color->name === 'Verde Energía' ? 1 : 2
            ]);
        }

        // Asociar fuentes profesionales
        $professionalFonts = Font::whereIn('family', ['Inter', 'Roboto'])->get();
        foreach ($professionalFonts as $font) {
            $solarIdentity->fonts()->attach($font->id, [
                'usage' => $font->weight === 400 ? 'body' : 'heading',
                'is_primary' => $font->weight === 400,
                'sort_order' => $font->weight === 400 ? 1 : 2
            ]);
        }

        // Identidad para proyecto eólico
        $windIdentity = VisualIdentity::create([
            'name' => 'Parque Eólico Regional',
            'description' => 'Identidad visual para parque eólico regional',
        ]);

        // Asociar colores eólicos
        $windColors = Color::whereIn('name', [
            'Azul Eólico', 'Azul Solar', 'Verde Sostenible'
        ])->get();

        foreach ($windColors as $color) {
            $windIdentity->colors()->attach($color->id, [
                'usage' => $color->name === 'Azul Eólico' ? 'primary' : 'secondary',
                'is_primary' => $color->name === 'Azul Eólico',
                'sort_order' => $color->name === 'Azul Eólico' ? 1 : 2
            ]);
        }

        // Asociar fuentes técnicas
        $technicalFonts = Font::whereIn('family', ['Source Code Pro', 'IBM Plex Mono'])->get();
        foreach ($technicalFonts as $font) {
            $windIdentity->fonts()->attach($font->id, [
                'usage' => 'technical',
                'is_primary' => false,
                'sort_order' => 1
            ]);
        }
    }

    /**
     * Crear identidades corporativas.
     */
    private function createCorporateIdentities(): void
    {
        // Identidad corporativa profesional
        $corporateIdentity = VisualIdentity::create([
            'name' => 'Identidad Corporativa Profesional',
            'description' => 'Identidad visual para empresas del sector energético',
        ]);

        // Asociar colores corporativos
        $corporateColors = Color::whereIn('name', [
            'Azul Corporativo', 'Verde Corporativo', 'Gris Oscuro', 'Blanco'
        ])->get();

        foreach ($corporateColors as $color) {
            $corporateIdentity->colors()->attach($color->id, [
                'usage' => $color->name === 'Azul Corporativo' ? 'primary' : 'secondary',
                'is_primary' => $color->name === 'Azul Corporativo',
                'sort_order' => $color->name === 'Azul Corporativo' ? 1 : 2
            ]);
        }

        // Asociar fuentes corporativas
        $corporateFonts = Font::whereIn('family', ['Inter', 'Montserrat'])->get();
        foreach ($corporateFonts as $font) {
            $corporateIdentity->fonts()->attach($font->id, [
                'usage' => $font->weight === 400 ? 'body' : 'heading',
                'is_primary' => $font->weight === 400,
                'sort_order' => $font->weight === 400 ? 1 : 2
            ]);
        }
    }

    /**
     * Crear identidades para festivales.
     */
    private function createFestivalIdentities(): void
    {
        // Identidad para festival de música
        $musicFestivalIdentity = VisualIdentity::create([
            'name' => 'Festival de Música Sostenible',
            'description' => 'Identidad visual para festival de música con enfoque sostenible',
        ]);

        // Asociar colores vibrantes
        $festivalColors = Color::whereIn('name', [
            'Naranja', 'Púrpura', 'Verde Energía', 'Amarillo Solar'
        ])->get();

        foreach ($festivalColors as $color) {
            $musicFestivalIdentity->colors()->attach($color->id, [
                'usage' => 'accent',
                'is_primary' => false,
                'sort_order' => 1
            ]);
        }

        // Asociar fuentes display
        $displayFonts = Font::whereIn('family', ['Oswald', 'Bebas Neue', 'Righteous'])->get();
        foreach ($displayFonts as $font) {
            $musicFestivalIdentity->fonts()->attach($font->id, [
                'usage' => 'display',
                'is_primary' => false,
                'sort_order' => 1
            ]);
        }
    }
}