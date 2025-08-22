<?php

namespace Database\Seeders;

use App\Models\Person;
use App\Models\PersonProfession;
use App\Models\Profession;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PersonProfessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener personas y profesiones disponibles
        $people = Person::all();
        $professions = Profession::all();
        
        if ($people->isEmpty()) {
            $this->command->warn('No hay personas en la base de datos. No se pueden crear relaciones persona-profesión.');
            return;
        }
        
        if ($professions->isEmpty()) {
            $this->command->warn('No hay profesiones en la base de datos. No se pueden crear relaciones persona-profesión.');
            return;
        }

        $createdRelations = [];
        $relationCount = 0;
        $currentYear = Carbon::now()->year;

        // Crear relaciones aleatorias para las personas
        foreach ($people as $person) {
            // Asignar entre 1 y 3 profesiones por persona
            $professionCount = rand(1, 3);
            $usedProfessions = [];
            
            for ($i = 0; $i < $professionCount; $i++) {
                // Seleccionar una profesión que no haya sido usada para esta persona
                $availableProfessions = $professions->whereNotIn('id', $usedProfessions);
                
                if ($availableProfessions->isEmpty()) {
                    break; // No hay más profesiones disponibles
                }
                
                $profession = $availableProfessions->random();
                $usedProfessions[] = $profession->id;
                
                // Generar datos realistas
                $startYear = rand(1970, $currentYear - 5);
                $endYear = rand(1, 10) <= 7 ? null : rand($startYear + 1, $currentYear); // 70% siguen activos
                $isPrimary = ($i === 0); // La primera profesión es la principal
                $isCurrent = ($endYear === null);
                
                $relation = PersonProfession::create([
                    'person_id' => $person->id,
                    'profession_id' => $profession->id,
                    'start_year' => $startYear,
                    'end_year' => $endYear,
                    'is_primary' => $isPrimary,
                    'is_current' => $isCurrent,
                    'notes' => $this->generateRandomNote($profession->name),
                ]);
                
                $createdRelations[] = [
                    'id' => $relation->id,
                    'person' => $person->name ?? 'Persona #' . $person->id,
                    'profession' => $profession->name,
                    'period' => $this->formatPeriod($startYear, $endYear),
                    'primary' => $isPrimary ? '⭐ Principal' : '• Secundaria',
                    'current' => $isCurrent ? '✅ Actual' : '❌ Pasada',
                ];
                
                $relationCount++;
            }
        }

        $this->command->info("Se han creado {$relationCount} relaciones persona-profesión.");
        
        // Mostrar tabla con las relaciones creadas
        $displayData = array_slice($createdRelations, 0, 20); // Mostrar solo las primeras 20
        $this->command->table(
            ['ID', 'Persona', 'Profesión', 'Período', 'Tipo', 'Estado'],
            $displayData
        );
        
        if (count($createdRelations) > 20) {
            $this->command->info("... y " . (count($createdRelations) - 20) . " relaciones más.");
        }

        // Estadísticas
        $totalRelations = PersonProfession::count();
        $currentRelations = PersonProfession::where('is_current', true)->count();
        $pastRelations = PersonProfession::where('is_current', false)->count();
        $primaryRelations = PersonProfession::where('is_primary', true)->count();
        
        // Estadísticas por profesión
        $professionStats = PersonProfession::selectRaw('professions.name, COUNT(*) as count')
            ->join('professions', 'person_profession.profession_id', '=', 'professions.id')
            ->groupBy('professions.id', 'professions.name')
            ->pluck('count', 'name')
            ->toArray();
        
        // Personas con múltiples profesiones
        $multipleProfessions = PersonProfession::selectRaw('person_id, COUNT(*) as profession_count')
            ->groupBy('person_id')
            ->having('profession_count', '>', 1)
            ->count();
        
        $this->command->newLine();
        $this->command->info("📊 Estadísticas:");
        $this->command->info("   • Total de relaciones: {$totalRelations}");
        $this->command->info("   • Profesiones actuales: {$currentRelations}");
        $this->command->info("   • Profesiones pasadas: {$pastRelations}");
        $this->command->info("   • Profesiones principales: {$primaryRelations}");
        $this->command->info("   • Personas con múltiples profesiones: {$multipleProfessions}");
        
        $this->command->newLine();
        $this->command->info("🎭 Por profesión:");
        foreach ($professionStats as $professionName => $count) {
            $this->command->info("   • {$professionName}: {$count} personas");
        }
        
        $this->command->newLine();
        $this->command->info("✅ Seeder de PersonProfession completado exitosamente.");
    }

    /**
     * Formatear el período de tiempo de una profesión
     */
    private function formatPeriod(?int $startYear, ?int $endYear): string
    {
        if ($startYear && $endYear) {
            return "{$startYear} - {$endYear}";
        } elseif ($startYear && !$endYear) {
            return "{$startYear} - Presente";
        } elseif (!$startYear && $endYear) {
            return "? - {$endYear}";
        } else {
            return "Sin fechas";
        }
    }

    /**
     * Generar una nota aleatoria para la profesión
     */
    private function generateRandomNote(string $professionName): string
    {
        $notes = [
            'Actor' => [
                'Especializado en teatro clásico',
                'Actor de reparto en producciones internacionales',
                'Formado en interpretación método',
                'Actor de doblaje profesional',
            ],
            'Escritor' => [
                'Autor de novelas de ficción',
                'Periodista especializado en cultura',
                'Escritor de guiones cinematográficos',
                'Poeta y ensayista',
            ],
            'Director de cine' => [
                'Director de documentales',
                'Especialista en cine independiente',
                'Director de cortometrajes premiados',
                'Realizador de videos musicales',
            ],
            'Cantante' => [
                'Intérprete de música popular',
                'Cantante de ópera profesional',
                'Artista de música tradicional',
                'Vocalista de banda de rock',
            ],
            'Artista visual' => [
                'Pintor contemporáneo',
                'Escultor de arte público',
                'Artista digital y multimedia',
                'Ilustrador profesional',
            ],
        ];
        
        if (isset($notes[$professionName])) {
            return $notes[$professionName][array_rand($notes[$professionName])];
        }
        
        return 'Profesional en su área de especialización';
    }
}
