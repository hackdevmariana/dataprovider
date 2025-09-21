<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserBookmark;
use App\Models\User;
use Carbon\Carbon;

class UserBookmarksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::take(20)->get();
        
        if ($users->isEmpty()) {
            $this->command->warn('No hay usuarios disponibles. Ejecuta primero UserSeeder.');
            return;
        }

        $bookmarkableTypes = [
            'App\\Models\\Topic',
            'App\\Models\\Cooperative',
            'App\\Models\\Challenge',
            'App\\Models\\Achievement',
        ];

        $folders = [
            'Energía Renovable',
            'Eficiencia Energética',
            'Sostenibilidad',
            'Cooperativas',
            'Proyectos',
            'Recursos',
            'Eventos',
            'Favoritos',
        ];

        $tags = [
            ['energía', 'renovable', 'solar'],
            ['eficiencia', 'ahorro', 'optimización'],
            ['sostenibilidad', 'medio ambiente', 'verde'],
            ['cooperativa', 'comunidad', 'colaboración'],
            ['proyecto', 'inversión', 'futuro'],
            ['recurso', 'guía', 'tutorial'],
            ['evento', 'conferencia', 'networking'],
            ['favorito', 'importante', 'destacado'],
        ];

        $created = 0;
        $maxAttempts = 200;
        $attempts = 0;
        
        while ($created < 60 && $attempts < $maxAttempts) {
            $attempts++;
            $userId = $users->random()->id;
            $bookmarkableType = $bookmarkableTypes[array_rand($bookmarkableTypes)];
            $bookmarkableId = rand(1, 10);
            
            if (!UserBookmark::where('user_id', $userId)
                ->where('bookmarkable_type', $bookmarkableType)
                ->where('bookmarkable_id', $bookmarkableId)
                ->exists()) {
                
                UserBookmark::create([
                'user_id' => $userId,
                'bookmarkable_type' => $bookmarkableType,
                'bookmarkable_id' => $bookmarkableId,
                'folder' => $folders[array_rand($folders)],
                'tags' => $tags[array_rand($tags)],
                'personal_notes' => 'Nota personal: ' . ($created + 1) . ' - Contenido interesante para revisar más tarde.',
                'priority' => rand(1, 5),
                'reminder_enabled' => rand(0, 1) == 1,
                'reminder_date' => rand(0, 1) ? Carbon::now()->addDays(rand(1, 30)) : null,
                'reminder_frequency' => rand(0, 1) ? ['once', 'weekly', 'monthly'][array_rand(['once', 'weekly', 'monthly'])] : null,
                'access_count' => rand(0, 50),
                'last_accessed_at' => Carbon::now()->subDays(rand(0, 30)),
                'is_public' => rand(0, 1) == 1,
                ]);
                $created++;
            }
        }
    }
}
