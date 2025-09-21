<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CooperativePost;
use App\Models\Cooperative;
use App\Models\User;
use Carbon\Carbon;

class CooperativePostsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cooperatives = Cooperative::take(5)->get();
        $users = User::take(15)->get();
        
        if ($cooperatives->isEmpty() || $users->isEmpty()) {
            $this->command->warn('No hay cooperativas o usuarios disponibles. Ejecuta primero CooperativeSeeder y UserSeeder.');
            return;
        }

        $postTypes = ['announcement', 'news', 'event', 'discussion', 'update'];
        $statuses = ['draft', 'published', 'archived'];
        $visibilities = ['public', 'members_only', 'board_only'];
        
        $titles = [
            'Nueva iniciativa de energía solar comunitaria',
            'Reunión mensual de la cooperativa',
            'Resultados del último trimestre',
            'Oportunidades de inversión en renovables',
            'Taller sobre eficiencia energética',
            'Actualización sobre proyectos en curso',
            'Celebración del aniversario de la cooperativa',
            'Nuevos miembros se unen a la comunidad',
            'Política de sostenibilidad actualizada',
            'Evento de networking para socios',
        ];

        for ($i = 0; $i < 30; $i++) {
            CooperativePost::create([
                'cooperative_id' => $cooperatives->random()->id,
                'author_id' => $users->random()->id,
                'title' => $titles[array_rand($titles)],
                'content' => 'Este es el contenido del post número ' . ($i + 1) . '. Contiene información relevante sobre la cooperativa y sus actividades.',
                'post_type' => $postTypes[array_rand($postTypes)],
                'status' => $statuses[array_rand($statuses)],
                'visibility' => $visibilities[array_rand($visibilities)],
                'attachments' => rand(0, 1) ? ['document.pdf', 'image.jpg'] : null,
                'metadata' => [
                    'tags' => ['energía', 'sostenibilidad', 'comunidad'],
                    'category' => 'general',
                    'priority' => rand(1, 5),
                ],
                'comments_enabled' => rand(0, 1) == 1,
                'is_pinned' => rand(0, 10) == 0, // 10% chance
                'is_featured' => rand(0, 5) == 0, // 20% chance
                'views_count' => rand(0, 500),
                'likes_count' => rand(0, 100),
                'comments_count' => rand(0, 50),
                'published_at' => Carbon::now()->subDays(rand(0, 90)),
                'pinned_until' => rand(0, 1) ? Carbon::now()->addDays(rand(1, 30)) : null,
            ]);
        }
    }
}
