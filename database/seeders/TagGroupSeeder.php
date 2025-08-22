<?php

namespace Database\Seeders;

use App\Models\TagGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tagGroups = [
            [
                'name' => 'Categorías Generales',
                'slug' => 'categorias-generales',
                'description' => 'Etiquetas de uso general para clasificar contenido básico',
            ],
            [
                'name' => 'Energía y Sostenibilidad',
                'slug' => 'energia-sostenibilidad',
                'description' => 'Etiquetas relacionadas con energía renovable, sostenibilidad y medio ambiente',
            ],
            [
                'name' => 'Ubicación Geográfica',
                'slug' => 'ubicacion-geografica',
                'description' => 'Etiquetas para clasificar contenido por ubicación geográfica',
            ],
            [
                'name' => 'Eventos y Festivales',
                'slug' => 'eventos-festivales',
                'description' => 'Etiquetas para categorizar eventos, fiestas y festivales',
            ],
            [
                'name' => 'Personas y Personajes',
                'slug' => 'personas-personajes',
                'description' => 'Etiquetas para clasificar personas, celebridades y personajes públicos',
            ],
            [
                'name' => 'Noticias y Medios',
                'slug' => 'noticias-medios',
                'description' => 'Etiquetas para categorizar noticias, artículos y contenido mediático',
            ],
            [
                'name' => 'Cooperativas y Organizaciones',
                'slug' => 'cooperativas-organizaciones',
                'description' => 'Etiquetas para cooperativas energéticas y organizaciones',
            ],
            [
                'name' => 'Tecnología e Innovación',
                'slug' => 'tecnologia-innovacion',
                'description' => 'Etiquetas relacionadas con tecnología, innovación y desarrollo',
            ],
            [
                'name' => 'Arte y Cultura',
                'slug' => 'arte-cultura',
                'description' => 'Etiquetas para contenido artístico, cultural y creativo',
            ],
            [
                'name' => 'Deportes y Entretenimiento',
                'slug' => 'deportes-entretenimiento',
                'description' => 'Etiquetas para actividades deportivas y de entretenimiento',
            ],
            [
                'name' => 'Educación y Formación',
                'slug' => 'educacion-formacion',
                'description' => 'Etiquetas para contenido educativo y formativo',
            ],
            [
                'name' => 'Salud y Bienestar',
                'slug' => 'salud-bienestar',
                'description' => 'Etiquetas relacionadas con salud, bienestar y calidad de vida',
            ],
            [
                'name' => 'Economía y Finanzas',
                'slug' => 'economia-finanzas',
                'description' => 'Etiquetas para contenido económico y financiero',
            ],
            [
                'name' => 'Política y Sociedad',
                'slug' => 'politica-sociedad',
                'description' => 'Etiquetas para temas políticos y sociales',
            ],
            [
                'name' => 'Ciencia e Investigación',
                'slug' => 'ciencia-investigacion',
                'description' => 'Etiquetas para contenido científico y de investigación',
            ],
        ];

        foreach ($tagGroups as $tagGroup) {
            TagGroup::firstOrCreate(
                ['slug' => $tagGroup['slug']],
                $tagGroup
            );
        }

        $this->command->info('Se han creado ' . count($tagGroups) . ' grupos de etiquetas.');
        
        // Mostrar tabla con los grupos creados
        $createdGroups = TagGroup::all(['id', 'name', 'slug'])->toArray();
        
        $this->command->table(
            ['ID', 'Nombre', 'Slug'],
            $createdGroups
        );

        // Estadísticas
        $totalGroups = TagGroup::count();
        $this->command->newLine();
        $this->command->info("📊 Estadísticas:");
        $this->command->info("   • Total de grupos de etiquetas: {$totalGroups}");
        $this->command->info("   • Grupos más recientes: " . TagGroup::latest()->take(3)->pluck('name')->implode(', '));
        
        $this->command->newLine();
        $this->command->info("✅ Seeder de TagGroup completado exitosamente.");
    }
}