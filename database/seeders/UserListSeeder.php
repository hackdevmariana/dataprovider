<?php

namespace Database\Seeders;

use App\Models\UserList;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserListSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Verificar que existan usuarios
        if (User::count() === 0) {
            $this->command->warn('No hay usuarios. Creando algunos usuarios de ejemplo...');
            User::factory(10)->create();
        }

        $users = User::all();

        $this->command->info('Creando listas de usuario...');

        // Listas básicas (40% de las listas)
        UserList::factory(40)
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Listas mixtas (15% de las listas)
        UserList::factory(15)
            ->mixed()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Listas de usuarios (10% de las listas)
        UserList::factory(10)
            ->users()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Listas de posts (10% de las listas)
        UserList::factory(10)
            ->posts()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Listas de proyectos (8% de las listas)
        UserList::factory(8)
            ->projects()
            ->create([
                'user_id' => $users->random()->id,
            ]);

        // Listas de empresas (7% de las listas)
        UserList::factory(7)
            ->companies()
            ->create([
                'user_id' => $users->random()->id,
            ]);

        // Listas de recursos (5% de las listas)
        UserList::factory(5)
            ->resources()
            ->create([
                'user_id' => $users->random()->id,
            ]);

        // Listas de eventos (3% de las listas)
        UserList::factory(3)
            ->events()
            ->create([
                'user_id' => $users->random()->id,
            ]);

        // Listas personalizadas (2% de las listas)
        UserList::factory(2)
            ->custom()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Listas privadas (30% de las listas)
        UserList::factory(30)
            ->private()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Listas públicas (40% de las listas)
        UserList::factory(40)
            ->public()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Listas para seguidores (20% de las listas)
        UserList::factory(20)
            ->followers()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Listas colaborativas (10% de las listas)
        UserList::factory(10)
            ->collaborative()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Listas manuales (60% de las listas)
        UserList::factory(60)
            ->manual()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Listas con auto-hashtag (15% de las listas)
        UserList::factory(15)
            ->autoHashtag()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Listas con auto-keyword (10% de las listas)
        UserList::factory(10)
            ->autoKeyword()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Listas con auto-author (8% de las listas)
        UserList::factory(8)
            ->autoAuthor()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Listas con auto-topic (7% de las listas)
        UserList::factory(7)
            ->autoTopic()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Listas destacadas (5% de las listas)
        UserList::factory(5)
            ->featured()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Listas plantilla (3% de las listas)
        UserList::factory(3)
            ->template()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Listas activas (90% de las listas)
        UserList::factory(90)
            ->active()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Listas inactivas (10% de las listas)
        UserList::factory(10)
            ->inactive()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Listas con muchos elementos (10% de las listas)
        UserList::factory(10)
            ->withItems(fake()->numberBetween(20, 50))
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Listas con muchos seguidores (8% de las listas)
        UserList::factory(8)
            ->withFollowers(fake()->numberBetween(50, 200))
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Listas con muchas vistas (12% de las listas)
        UserList::factory(12)
            ->withViews(fake()->numberBetween(500, 2000))
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Listas con muchos shares (6% de las listas)
        UserList::factory(6)
            ->withShares(fake()->numberBetween(20, 100))
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Listas con alto engagement (8% de las listas)
        UserList::factory(8)
            ->highEngagement()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Listas con bajo engagement (15% de las listas)
        UserList::factory(15)
            ->lowEngagement()
            ->create([
                'user_id' => fn() => $users->random()->id,
            ]);

        // Crear listas específicas para usuarios conocidos
        $this->createSpecificLists($users);

        $this->command->info('✅ Listas de usuario creadas exitosamente!');
    }

    private function createSpecificLists($users): void
    {
        // Listas para usuario específico
        $user1 = $users->first();
        if ($user1) {
            UserList::create([
                'user_id' => $user1->id,
                'name' => 'Mis Favoritos',
                'slug' => 'mis-favoritos',
                'description' => 'Una colección personal de mis recursos favoritos y contenido de calidad.',
                'icon' => '⭐',
                'color' => '#F59E0B',
                'list_type' => 'mixed',
                'allowed_content_types' => null,
                'visibility' => 'private',
                'collaborator_ids' => null,
                'allow_suggestions' => false,
                'allow_comments' => false,
                'curation_mode' => 'manual',
                'auto_criteria' => null,
                'items_count' => 25,
                'followers_count' => 0,
                'views_count' => 0,
                'shares_count' => 0,
                'engagement_score' => 25.0,
                'is_featured' => false,
                'is_template' => false,
                'is_active' => true,
            ]);

            UserList::create([
                'user_id' => $user1->id,
                'name' => 'Recursos de Desarrollo',
                'slug' => 'recursos-de-desarrollo',
                'description' => 'Herramientas, librerías y recursos esenciales para el desarrollo de software.',
                'icon' => '🔧',
                'color' => '#3B82F6',
                'list_type' => 'resources',
                'allowed_content_types' => ['link', 'resource', 'tool'],
                'visibility' => 'public',
                'collaborator_ids' => null,
                'allow_suggestions' => true,
                'allow_comments' => true,
                'curation_mode' => 'auto_keyword',
                'auto_criteria' => [
                    'keywords' => ['desarrollo', 'programación', 'herramientas', 'librerías'],
                    'min_quality_score' => 8,
                ],
                'items_count' => 45,
                'followers_count' => 120,
                'views_count' => 850,
                'shares_count' => 35,
                'engagement_score' => 285.0,
                'is_featured' => true,
                'is_template' => true,
                'is_active' => true,
            ]);
        }

        // Listas para otro usuario
        $user2 = $users->skip(1)->first();
        if ($user2) {
            UserList::create([
                'user_id' => $user2->id,
                'name' => 'Eventos del Sector',
                'slug' => 'eventos-del-sector',
                'description' => 'Conferencias, meetups y eventos importantes del sector tecnológico.',
                'icon' => '🎪',
                'color' => '#EC4899',
                'list_type' => 'events',
                'allowed_content_types' => ['event', 'conference', 'meetup'],
                'visibility' => 'public',
                'collaborator_ids' => null,
                'allow_suggestions' => true,
                'allow_comments' => false,
                'curation_mode' => 'auto_hashtag',
                'auto_criteria' => [
                    'hashtags' => ['#tecnologia', '#conferencia', '#meetup', '#evento'],
                    'min_engagement' => 50,
                ],
                'items_count' => 30,
                'followers_count' => 85,
                'views_count' => 650,
                'shares_count' => 28,
                'engagement_score' => 221.0,
                'is_featured' => true,
                'is_template' => false,
                'is_active' => true,
            ]);
        }

        // Listas para otro usuario
        $user3 = $users->skip(2)->first();
        if ($user3) {
            UserList::create([
                'user_id' => $user3->id,
                'name' => 'Startups Prometedoras',
                'slug' => 'startups-prometedoras',
                'description' => 'Empresas emergentes con potencial de crecimiento e innovación.',
                'icon' => '🚀',
                'color' => '#8B5CF6',
                'list_type' => 'companies',
                'allowed_content_types' => ['company', 'startup', 'organization'],
                'visibility' => 'public',
                'collaborator_ids' => null,
                'allow_suggestions' => true,
                'allow_comments' => true,
                'curation_mode' => 'auto_topic',
                'auto_criteria' => [
                    'topics' => ['startup', 'innovación', 'tecnología', 'emprendimiento'],
                    'min_relevance_score' => 0.8,
                ],
                'items_count' => 20,
                'followers_count' => 95,
                'views_count' => 720,
                'shares_count' => 42,
                'engagement_score' => 254.0,
                'is_featured' => true,
                'is_template' => false,
                'is_active' => true,
            ]);
        }
    }
}