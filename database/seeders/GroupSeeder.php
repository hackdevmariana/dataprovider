<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Group;
use App\Models\Artist;
use App\Models\Person;
use App\Models\Language;
use Carbon\Carbon;

class GroupSeeder extends Seeder
{
    /**
     * Ejecutar el seeder para grupos de música españoles e internacionales.
     */
    public function run(): void
    {
        $this->command->info('Creando grupos de música famosos...');

        // Buscar idioma español
        $spanish = Language::where('language', 'Spanish')->orWhere('iso_639_1', 'es')->first();
        if (!$spanish) {
            $spanish = Language::create([
                'language' => 'Spanish',
                'slug' => 'spanish',
                'native_name' => 'Español',
                'iso_639_1' => 'es',
                'iso_639_2' => 'spa',
                'rtl' => false
            ]);
        }

        // Crear grupos famosos
        $famousGroups = $this->getFamousGroups();
        $createdCount = 0;

        foreach ($famousGroups as $groupData) {
            $group = Group::create([
                'name' => $groupData['name'],
                'slug' => \Str::slug($groupData['name']),
                'description' => $groupData['description'],
            ]);

            // Crear artistas miembros del grupo si no existen
            if (isset($groupData['members'])) {
                foreach ($groupData['members'] as $memberData) {
                    $this->createGroupMember($group, $memberData, $spanish);
                }
            }

            $createdCount++;
        }

        $this->command->info("✅ Creados {$createdCount} grupos de música famosos");

        // Crear grupos adicionales con factory
        $additionalGroups = Group::factory()
            ->count(20)
            ->create();

        $this->command->info("✅ Creados {$additionalGroups->count()} grupos adicionales");

        // Mostrar estadísticas
        $this->showStatistics();
    }

    /**
     * Crear miembro del grupo.
     */
    private function createGroupMember(Group $group, array $memberData, Language $language): void
    {
        // Buscar o crear la persona asociada
        $person = Person::where('name', $memberData['person_name'])->first();
        if (!$person) {
            $person = Person::factory()->create([
                'name' => $memberData['person_name'],
                'slug' => \Str::slug($memberData['person_name']),
                'birth_date' => $memberData['birth_date'] ?? null,
                'notable_for' => $memberData['notable_for'] ?? 'Músico',
                'is_influencer' => true,
            ]);
        }

        // Buscar o crear el artista
        $artist = Artist::where('name', $memberData['name'])->first();
        if (!$artist) {
            $artist = Artist::create([
                'name' => $memberData['name'],
                'slug' => \Str::slug($memberData['name']),
                'stage_name' => $memberData['stage_name'] ?? $memberData['name'],
                'description' => $memberData['description'] ?? 'Miembro del grupo ' . $group->name,
                'birth_date' => $memberData['birth_date'] ?? null,
                'genre' => $memberData['genre'] ?? 'Pop/Rock',
                'person_id' => $person->id,
                'active_years_start' => $memberData['active_years_start'] ?? null,
                'active_years_end' => $memberData['active_years_end'] ?? null,
                'bio' => $memberData['bio'] ?? 'Artista miembro del grupo ' . $group->name,
                'social_links' => $memberData['social_links'] ?? null,
                'language_id' => $language->id,
            ]);
        }

        // Asociar artista al grupo
        $joinedAt = $memberData['joined_at'] ?? Carbon::now()->subYears(rand(1, 10));
        $leftAt = $memberData['left_at'] ?? null;

        $group->artists()->attach($artist->id, [
            'joined_at' => $joinedAt,
            'left_at' => $leftAt,
        ]);
    }

    /**
     * Datos de grupos famosos.
     */
    private function getFamousGroups(): array
    {
        return [
            // Grupos españoles
            [
                'name' => 'Mecano',
                'description' => 'Grupo español de pop-rock formado en 1981, uno de los más exitosos de la música española.',
                'members' => [
                    [
                        'name' => 'Ana Torroja',
                        'person_name' => 'Ana Torroja',
                        'stage_name' => 'Ana Torroja',
                        'birth_date' => '1959-12-28',
                        'genre' => 'Pop',
                        'description' => 'Vocalista principal de Mecano',
                        'bio' => 'Ana Torroja es una cantante española, vocalista del grupo Mecano.',
                        'active_years_start' => 1981,
                        'active_years_end' => 1992,
                        'joined_at' => '1981-01-01',
                        'left_at' => '1992-12-31',
                    ],
                    [
                        'name' => 'José María Cano',
                        'person_name' => 'José María Cano',
                        'stage_name' => 'José María Cano',
                        'birth_date' => '1959-02-21',
                        'genre' => 'Pop',
                        'description' => 'Teclista y compositor de Mecano',
                        'bio' => 'José María Cano es un músico español, teclista y compositor del grupo Mecano.',
                        'active_years_start' => 1981,
                        'active_years_end' => 1992,
                        'joined_at' => '1981-01-01',
                        'left_at' => '1992-12-31',
                    ],
                    [
                        'name' => 'Nacho Cano',
                        'person_name' => 'Nacho Cano',
                        'stage_name' => 'Nacho Cano',
                        'birth_date' => '1963-01-26',
                        'genre' => 'Pop',
                        'description' => 'Bajista y compositor de Mecano',
                        'bio' => 'Nacho Cano es un músico español, bajista y compositor del grupo Mecano.',
                        'active_years_start' => 1981,
                        'active_years_end' => 1992,
                        'joined_at' => '1981-01-01',
                        'left_at' => '1992-12-31',
                    ],
                ],
            ],
            [
                'name' => 'Héroes del Silencio',
                'description' => 'Grupo español de rock alternativo formado en 1984, uno de los más influyentes del rock español.',
                'members' => [
                    [
                        'name' => 'Enrique Bunbury',
                        'person_name' => 'Enrique Ortiz de Landázuri',
                        'stage_name' => 'Enrique Bunbury',
                        'birth_date' => '1967-08-11',
                        'genre' => 'Rock',
                        'description' => 'Vocalista y guitarrista de Héroes del Silencio',
                        'bio' => 'Enrique Bunbury es un músico español, vocalista y guitarrista del grupo Héroes del Silencio.',
                        'active_years_start' => 1984,
                        'active_years_end' => 1996,
                        'joined_at' => '1984-01-01',
                        'left_at' => '1996-12-31',
                    ],
                    [
                        'name' => 'Juan Valdivia',
                        'person_name' => 'Juan Valdivia',
                        'stage_name' => 'Juan Valdivia',
                        'birth_date' => '1967-01-14',
                        'genre' => 'Rock',
                        'description' => 'Guitarrista principal de Héroes del Silencio',
                        'bio' => 'Juan Valdivia es un guitarrista español, miembro fundador de Héroes del Silencio.',
                        'active_years_start' => 1984,
                        'active_years_end' => 1996,
                        'joined_at' => '1984-01-01',
                        'left_at' => '1996-12-31',
                    ],
                    [
                        'name' => 'Joaquín Cardiel',
                        'person_name' => 'Joaquín Cardiel',
                        'stage_name' => 'Joaquín Cardiel',
                        'birth_date' => '1966-01-07',
                        'genre' => 'Rock',
                        'description' => 'Bajista de Héroes del Silencio',
                        'bio' => 'Joaquín Cardiel es un bajista español, miembro de Héroes del Silencio.',
                        'active_years_start' => 1984,
                        'active_years_end' => 1996,
                        'joined_at' => '1984-01-01',
                        'left_at' => '1996-12-31',
                    ],
                    [
                        'name' => 'Pedro Andreu',
                        'person_name' => 'Pedro Andreu',
                        'stage_name' => 'Pedro Andreu',
                        'birth_date' => '1966-01-01',
                        'genre' => 'Rock',
                        'description' => 'Baterista de Héroes del Silencio',
                        'bio' => 'Pedro Andreu es un baterista español, miembro de Héroes del Silencio.',
                        'active_years_start' => 1984,
                        'active_years_end' => 1996,
                        'joined_at' => '1984-01-01',
                        'left_at' => '1996-12-31',
                    ],
                ],
            ],
            [
                'name' => 'La Oreja de Van Gogh',
                'description' => 'Grupo español de pop-rock formado en 1996, conocido por sus melodías pop y letras poéticas.',
                'members' => [
                    [
                        'name' => 'Amaia Montero',
                        'person_name' => 'Amaia Montero',
                        'stage_name' => 'Amaia Montero',
                        'birth_date' => '1976-08-26',
                        'genre' => 'Pop',
                        'description' => 'Vocalista principal de La Oreja de Van Gogh',
                        'bio' => 'Amaia Montero es una cantante española, vocalista del grupo La Oreja de Van Gogh.',
                        'active_years_start' => 1996,
                        'active_years_end' => 2007,
                        'joined_at' => '1996-01-01',
                        'left_at' => '2007-12-31',
                    ],
                    [
                        'name' => 'Pablo Benegas',
                        'person_name' => 'Pablo Benegas',
                        'stage_name' => 'Pablo Benegas',
                        'birth_date' => '1976-01-01',
                        'genre' => 'Pop',
                        'description' => 'Guitarrista de La Oreja de Van Gogh',
                        'bio' => 'Pablo Benegas es un guitarrista español, miembro de La Oreja de Van Gogh.',
                        'active_years_start' => 1996,
                        'active_years_end' => null,
                        'joined_at' => '1996-01-01',
                        'left_at' => null,
                    ],
                    [
                        'name' => 'Xabi San Martín',
                        'person_name' => 'Xabi San Martín',
                        'stage_name' => 'Xabi San Martín',
                        'birth_date' => '1976-01-01',
                        'genre' => 'Pop',
                        'description' => 'Teclista de La Oreja de Van Gogh',
                        'bio' => 'Xabi San Martín es un teclista español, miembro de La Oreja de Van Gogh.',
                        'active_years_start' => 1996,
                        'active_years_end' => null,
                        'joined_at' => '1996-01-01',
                        'left_at' => null,
                    ],
                ],
            ],
            [
                'name' => 'Estopa',
                'description' => 'Duo español de rumba-rock formado en 1999, conocido por su fusión de estilos musicales.',
                'members' => [
                    [
                        'name' => 'José Manuel Muñoz',
                        'person_name' => 'José Manuel Muñoz',
                        'stage_name' => 'José Manuel Muñoz',
                        'birth_date' => '1974-01-01',
                        'genre' => 'Rumba-Rock',
                        'description' => 'Vocalista y guitarrista de Estopa',
                        'bio' => 'José Manuel Muñoz es un músico español, vocalista y guitarrista del duo Estopa.',
                        'active_years_start' => 1999,
                        'active_years_end' => null,
                        'joined_at' => '1999-01-01',
                        'left_at' => null,
                    ],
                    [
                        'name' => 'David Muñoz',
                        'person_name' => 'David Muñoz',
                        'stage_name' => 'David Muñoz',
                        'birth_date' => '1976-01-01',
                        'genre' => 'Rumba-Rock',
                        'description' => 'Vocalista y guitarrista de Estopa',
                        'bio' => 'David Muñoz es un músico español, vocalista y guitarrista del duo Estopa.',
                        'active_years_start' => 1999,
                        'active_years_end' => null,
                        'joined_at' => '1999-01-01',
                        'left_at' => null,
                    ],
                ],
            ],
            [
                'name' => 'Fangoria',
                'description' => 'Grupo español de electropop formado en 1989, pioneros del género en España.',
                'members' => [
                    [
                        'name' => 'Alaska',
                        'person_name' => 'Olvido Gara Jova',
                        'stage_name' => 'Alaska',
                        'birth_date' => '1963-06-13',
                        'genre' => 'Electropop',
                        'description' => 'Vocalista de Fangoria',
                        'bio' => 'Alaska es una cantante española, vocalista del grupo Fangoria.',
                        'active_years_start' => 1989,
                        'active_years_end' => null,
                        'joined_at' => '1989-01-01',
                        'left_at' => null,
                    ],
                    [
                        'name' => 'Nacho Canut',
                        'person_name' => 'Nacho Canut',
                        'stage_name' => 'Nacho Canut',
                        'birth_date' => '1963-01-01',
                        'genre' => 'Electropop',
                        'description' => 'Teclista y compositor de Fangoria',
                        'bio' => 'Nacho Canut es un músico español, teclista y compositor del grupo Fangoria.',
                        'active_years_start' => 1989,
                        'active_years_end' => null,
                        'joined_at' => '1989-01-01',
                        'left_at' => null,
                    ],
                ],
            ],
            [
                'name' => 'Los Rodríguez',
                'description' => 'Grupo hispano-argentino de rock formado en 1991, mezcla de rock español y argentino.',
                'members' => [
                    [
                        'name' => 'Andrés Calamaro',
                        'person_name' => 'Andrés Calamaro',
                        'stage_name' => 'Andrés Calamaro',
                        'birth_date' => '1961-08-22',
                        'genre' => 'Rock',
                        'description' => 'Vocalista y guitarrista de Los Rodríguez',
                        'bio' => 'Andrés Calamaro es un músico argentino, vocalista y guitarrista del grupo Los Rodríguez.',
                        'active_years_start' => 1991,
                        'active_years_end' => 1997,
                        'joined_at' => '1991-01-01',
                        'left_at' => '1997-12-31',
                    ],
                    [
                        'name' => 'Julián Infante',
                        'person_name' => 'Julián Infante',
                        'stage_name' => 'Julián Infante',
                        'birth_date' => '1957-01-01',
                        'genre' => 'Rock',
                        'description' => 'Guitarrista de Los Rodríguez',
                        'bio' => 'Julián Infante es un guitarrista español, miembro de Los Rodríguez.',
                        'active_years_start' => 1991,
                        'active_years_end' => 1997,
                        'joined_at' => '1991-01-01',
                        'left_at' => '1997-12-31',
                    ],
                ],
            ],
            [
                'name' => 'Dover',
                'description' => 'Grupo español de rock alternativo formado en 1992, pioneros del grunge en España.',
                'members' => [
                    [
                        'name' => 'Amparo Llanos',
                        'person_name' => 'Amparo Llanos',
                        'stage_name' => 'Amparo Llanos',
                        'birth_date' => '1974-01-01',
                        'genre' => 'Rock Alternativo',
                        'description' => 'Vocalista y guitarrista de Dover',
                        'bio' => 'Amparo Llanos es una músico española, vocalista y guitarrista del grupo Dover.',
                        'active_years_start' => 1992,
                        'active_years_end' => 2006,
                        'joined_at' => '1992-01-01',
                        'left_at' => '2006-12-31',
                    ],
                    [
                        'name' => 'Cristina Llanos',
                        'person_name' => 'Cristina Llanos',
                        'stage_name' => 'Cristina Llanos',
                        'birth_date' => '1976-01-01',
                        'genre' => 'Rock Alternativo',
                        'description' => 'Guitarrista de Dover',
                        'bio' => 'Cristina Llanos es una guitarrista española, miembro del grupo Dover.',
                        'active_years_start' => 1992,
                        'active_years_end' => 2006,
                        'joined_at' => '1992-01-01',
                        'left_at' => '2006-12-31',
                    ],
                ],
            ],
            [
                'name' => 'Nacha Pop',
                'description' => 'Grupo español de pop-rock formado en 1978, parte de la Movida Madrileña.',
                'members' => [
                    [
                        'name' => 'Antonio Vega',
                        'person_name' => 'Antonio Vega',
                        'stage_name' => 'Antonio Vega',
                        'birth_date' => '1957-12-16',
                        'genre' => 'Pop-Rock',
                        'description' => 'Vocalista y guitarrista de Nacha Pop',
                        'bio' => 'Antonio Vega fue un músico español, vocalista y guitarrista del grupo Nacha Pop.',
                        'active_years_start' => 1978,
                        'active_years_end' => 1988,
                        'joined_at' => '1978-01-01',
                        'left_at' => '1988-12-31',
                    ],
                    [
                        'name' => 'José Ignacio Cano',
                        'person_name' => 'José Ignacio Cano',
                        'stage_name' => 'José Ignacio Cano',
                        'birth_date' => '1957-01-01',
                        'genre' => 'Pop-Rock',
                        'description' => 'Teclista de Nacha Pop',
                        'bio' => 'José Ignacio Cano es un músico español, teclista del grupo Nacha Pop.',
                        'active_years_start' => 1978,
                        'active_years_end' => 1988,
                        'joined_at' => '1978-01-01',
                        'left_at' => '1988-12-31',
                    ],
                ],
            ],
            [
                'name' => 'Radio Futura',
                'description' => 'Grupo español de rock formado en 1979, parte de la Movida Madrileña.',
                'members' => [
                    [
                        'name' => 'Santiago Auserón',
                        'person_name' => 'Santiago Auserón',
                        'stage_name' => 'Santiago Auserón',
                        'birth_date' => '1954-07-25',
                        'genre' => 'Rock',
                        'description' => 'Vocalista y guitarrista de Radio Futura',
                        'bio' => 'Santiago Auserón es un músico español, vocalista y guitarrista del grupo Radio Futura.',
                        'active_years_start' => 1979,
                        'active_years_end' => 1992,
                        'joined_at' => '1979-01-01',
                        'left_at' => '1992-12-31',
                    ],
                    [
                        'name' => 'Luis Auserón',
                        'person_name' => 'Luis Auserón',
                        'stage_name' => 'Luis Auserón',
                        'birth_date' => '1957-01-01',
                        'genre' => 'Rock',
                        'description' => 'Bajista de Radio Futura',
                        'bio' => 'Luis Auserón es un músico español, bajista del grupo Radio Futura.',
                        'active_years_start' => 1979,
                        'active_years_end' => 1992,
                        'joined_at' => '1979-01-01',
                        'left_at' => '1992-12-31',
                    ],
                ],
            ],
            [
                'name' => 'Los Secretos',
                'description' => 'Grupo español de pop-rock formado en 1978, parte de la Movida Madrileña.',
                'members' => [
                    [
                        'name' => 'Enrique Urquijo',
                        'person_name' => 'Enrique Urquijo',
                        'stage_name' => 'Enrique Urquijo',
                        'birth_date' => '1960-01-01',
                        'genre' => 'Pop-Rock',
                        'description' => 'Vocalista y guitarrista de Los Secretos',
                        'bio' => 'Enrique Urquijo fue un músico español, vocalista y guitarrista del grupo Los Secretos.',
                        'active_years_start' => 1978,
                        'active_years_end' => 1999,
                        'joined_at' => '1978-01-01',
                        'left_at' => '1999-12-31',
                    ],
                    [
                        'name' => 'Álvaro Urquijo',
                        'person_name' => 'Álvaro Urquijo',
                        'stage_name' => 'Álvaro Urquijo',
                        'birth_date' => '1962-01-01',
                        'genre' => 'Pop-Rock',
                        'description' => 'Guitarrista de Los Secretos',
                        'bio' => 'Álvaro Urquijo es un músico español, guitarrista del grupo Los Secretos.',
                        'active_years_start' => 1978,
                        'active_years_end' => null,
                        'joined_at' => '1999-01-01',
                        'left_at' => null,
                    ],
                ],
            ],
        ];
    }

    /**
     * Mostrar estadísticas del seeder.
     */
    private function showStatistics(): void
    {
        $totalGroups = Group::count();
        $totalArtists = Artist::count();
        $totalGroupMemberships = \DB::table('artist_group_member')->count();

        $this->command->info('📊 Estadísticas del seeder:');
        $this->command->info("   • Total de grupos: {$totalGroups}");
        $this->command->info("   • Total de artistas: {$totalArtists}");
        $this->command->info("   • Total de membresías en grupos: {$totalGroupMemberships}");

        // Mostrar algunos grupos creados
        $recentGroups = Group::with('artists')->latest()->take(5)->get();
        $this->command->info('🎵 Últimos grupos creados:');
        foreach ($recentGroups as $group) {
            $memberCount = $group->artists->count();
            $this->command->info("   • {$group->name} ({$memberCount} miembros)");
        }
    }
}
