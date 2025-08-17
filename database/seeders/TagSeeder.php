<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            // Géneros musicales
            ['name' => 'Rock', 'slug' => 'rock', 'tag_type' => 'topic', 'is_searchable' => true],
            ['name' => 'Pop', 'slug' => 'pop', 'tag_type' => 'topic', 'is_searchable' => true],
            ['name' => 'Jazz', 'slug' => 'jazz', 'tag_type' => 'topic', 'is_searchable' => true],
            ['name' => 'Clásica', 'slug' => 'clasica', 'tag_type' => 'topic', 'is_searchable' => true],
            ['name' => 'Flamenco', 'slug' => 'flamenco', 'tag_type' => 'topic', 'is_searchable' => true],
            ['name' => 'Electrónica', 'slug' => 'electronica', 'tag_type' => 'topic', 'is_searchable' => true],
            ['name' => 'Reggaeton', 'slug' => 'reggaeton', 'tag_type' => 'topic', 'is_searchable' => true],
            ['name' => 'Hip Hop', 'slug' => 'hip-hop', 'tag_type' => 'topic', 'is_searchable' => true],
            ['name' => 'Folk', 'slug' => 'folk', 'tag_type' => 'topic', 'is_searchable' => true],
            ['name' => 'Indie', 'slug' => 'indie', 'tag_type' => 'topic', 'is_searchable' => true],

            // Temáticas culturales
            ['name' => 'Tradicional', 'slug' => 'tradicional', 'tag_type' => 'theme', 'is_searchable' => true],
            ['name' => 'Moderno', 'slug' => 'moderno', 'tag_type' => 'theme', 'is_searchable' => true],
            ['name' => 'Familiar', 'slug' => 'familiar', 'tag_type' => 'theme', 'is_searchable' => true],
            ['name' => 'Juvenil', 'slug' => 'juvenil', 'tag_type' => 'theme', 'is_searchable' => true],
            ['name' => 'Nocturno', 'slug' => 'nocturno', 'tag_type' => 'theme', 'is_searchable' => true],
            ['name' => 'Gratuito', 'slug' => 'gratuito', 'tag_type' => 'theme', 'is_searchable' => true],
            ['name' => 'Al aire libre', 'slug' => 'aire-libre', 'tag_type' => 'theme', 'is_searchable' => true],
            ['name' => 'Interior', 'slug' => 'interior', 'tag_type' => 'theme', 'is_searchable' => true],

            // Profesiones artísticas
            ['name' => 'Músico', 'slug' => 'musico', 'tag_type' => 'topic', 'is_searchable' => true],
            ['name' => 'Actor', 'slug' => 'actor', 'tag_type' => 'topic', 'is_searchable' => true],
            ['name' => 'Director', 'slug' => 'director', 'tag_type' => 'topic', 'is_searchable' => true],
            ['name' => 'Escritor', 'slug' => 'escritor', 'tag_type' => 'topic', 'is_searchable' => true],
            ['name' => 'Pintor', 'slug' => 'pintor', 'tag_type' => 'topic', 'is_searchable' => true],
            ['name' => 'Escultor', 'slug' => 'escultor', 'tag_type' => 'topic', 'is_searchable' => true],
            ['name' => 'Fotógrafo', 'slug' => 'fotografo', 'tag_type' => 'topic', 'is_searchable' => true],
            ['name' => 'Bailarín', 'slug' => 'bailarin', 'tag_type' => 'topic', 'is_searchable' => true],

            // Deportes
            ['name' => 'Fútbol', 'slug' => 'futbol', 'tag_type' => 'topic', 'is_searchable' => true],
            ['name' => 'Baloncesto', 'slug' => 'baloncesto', 'tag_type' => 'topic', 'is_searchable' => true],
            ['name' => 'Tenis', 'slug' => 'tenis', 'tag_type' => 'topic', 'is_searchable' => true],
            ['name' => 'Atletismo', 'slug' => 'atletismo', 'tag_type' => 'topic', 'is_searchable' => true],
            ['name' => 'Ciclismo', 'slug' => 'ciclismo', 'tag_type' => 'topic', 'is_searchable' => true],
            ['name' => 'Natación', 'slug' => 'natacion', 'tag_type' => 'topic', 'is_searchable' => true],

            // Días internacionales
            ['name' => 'Día Internacional de la Mujer', 'slug' => 'dia-mujer', 'tag_type' => 'theme', 'is_searchable' => true],
            ['name' => 'Día del Trabajo', 'slug' => 'dia-trabajo', 'tag_type' => 'theme', 'is_searchable' => true],
            ['name' => 'Día de la Paz', 'slug' => 'dia-paz', 'tag_type' => 'theme', 'is_searchable' => true],
            ['name' => 'Día del Medio Ambiente', 'slug' => 'dia-medio-ambiente', 'tag_type' => 'theme', 'is_searchable' => true],

            // Santoral
            ['name' => 'San José', 'slug' => 'san-jose', 'tag_type' => 'theme', 'is_searchable' => true],
            ['name' => 'San Juan', 'slug' => 'san-juan', 'tag_type' => 'theme', 'is_searchable' => true],
            ['name' => 'Santa María', 'slug' => 'santa-maria', 'tag_type' => 'theme', 'is_searchable' => true],
            ['name' => 'San Pedro', 'slug' => 'san-pedro', 'tag_type' => 'theme', 'is_searchable' => true],

            // Temáticas energéticas
            ['name' => 'Energía Solar', 'slug' => 'energia-solar', 'tag_type' => 'topic', 'is_searchable' => true],
            ['name' => 'Energía Eólica', 'slug' => 'energia-eolica', 'tag_type' => 'topic', 'is_searchable' => true],
            ['name' => 'Autoconsumo', 'slug' => 'autoconsumo', 'tag_type' => 'topic', 'is_searchable' => true],
            ['name' => 'Cooperativa Energética', 'slug' => 'cooperativa-energetica', 'tag_type' => 'topic', 'is_searchable' => true],
            ['name' => 'Sostenibilidad', 'slug' => 'sostenibilidad', 'tag_type' => 'topic', 'is_searchable' => true],
            ['name' => 'Renovables', 'slug' => 'renovables', 'tag_type' => 'topic', 'is_searchable' => true],

            // Estados de ánimo
            ['name' => 'Alegre', 'slug' => 'alegre', 'tag_type' => 'mood', 'is_searchable' => true],
            ['name' => 'Melancólico', 'slug' => 'melancolico', 'tag_type' => 'mood', 'is_searchable' => true],
            ['name' => 'Festivo', 'slug' => 'festivo', 'tag_type' => 'mood', 'is_searchable' => true],
            ['name' => 'Relajante', 'slug' => 'relajante', 'tag_type' => 'mood', 'is_searchable' => true],
            ['name' => 'Enérgico', 'slug' => 'energico', 'tag_type' => 'mood', 'is_searchable' => true],

            // Temporadas
            ['name' => 'Primavera', 'slug' => 'primavera', 'tag_type' => 'theme', 'is_searchable' => true],
            ['name' => 'Verano', 'slug' => 'verano', 'tag_type' => 'theme', 'is_searchable' => true],
            ['name' => 'Otoño', 'slug' => 'otono', 'tag_type' => 'theme', 'is_searchable' => true],
            ['name' => 'Invierno', 'slug' => 'invierno', 'tag_type' => 'theme', 'is_searchable' => true],
            ['name' => 'Navidad', 'slug' => 'navidad', 'tag_type' => 'theme', 'is_searchable' => true],
            ['name' => 'Semana Santa', 'slug' => 'semana-santa', 'tag_type' => 'theme', 'is_searchable' => true],

            // Tecnología
            ['name' => 'YouTuber', 'slug' => 'youtuber', 'tag_type' => 'topic', 'is_searchable' => true],
            ['name' => 'Influencer', 'slug' => 'influencer', 'tag_type' => 'topic', 'is_searchable' => true],
            ['name' => 'Streamer', 'slug' => 'streamer', 'tag_type' => 'topic', 'is_searchable' => true],
            ['name' => 'Podcaster', 'slug' => 'podcaster', 'tag_type' => 'topic', 'is_searchable' => true],
            ['name' => 'TikToker', 'slug' => 'tiktoker', 'tag_type' => 'topic', 'is_searchable' => true],
        ];

        foreach ($tags as $tag) {
            Tag::firstOrCreate(
                ['slug' => $tag['slug']],
                $tag
            );
        }

        $this->command->info('Tag seeder completed: ' . count($tags) . ' tags created/updated.');
    }
}