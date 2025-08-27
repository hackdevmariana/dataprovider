<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Topic;
use App\Models\User;

class TopicSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@demo.com')->first();
        
        if (!$admin) {
            $this->command->error('No se encontró el usuario admin. Ejecuta UserSeeder primero.');
            return;
        }

        $topics = [
            [
                'name' => 'Energía Solar',
                'slug' => 'energia-solar',
                'description' => 'Discusiones sobre energía solar, paneles fotovoltaicos, instalaciones y mantenimiento.',
                'rules' => 'Mantén las discusiones enfocadas en energía solar. Respeta las opiniones de otros.',
                'welcome_message' => '¡Bienvenido al tema de Energía Solar! Comparte tus experiencias y conocimientos.',
                'icon' => 'fas-sun',
                'color' => '#FFD700',
                'category' => 'technology',
                'difficulty_level' => 'beginner',
                'creator_id' => $admin->id,
                'visibility' => 'public',
                'join_policy' => 'open',
                'post_permission' => 'members',
                'comment_permission' => 'members',
            ],
            [
                'name' => 'Cooperativas Energéticas',
                'slug' => 'cooperativas-energeticas',
                'description' => 'Información sobre cooperativas energéticas, cómo unirse y beneficios.',
                'rules' => 'Información precisa sobre cooperativas. No spam comercial.',
                'welcome_message' => '¡Bienvenido al tema de Cooperativas Energéticas!',
                'icon' => 'fas-users',
                'color' => '#4CAF50',
                'category' => 'cooperative',
                'difficulty_level' => 'beginner',
                'creator_id' => $admin->id,
                'visibility' => 'public',
                'join_policy' => 'open',
                'post_permission' => 'members',
                'comment_permission' => 'members',
            ],
            [
                'name' => 'Eficiencia Energética',
                'slug' => 'eficiencia-energetica',
                'description' => 'Consejos y trucos para mejorar la eficiencia energética en el hogar.',
                'rules' => 'Consejos prácticos y verificables. No pseudociencia.',
                'welcome_message' => '¡Bienvenido al tema de Eficiencia Energética!',
                'icon' => 'fas-leaf',
                'color' => '#8BC34A',
                'category' => 'efficiency',
                'difficulty_level' => 'beginner',
                'creator_id' => $admin->id,
                'visibility' => 'public',
                'join_policy' => 'open',
                'post_permission' => 'members',
                'comment_permission' => 'members',
            ],
            [
                'name' => 'Legislación Energética',
                'slug' => 'legislacion-energetica',
                'description' => 'Discusiones sobre leyes y regulaciones relacionadas con la energía.',
                'rules' => 'Información legal precisa. Consulta con profesionales si es necesario.',
                'welcome_message' => '¡Bienvenido al tema de Legislación Energética!',
                'icon' => 'fas-gavel',
                'color' => '#2196F3',
                'category' => 'legislation',
                'difficulty_level' => 'intermediate',
                'creator_id' => $admin->id,
                'visibility' => 'public',
                'join_policy' => 'open',
                'post_permission' => 'members',
                'comment_permission' => 'members',
            ],
            [
                'name' => 'Financiación de Proyectos',
                'slug' => 'financiacion-proyectos',
                'description' => 'Opciones de financiación para proyectos energéticos y renovables.',
                'rules' => 'Información financiera verificable. No promesas de retornos garantizados.',
                'welcome_message' => '¡Bienvenido al tema de Financiación de Proyectos!',
                'icon' => 'fas-euro-sign',
                'color' => '#FF9800',
                'category' => 'financing',
                'difficulty_level' => 'intermediate',
                'creator_id' => $admin->id,
                'visibility' => 'public',
                'join_policy' => 'open',
                'post_permission' => 'members',
                'comment_permission' => 'members',
            ],
        ];

        foreach ($topics as $topicData) {
            Topic::create($topicData);
        }

        $this->command->info('✅ Creados ' . count($topics) . ' temas básicos');
    }
}
