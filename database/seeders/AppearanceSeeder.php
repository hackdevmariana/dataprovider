<?php

namespace Database\Seeders;

use App\Models\Appearance;
use App\Models\Person;
use Illuminate\Database\Seeder;

class AppearanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener todas las personas para asignar appearances
        $people = Person::all();
        
        if ($people->isEmpty()) {
            $this->command->warn('No hay personas en la base de datos. No se pueden crear appearances.');
            return;
        }

        // Datos de aspecto físico de personas famosas conocidas
        $famousAppearances = [
            // Cantantes y músicos
            [
                'height_cm' => 175,
                'weight_kg' => 70,
                'body_type' => 'mesomorfo',
                'note' => 'Michael Jackson - Rey del Pop'
            ],
            [
                'height_cm' => 164,
                'weight_kg' => 54,
                'body_type' => 'ectomorfo',
                'note' => 'Madonna - Reina del Pop'
            ],
            [
                'height_cm' => 182,
                'weight_kg' => 77,
                'body_type' => 'mesomorfo',
                'note' => 'Elvis Presley - Rey del Rock'
            ],
            [
                'height_cm' => 157,
                'weight_kg' => 50,
                'body_type' => 'ectomorfo',
                'note' => 'Lady Gaga - Artista'
            ],
            [
                'height_cm' => 165,
                'weight_kg' => 57,
                'body_type' => 'mesomorfo',
                'note' => 'Bruno Mars - Cantante'
            ],
            [
                'height_cm' => 169,
                'weight_kg' => 65,
                'body_type' => 'mesomorfo',
                'note' => 'Beyoncé - Cantante'
            ],
            [
                'height_cm' => 188,
                'weight_kg' => 83,
                'body_type' => 'mesomorfo',
                'note' => 'Jay-Z - Rapero'
            ],
            [
                'height_cm' => 173,
                'weight_kg' => 70,
                'body_type' => 'mesomorfo',
                'note' => 'Kanye West - Rapero'
            ],
            [
                'height_cm' => 161,
                'weight_kg' => 52,
                'body_type' => 'ectomorfo',
                'note' => 'Billie Eilish - Cantante'
            ],
            [
                'height_cm' => 155,
                'weight_kg' => 48,
                'body_type' => 'ectomorfo',
                'note' => 'Ariana Grande - Cantante'
            ],

            // Actores y actrices
            [
                'height_cm' => 170,
                'weight_kg' => 78,
                'body_type' => 'mesomorfo',
                'note' => 'Tom Cruise - Actor'
            ],
            [
                'height_cm' => 180,
                'weight_kg' => 75,
                'body_type' => 'mesomorfo',
                'note' => 'Brad Pitt - Actor'
            ],
            [
                'height_cm' => 175,
                'weight_kg' => 55,
                'body_type' => 'ectomorfo',
                'note' => 'Angelina Jolie - Actriz'
            ],
            [
                'height_cm' => 168,
                'weight_kg' => 58,
                'body_type' => 'mesomorfo',
                'note' => 'Jennifer Lawrence - Actriz'
            ],
            [
                'height_cm' => 183,
                'weight_kg' => 88,
                'body_type' => 'mesomorfo',
                'note' => 'Dwayne Johnson - Actor'
            ],

            // Deportistas
            [
                'height_cm' => 198,
                'weight_kg' => 98,
                'body_type' => 'mesomorfo',
                'note' => 'LeBron James - Baloncesto'
            ],
            [
                'height_cm' => 170,
                'weight_kg' => 72,
                'body_type' => 'mesomorfo',
                'note' => 'Lionel Messi - Fútbol'
            ],
            [
                'height_cm' => 187,
                'weight_kg' => 84,
                'body_type' => 'mesomorfo',
                'note' => 'Cristiano Ronaldo - Fútbol'
            ],
            [
                'height_cm' => 175,
                'weight_kg' => 70,
                'body_type' => 'mesomorfo',
                'note' => 'Serena Williams - Tenis'
            ],
            [
                'height_cm' => 188,
                'weight_kg' => 84,
                'body_type' => 'mesomorfo',
                'note' => 'Rafael Nadal - Tenis'
            ],
        ];

        // Tipos de cuerpo disponibles
        $bodyTypes = ['ectomorfo', 'mesomorfo', 'endomorfo'];
        
        $createdAppearances = [];
        $appearanceCount = 0;

        // Crear appearances para personas famosas conocidas
        foreach ($famousAppearances as $index => $appearanceData) {
            if ($index >= $people->count()) {
                break; // No hay más personas disponibles
            }
            
            $person = $people[$index];
            
            // Verificar que la persona no tenga ya un appearance
            $existingAppearance = Appearance::where('person_id', $person->id)->first();
            if ($existingAppearance) {
                continue;
            }
            
            $appearance = Appearance::create([
                'person_id' => $person->id,
                'height_cm' => $appearanceData['height_cm'],
                'weight_kg' => $appearanceData['weight_kg'],
                'body_type' => $appearanceData['body_type'],
            ]);
            
            $createdAppearances[] = [
                'id' => $appearance->id,
                'person' => $person->name ?? 'Persona #' . $person->id,
                'height' => $appearance->height_cm . ' cm',
                'weight' => $appearance->weight_kg . ' kg',
                'body_type' => ucfirst($appearance->body_type),
                'note' => $appearanceData['note'] ?? '',
            ];
            
            $appearanceCount++;
        }

        // Crear appearances aleatorios para las personas restantes
        $remainingPeople = $people->skip(count($famousAppearances));
        
        foreach ($remainingPeople as $person) {
            // Verificar que la persona no tenga ya un appearance
            $existingAppearance = Appearance::where('person_id', $person->id)->first();
            if ($existingAppearance) {
                continue;
            }
            
            // Generar datos aleatorios pero realistas
            $height = rand(150, 200); // Entre 1.50m y 2.00m
            $weight = $this->calculateRealisticWeight($height);
            $bodyType = $bodyTypes[array_rand($bodyTypes)];
            
            $appearance = Appearance::create([
                'person_id' => $person->id,
                'height_cm' => $height,
                'weight_kg' => $weight,
                'body_type' => $bodyType,
            ]);
            
            $createdAppearances[] = [
                'id' => $appearance->id,
                'person' => $person->name ?? 'Persona #' . $person->id,
                'height' => $appearance->height_cm . ' cm',
                'weight' => $appearance->weight_kg . ' kg',
                'body_type' => ucfirst($appearance->body_type),
                'note' => 'Generado aleatoriamente',
            ];
            
            $appearanceCount++;
        }

        $this->command->info("Se han creado {$appearanceCount} perfiles físicos para personas.");
        
        // Mostrar tabla con los appearances creados
        $displayData = array_slice($createdAppearances, 0, 15); // Mostrar solo los primeros 15
        $this->command->table(
            ['ID', 'Persona', 'Altura', 'Peso', 'Tipo Cuerpo', 'Nota'],
            $displayData
        );
        
        if (count($createdAppearances) > 15) {
            $this->command->info("... y " . (count($createdAppearances) - 15) . " perfiles más.");
        }

        // Estadísticas
        $totalAppearances = Appearance::count();
        $averageHeight = Appearance::avg('height_cm');
        $averageWeight = Appearance::avg('weight_kg');
        $bodyTypeStats = Appearance::selectRaw('body_type, COUNT(*) as count')
            ->groupBy('body_type')
            ->pluck('count', 'body_type')
            ->toArray();
        
        $this->command->newLine();
        $this->command->info("📊 Estadísticas:");
        $this->command->info("   • Total de perfiles físicos: {$totalAppearances}");
        $this->command->info("   • Altura promedio: " . round($averageHeight, 1) . " cm");
        $this->command->info("   • Peso promedio: " . round($averageWeight, 1) . " kg");
        $this->command->info("   • Por tipo de cuerpo:");
        foreach ($bodyTypeStats as $type => $count) {
            $typeLabel = match($type) {
                'ectomorfo' => 'Ectomorfo (delgado)',
                'mesomorfo' => 'Mesomorfo (atlético)',
                'endomorfo' => 'Endomorfo (robusto)',
                default => ucfirst($type)
            };
            $this->command->info("     - {$typeLabel}: {$count}");
        }
        
        $this->command->newLine();
        $this->command->info("✅ Seeder de Appearance completado exitosamente.");
    }

    /**
     * Calcular un peso realista basado en la altura
     */
    private function calculateRealisticWeight(int $height): int
    {
        // Usar IMC entre 18.5 y 29.9 (normal a sobrepeso leve)
        $minBMI = 18.5;
        $maxBMI = 29.9;
        
        $heightInMeters = $height / 100;
        $minWeight = $minBMI * ($heightInMeters * $heightInMeters);
        $maxWeight = $maxBMI * ($heightInMeters * $heightInMeters);
        
        return rand((int)$minWeight, (int)$maxWeight);
    }
}