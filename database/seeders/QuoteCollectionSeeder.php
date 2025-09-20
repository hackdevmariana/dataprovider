<?php

namespace Database\Seeders;

use App\Models\QuoteCollection;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuoteCollectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creando colecciones de citas...');

        // Verificar que existen usuarios
        $users = User::limit(10)->get();
        if ($users->isEmpty()) {
            $this->command->warn('No hay usuarios disponibles. Creando usuario de prueba...');
            User::create([
                'name' => 'Usuario de Prueba',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
            ]);
            $users = User::limit(10)->get();
        }

        $collectionTemplates = [
            [
                'name' => 'Citas de Motivación',
                'description' => 'Una colección inspiradora de citas motivacionales para superar obstáculos y alcanzar tus metas.',
                'theme' => 'motivación',
                'tags' => ['motivación', 'inspiración', 'superación', 'éxito'],
            ],
            [
                'name' => 'Sabiduría Antigua',
                'description' => 'Citas de filósofos y pensadores clásicos que han trascendido el tiempo.',
                'theme' => 'filosofía',
                'tags' => ['filosofía', 'sabiduría', 'antiguo', 'reflexión'],
            ],
            [
                'name' => 'Citas de Amor',
                'description' => 'Las más hermosas citas sobre el amor, la pasión y las relaciones.',
                'theme' => 'amor',
                'tags' => ['amor', 'romance', 'relaciones', 'pasión'],
            ],
            [
                'name' => 'Frases de Liderazgo',
                'description' => 'Citas inspiradoras sobre liderazgo, gestión de equipos y toma de decisiones.',
                'theme' => 'liderazgo',
                'tags' => ['liderazgo', 'gestión', 'equipos', 'decisión'],
            ],
            [
                'name' => 'Citas de Escritores',
                'description' => 'Las mejores citas de autores famosos sobre la escritura y la literatura.',
                'theme' => 'literatura',
                'tags' => ['literatura', 'escritura', 'autores', 'creatividad'],
            ],
            [
                'name' => 'Sabiduría Empresarial',
                'description' => 'Citas de empresarios exitosos sobre innovación, negocios y emprendimiento.',
                'theme' => 'negocios',
                'tags' => ['negocios', 'empresa', 'innovación', 'emprendimiento'],
            ],
            [
                'name' => 'Citas de Vida',
                'description' => 'Reflexiones profundas sobre la vida, la felicidad y el propósito.',
                'theme' => 'vida',
                'tags' => ['vida', 'felicidad', 'propósito', 'reflexión'],
            ],
            [
                'name' => 'Frases de Superación',
                'description' => 'Citas poderosas sobre superar adversidades y crecer personalmente.',
                'theme' => 'superación',
                'tags' => ['superación', 'adversidad', 'crecimiento', 'resiliencia'],
            ],
            [
                'name' => 'Citas de Ciencia',
                'description' => 'Reflexiones de científicos sobre el conocimiento, la investigación y el descubrimiento.',
                'theme' => 'ciencia',
                'tags' => ['ciencia', 'conocimiento', 'investigación', 'descubrimiento'],
            ],
            [
                'name' => 'Sabiduría Oriental',
                'description' => 'Citas de tradiciones orientales sobre mindfulness, equilibrio y espiritualidad.',
                'theme' => 'espiritualidad',
                'tags' => ['espiritualidad', 'mindfulness', 'equilibrio', 'oriental'],
            ],
            [
                'name' => 'Citas de Arte',
                'description' => 'Reflexiones de artistas sobre la creatividad, la belleza y la expresión artística.',
                'theme' => 'arte',
                'tags' => ['arte', 'creatividad', 'belleza', 'expresión'],
            ],
            [
                'name' => 'Frases de Deportes',
                'description' => 'Citas motivacionales de deportistas sobre disciplina, esfuerzo y victoria.',
                'theme' => 'deportes',
                'tags' => ['deportes', 'disciplina', 'esfuerzo', 'victoria'],
            ],
        ];

        $count = 0;
        foreach ($collectionTemplates as $template) {
            $collection = QuoteCollection::create([
                'name' => $template['name'],
                'description' => $template['description'],
                'created_by' => $users->random()->id,
                'theme' => $template['theme'],
                'tags' => $template['tags'],
                'quotes_count' => fake()->numberBetween(5, 25),
                'is_public' => fake()->boolean(80), // 80% públicas
                'is_featured' => fake()->boolean(15), // 15% destacadas
                'views_count' => fake()->numberBetween(0, 1000),
                'likes_count' => fake()->numberBetween(0, 200),
            ]);

            $count++;
        }

        // Crear algunas colecciones adicionales aleatorias
        $additionalCollections = fake()->numberBetween(5, 10);
        for ($i = 0; $i < $additionalCollections; $i++) {
            $themes = ['inspiración', 'reflexión', 'humor', 'naturaleza', 'tecnología', 'educación', 'salud', 'familia'];
            $tags = [
                ['inspiración', 'positivo'],
                ['reflexión', 'profundo'],
                ['humor', 'divertido'],
                ['naturaleza', 'paz'],
                ['tecnología', 'futuro'],
                ['educación', 'aprendizaje'],
                ['salud', 'bienestar'],
                ['familia', 'valores'],
            ];

            $collection = QuoteCollection::create([
                'name' => fake()->sentence(3),
                'description' => fake()->paragraph(2),
                'created_by' => $users->random()->id,
                'theme' => fake()->randomElement($themes),
                'tags' => fake()->randomElement($tags),
                'quotes_count' => fake()->numberBetween(3, 15),
                'is_public' => fake()->boolean(70),
                'is_featured' => fake()->boolean(10),
                'views_count' => fake()->numberBetween(0, 500),
                'likes_count' => fake()->numberBetween(0, 100),
            ]);

            $count++;
        }

        $this->command->info("✅ Creadas {$count} colecciones de citas");
        $this->showStatistics();
    }

    /**
     * Mostrar estadísticas de las colecciones creadas.
     */
    private function showStatistics(): void
    {
        $stats = [
            'Total colecciones' => QuoteCollection::count(),
            'Colecciones públicas' => QuoteCollection::where('is_public', true)->count(),
            'Colecciones privadas' => QuoteCollection::where('is_public', false)->count(),
            'Colecciones destacadas' => QuoteCollection::where('is_featured', true)->count(),
            'Con más de 10 citas' => QuoteCollection::where('quotes_count', '>', 10)->count(),
            'Con más de 100 vistas' => QuoteCollection::where('views_count', '>', 100)->count(),
            'Con más de 50 likes' => QuoteCollection::where('likes_count', '>', 50)->count(),
        ];

        $this->command->info("\n📊 Estadísticas de colecciones:");
        foreach ($stats as $type => $count) {
            $this->command->info("   {$type}: {$count}");
        }

        // Temas más populares
        $popularThemes = QuoteCollection::selectRaw('theme, COUNT(*) as count')
                                       ->groupBy('theme')
                                       ->orderBy('count', 'desc')
                                       ->limit(5)
                                       ->get();

        if ($popularThemes->isNotEmpty()) {
            $this->command->info("\n🎯 Temas más populares:");
            foreach ($popularThemes as $theme) {
                $this->command->info("   {$theme->theme}: {$theme->count} colecciones");
            }
        }

        // Colecciones más populares
        $popularCollections = QuoteCollection::where('is_public', true)
                                            ->orderByRaw('(likes_count * 2 + views_count) DESC')
                                            ->limit(3)
                                            ->get();

        if ($popularCollections->isNotEmpty()) {
            $this->command->info("\n⭐ Colecciones más populares:");
            foreach ($popularCollections as $collection) {
                $score = round($collection->popularity_score * 100, 1);
                $this->command->info("   {$collection->name}: {$score}% popularidad ({$collection->views_count} vistas, {$collection->likes_count} likes)");
            }
        }

        // Estadísticas por creador
        $creatorStats = QuoteCollection::selectRaw('created_by, COUNT(*) as collections_count, SUM(quotes_count) as total_quotes')
                                      ->groupBy('created_by')
                                      ->orderBy('collections_count', 'desc')
                                      ->limit(3)
                                      ->get();

        if ($creatorStats->isNotEmpty()) {
            $this->command->info("\n👤 Creadores más activos:");
            foreach ($creatorStats as $creator) {
                $user = User::find($creator->created_by);
                $name = $user ? $user->name : "Usuario {$creator->created_by}";
                $this->command->info("   {$name}: {$creator->collections_count} colecciones, {$creator->total_quotes} citas totales");
            }
        }
    }
}