<?php

namespace Database\Seeders;

use App\Models\MediaContact;
use App\Models\MediaOutlet;
use Illuminate\Database\Seeder;

class MediaContactSeeder extends Seeder
{
    public function run(): void
    {
        $mediaOutlets = MediaOutlet::all();
        
        if ($mediaOutlets->isEmpty()) {
            $this->command->warn('No hay medios de comunicación disponibles.');
            return;
        }

        $this->createContacts($mediaOutlets);
    }

    private function createContacts($mediaOutlets): void
    {
        $types = ['editor', 'redactor', 'prensa', 'comunicacion', 'corresponsal', 'freelancer'];
        
        foreach ($mediaOutlets->take(5) as $outlet) {
            foreach ($types as $type) {
                MediaContact::create([
                    'media_outlet_id' => $outlet->id,
                    'type' => $type,
                    'contact_name' => fake()->name(),
                    'job_title' => $this->getJobTitle($type),
                    'department' => fake()->randomElement(['Redacción', 'Prensa', 'Comunicación']),
                    'phone' => fake()->optional(0.8)->phoneNumber(),
                    'mobile_phone' => fake()->optional(0.9)->phoneNumber(),
                    'email' => fake()->unique()->safeEmail(),
                    'secondary_email' => fake()->optional(0.3)->safeEmail(),
                    'specializations' => $this->getSpecializations($type),
                    'coverage_areas' => ['Madrid', 'Barcelona', 'Valencia'],
                    'preferred_contact_method' => fake()->randomElement(['email', 'phone', 'mobile_phone']),
                    'availability_schedule' => $this->getSchedule(),
                    'language_preference' => 'es',
                    'accepts_press_releases' => fake()->boolean(80),
                    'accepts_interviews' => fake()->boolean(70),
                    'accepts_events_invitations' => fake()->boolean(60),
                    'is_freelancer' => $type === 'freelancer',
                    'is_active' => true,
                    'is_verified' => fake()->boolean(75),
                    'priority_level' => fake()->numberBetween(2, 5),
                    'response_rate' => fake()->randomFloat(2, 0.3, 0.9),
                    'contacts_count' => fake()->numberBetween(5, 50),
                    'successful_contacts' => fake()->numberBetween(2, 40),
                    'social_media_profiles' => ['twitter' => fake()->userName()],
                    'bio' => fake()->optional(0.6)->paragraph(),
                    'recent_articles' => $this->getArticles(),
                    'notes' => fake()->optional(0.4)->sentence(),
                    'interaction_history' => $this->getHistory(),
                    'last_contacted_at' => fake()->optional(0.7)->dateTimeBetween('-30 days', 'now'),
                    'last_response_at' => fake()->optional(0.6)->dateTimeBetween('-20 days', 'now'),
                    'verified_at' => fake()->optional(0.8)->dateTimeBetween('-1 year', 'now'),
                ]);
            }
        }
    }

    private function getJobTitle($type): string
    {
        return match($type) {
            'editor' => 'Editor',
            'redactor' => 'Redactor',
            'prensa' => 'Responsable de Prensa',
            'comunicacion' => 'Responsable de Comunicación',
            'corresponsal' => 'Corresponsal',
            'freelancer' => 'Periodista Freelance',
            default => 'Contacto'
        };
    }

    private function getSpecializations($type): array
    {
        return match($type) {
            'editor' => ['politica', 'economia'],
            'redactor' => ['tecnologia', 'cultura'],
            'prensa' => ['comunicacion', 'eventos'],
            'comunicacion' => ['marketing', 'eventos'],
            'corresponsal' => ['politica', 'internacional'],
            'freelancer' => ['tecnologia', 'medio_ambiente'],
            default => ['general']
        };
    }

    private function getSchedule(): array
    {
        return [
            'monday' => ['start' => '09:00', 'end' => '18:00'],
            'tuesday' => ['start' => '09:00', 'end' => '18:00'],
            'wednesday' => ['start' => '09:00', 'end' => '18:00'],
            'thursday' => ['start' => '09:00', 'end' => '18:00'],
            'friday' => ['start' => '09:00', 'end' => '17:00'],
            'saturday' => 'off',
            'sunday' => 'off',
        ];
    }

    private function getArticles(): array
    {
        return [
            [
                'title' => fake()->sentence(),
                'url' => fake()->url(),
                'published_at' => fake()->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
            ]
        ];
    }

    private function getHistory(): array
    {
        return [
            [
                'type' => 'email',
                'description' => fake()->sentence(),
                'successful' => fake()->boolean(70),
                'timestamp' => fake()->dateTimeBetween('-30 days', 'now')->format('Y-m-d H:i:s'),
            ]
        ];
    }
}