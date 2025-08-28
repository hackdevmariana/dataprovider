<?php

namespace Database\Seeders;

use App\Models\UserGeneratedContent;
use App\Models\User;
use App\Models\MediaOutlet;
use Illuminate\Database\Seeder;

class UserGeneratedContentSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener algunos usuarios y media outlets para las relaciones
        $users = User::limit(10)->get();
        $mediaOutlets = MediaOutlet::limit(5)->get();

        if ($users->isEmpty() || $mediaOutlets->isEmpty()) {
            $this->command->info('No hay usuarios o media outlets disponibles. Ejecuta UserSeeder y MediaOutletSeeder primero.');
            return;
        }

        $contents = [
            [
                'user_id' => $users->random()->id,
                'related_type' => MediaOutlet::class,
                'related_id' => $mediaOutlets->random()->id,
                'content' => 'Sería genial tener más entrevistas con expertos locales que estén trabajando en estos temas.',
                'type' => 'suggestion',
                'status' => 'pending',
            ],
            [
                'user_id' => $users->random()->id,
                'related_type' => MediaOutlet::class,
                'related_id' => $mediaOutlets->random()->id,
                'content' => 'Me encanta la cobertura sobre sostenibilidad. ¿Podrían hacer más reportajes sobre energías renovables?',
                'type' => 'suggestion',
                'status' => 'approved',
            ],
            [
                'user_id' => $users->random()->id,
                'related_type' => MediaOutlet::class,
                'related_id' => $mediaOutlets->random()->id,
                'content' => 'Excelente artículo sobre el cambio climático. Muy informativo y bien documentado.',
                'type' => 'comment',
                'status' => 'approved',
            ],
            [
                'user_id' => $users->random()->id,
                'related_type' => MediaOutlet::class,
                'related_id' => $mediaOutlets->random()->id,
                'content' => '¿Podrían cubrir más eventos locales relacionados con el medio ambiente?',
                'type' => 'suggestion',
                'status' => 'pending',
            ],
            [
                'user_id' => $users->random()->id,
                'related_type' => MediaOutlet::class,
                'related_id' => $mediaOutlets->random()->id,
                'content' => 'Muy buen trabajo en la sección de tecnología verde. Sigan así.',
                'type' => 'comment',
                'status' => 'approved',
            ],
        ];

        foreach ($contents as $content) {
            UserGeneratedContent::create($content);
        }

        $this->command->info('✅ Creados ' . count($contents) . ' contenidos generados por usuarios');
    }
}