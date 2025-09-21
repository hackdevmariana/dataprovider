<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserAchievement;
use App\Models\User;
use App\Models\Achievement;
use Carbon\Carbon;

class UserAchievementsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::take(20)->get();
        $achievements = Achievement::all();
        
        if ($users->isEmpty() || $achievements->isEmpty()) {
            $this->command->warn('No hay usuarios o achievements disponibles. Ejecuta primero UserSeeder y AchievementsSeeder.');
            return;
        }

        $created = 0;
        $maxAttempts = 200;
        $attempts = 0;
        
        while ($created < 40 && $attempts < $maxAttempts) {
            $attempts++;
            $userId = $users->random()->id;
            $achievementId = $achievements->random()->id;
            $level = rand(1, 5);
            
            if (!UserAchievement::where('user_id', $userId)
                ->where('achievement_id', $achievementId)
                ->where('level', $level)
                ->exists()) {
                
                UserAchievement::create([
                'user_id' => $userId,
                'achievement_id' => $achievementId,
                'progress' => rand(0, 100),
                'level' => $level,
                'is_completed' => rand(0, 1) == 1,
                'completed_at' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 15)) : null,
                'metadata' => [
                    'energy_saved' => rand(10, 1000),
                    'solar_produced' => rand(50, 5000),
                    'efficiency_improvement' => rand(5, 50),
                ],
                'value_achieved' => rand(10, 1000),
                'points_earned' => rand(10, 500),
                'is_notified' => rand(0, 1) == 1,
                ]);
                $created++;
            }
        }
    }
}
