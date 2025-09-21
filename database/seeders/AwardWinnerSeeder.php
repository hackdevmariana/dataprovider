<?php

namespace Database\Seeders;

use App\Models\AwardWinner;
use App\Models\Person;
use App\Models\Award;
use App\Models\Work;
use App\Models\Municipality;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AwardWinnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener datos existentes
        $persons = Person::limit(50)->get();
        $awards = Award::all();
        $works = Work::limit(20)->get();
        $municipalities = Municipality::limit(10)->get();
        
        if ($persons->isEmpty()) {
            $this->command->warn('No hay personas disponibles. Creando personas de ejemplo...');
            $persons = Person::factory()->count(20)->create();
        }
        
        if ($awards->isEmpty()) {
            $this->command->warn('No hay premios disponibles. Creando premios de ejemplo...');
            $awards = Award::factory()->count(10)->create();
        }

        $classifications = ['winner', 'finalist', 'other'];
        $years = range(1990, 2024);

        // Crear ganadores de premios específicos y realistas
        $awardWinners = [];

        foreach ($awards as $award) {
            $numWinners = rand(2, 8); // Entre 2 y 8 ganadores por premio
            $selectedPersons = $persons->random(min($numWinners, $persons->count()));
            
            foreach ($selectedPersons as $index => $person) {
                $classification = $index === 0 ? 'winner' : fake()->randomElement($classifications);
                $year = fake()->randomElement($years);
                
                $awardWinners[] = [
                    'person_id' => $person->id,
                    'award_id' => $award->id,
                    'year' => $year,
                    'classification' => $classification,
                    'work_id' => $works->isNotEmpty() ? fake()->optional(0.6)->randomElement($works->pluck('id')->toArray()) : null,
                    'municipality_id' => $municipalities->isNotEmpty() ? fake()->optional(0.4)->randomElement($municipalities->pluck('id')->toArray()) : null,
                ];
            }
        }

        // Insertar los ganadores de premios
        foreach ($awardWinners as $winner) {
            AwardWinner::updateOrCreate(
                [
                    'person_id' => $winner['person_id'],
                    'award_id' => $winner['award_id'],
                    'year' => $winner['year']
                ], // Evitar duplicados
                $winner
            );
        }

        $this->command->info('Ganadores de premios creados exitosamente.');
    }
}