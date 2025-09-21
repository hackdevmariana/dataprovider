<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TopicFollowing;
use App\Models\User;
use App\Models\Topic;
use Carbon\Carbon;

class TopicFollowingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::take(20)->get();
        $topics = Topic::take(10)->get();
        
        if ($users->isEmpty() || $topics->isEmpty()) {
            $this->command->warn('No hay usuarios o topics disponibles. Ejecuta primero UserSeeder y TopicSeeder.');
            return;
        }

        $followTypes = ['following', 'watching', 'ignoring'];
        
        $created = 0;
        $maxAttempts = 200;
        $attempts = 0;
        
        while ($created < 50 && $attempts < $maxAttempts) {
            $attempts++;
            $userId = $users->random()->id;
            $topicId = $topics->random()->id;
            
            // Verificar si ya existe esta combinación
            if (!TopicFollowing::where('user_id', $userId)->where('topic_id', $topicId)->exists()) {
                TopicFollowing::create([
                    'user_id' => $userId,
                    'topic_id' => $topicId,
                    'follow_type' => $followTypes[array_rand($followTypes)],
                    'notifications_enabled' => rand(0, 1) == 1,
                    'notification_preferences' => [
                        'new_posts' => rand(0, 1) == 1,
                        'replies' => rand(0, 1) == 1,
                        'mentions' => rand(0, 1) == 1,
                        'weekly_digest' => rand(0, 1) == 1,
                    ],
                    'followed_at' => Carbon::now()->subDays(rand(1, 365)),
                    'last_visited_at' => Carbon::now()->subDays(rand(0, 30)),
                    'visit_count' => rand(1, 100),
                ]);
                $created++;
            }
        }
    }
}
