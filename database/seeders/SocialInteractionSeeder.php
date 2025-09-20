<?php

namespace Database\Seeders;

use App\Models\SocialInteraction;
use App\Models\User;
use App\Models\TopicPost;
use App\Models\TopicComment;
use App\Models\UserReview;
use App\Models\EnergyInstallation;
use App\Models\Cooperative;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SocialInteractionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener usuarios existentes
        $users = User::take(30)->get();
        
        if ($users->isEmpty()) {
            $this->command->warn('No hay usuarios disponibles para crear interacciones. Ejecuta UserSeeder primero.');
            return;
        }

        // Obtener entidades interactuables
        $topicPosts = TopicPost::take(20)->get();
        $topicComments = TopicComment::take(20)->get();
        $userReviews = UserReview::take(20)->get();
        $energyInstallations = EnergyInstallation::take(20)->get();
        $cooperatives = Cooperative::take(20)->get();

        $interactableEntities = collect()
            ->merge($topicPosts->map(fn($post) => ['type' => TopicPost::class, 'id' => $post->id]))
            ->merge($topicComments->map(fn($comment) => ['type' => TopicComment::class, 'id' => $comment->id]))
            ->merge($userReviews->map(fn($review) => ['type' => UserReview::class, 'id' => $review->id]))
            ->merge($energyInstallations->map(fn($installation) => ['type' => EnergyInstallation::class, 'id' => $installation->id]))
            ->merge($cooperatives->map(fn($coop) => ['type' => Cooperative::class, 'id' => $coop->id]));

        if ($interactableEntities->isEmpty()) {
            $this->command->warn('No hay entidades disponibles para interactuar. Crea algunas entidades primero.');
            return;
        }

        $interactionTypes = [
            'like', 'love', 'wow', 'celebrate', 'support',
            'share', 'bookmark', 'follow', 'subscribe',
            'report', 'hide', 'block'
        ];

        $sources = ['web', 'mobile_app', 'api', 'admin_panel'];
        $deviceTypes = ['desktop', 'mobile', 'tablet', 'unknown'];
        $statuses = ['active', 'withdrawn', 'expired'];

        $this->command->info('Creando interacciones sociales...');

        for ($i = 0; $i < 100; $i++) {
            $user = $users->random();
            $entity = $interactableEntities->random();
            $interactionType = fake()->randomElement($interactionTypes);
            
            // Generar datos de interacción
            $interactionData = [];
            
            if ($interactionType === 'share') {
                $interactionData = [
                    'platform' => fake()->randomElement(['facebook', 'twitter', 'linkedin', 'whatsapp']),
                    'message' => fake()->sentence(),
                    'shared_at' => now()->toISOString()
                ];
            } elseif ($interactionType === 'bookmark') {
                $interactionData = [
                    'folder' => fake()->randomElement(['favorites', 'read_later', 'important']),
                    'tags' => fake()->words(3),
                    'notes' => fake()->boolean(30) ? fake()->sentence() : null
                ];
            } elseif (in_array($interactionType, ['report', 'hide', 'block'])) {
                $interactionData = [
                    'reason' => fake()->randomElement([
                        'spam', 'inappropriate_content', 'harassment', 
                        'false_information', 'copyright_violation'
                    ]),
                    'details' => fake()->boolean(50) ? fake()->sentence() : null
                ];
            }

            $interaction = SocialInteraction::create([
                'user_id' => $user->id,
                'interactable_type' => $entity['type'],
                'interactable_id' => $entity['id'],
                'interaction_type' => $interactionType,
                'interaction_note' => fake()->boolean(20) ? fake()->sentence() : null,
                'interaction_data' => !empty($interactionData) ? $interactionData : null,
                'source' => fake()->randomElement($sources),
                'device_type' => fake()->randomElement($deviceTypes),
                'latitude' => fake()->boolean(30) ? fake()->latitude() : null,
                'longitude' => fake()->boolean(30) ? fake()->longitude() : null,
                'is_public' => fake()->boolean(80),
                'notify_author' => fake()->boolean(70),
                'show_in_activity' => fake()->boolean(85),
                'engagement_weight' => fake()->numberBetween(1, 10),
                'quality_score' => fake()->randomFloat(2, 0, 100),
                'interaction_expires_at' => fake()->boolean(10) ? fake()->dateTimeBetween('now', '+1 month') : null,
                'is_temporary' => fake()->boolean(5),
                'status' => fake()->randomElement($statuses),
            ]);

            // Simular algunas interacciones como expiradas
            if ($interaction->interaction_expires_at && $interaction->interaction_expires_at->isPast()) {
                $interaction->update(['status' => 'expired']);
            }
        }

        $this->command->info('✅ SocialInteractionSeeder completado: 100 interacciones creadas');
    }
}