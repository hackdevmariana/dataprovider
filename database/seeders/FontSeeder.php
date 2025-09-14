<?php

namespace Database\Seeders;

use App\Models\Font;
use Illuminate\Database\Seeder;

class FontSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fuentes Sans-serif populares
        $this->createSansSerifFonts();
        
        // Fuentes Serif elegantes
        $this->createSerifFonts();
        
        // Fuentes Monospace para código
        $this->createMonospaceFonts();
        
        // Fuentes Display/Decorativas
        $this->createDisplayFonts();
    }

    /**
     * Crear fuentes Sans-serif.
     */
    private function createSansSerifFonts(): void
    {
        $sansSerifFonts = [
            [
                'name' => 'Inter Regular',
                'family' => 'Inter',
                'style' => 'Regular',
                'weight' => 400,
                'license' => 'Open Font License',
                'source_url' => 'https://fonts.googleapis.com/css2?family=Inter:wght@400&display=swap',
                'is_default' => true,
            ],
            [
                'name' => 'Inter Medium',
                'family' => 'Inter',
                'style' => 'Medium',
                'weight' => 500,
                'license' => 'Open Font License',
                'source_url' => 'https://fonts.googleapis.com/css2?family=Inter:wght@500&display=swap',
                'is_default' => false,
            ],
            [
                'name' => 'Inter Bold',
                'family' => 'Inter',
                'style' => 'Bold',
                'weight' => 700,
                'license' => 'Open Font License',
                'source_url' => 'https://fonts.googleapis.com/css2?family=Inter:wght@700&display=swap',
                'is_default' => false,
            ],
            [
                'name' => 'Roboto Regular',
                'family' => 'Roboto',
                'style' => 'Regular',
                'weight' => 400,
                'license' => 'Apache License 2.0',
                'source_url' => 'https://fonts.googleapis.com/css2?family=Roboto:wght@400&display=swap',
                'is_default' => false,
            ],
            [
                'name' => 'Roboto Medium',
                'family' => 'Roboto',
                'style' => 'Medium',
                'weight' => 500,
                'license' => 'Apache License 2.0',
                'source_url' => 'https://fonts.googleapis.com/css2?family=Roboto:wght@500&display=swap',
                'is_default' => false,
            ],
            [
                'name' => 'Open Sans Regular',
                'family' => 'Open Sans',
                'style' => 'Regular',
                'weight' => 400,
                'license' => 'Apache License 2.0',
                'source_url' => 'https://fonts.googleapis.com/css2?family=Open+Sans:wght@400&display=swap',
                'is_default' => false,
            ],
            [
                'name' => 'Lato Regular',
                'family' => 'Lato',
                'style' => 'Regular',
                'weight' => 400,
                'license' => 'Open Font License',
                'source_url' => 'https://fonts.googleapis.com/css2?family=Lato:wght@400&display=swap',
                'is_default' => false,
            ],
            [
                'name' => 'Montserrat Regular',
                'family' => 'Montserrat',
                'style' => 'Regular',
                'weight' => 400,
                'license' => 'Open Font License',
                'source_url' => 'https://fonts.googleapis.com/css2?family=Montserrat:wght@400&display=swap',
                'is_default' => false,
            ],
            [
                'name' => 'Montserrat Bold',
                'family' => 'Montserrat',
                'style' => 'Bold',
                'weight' => 700,
                'license' => 'Open Font License',
                'source_url' => 'https://fonts.googleapis.com/css2?family=Montserrat:wght@700&display=swap',
                'is_default' => false,
            ],
            [
                'name' => 'Poppins Regular',
                'family' => 'Poppins',
                'style' => 'Regular',
                'weight' => 400,
                'license' => 'Open Font License',
                'source_url' => 'https://fonts.googleapis.com/css2?family=Poppins:wght@400&display=swap',
                'is_default' => false,
            ],
        ];

        foreach ($sansSerifFonts as $fontData) {
            Font::create($fontData);
        }
    }

    /**
     * Crear fuentes Serif.
     */
    private function createSerifFonts(): void
    {
        $serifFonts = [
            [
                'name' => 'Playfair Display Regular',
                'family' => 'Playfair Display',
                'style' => 'Regular',
                'weight' => 400,
                'license' => 'Open Font License',
                'source_url' => 'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400&display=swap',
                'is_default' => false,
            ],
            [
                'name' => 'Playfair Display Bold',
                'family' => 'Playfair Display',
                'style' => 'Bold',
                'weight' => 700,
                'license' => 'Open Font License',
                'source_url' => 'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap',
                'is_default' => false,
            ],
            [
                'name' => 'Merriweather Regular',
                'family' => 'Merriweather',
                'style' => 'Regular',
                'weight' => 400,
                'license' => 'Open Font License',
                'source_url' => 'https://fonts.googleapis.com/css2?family=Merriweather:wght@400&display=swap',
                'is_default' => false,
            ],
            [
                'name' => 'Lora Regular',
                'family' => 'Lora',
                'style' => 'Regular',
                'weight' => 400,
                'license' => 'Open Font License',
                'source_url' => 'https://fonts.googleapis.com/css2?family=Lora:wght@400&display=swap',
                'is_default' => false,
            ],
            [
                'name' => 'Source Serif Pro Regular',
                'family' => 'Source Serif Pro',
                'style' => 'Regular',
                'weight' => 400,
                'license' => 'Open Font License',
                'source_url' => 'https://fonts.googleapis.com/css2?family=Source+Serif+Pro:wght@400&display=swap',
                'is_default' => false,
            ],
            [
                'name' => 'Crimson Text Regular',
                'family' => 'Crimson Text',
                'style' => 'Regular',
                'weight' => 400,
                'license' => 'Open Font License',
                'source_url' => 'https://fonts.googleapis.com/css2?family=Crimson+Text:wght@400&display=swap',
                'is_default' => false,
            ],
        ];

        foreach ($serifFonts as $fontData) {
            Font::create($fontData);
        }
    }

    /**
     * Crear fuentes Monospace.
     */
    private function createMonospaceFonts(): void
    {
        $monospaceFonts = [
            [
                'name' => 'Fira Code Regular',
                'family' => 'Fira Code',
                'style' => 'Regular',
                'weight' => 400,
                'license' => 'Open Font License',
                'source_url' => 'https://fonts.googleapis.com/css2?family=Fira+Code:wght@400&display=swap',
                'is_default' => false,
            ],
            [
                'name' => 'Fira Code Medium',
                'family' => 'Fira Code',
                'style' => 'Medium',
                'weight' => 500,
                'license' => 'Open Font License',
                'source_url' => 'https://fonts.googleapis.com/css2?family=Fira+Code:wght@500&display=swap',
                'is_default' => false,
            ],
            [
                'name' => 'Source Code Pro Regular',
                'family' => 'Source Code Pro',
                'style' => 'Regular',
                'weight' => 400,
                'license' => 'Open Font License',
                'source_url' => 'https://fonts.googleapis.com/css2?family=Source+Code+Pro:wght@400&display=swap',
                'is_default' => false,
            ],
            [
                'name' => 'JetBrains Mono Regular',
                'family' => 'JetBrains Mono',
                'style' => 'Regular',
                'weight' => 400,
                'license' => 'Open Font License',
                'source_url' => 'https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400&display=swap',
                'is_default' => false,
            ],
            [
                'name' => 'IBM Plex Mono Regular',
                'family' => 'IBM Plex Mono',
                'style' => 'Regular',
                'weight' => 400,
                'license' => 'Open Font License',
                'source_url' => 'https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400&display=swap',
                'is_default' => false,
            ],
        ];

        foreach ($monospaceFonts as $fontData) {
            Font::create($fontData);
        }
    }

    /**
     * Crear fuentes Display/Decorativas.
     */
    private function createDisplayFonts(): void
    {
        $displayFonts = [
            [
                'name' => 'Oswald Regular',
                'family' => 'Oswald',
                'style' => 'Regular',
                'weight' => 400,
                'license' => 'Open Font License',
                'source_url' => 'https://fonts.googleapis.com/css2?family=Oswald:wght@400&display=swap',
                'is_default' => false,
            ],
            [
                'name' => 'Oswald Bold',
                'family' => 'Oswald',
                'style' => 'Bold',
                'weight' => 700,
                'license' => 'Open Font License',
                'source_url' => 'https://fonts.googleapis.com/css2?family=Oswald:wght@700&display=swap',
                'is_default' => false,
            ],
            [
                'name' => 'Bebas Neue Regular',
                'family' => 'Bebas Neue',
                'style' => 'Regular',
                'weight' => 400,
                'license' => 'Open Font License',
                'source_url' => 'https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap',
                'is_default' => false,
            ],
            [
                'name' => 'Righteous Regular',
                'family' => 'Righteous',
                'style' => 'Regular',
                'weight' => 400,
                'license' => 'Open Font License',
                'source_url' => 'https://fonts.googleapis.com/css2?family=Righteous&display=swap',
                'is_default' => false,
            ],
            [
                'name' => 'Fredoka One Regular',
                'family' => 'Fredoka One',
                'style' => 'Regular',
                'weight' => 400,
                'license' => 'Open Font License',
                'source_url' => 'https://fonts.googleapis.com/css2?family=Fredoka+One&display=swap',
                'is_default' => false,
            ],
            [
                'name' => 'Lobster Regular',
                'family' => 'Lobster',
                'style' => 'Regular',
                'weight' => 400,
                'license' => 'Open Font License',
                'source_url' => 'https://fonts.googleapis.com/css2?family=Lobster&display=swap',
                'is_default' => false,
            ],
        ];

        foreach ($displayFonts as $fontData) {
            Font::create($fontData);
        }
    }
}