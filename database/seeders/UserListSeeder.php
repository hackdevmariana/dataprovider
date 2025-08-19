<?php

namespace Database\Seeders;

use App\Models\UserList;
use App\Models\User;
use App\Models\Cooperative;
use App\Models\NewsArticle;
use App\Models\Event;
use Illuminate\Database\Seeder;

class UserListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📋 Creando listas personalizadas de usuarios...');

        $users = User::limit(10)->get();

        if ($users->isEmpty()) {
            $this->command->warn('⚠️ No hay usuarios disponibles. Creando algunos usuarios primero...');
            User::factory()->count(5)->create();
            $users = User::limit(5)->get();
        }

        // Listas destacadas públicas
        $featuredLists = [
            [
                'name' => 'Mejores Cooperativas de España',
                'description' => 'Lista curada de las cooperativas energéticas más activas y confiables del país',
                'list_type' => 'companies',
                'icon' => 'users',
                'color' => '#10B981',
            ],
            [
                'name' => 'Instaladores Verificados',
                'description' => 'Profesionales de instalación solar con certificaciones y reviews positivas',
                'list_type' => 'users',
                'icon' => 'shield-check',
                'color' => '#3B82F6',
            ],
            [
                'name' => 'Recursos Imprescindibles',
                'description' => 'Artículos, guías y herramientas esenciales para el autoconsumo',
                'list_type' => 'resources',
                'icon' => 'book-open',
                'color' => '#F59E0B',
            ],
            [
                'name' => 'Eventos de Energía Renovable',
                'description' => 'Conferencias, talleres y encuentros del sector energético',
                'list_type' => 'events',
                'icon' => 'calendar',
                'color' => '#8B5CF6',
            ],
        ];

        foreach ($featuredLists as $listData) {
            $user = $users->random();
            $list = UserList::create(array_merge($listData, [
                'user_id' => $user->id,
                'visibility' => 'public',
                'is_featured' => true,
                'items_count' => fake()->numberBetween(10, 50),
                'followers_count' => fake()->numberBetween(50, 300),
                'views_count' => fake()->numberBetween(500, 2000),
                'shares_count' => fake()->numberBetween(20, 100),
                'engagement_score' => fake()->randomFloat(2, 200, 800),
            ]));

            $this->command->info("✅ Lista destacada creada: {$list->name}");
        }

        // Listas por cada usuario
        foreach ($users as $user) {
            // Lista personal privada
            UserList::factory()
                   ->for($user)
                   ->state([
                       'name' => 'Mis Favoritos',
                       'description' => 'Contenido que me interesa y quiero revisar más tarde',
                       'list_type' => 'mixed',
                       'visibility' => 'private',
                       'icon' => 'heart',
                       'color' => '#EF4444',
                   ])
                   ->create();

            // Lista pública del usuario
            UserList::factory()
                   ->for($user)
                   ->public()
                   ->create();

            // Posibilidad de lista colaborativa
            if (fake()->boolean(30)) {
                $collaborators = $users->where('id', '!=', $user->id)
                                     ->random(fake()->numberBetween(1, 3))
                                     ->pluck('id')
                                     ->toArray();

                UserList::factory()
                       ->for($user)
                       ->collaborative()
                       ->state([
                           'collaborator_ids' => $collaborators,
                       ])
                       ->create();
            }
        }

        // Listas por tipo específico
        $listTypes = ['users', 'posts', 'projects', 'companies', 'resources', 'events'];
        
        foreach ($listTypes as $type) {
            UserList::factory()
                   ->type($type)
                   ->for($users->random())
                   ->count(fake()->numberBetween(1, 3))
                   ->create();
        }

        // Plantillas de listas
        $templates = [
            [
                'name' => 'Plantilla: Seguimiento de Instalación',
                'description' => 'Plantilla para organizar el proceso de instalación solar',
                'list_type' => 'mixed',
                'icon' => 'clipboard-list',
                'color' => '#6B7280',
            ],
            [
                'name' => 'Plantilla: Comparativa de Ofertas',
                'description' => 'Plantilla para comparar diferentes ofertas de instalación',
                'list_type' => 'companies',
                'icon' => 'scale',
                'color' => '#059669',
            ],
        ];

        foreach ($templates as $templateData) {
            UserList::factory()
                   ->template()
                   ->for($users->first())
                   ->state($templateData)
                   ->create();
        }

        // Añadir algunos elementos de ejemplo a las listas
        $this->addSampleItemsToLists();

        // Estadísticas finales
        $total = UserList::count();
        $public = UserList::where('visibility', 'public')->count();
        $featured = UserList::where('is_featured', true)->count();
        $collaborative = UserList::where('visibility', 'collaborative')->count();
        $templates = UserList::where('is_template', true)->count();
        $byType = UserList::selectRaw('list_type, COUNT(*) as count')
                         ->groupBy('list_type')
                         ->pluck('count', 'list_type')
                         ->toArray();

        $this->command->info("📊 Estadísticas de Listas:");
        $this->command->info("   Total: {$total}");
        $this->command->info("   Públicas: {$public}");
        $this->command->info("   Destacadas: {$featured}");
        $this->command->info("   Colaborativas: {$collaborative}");
        $this->command->info("   Plantillas: {$templates}");
        
        foreach ($byType as $type => $count) {
            $this->command->info("   {$type}: {$count}");
        }

        $this->command->info('🎉 Listas de usuarios creadas exitosamente!');
    }

    /**
     * Añadir elementos de ejemplo a algunas listas.
     */
    private function addSampleItemsToLists(): void
    {
        $lists = UserList::where('visibility', 'public')
                        ->where('is_active', true)
                        ->limit(5)
                        ->get();

        foreach ($lists as $list) {
            $itemCount = fake()->numberBetween(3, 15);
            
            for ($i = 0; $i < $itemCount; $i++) {
                // Determinar qué tipo de contenido añadir según el tipo de lista
                $content = $this->getRandomContentForListType($list->list_type);
                
                if ($content) {
                    $list->addItem($content, $list->user, [
                        'note' => fake()->optional(0.3)->sentence(),
                        'rating' => fake()->optional(0.4)->randomFloat(1, 1, 5),
                        'mode' => 'manual',
                    ]);
                }
            }

            $this->command->info("   ➕ Añadidos {$itemCount} elementos a: {$list->name}");
        }
    }

    /**
     * Obtener contenido aleatorio según el tipo de lista.
     */
    private function getRandomContentForListType(string $listType)
    {
        return match($listType) {
            'companies' => Cooperative::inRandomOrder()->first(),
            'resources', 'posts' => NewsArticle::inRandomOrder()->first(),
            'events' => Event::inRandomOrder()->first(),
            'users' => User::inRandomOrder()->first(),
            default => collect([
                Cooperative::inRandomOrder()->first(),
                NewsArticle::inRandomOrder()->first(),
                Event::inRandomOrder()->first(),
                User::inRandomOrder()->first(),
            ])->filter()->random(),
        };
    }
}
