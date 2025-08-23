<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Group;
use App\Models\Artist;
use App\Models\Person;
use App\Models\Language;
use App\Models\Alias;
use Carbon\Carbon;

class SpanishRapSeeder extends Seeder
{
    /**
     * Ejecutar el seeder para grupos de rap español.
     */
    public function run(): void
    {
        $this->command->info('🎤 Creando grupos de rap español...');

        // Buscar idioma español
        $spanish = Language::where('language', 'Spanish')->orWhere('iso_639_1', 'es')->first();
        if (!$spanish) {
            $spanish = Language::factory()->create([
                'language' => 'Spanish',
                'slug' => 'spanish',
                'native_name' => 'Español',
                'iso_639_1' => 'es',
                'iso_639_2' => 'spa'
            ]);
        }

        // Crear grupos de rap español
        $rapGroups = $this->getSpanishRapGroups();
        $createdCount = 0;

        foreach ($rapGroups as $groupData) {
            $group = Group::create([
                'name' => $groupData['name'],
                'description' => $groupData['description'],
            ]);

            // Crear artistas miembros del grupo
            if (isset($groupData['members'])) {
                foreach ($groupData['members'] as $memberData) {
                    $this->createRapArtist($group, $memberData, $spanish);
                }
            }

            $createdCount++;
        }

        $this->command->info("✅ Creados {$createdCount} grupos de rap español");

        // Mostrar estadísticas
        $this->showStatistics();
    }

    /**
     * Crear artista de rap y sus AKAs.
     */
    private function createRapArtist(Group $group, array $memberData, Language $language): void
    {
        // Buscar o crear la persona asociada
        $person = Person::where('name', $memberData['person_name'])->first();
        if (!$person) {
            $person = Person::create([
                'name' => $memberData['person_name'],
                'slug' => \Str::slug($memberData['person_name']),
                'birth_date' => $memberData['birth_date'] ?? null,
                'notable_for' => $memberData['notable_for'] ?? 'Rapero',
                'is_influencer' => true,
                'short_bio' => $memberData['bio'] ?? 'Artista de rap español',
            ]);
        }

        // Crear AKAs si existen
        if (isset($memberData['akas'])) {
            foreach ($memberData['akas'] as $akaData) {
                Alias::create([
                    'person_id' => $person->id,
                    'name' => $akaData['name'],
                    'type' => $akaData['type'] ?? 'stage_name',
                    'is_primary' => $akaData['is_primary'] ?? false,
                ]);
            }
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
                'genre' => $memberData['genre'] ?? 'Rap/Hip-Hop',
                'person_id' => $person->id,
                'active_years_start' => $memberData['active_years_start'] ?? null,
                'active_years_end' => $memberData['active_years_end'] ?? null,
                'bio' => $memberData['bio'] ?? 'Artista de rap español, miembro del grupo ' . $group->name,
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
     * Datos de grupos de rap español.
     */
    private function getSpanishRapGroups(): array
    {
        return [
            [
                'name' => 'Violadores del Verso',
                'description' => 'Grupo de rap español formado en 1995, uno de los más influyentes del hip-hop español.',
                'members' => [
                    [
                        'name' => 'Kase.O',
                        'person_name' => 'Javier Ibarra Ramos',
                        'stage_name' => 'Kase.O',
                        'birth_date' => '1980-03-27',
                        'genre' => 'Rap/Hip-Hop',
                        'description' => 'MC principal y fundador de Violadores del Verso',
                        'bio' => 'Javier Ibarra Ramos, conocido como Kase.O, es un MC español fundador de Violadores del Verso.',
                        'active_years_start' => 1995,
                        'active_years_end' => 2016,
                        'joined_at' => '1995-01-01',
                        'left_at' => '2016-12-31',
                        'akas' => [
                            ['name' => 'Kase.O', 'type' => 'stage_name', 'context' => 'Violadores del Verso', 'is_primary' => true],
                            ['name' => 'Jazz Magnetism', 'type' => 'stage_name', 'context' => 'Proyecto en solitario'],
                        ],
                    ],
                    [
                        'name' => 'Lírico',
                        'person_name' => 'Carlos García',
                        'stage_name' => 'Lírico',
                        'birth_date' => '1978-01-01',
                        'genre' => 'Rap/Hip-Hop',
                        'description' => 'MC de Violadores del Verso',
                        'bio' => 'Carlos García, conocido como Lírico, es un MC español miembro de Violadores del Verso.',
                        'active_years_start' => 1995,
                        'active_years_end' => 2016,
                        'joined_at' => '1995-01-01',
                        'left_at' => '2016-12-31',
                        'akas' => [
                            ['name' => 'Lírico', 'type' => 'stage_name', 'context' => 'Violadores del Verso', 'is_primary' => true],
                        ],
                    ],
                    [
                        'name' => 'Sho-Hai',
                        'person_name' => 'Jorge García',
                        'stage_name' => 'Sho-Hai',
                        'birth_date' => '1979-01-01',
                        'genre' => 'Rap/Hip-Hop',
                        'description' => 'MC de Violadores del Verso',
                        'bio' => 'Jorge García, conocido como Sho-Hai, es un MC español miembro de Violadores del Verso.',
                        'active_years_start' => 1995,
                        'active_years_end' => 2016,
                        'joined_at' => '1995-01-01',
                        'left_at' => '2016-12-31',
                        'akas' => [
                            ['name' => 'Sho-Hai', 'type' => 'stage_name', 'context' => 'Violadores del Verso', 'is_primary' => true],
                        ],
                    ],
                    [
                        'name' => 'R de Rumba',
                        'person_name' => 'Roberto Rojas',
                        'stage_name' => 'R de Rumba',
                        'birth_date' => '1977-01-01',
                        'genre' => 'Rap/Hip-Hop',
                        'description' => 'MC de Violadores del Verso',
                        'bio' => 'Roberto Rojas, conocido como R de Rumba, es un MC español miembro de Violadores del Verso.',
                        'active_years_start' => 1995,
                        'active_years_end' => 2016,
                        'joined_at' => '1995-01-01',
                        'left_at' => '2016-12-31',
                        'akas' => [
                            ['name' => 'R de Rumba', 'type' => 'stage_name', 'context' => 'Violadores del Verso', 'is_primary' => true],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'CPV (Club de los Poetas Violentos)',
                'description' => 'Grupo de rap español formado en 1996, conocido por su estilo hardcore y letras contundentes.',
                'members' => [
                    [
                        'name' => 'El Meswy',
                        'person_name' => 'Jesús Meswy',
                        'stage_name' => 'El Meswy',
                        'birth_date' => '1975-01-01',
                        'genre' => 'Rap/Hip-Hop',
                        'description' => 'MC principal de CPV',
                        'bio' => 'Jesús Meswy, conocido como El Meswy, es un MC español miembro de CPV.',
                        'active_years_start' => 1996,
                        'active_years_end' => 2005,
                        'joined_at' => '1996-01-01',
                        'left_at' => '2005-12-31',
                        'akas' => [
                            ['name' => 'El Meswy', 'type' => 'stage_name', 'context' => 'CPV', 'is_primary' => true],
                        ],
                    ],
                    [
                        'name' => 'Kamikaze',
                        'person_name' => 'Carlos Kamikaze',
                        'stage_name' => 'Kamikaze',
                        'birth_date' => '1976-01-01',
                        'genre' => 'Rap/Hip-Hop',
                        'description' => 'MC de CPV',
                        'bio' => 'Carlos Kamikaze, conocido como Kamikaze, es un MC español miembro de CPV.',
                        'active_years_start' => 1996,
                        'active_years_end' => 2005,
                        'joined_at' => '1996-01-01',
                        'left_at' => '2005-12-31',
                        'akas' => [
                            ['name' => 'Kamikaze', 'type' => 'stage_name', 'context' => 'CPV', 'is_primary' => true],
                        ],
                    ],
                    [
                        'name' => 'Paco King',
                        'person_name' => 'Francisco King',
                        'stage_name' => 'Paco King',
                        'birth_date' => '1977-01-01',
                        'genre' => 'Rap/Hip-Hop',
                        'description' => 'MC de CPV',
                        'bio' => 'Francisco King, conocido como Paco King, es un MC español miembro de CPV.',
                        'active_years_start' => 1996,
                        'active_years_end' => 2005,
                        'joined_at' => '1996-01-01',
                        'left_at' => '2005-12-31',
                        'akas' => [
                            ['name' => 'Paco King', 'type' => 'stage_name', 'context' => 'CPV', 'is_primary' => true],
                        ],
                    ],
                    [
                        'name' => 'Supernafamacho',
                        'person_name' => 'Miguel Supernafamacho',
                        'stage_name' => 'Supernafamacho',
                        'birth_date' => '1976-01-01',
                        'genre' => 'Rap/Hip-Hop',
                        'description' => 'MC de CPV',
                        'bio' => 'Miguel Supernafamacho, conocido como Supernafamacho, es un MC español miembro de CPV.',
                        'active_years_start' => 1996,
                        'active_years_end' => 2005,
                        'joined_at' => '1996-01-01',
                        'left_at' => '2005-12-31',
                        'akas' => [
                            ['name' => 'Supernafamacho', 'type' => 'stage_name', 'context' => 'CPV', 'is_primary' => true],
                        ],
                    ],
                    [
                        'name' => 'Mr. Rango',
                        'person_name' => 'Roberto Rango',
                        'stage_name' => 'Mr. Rango',
                        'birth_date' => '1978-01-01',
                        'genre' => 'Rap/Hip-Hop',
                        'description' => 'MC de CPV',
                        'bio' => 'Roberto Rango, conocido como Mr. Rango, es un MC español miembro de CPV.',
                        'active_years_start' => 1996,
                        'active_years_end' => 2005,
                        'joined_at' => '1996-01-01',
                        'left_at' => '2005-12-31',
                        'akas' => [
                            ['name' => 'Mr. Rango', 'type' => 'stage_name', 'context' => 'CPV', 'is_primary' => true],
                        ],
                    ],
                    [
                        'name' => 'Jota Mayúscula',
                        'person_name' => 'José Mayúscula',
                        'stage_name' => 'Jota Mayúscula',
                        'birth_date' => '1977-01-01',
                        'genre' => 'Rap/Hip-Hop',
                        'description' => 'MC de CPV',
                        'bio' => 'José Mayúscula, conocido como Jota Mayúscula, es un MC español miembro de CPV.',
                        'active_years_start' => 1996,
                        'active_years_end' => 2005,
                        'joined_at' => '1996-01-01',
                        'left_at' => '2005-12-31',
                        'akas' => [
                            ['name' => 'Jota Mayúscula', 'type' => 'stage_name', 'context' => 'CPV', 'is_primary' => true],
                        ],
                    ],
                ],
            ],
            [
                'name' => '7 Notas 7 Colores',
                'description' => 'Grupo de rap español formado en 1994, pioneros del hip-hop en España.',
                'members' => [
                    [
                        'name' => 'Elphomega',
                        'person_name' => 'Alfonso Fernández',
                        'stage_name' => 'Elphomega',
                        'birth_date' => '1975-01-01',
                        'genre' => 'Rap/Hip-Hip',
                        'description' => 'MC principal de 7 Notas 7 Colores',
                        'bio' => 'Alfonso Fernández, conocido como Elphomega, es un MC español miembro de 7 Notas 7 Colores.',
                        'active_years_start' => 1994,
                        'active_years_end' => 2000,
                        'joined_at' => '1994-01-01',
                        'left_at' => '2000-12-31',
                        'akas' => [
                            ['name' => 'Elphomega', 'type' => 'stage_name', 'context' => '7 Notas 7 Colores', 'is_primary' => true],
                        ],
                    ],
                    [
                        'name' => 'El Santo',
                        'person_name' => 'Santiago Zannou',
                        'stage_name' => 'El Santo',
                        'birth_date' => '1975-01-01',
                        'genre' => 'Rap/Hip-Hop',
                        'description' => 'MC de 7 Notas 7 Colores',
                        'bio' => 'Santiago Zannou, conocido como El Santo, es un MC español miembro de 7 Notas 7 Colores.',
                        'active_years_start' => 1994,
                        'active_years_end' => 2000,
                        'joined_at' => '1994-01-01',
                        'left_at' => '2000-12-31',
                        'akas' => [
                            ['name' => 'El Santo', 'type' => 'stage_name', 'context' => '7 Notas 7 Colores', 'is_primary' => true],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'A3Bandas',
                'description' => 'Grupo de rap español formado en 1998, conocido por su estilo underground.',
                'members' => [
                    [
                        'name' => 'Zatu',
                        'person_name' => 'Saturnino Rey',
                        'stage_name' => 'Zatu',
                        'birth_date' => '1978-01-01',
                        'genre' => 'Rap/Hip-Hop',
                        'description' => 'MC principal de A3Bandas',
                        'bio' => 'Saturnino Rey, conocido como Zatu, es un MC español miembro de A3Bandas.',
                        'active_years_start' => 1998,
                        'active_years_end' => 2008,
                        'joined_at' => '1998-01-01',
                        'left_at' => '2008-12-31',
                        'akas' => [
                            ['name' => 'Zatu', 'type' => 'stage_name', 'context' => 'A3Bandas', 'is_primary' => true],
                        ],
                    ],
                    [
                        'name' => 'Dose',
                        'person_name' => 'David Fernández',
                        'stage_name' => 'Dose',
                        'birth_date' => '1979-01-01',
                        'genre' => 'Rap/Hip-Hop',
                        'description' => 'MC de A3Bandas',
                        'bio' => 'David Fernández, conocido como Dose, es un MC español miembro de A3Bandas.',
                        'active_years_start' => 1998,
                        'active_years_end' => 2008,
                        'joined_at' => '1998-01-01',
                        'left_at' => '2008-12-31',
                        'akas' => [
                            ['name' => 'Dose', 'type' => 'stage_name', 'context' => 'A3Bandas', 'is_primary' => true],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'SFDK',
                'description' => 'Grupo de rap español formado en 1992, uno de los más longevos del hip-hop español.',
                'members' => [
                    [
                        'name' => 'Zatu',
                        'person_name' => 'Saturnino Rey',
                        'stage_name' => 'Zatu',
                        'birth_date' => '1978-01-01',
                        'genre' => 'Rap/Hip-Hop',
                        'description' => 'MC principal de SFDK',
                        'bio' => 'Saturnino Rey, conocido como Zatu, es un MC español miembro de SFDK.',
                        'active_years_start' => 1992,
                        'active_years_end' => null,
                        'joined_at' => '1992-01-01',
                        'left_at' => null,
                        'akas' => [
                            ['name' => 'Zatu', 'type' => 'stage_name', 'context' => 'SFDK', 'is_primary' => true],
                        ],
                    ],
                    [
                        'name' => 'Acción Sánchez',
                        'person_name' => 'Francisco Sánchez',
                        'stage_name' => 'Acción Sánchez',
                        'birth_date' => '1977-01-01',
                        'genre' => 'Rap/Hip-Hop',
                        'description' => 'MC de SFDK',
                        'bio' => 'Francisco Sánchez, conocido como Acción Sánchez, es un MC español miembro de SFDK.',
                        'active_years_start' => 1992,
                        'active_years_end' => null,
                        'joined_at' => '1992-01-01',
                        'left_at' => null,
                        'akas' => [
                            ['name' => 'Acción Sánchez', 'type' => 'stage_name', 'context' => 'SFDK', 'is_primary' => true],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'ToteKing',
                'description' => 'Artista en solitario de rap español, conocido por su estilo lírico y poético.',
                'members' => [
                    [
                        'name' => 'ToteKing',
                        'person_name' => 'Javier García',
                        'stage_name' => 'ToteKing',
                        'birth_date' => '1978-01-01',
                        'genre' => 'Rap/Hip-Hop',
                        'description' => 'MC en solitario',
                        'bio' => 'Javier García, conocido como ToteKing, es un MC español en solitario.',
                        'active_years_start' => 1998,
                        'active_years_end' => null,
                        'joined_at' => '1998-01-01',
                        'left_at' => null,
                        'akas' => [
                            ['name' => 'ToteKing', 'type' => 'stage_name', 'context' => 'Rap en solitario', 'is_primary' => true],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Nach',
                'description' => 'Artista en solitario de rap español, conocido por su estilo introspectivo.',
                'members' => [
                    [
                        'name' => 'Nach',
                        'person_name' => 'Ignacio Fornés',
                        'stage_name' => 'Nach',
                        'birth_date' => '1974-01-01',
                        'genre' => 'Rap/Hip-Hop',
                        'description' => 'MC en solitario',
                        'bio' => 'Ignacio Fornés, conocido como Nach, es un MC español en solitario.',
                        'active_years_start' => 1994,
                        'active_years_end' => null,
                        'joined_at' => '1994-01-01',
                        'left_at' => null,
                        'akas' => [
                            ['name' => 'Nach', 'type' => 'stage_name', 'context' => 'Rap en solitario', 'is_primary' => true],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Los Chikos del Maíz',
                'description' => 'Grupo de rap español formado en 2003, conocido por su contenido político y social.',
                'members' => [
                    [
                        'name' => 'El Chojin',
                        'person_name' => 'Domingo Edjang Moreno',
                        'stage_name' => 'El Chojin',
                        'birth_date' => '1977-01-01',
                        'genre' => 'Rap/Hip-Hop',
                        'description' => 'MC principal de Los Chikos del Maíz',
                        'bio' => 'Domingo Edjang Moreno, conocido como El Chojin, es un MC español miembro de Los Chikos del Maíz.',
                        'active_years_start' => 2003,
                        'active_years_end' => 2018,
                        'joined_at' => '2003-01-01',
                        'left_at' => '2018-12-31',
                        'akas' => [
                            ['name' => 'El Chojin', 'type' => 'stage_name', 'context' => 'Los Chikos del Maíz', 'is_primary' => true],
                        ],
                    ],
                    [
                        'name' => 'El Langui',
                        'person_name' => 'Juan Manuel Montilla',
                        'stage_name' => 'El Langui',
                        'birth_date' => '1977-01-01',
                        'genre' => 'Rap/Hip-Hop',
                        'description' => 'MC de Los Chikos del Maíz',
                        'bio' => 'Juan Manuel Montilla, conocido como El Langui, es un MC español miembro de Los Chikos del Maíz.',
                        'active_years_start' => 2003,
                        'active_years_end' => 2018,
                        'joined_at' => '2003-01-01',
                        'left_at' => '2018-12-31',
                        'akas' => [
                            ['name' => 'El Langui', 'type' => 'stage_name', 'context' => 'Los Chikos del Maíz', 'is_primary' => true],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Falsalarma',
                'description' => 'Grupo de rap español formado en 1996, pioneros del hip-hop en Andalucía.',
                'members' => [
                    [
                        'name' => 'El Tralla',
                        'person_name' => 'Francisco Tralla',
                        'stage_name' => 'El Tralla',
                        'birth_date' => '1976-01-01',
                        'genre' => 'Rap/Hip-Hop',
                        'description' => 'MC principal de Falsalarma',
                        'bio' => 'Francisco Tralla, conocido como El Tralla, es un MC español miembro de Falsalarma.',
                        'active_years_start' => 1996,
                        'active_years_end' => 2006,
                        'joined_at' => '1996-01-01',
                        'left_at' => '2006-12-31',
                        'akas' => [
                            ['name' => 'El Tralla', 'type' => 'stage_name', 'context' => 'Falsalarma', 'is_primary' => true],
                        ],
                    ],
                    [
                        'name' => 'El Santo',
                        'person_name' => 'Santiago Zannou',
                        'stage_name' => 'El Santo',
                        'birth_date' => '1975-01-01',
                        'genre' => 'Rap/Hip-Hop',
                        'description' => 'MC de Falsalarma',
                        'bio' => 'Santiago Zannou, conocido como El Santo, es un MC español miembro de Falsalarma.',
                        'active_years_start' => 1996,
                        'active_years_end' => 2006,
                        'joined_at' => '1996-01-01',
                        'left_at' => '2006-12-31',
                        'akas' => [
                            ['name' => 'El Santo', 'type' => 'stage_name', 'context' => 'Falsalarma', 'is_primary' => true],
                        ],
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
        $totalPersons = Person::count();
        $totalAliases = Alias::count();
        $totalGroupMemberships = \DB::table('artist_group_member')->count();

        $this->command->info('📊 Estadísticas del seeder de rap español:');
        $this->command->info("   • Total de grupos: {$totalGroups}");
        $this->command->info("   • Total de artistas: {$totalArtists}");
        $this->command->info("   • Total de personas: {$totalPersons}");
        $this->command->info("   • Total de AKAs: {$totalAliases}");
        $this->command->info("   • Total de membresías en grupos: {$totalGroupMemberships}");

        // Mostrar algunos grupos de rap creados
        $rapGroups = Group::whereIn('name', [
            'Violadores del Verso', 'CPV', '7 Notas 7 Colores', 'A3Bandas', 'SFDK'
        ])->with('artists')->get();
        
        $this->command->info('🎤 Grupos de rap español creados:');
        foreach ($rapGroups as $group) {
            $memberCount = $group->artists->count();
            $this->command->info("   • {$group->name} ({$memberCount} miembros)");
        }

        // Mostrar algunos AKAs creados
        $recentAliases = Alias::with('person')->latest()->take(10)->get();
        $this->command->info('🏷️ Últimos AKAs creados:');
        foreach ($recentAliases as $alias) {
            $this->command->info("   • {$alias->person->name} → {$alias->alias} ({$alias->type})");
        }
    }
}
