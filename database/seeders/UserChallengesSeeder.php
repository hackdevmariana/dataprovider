<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserChallenge;
use App\Models\User;
use App\Models\Challenge;
use Carbon\Carbon;

class UserChallengesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::take(20)->get();
        $challenges = Challenge::all();
        
        if ($users->isEmpty() || $challenges->isEmpty()) {
            $this->command->warn('No hay usuarios o challenges disponibles. Ejecuta primero UserSeeder y ChallengesSeeder.');
            return;
        }

        $statuses = ['registered', 'active', 'completed', 'failed', 'abandoned'];
        
        $created = 0;
        $maxAttempts = 200;
        $attempts = 0;
        
        while ($created < 50 && $attempts < $maxAttempts) {
            $attempts++;
            $userId = $users->random()->id;
            $challengeId = $challenges->random()->id;
            
            if (!UserChallenge::where('user_id', $userId)
                ->where('challenge_id', $challengeId)
                ->exists()) {
                
                UserChallenge::create([
                'user_id' => $userId,
                'challenge_id' => $challengeId,
                'status' => $statuses[array_rand($statuses)],
                'joined_at' => Carbon::now()->subDays(rand(1, 60)),
                'completed_at' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 30)) : null,
                'progress' => [
                    'energy_saved' => rand(100, 5000),
                    'solar_produced' => rand(200, 10000),
                    'community_impact' => rand(1, 100),
                    'team_contribution' => rand(1, 100),
                ],
                'current_value' => rand(100, 5000),
                'ranking_position' => rand(1, 50),
                'points_earned' => rand(50, 1000),
                'reward_earned' => rand(10, 500),
                'achievements_unlocked' => [
                    'achievement_1' => rand(0, 1) == 1,
                    'achievement_2' => rand(0, 1) == 1,
                ],
                'notes' => 'Notas del reto: ' . ($created + 1),
                'is_team_leader' => rand(0, 10) == 0,
                'team_id' => rand(0, 1) ? rand(1, 5) : null,
                ]);
                $created++;
            }
        }
    }
}
