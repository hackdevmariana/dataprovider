<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserAchievement;
use App\Models\User;
use App\Models\Achievement;
use Carbon\Carbon;

class UserAchievementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar user-achievements existentes
        UserAchievement::truncate();

        $users = User::all();
        $achievements = Achievement::all();

        if ($users->isEmpty()) {
            $this->command->warn('No hay usuarios disponibles para crear logros.');
            return;
        }

        if ($achievements->isEmpty()) {
            $this->command->warn('No hay logros disponibles para asignar.');
            return;
        }

        // Crear user-achievements para cada usuario
        foreach ($users as $user) {
            $this->createUserAchievements($user, $achievements);
        }

        $this->command->info('✅ Se han creado ' . UserAchievement::count() . ' user-achievements.');
    }

    private function createUserAchievements(User $user, $achievements): void
    {
        // Seleccionar aleatoriamente entre 5-12 logros para cada usuario
        $selectedAchievements = $achievements->random(rand(5, min(12, $achievements->count())));
        
        foreach ($selectedAchievements as $achievement) {
            $this->createUserAchievement($user, $achievement);
        }
    }

    private function createUserAchievement(User $user, Achievement $achievement): void
    {
        // Determinar el estado del logro
        $isCompleted = $this->shouldBeCompleted();
        $progress = $isCompleted ? 100 : $this->getRandomProgress();
        $level = $this->getRandomLevel($achievement);
        
        // Verificar si ya existe un user-achievement para este usuario, logro y nivel
        $existingUserAchievement = UserAchievement::where([
            'user_id' => $user->id,
            'achievement_id' => $achievement->id,
            'level' => $level,
        ])->first();

        if ($existingUserAchievement) {
            return; // Ya existe, saltar
        }

        $userAchievementData = [
            'user_id' => $user->id,
            'achievement_id' => $achievement->id,
            'progress' => $progress,
            'level' => $level,
            'is_completed' => $isCompleted,
            'completed_at' => $isCompleted ? $this->getRandomCompletionDate() : null,
            'metadata' => $this->generateMetadata($achievement, $isCompleted),
            'value_achieved' => $isCompleted ? $this->getRandomValueAchieved($achievement) : null,
            'points_earned' => $isCompleted ? $achievement->points : 0,
            'is_notified' => $isCompleted ? $this->shouldBeNotified() : false,
        ];

        UserAchievement::create($userAchievementData);
    }

    private function shouldBeCompleted(): bool
    {
        // 60% de probabilidad de estar completado
        return rand(1, 100) <= 60;
    }

    private function getRandomProgress(): int
    {
        // Progreso entre 0 y 95 (no 100 para evitar auto-completado)
        return rand(0, 95);
    }

    private function getRandomLevel(Achievement $achievement): int
    {
        // Para logros progresivos, usar niveles 1-3
        // Para logros simples, usar solo nivel 1
        if ($achievement->is_progressive ?? false) {
            return rand(1, 3);
        }
        
        return 1;
    }

    private function getRandomCompletionDate(): Carbon
    {
        // Fecha de completado entre 30 días atrás y ahora
        return now()->subDays(rand(0, 30));
    }

    private function shouldBeNotified(): bool
    {
        // 80% de probabilidad de haber sido notificado si está completado
        return rand(1, 100) <= 80;
    }

    private function generateMetadata(Achievement $achievement, bool $isCompleted): array
    {
        $metadata = [];

        if ($isCompleted) {
            $metadata['completion_method'] = $this->getRandomCompletionMethod();
            $metadata['completion_time'] = now()->toISOString();
            $metadata['difficulty_rating'] = rand(1, 5);
            
            // Metadatos específicos según el tipo de logro
            if (str_contains(strtolower($achievement->name), 'post')) {
                $metadata['posts_created'] = rand(1, 20);
                $metadata['engagement_rate'] = rand(50, 95) / 100;
            } elseif (str_contains(strtolower($achievement->name), 'comment')) {
                $metadata['comments_made'] = rand(5, 50);
                $metadata['helpful_votes'] = rand(10, 100);
            } elseif (str_contains(strtolower($achievement->name), 'project')) {
                $metadata['projects_contributed'] = rand(1, 10);
                $metadata['collaboration_score'] = rand(70, 100) / 100;
            } elseif (str_contains(strtolower($achievement->name), 'energy')) {
                $metadata['installations_visited'] = rand(3, 15);
                $metadata['knowledge_shared'] = rand(5, 25);
            } elseif (str_contains(strtolower($achievement->name), 'cooperative')) {
                $metadata['cooperatives_joined'] = rand(1, 5);
                $metadata['community_contribution'] = rand(60, 100) / 100;
            } elseif (str_contains(strtolower($achievement->name), 'region')) {
                $metadata['regions_explored'] = rand(2, 8);
                $metadata['local_knowledge'] = rand(70, 100) / 100;
            } elseif (str_contains(strtolower($achievement->name), 'species')) {
                $metadata['species_identified'] = rand(5, 20);
                $metadata['conservation_efforts'] = rand(50, 100) / 100;
            } else {
                // Metadatos genéricos para otros tipos de logros
                $metadata['general_contribution'] = rand(40, 90) / 100;
                $metadata['learning_progress'] = rand(60, 100) / 100;
            }
        } else {
            // Metadatos para logros en progreso
            $metadata['started_at'] = now()->subDays(rand(1, 60))->toISOString();
            $metadata['current_streak'] = rand(1, 14);
            $metadata['estimated_completion'] = now()->addDays(rand(1, 90))->toISOString();
            
            // Metadatos específicos según el tipo de logro
            if (str_contains(strtolower($achievement->name), 'post')) {
                $metadata['posts_needed'] = rand(5, 20);
                $metadata['current_posts'] = rand(1, 15);
            } elseif (str_contains(strtolower($achievement->name), 'comment')) {
                $metadata['comments_needed'] = rand(10, 50);
                $metadata['current_comments'] = rand(2, 30);
            } elseif (str_contains(strtolower($achievement->name), 'project')) {
                $metadata['projects_needed'] = rand(3, 10);
                $metadata['current_projects'] = rand(1, 5);
            } elseif (str_contains(strtolower($achievement->name), 'energy')) {
                $metadata['installations_needed'] = rand(5, 20);
                $metadata['current_installations'] = rand(1, 10);
            } elseif (str_contains(strtolower($achievement->name), 'cooperative')) {
                $metadata['cooperatives_needed'] = rand(2, 8);
                $metadata['current_cooperatives'] = rand(1, 3);
            } elseif (str_contains(strtolower($achievement->name), 'region')) {
                $metadata['regions_needed'] = rand(3, 10);
                $metadata['current_regions'] = rand(1, 6);
            } elseif (str_contains(strtolower($achievement->name), 'species')) {
                $metadata['species_needed'] = rand(8, 25);
                $metadata['current_species'] = rand(2, 15);
            } else {
                // Metadatos genéricos para otros tipos de logros
                $metadata['target_value'] = rand(50, 200);
                $metadata['current_value'] = rand(10, 100);
            }
        }

        return $metadata;
    }

    private function getRandomCompletionMethod(): string
    {
        $methods = [
            'natural_progression',
            'dedicated_effort',
            'collaboration',
            'research',
            'practice',
            'exploration',
            'community_help',
            'tutorial_following',
            'experimentation',
            'consistent_activity'
        ];
        
        return $methods[array_rand($methods)];
    }

    private function getRandomValueAchieved(Achievement $achievement): float
    {
        // Valor alcanzado basado en el tipo de logro
        if (str_contains(strtolower($achievement->name), 'post')) {
            return rand(15, 50); // Posts creados
        } elseif (str_contains(strtolower($achievement->name), 'comment')) {
            return rand(25, 100); // Comentarios realizados
        } elseif (str_contains(strtolower($achievement->name), 'project')) {
            return rand(5, 20); // Proyectos contribuidos
        } elseif (str_contains(strtolower($achievement->name), 'energy')) {
            return rand(10, 30); // Instalaciones visitadas
        } elseif (str_contains(strtolower($achievement->name), 'cooperative')) {
            return rand(3, 10); // Cooperativas unidas
        } elseif (str_contains(strtolower($achievement->name), 'region')) {
            return rand(5, 15); // Regiones exploradas
        } elseif (str_contains(strtolower($achievement->name), 'species')) {
            return rand(10, 40); // Especies identificadas
        } else {
            // Valor genérico
            return rand(20, 80);
        }
    }
}
