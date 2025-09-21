<?php

namespace Database\Seeders;

use App\Models\Timeline;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TimelineSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creando líneas de tiempo...');

        // Verificar que existen usuarios
        $users = User::limit(5)->get();
        if ($users->isEmpty()) {
            $this->command->warn('No hay usuarios disponibles. Creando usuarios de prueba...');
            for ($i = 1; $i <= 3; $i++) {
                User::create([
                    'name' => 'Usuario de Prueba ' . $i,
                    'email' => 'usuario' . $i . '@example.com',
                    'password' => bcrypt('password'),
                ]);
            }
            $users = User::limit(5)->get();
        }

        $timelines = [
            [
                'title' => 'Historia de España',
                'description' => 'Línea de tiempo que abarca los principales eventos históricos de España.',
                'theme' => 'historical',
                'start_date' => '711-01-01',
                'end_date' => '2023-12-31',
                'events' => json_encode(['Reconquista', 'Descubrimiento de América', 'Guerra Civil']),
                'view_type' => 'chronological',
                'categories' => json_encode(['Historia', 'España']),
                'is_public' => true,
                'created_by' => $users->random()->id,
            ],
            [
                'title' => 'Evolución de la Tecnología',
                'description' => 'Cronología de los avances tecnológicos más importantes.',
                'theme' => 'technological',
                'start_date' => '1440-01-01',
                'end_date' => '2023-12-31',
                'events' => json_encode(['Imprenta', 'Máquina de vapor', 'Internet']),
                'view_type' => 'timeline',
                'categories' => json_encode(['Tecnología', 'Innovación']),
                'is_public' => true,
                'created_by' => $users->random()->id,
            ],
            [
                'title' => 'Arte Renacentista',
                'description' => 'Línea de tiempo del arte renacentista en Italia.',
                'theme' => 'artistic',
                'start_date' => '1400-01-01',
                'end_date' => '1600-12-31',
                'events' => json_encode(['Leonardo da Vinci', 'Miguel Ángel', 'Rafael']),
                'view_type' => 'chronological',
                'categories' => json_encode(['Arte', 'Renacimiento']),
                'is_public' => true,
                'created_by' => $users->random()->id,
            ],
            [
                'title' => 'Mi Vida Personal',
                'description' => 'Línea de tiempo personal con los momentos más importantes.',
                'theme' => 'personal',
                'start_date' => '1990-01-01',
                'end_date' => '2023-12-31',
                'events' => json_encode(['Nacimiento', 'Graduación', 'Primer trabajo']),
                'view_type' => 'personal',
                'categories' => json_encode(['Personal', 'Familia']),
                'is_public' => false,
                'created_by' => $users->random()->id,
            ],
            [
                'title' => 'Guerras Mundiales',
                'description' => 'Cronología de las dos guerras mundiales.',
                'theme' => 'military',
                'start_date' => '1914-07-28',
                'end_date' => '1945-09-02',
                'events' => json_encode(['Primera Guerra Mundial', 'Segunda Guerra Mundial']),
                'view_type' => 'chronological',
                'categories' => json_encode(['Guerra', 'Historia']),
                'is_public' => true,
                'created_by' => $users->random()->id,
            ],
        ];

        $count = 0;
        foreach ($timelines as $timelineData) {
            Timeline::create($timelineData);
            $count++;
        }

        // Crear líneas de tiempo adicionales aleatorias
        $themes = ['historical', 'personal', 'cultural', 'scientific', 'artistic', 'political', 'social', 'technological', 'religious', 'military', 'economic', 'environmental'];
        $viewTypes = ['chronological', 'timeline', 'personal', 'categorical'];

        for ($i = 0; $i < 10; $i++) {
            $startDate = fake()->dateTimeBetween('-10 years', 'now');
            $endDate = fake()->dateTimeBetween($startDate, '+5 years');
            
            Timeline::create([
                'title' => fake()->sentence(3),
                'description' => fake()->paragraph(2),
                'theme' => fake()->randomElement($themes),
                'start_date' => $startDate,
                'end_date' => $endDate,
                'events' => json_encode(fake()->randomElements(['Evento 1', 'Evento 2', 'Evento 3'], rand(1, 3))),
                'view_type' => fake()->randomElement($viewTypes),
                'categories' => json_encode(fake()->randomElements(['Categoría 1', 'Categoría 2'], rand(1, 2))),
                'is_public' => fake()->boolean(70),
                'created_by' => fake()->randomElement($users->pluck('id')->toArray()),
            ]);
            $count++;
        }

        $this->command->info("✅ Creadas {$count} líneas de tiempo");
        $this->showStatistics();
    }

    private function showStatistics(): void
    {
        $total = Timeline::count();
        $public = Timeline::where('is_public', true)->count();
        
        $this->command->info("\n📊 Estadísticas:");
        $this->command->info("   Total líneas de tiempo: {$total}");
        $this->command->info("   Públicas: {$public}");
        
        $themes = Timeline::selectRaw('theme, COUNT(*) as count')->groupBy('theme')->get();
        $this->command->info("\n🎨 Por tema:");
        foreach ($themes as $theme) {
            $this->command->info("   {$theme->theme}: {$theme->count}");
        }
        
        $viewTypes = Timeline::selectRaw('view_type, COUNT(*) as count')->groupBy('view_type')->get();
        $this->command->info("\n👁️ Por tipo de vista:");
        foreach ($viewTypes as $viewType) {
            $this->command->info("   {$viewType->view_type}: {$viewType->count}");
        }
    }
}