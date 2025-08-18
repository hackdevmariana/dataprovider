<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MediaContact;
use App\Models\MediaOutlet;

class MediaContactSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creando contactos de medios de comunicación...');

        $mediaOutlets = MediaOutlet::all();
        if ($mediaOutlets->isEmpty()) {
            $this->command->error('No se encontraron medios de comunicación. Ejecuta primero MediaOutletSeeder.');
            return;
        }

        $totalContacts = 0;

        foreach ($mediaOutlets as $mediaOutlet) {
            $contactsCount = $this->getContactsCountForMedia($mediaOutlet);
            
            // Contactos principales
            $mainContacts = MediaContact::factory()
                ->count($contactsCount['main'])
                ->verified()
                ->highPriority()
                ->create(['media_outlet_id' => $mediaOutlet->id]);

            // Contactos especializados en sostenibilidad
            if ($mediaOutlet->covers_sustainability) {
                $sustainabilityContacts = MediaContact::factory()
                    ->count($contactsCount['sustainability'])
                    ->sustainabilityFocused()
                    ->verified()
                    ->create(['media_outlet_id' => $mediaOutlet->id]);
                
                $totalContacts += $sustainabilityContacts->count();
            }

            // Contactos adicionales
            $additionalContacts = MediaContact::factory()
                ->count($contactsCount['additional'])
                ->create(['media_outlet_id' => $mediaOutlet->id]);

            $totalContacts += $mainContacts->count() + $additionalContacts->count();
        }

        $this->command->info("✅ Creados {$totalContacts} contactos para {$mediaOutlets->count()} medios");

        // Contactos freelance asignados a medios aleatorios
        $freelanceContacts = MediaContact::factory()
            ->count(25)
            ->freelancer()
            ->create([
                'media_outlet_id' => $mediaOutlets->random()->id,
                'is_freelancer' => true,
                'department' => 'Freelance'
            ]);

        $totalContacts += $freelanceContacts->count();
        $this->command->info("✅ Creados {$freelanceContacts->count()} contactos freelance");

        $this->command->info("🎉 Total de contactos de medios creados: {$totalContacts}");
        $this->showStatistics();
    }

    private function getContactsCountForMedia(MediaOutlet $mediaOutlet): array
    {
        $isLargeMedia = $mediaOutlet->monthly_pageviews > 10000000 || 
                       $mediaOutlet->social_media_followers > 1000000;
        
        $isVerified = $mediaOutlet->is_verified;
        $coversSustainability = $mediaOutlet->covers_sustainability;

        if ($isLargeMedia) {
            return [
                'main' => fake()->numberBetween(4, 8),
                'sustainability' => $coversSustainability ? fake()->numberBetween(2, 4) : 0,
                'additional' => fake()->numberBetween(3, 6),
            ];
        } elseif ($isVerified) {
            return [
                'main' => fake()->numberBetween(2, 5),
                'sustainability' => $coversSustainability ? fake()->numberBetween(1, 3) : 0,
                'additional' => fake()->numberBetween(2, 4),
            ];
        } else {
            return [
                'main' => fake()->numberBetween(1, 3),
                'sustainability' => $coversSustainability ? fake()->numberBetween(1, 2) : 0,
                'additional' => fake()->numberBetween(1, 2),
            ];
        }
    }

    private function showStatistics(): void
    {
        $stats = [
            'Total contactos' => MediaContact::count(),
            'Verificados' => MediaContact::where('is_verified', true)->count(),
            'Alta prioridad' => MediaContact::where('priority_level', '>=', 3)->count(),
            'Freelancers' => MediaContact::where('is_freelancer', true)->count(),
        ];

        $this->command->info("\n📊 Estadísticas de contactos creados:");
        foreach ($stats as $type => $count) {
            $this->command->info("   {$type}: {$count}");
        }
    }
}