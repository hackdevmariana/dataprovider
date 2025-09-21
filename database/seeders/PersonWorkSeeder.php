<?php

namespace Database\Seeders;

use App\Models\PersonWork;
use App\Models\Person;
use App\Models\Work;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PersonWorkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creando relaciones Person-Work...');

        // Obtener personas y obras existentes
        $people = Person::all();
        $works = Work::all();

        if ($people->isEmpty() || $works->isEmpty()) {
            $this->command->warn('No hay personas o obras en la base de datos. Ejecuta primero PersonSeeder y WorkSeeder.');
            return;
        }

        $createdCount = 0;

        // Crear relaciones realistas entre personas y obras
        $personWorkData = $this->getPersonWorkData();

        foreach ($personWorkData as $data) {
            // Buscar persona por nombre o slug
            $person = $people->firstWhere('name', $data['person_name']) 
                ?? $people->firstWhere('slug', \Str::slug($data['person_name']));

            if (!$person) {
                $this->command->warn("Persona no encontrada: {$data['person_name']}");
                continue;
            }

            // Buscar obra por título o slug
            $work = $works->firstWhere('title', $data['work_title'])
                ?? $works->firstWhere('slug', \Str::slug($data['work_title']));

            if (!$work) {
                $this->command->warn("Obra no encontrada: {$data['work_title']}");
                continue;
            }

            // Crear la relación
            PersonWork::updateOrCreate(
                [
                    'person_id' => $person->id,
                    'work_id' => $work->id,
                    'role' => $data['role'],
                ],
                [
                    'character_name' => $data['character_name'] ?? null,
                    'credited_as' => $data['credited_as'] ?? null,
                    'billing_order' => $data['billing_order'] ?? null,
                    'contribution_pct' => $data['contribution_pct'] ?? null,
                    'is_primary' => $data['is_primary'] ?? false,
                    'notes' => $data['notes'] ?? null,
                ]
            );

            $createdCount++;
        }

        // Crear algunas relaciones aleatorias adicionales
        $this->createRandomRelations($people, $works, 20);

        $this->command->info("✅ Creadas {$createdCount} relaciones Person-Work específicas");
        $this->command->info("✅ Creadas 20 relaciones Person-Work aleatorias");
        $this->showStatistics();
    }

    /**
     * Datos específicos de relaciones Person-Work famosas.
     */
    private function getPersonWorkData(): array
    {
        return [
            // Relaciones de Pablo Picasso
            [
                'person_name' => 'Pablo Picasso',
                'work_title' => 'Guernica',
                'role' => 'pintor',
                'is_primary' => true,
                'contribution_pct' => 100.00,
                'notes' => 'Obra maestra del cubismo',
            ],
            [
                'person_name' => 'Pablo Picasso',
                'work_title' => 'Las señoritas de Avignon',
                'role' => 'pintor',
                'is_primary' => true,
                'contribution_pct' => 100.00,
                'notes' => 'Considerada la primera obra cubista',
            ],

            // Relaciones de Salvador Dalí
            [
                'person_name' => 'Salvador Dalí',
                'work_title' => 'La persistencia de la memoria',
                'role' => 'pintor',
                'is_primary' => true,
                'contribution_pct' => 100.00,
                'notes' => 'Una de las obras más famosas del surrealismo',
            ],

            // Relaciones de Paco de Lucía
            [
                'person_name' => 'Paco de Lucía',
                'work_title' => 'Entre dos aguas',
                'role' => 'compositor',
                'is_primary' => true,
                'contribution_pct' => 100.00,
                'notes' => 'Rumba flamenca más famosa del guitarrista',
            ],
            [
                'person_name' => 'Paco de Lucía',
                'work_title' => 'Almoraima',
                'role' => 'compositor',
                'is_primary' => true,
                'contribution_pct' => 100.00,
                'notes' => 'Álbum que revolucionó el flamenco',
            ],

            // Relaciones de Pedro Almodóvar
            [
                'person_name' => 'Pedro Almodóvar',
                'work_title' => 'Todo sobre mi madre',
                'role' => 'director',
                'is_primary' => true,
                'contribution_pct' => 100.00,
                'notes' => 'Ganadora del Óscar a mejor película extranjera',
            ],
            [
                'person_name' => 'Pedro Almodóvar',
                'work_title' => 'Volver',
                'role' => 'director',
                'is_primary' => true,
                'contribution_pct' => 100.00,
                'notes' => 'Película protagonizada por Penélope Cruz',
            ],

            // Relaciones de Penélope Cruz
            [
                'person_name' => 'Penélope Cruz',
                'work_title' => 'Volver',
                'role' => 'actriz',
                'character_name' => 'Raimunda',
                'billing_order' => 1,
                'is_primary' => true,
                'contribution_pct' => 100.00,
                'notes' => 'Ganadora del premio de interpretación en Cannes',
            ],
            [
                'person_name' => 'Penélope Cruz',
                'work_title' => 'Todo sobre mi madre',
                'role' => 'actriz',
                'character_name' => 'Hermana Rosa',
                'billing_order' => 2,
                'is_primary' => false,
                'contribution_pct' => 30.00,
                'notes' => 'Papel secundario pero memorable',
            ],

            // Relaciones de Javier Bardem
            [
                'person_name' => 'Javier Bardem',
                'work_title' => 'No es país para viejos',
                'role' => 'actor',
                'character_name' => 'Anton Chigurh',
                'billing_order' => 1,
                'is_primary' => true,
                'contribution_pct' => 100.00,
                'notes' => 'Ganador del Óscar al mejor actor de reparto',
            ],

            // Relaciones de Miguel de Cervantes
            [
                'person_name' => 'Miguel de Cervantes',
                'work_title' => 'Don Quijote de la Mancha',
                'role' => 'escritor',
                'is_primary' => true,
                'contribution_pct' => 100.00,
                'notes' => 'Considerada la primera novela moderna',
            ],

            // Relaciones de Federico García Lorca
            [
                'person_name' => 'Federico García Lorca',
                'work_title' => 'La casa de Bernarda Alba',
                'role' => 'dramaturgo',
                'is_primary' => true,
                'contribution_pct' => 100.00,
                'notes' => 'Una de las obras más importantes del teatro español',
            ],
            [
                'person_name' => 'Federico García Lorca',
                'work_title' => 'Romancero gitano',
                'role' => 'poeta',
                'is_primary' => true,
                'contribution_pct' => 100.00,
                'notes' => 'Colección de poemas más famosa del autor',
            ],

            // Relaciones de Santiago Ramón y Cajal
            [
                'person_name' => 'Santiago Ramón y Cajal',
                'work_title' => 'Textura del sistema nervioso del hombre y de los vertebrados',
                'role' => 'científico',
                'is_primary' => true,
                'contribution_pct' => 100.00,
                'notes' => 'Obra fundamental en neurociencia, Premio Nobel 1906',
            ],
        ];
    }

    /**
     * Crear relaciones aleatorias entre personas y obras.
     */
    private function createRandomRelations($people, $works, $count): void
    {
        $roles = ['actor', 'director', 'escritor', 'compositor', 'pintor', 'productor', 'guionista', 'músico'];
        $characterNames = ['Protagonista', 'Antagonista', 'Narrador', 'Cameo', 'Extra', 'Figurante'];

        for ($i = 0; $i < $count; $i++) {
            $person = $people->random();
            $work = $works->random();
            $role = $roles[array_rand($roles)];

            PersonWork::updateOrCreate(
                [
                    'person_id' => $person->id,
                    'work_id' => $work->id,
                    'role' => $role,
                ],
                [
                    'character_name' => in_array($role, ['actor', 'actriz']) ? $characterNames[array_rand($characterNames)] : null,
                    'credited_as' => $person->name,
                    'billing_order' => rand(1, 10),
                    'contribution_pct' => rand(10, 100),
                    'is_primary' => rand(0, 1) == 1,
                    'notes' => 'Relación generada aleatoriamente',
                ]
            );
        }
    }

    /**
     * Mostrar estadísticas de las relaciones creadas.
     */
    private function showStatistics(): void
    {
        $stats = [
            'Total relaciones' => PersonWork::count(),
            'Actores' => PersonWork::where('role', 'actor')->count(),
            'Directores' => PersonWork::where('role', 'director')->count(),
            'Escritores' => PersonWork::where('role', 'escritor')->count(),
            'Roles principales' => PersonWork::where('is_primary', true)->count(),
            'Con personaje' => PersonWork::whereNotNull('character_name')->count(),
            'Con porcentaje de contribución' => PersonWork::whereNotNull('contribution_pct')->count(),
        ];

        $this->command->info("\n📊 Estadísticas de PersonWork:");
        foreach ($stats as $type => $count) {
            $this->command->info("   {$type}: {$count}");
        }

        // Mostrar roles más comunes
        $roles = PersonWork::selectRaw('role, COUNT(*) as count')
            ->groupBy('role')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        if ($roles->isNotEmpty()) {
            $this->command->info("\n🎭 Roles más comunes:");
            foreach ($roles as $role) {
                $this->command->info("   {$role->role}: {$role->count}");
            }
        }
    }
}