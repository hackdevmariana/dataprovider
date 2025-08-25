<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserChallenge;
use App\Models\User;
use App\Models\Challenge;
use Carbon\Carbon;

class UserChallengeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar user-challenges existentes
        UserChallenge::truncate();

        $users = User::all();
        $challenges = Challenge::all();

        if ($users->isEmpty()) {
            $this->command->warn('No hay usuarios disponibles para crear user-challenges.');
            return;
        }

        if ($challenges->isEmpty()) {
            $this->command->warn('No hay retos disponibles para asignar.');
            return;
        }

        // Crear user-challenges para cada usuario
        foreach ($users as $user) {
            $this->createUserChallenges($user, $challenges);
        }

        $this->command->info('✅ Se han creado ' . UserChallenge::count() . ' user-challenges.');
    }

    private function createUserChallenges(User $user, $challenges): void
    {
        // Seleccionar aleatoriamente entre 3-7 retos para cada usuario
        $selectedChallenges = $challenges->random(rand(3, min(7, $challenges->count())));
        
        foreach ($selectedChallenges as $challenge) {
            $this->createUserChallenge($user, $challenge);
        }
    }

    private function createUserChallenge(User $user, Challenge $challenge): void
    {
        // Verificar si ya existe un user-challenge para este usuario y reto
        $existingUserChallenge = UserChallenge::where([
            'user_id' => $user->id,
            'challenge_id' => $challenge->id,
        ])->first();

        if ($existingUserChallenge) {
            return; // Ya existe, saltar
        }

        // Determinar el estado del reto
        $status = $this->getRandomStatus();
        $joinedAt = $this->getRandomJoinedDate();
        $completedAt = $status === 'completed' ? $this->getRandomCompletionDate($joinedAt) : null;
        
        $userChallengeData = [
            'user_id' => $user->id,
            'challenge_id' => $challenge->id,
            'status' => $status,
            'joined_at' => $joinedAt,
            'completed_at' => $completedAt,
            'progress' => $this->generateProgress($challenge, $status),
            'current_value' => $this->getCurrentValue($challenge, $status),
            'ranking_position' => $this->getRankingPosition($status),
            'points_earned' => $status === 'completed' ? $this->getRandomPoints($challenge) : 0,
            'reward_earned' => $status === 'completed' ? $this->getRandomReward($challenge) : 0,
            'achievements_unlocked' => $this->getAchievementsUnlocked($status),
            'notes' => $this->getRandomNotes($status),
            'is_team_leader' => $this->shouldBeTeamLeader(),
            'team_id' => $this->shouldBeTeamLeader() ? rand(1, 5) : null,
        ];

        UserChallenge::create($userChallengeData);
    }

    private function getRandomStatus(): string
    {
        $statuses = [
            'registered' => 20,   // 20% registrado
            'active' => 30,       // 30% activo
            'completed' => 35,    // 35% completado
            'failed' => 10,       // 10% fallido
            'abandoned' => 5,     // 5% abandonado
        ];

        $random = rand(1, 100);
        $cumulative = 0;
        
        foreach ($statuses as $status => $probability) {
            $cumulative += $probability;
            if ($random <= $cumulative) {
                return $status;
            }
        }
        
        return 'registered'; // Fallback
    }

    private function getRandomJoinedDate(): Carbon
    {
        // Fecha de unión entre 60 días atrás y ahora
        return now()->subDays(rand(0, 60));
    }

    private function getRandomCompletionDate(Carbon $joinedAt): Carbon
    {
        // Fecha de completado entre la fecha de unión y ahora
        $daysSinceJoined = $joinedAt->diffInDays(now());
        $completionDays = rand(1, min($daysSinceJoined, 30)); // Máximo 30 días para completar
        
        return $joinedAt->addDays($completionDays);
    }

    private function generateProgress(Challenge $challenge, string $status): array
    {
        $progress = [];
        
        if ($status === 'completed') {
            // Progreso completo
            $progress = [
                'milestone_1' => ['completed' => true, 'date' => now()->subDays(rand(1, 10))->toISOString()],
                'milestone_2' => ['completed' => true, 'date' => now()->subDays(rand(1, 8))->toISOString()],
                'milestone_3' => ['completed' => true, 'date' => now()->subDays(rand(1, 5))->toISOString()],
                'final_goal' => ['completed' => true, 'date' => now()->toISOString()],
            ];
        } elseif ($status === 'active') {
            // Progreso parcial
            $milestones = rand(1, 3);
            for ($i = 1; $i <= $milestones; $i++) {
                $progress["milestone_{$i}"] = [
                    'completed' => true,
                    'date' => now()->subDays(rand(1, 15))->toISOString()
                ];
            }
            
            // Agregar milestone en progreso
            $progress["milestone_" . ($milestones + 1)] = [
                'completed' => false,
                'progress' => rand(20, 80),
                'started_at' => now()->subDays(rand(1, 5))->toISOString()
            ];
        } elseif ($status === 'failed') {
            // Progreso fallido
            $progress = [
                'milestone_1' => ['completed' => true, 'date' => now()->subDays(rand(5, 20))->toISOString()],
                'milestone_2' => ['completed' => false, 'failed_at' => now()->subDays(rand(1, 10))->toISOString(), 'reason' => 'time_limit_exceeded'],
            ];
        } elseif ($status === 'abandoned') {
            // Progreso abandonado
            $progress = [
                'milestone_1' => ['completed' => false, 'abandoned_at' => now()->subDays(rand(5, 25))->toISOString(), 'reason' => 'user_choice'],
            ];
        } else {
            // Registrado pero no iniciado
            $progress = [
                'status' => 'ready_to_start',
                'registered_at' => now()->toISOString(),
            ];
        }

        return $progress;
    }

    private function getCurrentValue(Challenge $challenge, string $status): float
    {
        // Obtener el objetivo del reto de manera segura
        $mainGoal = $this->getChallengeGoal($challenge);
        
        if ($status === 'completed') {
            // Valor completo del reto
            return $mainGoal;
        } elseif ($status === 'active') {
            // Valor parcial
            return $mainGoal * (rand(30, 80) / 100);
        } elseif ($status === 'failed') {
            // Valor antes de fallar
            return $mainGoal * (rand(40, 70) / 100);
        } elseif ($status === 'abandoned') {
            // Valor mínimo antes de abandonar
            return $mainGoal * (rand(10, 30) / 100);
        } else {
            // Registrado pero no iniciado
            return 0;
        }
    }

    private function getChallengeGoal(Challenge $challenge): float
    {
        $goals = $challenge->goals;
        
        if (is_array($goals)) {
            return (float) (array_values($goals)[0] ?? 100);
        } elseif (is_string($goals)) {
            // Intentar decodificar JSON si es un string
            $decoded = json_decode($goals, true);
            if (is_array($decoded)) {
                return (float) (array_values($decoded)[0] ?? 100);
            }
            // Si no es JSON válido, usar un valor por defecto
            return 100.0;
        } else {
            // Valor por defecto
            return 100.0;
        }
    }

    private function getRankingPosition(string $status): ?int
    {
        if ($status === 'completed') {
            // Posición en ranking para retos completados
            return rand(1, 50);
        } elseif ($status === 'active') {
            // Posición en ranking para retos activos
            return rand(1, 100);
        } else {
            // Sin ranking para otros estados
            return null;
        }
    }

    private function getRandomPoints(Challenge $challenge): int
    {
        // Puntos basados en el tipo de reto
        $basePoints = $challenge->points ?? 100;
        
        if (str_contains(strtolower($challenge->name), 'energía')) {
            return $basePoints + rand(50, 200); // Bonus por retos de energía
        } elseif (str_contains(strtolower($challenge->name), 'sostenibilidad')) {
            return $basePoints + rand(30, 150); // Bonus por retos de sostenibilidad
        } elseif (str_contains(strtolower($challenge->name), 'comunidad')) {
            return $basePoints + rand(20, 100); // Bonus por retos de comunidad
        } else {
            return $basePoints + rand(0, 50); // Bonus estándar
        }
    }

    private function getRandomReward(Challenge $challenge): float
    {
        // Recompensa económica basada en el tipo de reto
        $baseReward = 10.0;
        
        if (str_contains(strtolower($challenge->name), 'energía')) {
            return $baseReward + rand(5, 25); // Recompensa alta por retos de energía
        } elseif (str_contains(strtolower($challenge->name), 'sostenibilidad')) {
            return $baseReward + rand(3, 20); // Recompensa media por retos de sostenibilidad
        } elseif (str_contains(strtolower($challenge->name), 'comunidad')) {
            return $baseReward + rand(2, 15); // Recompensa media por retos de comunidad
        } else {
            return $baseReward + rand(1, 10); // Recompensa estándar
        }
    }

    private function getAchievementsUnlocked(string $status): ?array
    {
        if ($status === 'completed') {
            // Logros desbloqueados al completar el reto
            $achievementCount = rand(1, 3);
            $achievements = [];
            
            for ($i = 0; $i < $achievementCount; $i++) {
                $achievements[] = rand(1, 20); // IDs de logros ficticios
            }
            
            return $achievements;
        } elseif ($status === 'active') {
            // Algunos logros desbloqueados durante el progreso
            if (rand(1, 100) <= 40) { // 40% de probabilidad
                return [rand(1, 20)];
            }
        }
        
        return null;
    }

    private function getRandomNotes(string $status): ?string
    {
        if (rand(1, 100) <= 60) { // 60% de probabilidad de tener notas
            return match ($status) {
                'completed' => $this->getCompletionNotes(),
                'active' => $this->getActiveNotes(),
                'failed' => $this->getFailedNotes(),
                'abandoned' => $this->getAbandonedNotes(),
                default => $this->getRegisteredNotes(),
            };
        }
        
        return null;
    }

    private function getCompletionNotes(): string
    {
        $notes = [
            "¡Reto completado exitosamente! Fue una experiencia muy gratificante.",
            "Excelente reto, aprendí mucho sobre sostenibilidad energética.",
            "Completado con el equipo. La colaboración fue clave para el éxito.",
            "Reto desafiante pero muy educativo. Recomiendo participar.",
            "Meta alcanzada antes del tiempo límite. Satisfecho con los resultados.",
        ];
        
        return $notes[array_rand($notes)];
    }

    private function getActiveNotes(): string
    {
        $notes = [
            "Reto en progreso. Estoy aprendiendo mucho en el camino.",
            "Participando activamente. El reto es más desafiante de lo esperado.",
            "En marcha con el equipo. La coordinación está funcionando bien.",
            "Progreso constante. Cada día aprendo algo nuevo.",
            "Reto interesante. Me está ayudando a desarrollar nuevas habilidades.",
        ];
        
        return $notes[array_rand($notes)];
    }

    private function getFailedNotes(): string
    {
        $notes = [
            "Reto fallido por falta de tiempo. Intentaré de nuevo.",
            "No pude completar el reto. Necesito más preparación.",
            "Fallé en el último milestone. Fue una experiencia de aprendizaje.",
            "Reto fallido por problemas técnicos. Espero que se resuelvan.",
            "No logré alcanzar la meta. Evaluaré mis estrategias.",
        ];
        
        return $notes[array_rand($notes)];
    }

    private function getAbandonedNotes(): string
    {
        $notes = [
            "Decidí abandonar el reto. No era el momento adecuado.",
            "Abandonado por cambios en mis prioridades.",
            "Reto abandonado temporalmente. Lo retomaré más adelante.",
            "No pude continuar por compromisos personales.",
            "Abandonado por falta de interés en el tema.",
        ];
        
        return $notes[array_rand($notes)];
    }

    private function getRegisteredNotes(): string
    {
        $notes = [
            "Registrado y listo para comenzar. ¡Emocionado por el reto!",
            "Inscrito en el reto. Preparándome para la participación.",
            "Registrado. Esperando el momento adecuado para iniciar.",
            "Listo para participar. He revisado los requisitos.",
            "Inscrito en el reto. Planificando mi estrategia.",
        ];
        
        return $notes[array_rand($notes)];
    }

    private function shouldBeTeamLeader(): bool
    {
        // 15% de probabilidad de ser líder de equipo
        return rand(1, 100) <= 15;
    }
}
