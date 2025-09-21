<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Font;

class FontsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fonts = [
            // Fuentes del sistema
            [
                'name' => 'Arial',
                'family' => 'Arial, sans-serif',
                'style' => 'normal',
                'weight' => 400,
                'license' => 'System Font',
                'source_url' => 'https://docs.microsoft.com/en-us/typography/font-list/arial',
                'is_default' => true,
            ],
            [
                'name' => 'Arial Bold',
                'family' => 'Arial, sans-serif',
                'style' => 'normal',
                'weight' => 700,
                'license' => 'System Font',
                'source_url' => 'https://docs.microsoft.com/en-us/typography/font-list/arial',
                'is_default' => false,
            ],
            [
                'name' => 'Times New Roman',
                'family' => 'Times New Roman, serif',
                'style' => 'normal',
                'weight' => 400,
                'license' => 'System Font',
                'source_url' => 'https://docs.microsoft.com/en-us/typography/font-list/times-new-roman',
                'is_default' => false,
            ],
            [
                'name' => 'Courier New',
                'family' => 'Courier New, monospace',
                'style' => 'normal',
                'weight' => 400,
                'license' => 'System Font',
                'source_url' => 'https://docs.microsoft.com/en-us/typography/font-list/courier-new',
                'is_default' => false,
            ],

            // Fuentes de Google Fonts
            [
                'name' => 'Roboto',
                'family' => 'Roboto, sans-serif',
                'style' => 'normal',
                'weight' => 400,
                'license' => 'Apache License 2.0',
                'source_url' => 'https://fonts.google.com/specimen/Roboto',
                'is_default' => false,
            ],
            [
                'name' => 'Roboto Medium',
                'family' => 'Roboto, sans-serif',
                'style' => 'normal',
                'weight' => 500,
                'license' => 'Apache License 2.0',
                'source_url' => 'https://fonts.google.com/specimen/Roboto',
                'is_default' => false,
            ],
            [
                'name' => 'Roboto Bold',
                'family' => 'Roboto, sans-serif',
                'style' => 'normal',
                'weight' => 700,
                'license' => 'Apache License 2.0',
                'source_url' => 'https://fonts.google.com/specimen/Roboto',
                'is_default' => false,
            ],
            [
                'name' => 'Open Sans',
                'family' => 'Open Sans, sans-serif',
                'style' => 'normal',
                'weight' => 400,
                'license' => 'Apache License 2.0',
                'source_url' => 'https://fonts.google.com/specimen/Open+Sans',
                'is_default' => false,
            ],
            [
                'name' => 'Open Sans Bold',
                'family' => 'Open Sans, sans-serif',
                'style' => 'normal',
                'weight' => 700,
                'license' => 'Apache License 2.0',
                'source_url' => 'https://fonts.google.com/specimen/Open+Sans',
                'is_default' => false,
            ],
            [
                'name' => 'Lato',
                'family' => 'Lato, sans-serif',
                'style' => 'normal',
                'weight' => 400,
                'license' => 'SIL Open Font License 1.1',
                'source_url' => 'https://fonts.google.com/specimen/Lato',
                'is_default' => false,
            ],
            [
                'name' => 'Lato Bold',
                'family' => 'Lato, sans-serif',
                'style' => 'normal',
                'weight' => 700,
                'license' => 'SIL Open Font License 1.1',
                'source_url' => 'https://fonts.google.com/specimen/Lato',
                'is_default' => false,
            ],
            [
                'name' => 'Montserrat',
                'family' => 'Montserrat, sans-serif',
                'style' => 'normal',
                'weight' => 400,
                'license' => 'SIL Open Font License 1.1',
                'source_url' => 'https://fonts.google.com/specimen/Montserrat',
                'is_default' => false,
            ],
            [
                'name' => 'Montserrat Bold',
                'family' => 'Montserrat, sans-serif',
                'style' => 'normal',
                'weight' => 700,
                'license' => 'SIL Open Font License 1.1',
                'source_url' => 'https://fonts.google.com/specimen/Montserrat',
                'is_default' => false,
            ],

            // Fuentes serif
            [
                'name' => 'Playfair Display',
                'family' => 'Playfair Display, serif',
                'style' => 'normal',
                'weight' => 400,
                'license' => 'SIL Open Font License 1.1',
                'source_url' => 'https://fonts.google.com/specimen/Playfair+Display',
                'is_default' => false,
            ],
            [
                'name' => 'Playfair Display Bold',
                'family' => 'Playfair Display, serif',
                'style' => 'normal',
                'weight' => 700,
                'license' => 'SIL Open Font License 1.1',
                'source_url' => 'https://fonts.google.com/specimen/Playfair+Display',
                'is_default' => false,
            ],
            [
                'name' => 'Merriweather',
                'family' => 'Merriweather, serif',
                'style' => 'normal',
                'weight' => 400,
                'license' => 'SIL Open Font License 1.1',
                'source_url' => 'https://fonts.google.com/specimen/Merriweather',
                'is_default' => false,
            ],
            [
                'name' => 'Merriweather Bold',
                'family' => 'Merriweather, serif',
                'style' => 'normal',
                'weight' => 700,
                'license' => 'SIL Open Font License 1.1',
                'source_url' => 'https://fonts.google.com/specimen/Merriweather',
                'is_default' => false,
            ],

            // Fuentes monoespaciadas
            [
                'name' => 'Source Code Pro',
                'family' => 'Source Code Pro, monospace',
                'style' => 'normal',
                'weight' => 400,
                'license' => 'SIL Open Font License 1.1',
                'source_url' => 'https://fonts.google.com/specimen/Source+Code+Pro',
                'is_default' => false,
            ],
            [
                'name' => 'Source Code Pro Bold',
                'family' => 'Source Code Pro, monospace',
                'style' => 'normal',
                'weight' => 700,
                'license' => 'SIL Open Font License 1.1',
                'source_url' => 'https://fonts.google.com/specimen/Source+Code+Pro',
                'is_default' => false,
            ],
            [
                'name' => 'Fira Code',
                'family' => 'Fira Code, monospace',
                'style' => 'normal',
                'weight' => 400,
                'license' => 'SIL Open Font License 1.1',
                'source_url' => 'https://fonts.google.com/specimen/Fira+Code',
                'is_default' => false,
            ],

            // Fuentes para títulos
            [
                'name' => 'Oswald',
                'family' => 'Oswald, sans-serif',
                'style' => 'normal',
                'weight' => 400,
                'license' => 'SIL Open Font License 1.1',
                'source_url' => 'https://fonts.google.com/specimen/Oswald',
                'is_default' => false,
            ],
            [
                'name' => 'Oswald Bold',
                'family' => 'Oswald, sans-serif',
                'style' => 'normal',
                'weight' => 700,
                'license' => 'SIL Open Font License 1.1',
                'source_url' => 'https://fonts.google.com/specimen/Oswald',
                'is_default' => false,
            ],
            [
                'name' => 'Raleway',
                'family' => 'Raleway, sans-serif',
                'style' => 'normal',
                'weight' => 400,
                'license' => 'SIL Open Font License 1.1',
                'source_url' => 'https://fonts.google.com/specimen/Raleway',
                'is_default' => false,
            ],
            [
                'name' => 'Raleway Bold',
                'family' => 'Raleway, sans-serif',
                'style' => 'normal',
                'weight' => 700,
                'license' => 'SIL Open Font License 1.1',
                'source_url' => 'https://fonts.google.com/specimen/Raleway',
                'is_default' => false,
            ],

            // Fuentes para contenido
            [
                'name' => 'Source Sans Pro',
                'family' => 'Source Sans Pro, sans-serif',
                'style' => 'normal',
                'weight' => 400,
                'license' => 'SIL Open Font License 1.1',
                'source_url' => 'https://fonts.google.com/specimen/Source+Sans+Pro',
                'is_default' => false,
            ],
            [
                'name' => 'Source Sans Pro Bold',
                'family' => 'Source Sans Pro, sans-serif',
                'style' => 'normal',
                'weight' => 700,
                'license' => 'SIL Open Font License 1.1',
                'source_url' => 'https://fonts.google.com/specimen/Source+Sans+Pro',
                'is_default' => false,
            ],
            [
                'name' => 'Nunito',
                'family' => 'Nunito, sans-serif',
                'style' => 'normal',
                'weight' => 400,
                'license' => 'SIL Open Font License 1.1',
                'source_url' => 'https://fonts.google.com/specimen/Nunito',
                'is_default' => false,
            ],
            [
                'name' => 'Nunito Bold',
                'family' => 'Nunito, sans-serif',
                'style' => 'normal',
                'weight' => 700,
                'license' => 'SIL Open Font License 1.1',
                'source_url' => 'https://fonts.google.com/specimen/Nunito',
                'is_default' => false,
            ],

            // Fuentes especiales
            [
                'name' => 'Poppins',
                'family' => 'Poppins, sans-serif',
                'style' => 'normal',
                'weight' => 400,
                'license' => 'SIL Open Font License 1.1',
                'source_url' => 'https://fonts.google.com/specimen/Poppins',
                'is_default' => false,
            ],
            [
                'name' => 'Poppins Bold',
                'family' => 'Poppins, sans-serif',
                'style' => 'normal',
                'weight' => 700,
                'license' => 'SIL Open Font License 1.1',
                'source_url' => 'https://fonts.google.com/specimen/Poppins',
                'is_default' => false,
            ],
            [
                'name' => 'Inter',
                'family' => 'Inter, sans-serif',
                'style' => 'normal',
                'weight' => 400,
                'license' => 'SIL Open Font License 1.1',
                'source_url' => 'https://fonts.google.com/specimen/Inter',
                'is_default' => false,
            ],
            [
                'name' => 'Inter Bold',
                'family' => 'Inter, sans-serif',
                'style' => 'normal',
                'weight' => 700,
                'license' => 'SIL Open Font License 1.1',
                'source_url' => 'https://fonts.google.com/specimen/Inter',
                'is_default' => false,
            ],

            // Fuentes para datos y tablas
            [
                'name' => 'IBM Plex Sans',
                'family' => 'IBM Plex Sans, sans-serif',
                'style' => 'normal',
                'weight' => 400,
                'license' => 'SIL Open Font License 1.1',
                'source_url' => 'https://fonts.google.com/specimen/IBM+Plex+Sans',
                'is_default' => false,
            ],
            [
                'name' => 'IBM Plex Sans Bold',
                'family' => 'IBM Plex Sans, sans-serif',
                'style' => 'normal',
                'weight' => 700,
                'license' => 'SIL Open Font License 1.1',
                'source_url' => 'https://fonts.google.com/specimen/IBM+Plex+Sans',
                'is_default' => false,
            ],
            [
                'name' => 'Work Sans',
                'family' => 'Work Sans, sans-serif',
                'style' => 'normal',
                'weight' => 400,
                'license' => 'SIL Open Font License 1.1',
                'source_url' => 'https://fonts.google.com/specimen/Work+Sans',
                'is_default' => false,
            ],
            [
                'name' => 'Work Sans Bold',
                'family' => 'Work Sans, sans-serif',
                'style' => 'normal',
                'weight' => 700,
                'license' => 'SIL Open Font License 1.1',
                'source_url' => 'https://fonts.google.com/specimen/Work+Sans',
                'is_default' => false,
            ],

            // Fuentes para formularios
            [
                'name' => 'Noto Sans',
                'family' => 'Noto Sans, sans-serif',
                'style' => 'normal',
                'weight' => 400,
                'license' => 'SIL Open Font License 1.1',
                'source_url' => 'https://fonts.google.com/specimen/Noto+Sans',
                'is_default' => false,
            ],
            [
                'name' => 'Noto Sans Bold',
                'family' => 'Noto Sans, sans-serif',
                'style' => 'normal',
                'weight' => 700,
                'license' => 'SIL Open Font License 1.1',
                'source_url' => 'https://fonts.google.com/specimen/Noto+Sans',
                'is_default' => false,
            ],
            [
                'name' => 'Rubik',
                'family' => 'Rubik, sans-serif',
                'style' => 'normal',
                'weight' => 400,
                'license' => 'SIL Open Font License 1.1',
                'source_url' => 'https://fonts.google.com/specimen/Rubik',
                'is_default' => false,
            ],
            [
                'name' => 'Rubik Bold',
                'family' => 'Rubik, sans-serif',
                'style' => 'normal',
                'weight' => 700,
                'license' => 'SIL Open Font License 1.1',
                'source_url' => 'https://fonts.google.com/specimen/Rubik',
                'is_default' => false,
            ],

            // Fuentes para iconos y símbolos
            [
                'name' => 'Material Icons',
                'family' => 'Material Icons',
                'style' => 'normal',
                'weight' => 400,
                'license' => 'Apache License 2.0',
                'source_url' => 'https://fonts.google.com/icons',
                'is_default' => false,
            ],
            [
                'name' => 'Font Awesome',
                'family' => 'Font Awesome',
                'style' => 'normal',
                'weight' => 400,
                'license' => 'Font Awesome Free License',
                'source_url' => 'https://fontawesome.com/',
                'is_default' => false,
            ],
        ];

        foreach ($fonts as $font) {
            Font::create($font);
        }

        $this->command->info('Se han creado ' . count($fonts) . ' fuentes.');
    }
}

