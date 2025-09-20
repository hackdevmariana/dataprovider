<?php

namespace Database\Seeders;

use App\Models\ListItem;
use App\Models\UserList;
use App\Models\User;
use App\Models\NewsArticle;
use App\Models\TopicPost;
use App\Models\CooperativePost;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ListItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creando elementos de listas personalizadas...');

        // Verificar que existen usuarios y listas
        $users = User::limit(5)->get();
        $userLists = UserList::limit(10)->get();

        if ($users->isEmpty()) {
            $this->command->warn('No hay usuarios disponibles. Creando usuario de prueba...');
            $user = User::create([
                'name' => 'Usuario de Prueba',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
            ]);
            $users = collect([$user]);
        }

        if ($userLists->isEmpty()) {
            $this->command->warn('No hay listas disponibles. Creando listas de prueba...');
            foreach ($users as $user) {
                UserList::create([
                    'user_id' => $user->id,
                    'name' => 'Mi Lista Favorita',
                    'description' => 'Una lista de contenido interesante',
                    'list_type' => 'curated',
                    'visibility' => 'public',
                    'is_active' => true,
                ]);
            }
            $userLists = UserList::limit(10)->get();
        }

        // Obtener contenido disponible para añadir a las listas
        $newsArticles = NewsArticle::limit(20)->get();
        $topicPosts = TopicPost::limit(20)->get();
        $cooperativePosts = CooperativePost::limit(20)->get();

        $contentItems = collect()
            ->merge($newsArticles->map(fn($item) => ['type' => NewsArticle::class, 'id' => $item->id]))
            ->merge($topicPosts->map(fn($item) => ['type' => TopicPost::class, 'id' => $item->id]))
            ->merge($cooperativePosts->map(fn($item) => ['type' => CooperativePost::class, 'id' => $item->id]));

        $addedModes = ['manual', 'auto_hashtag', 'auto_keyword', 'auto_author', 'suggested', 'imported'];
        $statuses = ['active', 'pending', 'rejected', 'archived'];
        $tags = [
            ['energía', 'sostenibilidad'],
            ['tecnología', 'innovación'],
            ['cooperativas', 'comunidad'],
            ['noticias', 'actualidad'],
            ['proyectos', 'desarrollo'],
        ];

        $count = 0;
        foreach ($userLists as $list) {
            // Añadir entre 3-8 elementos por lista
            $itemsCount = rand(3, 8);
            
            for ($i = 0; $i < $itemsCount; $i++) {
                if ($contentItems->isEmpty()) {
                    break;
                }

                $contentItem = $contentItems->random();
                $addedBy = $users->random();
                
                // Verificar que no existe ya este elemento en la lista
                $exists = ListItem::where('user_list_id', $list->id)
                    ->where('listable_type', $contentItem['type'])
                    ->where('listable_id', $contentItem['id'])
                    ->exists();

                if ($exists) {
                    continue;
                }

                $listItem = ListItem::create([
                    'user_list_id' => $list->id,
                    'listable_type' => $contentItem['type'],
                    'listable_id' => $contentItem['id'],
                    'added_by' => $addedBy->id,
                    'position' => $i + 1,
                    'personal_note' => fake()->optional(0.7)->sentence(),
                    'tags' => fake()->optional(0.6)->randomElement($tags),
                    'personal_rating' => fake()->optional(0.5)->randomFloat(1, 1.0, 5.0),
                    'added_mode' => fake()->randomElement($addedModes),
                    'status' => fake()->randomElement($statuses),
                    'reviewed_by' => fake()->optional(0.3)->randomElement($users->pluck('id')->toArray()),
                    'reviewed_at' => fake()->optional(0.3)->dateTimeBetween('-30 days', 'now'),
                    'clicks_count' => fake()->numberBetween(0, 50),
                    'likes_count' => fake()->numberBetween(0, 20),
                    'last_accessed_at' => fake()->optional(0.4)->dateTimeBetween('-7 days', 'now'),
                ]);

                $count++;
            }
        }

        $this->command->info("✅ Creados {$count} elementos de lista");
        $this->showStatistics();
    }

    /**
     * Mostrar estadísticas de los elementos creados.
     */
    private function showStatistics(): void
    {
        $stats = [
            'Total elementos' => ListItem::count(),
            'Elementos activos' => ListItem::where('status', 'active')->count(),
            'Elementos pendientes' => ListItem::where('status', 'pending')->count(),
            'Elementos archivados' => ListItem::where('status', 'archived')->count(),
            'Con notas personales' => ListItem::whereNotNull('personal_note')->count(),
            'Con rating personal' => ListItem::whereNotNull('personal_rating')->count(),
        ];

        $this->command->info("\n📊 Estadísticas de elementos de lista:");
        foreach ($stats as $type => $count) {
            $this->command->info("   {$type}: {$count}");
        }

        // Modos de adición más comunes
        $modes = ListItem::selectRaw('added_mode, COUNT(*) as count')
                        ->groupBy('added_mode')
                        ->orderBy('count', 'desc')
                        ->get();

        if ($modes->isNotEmpty()) {
            $this->command->info("\n📝 Modos de adición:");
            foreach ($modes as $mode) {
                $this->command->info("   {$mode->added_mode}: {$mode->count}");
            }
        }

        // Tipos de contenido más añadidos
        $contentTypes = ListItem::selectRaw('listable_type, COUNT(*) as count')
                              ->groupBy('listable_type')
                              ->orderBy('count', 'desc')
                              ->get();

        if ($contentTypes->isNotEmpty()) {
            $this->command->info("\n📄 Tipos de contenido:");
            foreach ($contentTypes as $type) {
                $className = class_basename($type->listable_type);
                $this->command->info("   {$className}: {$type->count}");
            }
        }
    }
}