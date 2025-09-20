<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TopicPost;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TopicPostSeeder extends Seeder
{
    public function run(): void
    {
        $topics = Topic::take(3)->get();
        $users = User::take(10)->get();

        if ($topics->isEmpty() || $users->isEmpty()) {
            $this->command->warn('No hay temas o usuarios disponibles. Ejecuta TopicSeeder y UserSeeder primero.');
            return;
        }

        $this->command->info('🚀 Iniciando TopicPostSeeder optimizado...');
        $postCount = 0;
        $posts = [];

        foreach ($topics as $topic) {
            $postsPerTopic = rand(1, 2);

            for ($i = 0; $i < $postsPerTopic; $i++) {
                $author = $users->random();
                $createdAt = Carbon::now()->subDays(rand(1, 30));

                $postCount++;
                $title = "Post de prueba {$postCount} - {$topic->name}";

                $posts[] = [
                    'topic_id' => $topic->id,
                    'user_id' => $author->id,
                    'title' => $title,
                    'slug' => Str::slug($title) . '-' . Str::random(5),
                    'body' => "Este es un post de prueba para el tema {$topic->name}.",
                    'excerpt' => "Post de prueba para {$topic->name}",
                    'post_type' => 'discussion',
                    'status' => 'published',
                    'language' => 'es',
                    'views_count' => rand(10, 100),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt->copy()->addDays(rand(0, 10)),
                ];

                $this->command->info("   ✅ Post {$postCount} generado para tema: {$topic->name}");
            }
        }

        TopicPost::insert($posts); // Inserta todos de golpe (más eficiente)
        $this->command->info("🎉 TopicPostSeeder completado. Total de posts: {$postCount}");
    }
}

