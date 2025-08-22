<?php

namespace Database\Seeders;

use App\Models\Alias;
use App\Models\Person;
use Illuminate\Database\Seeder;

class AliasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener algunas personas para asignar aliases
        $people = Person::inRandomOrder()->limit(50)->get();
        
        if ($people->isEmpty()) {
            $this->command->warn('No hay personas en la base de datos. Creando aliases sin asignar a personas.');
            return;
        }

        // Aliases famosos organizados por tipo
        $aliases = [
            // Nicknames famosos
            'nickname' => [
                'El Rey del Pop' => 'Michael Jackson',
                'La Reina del Pop' => 'Madonna',
                'El Rey del Rock' => 'Elvis Presley',
                'La Diva' => 'Mariah Carey',
                'El Boss' => 'Bruce Springsteen',
                'La Reina del Soul' => 'Aretha Franklin',
                'El Rey del Blues' => 'B.B. King',
                'La Voz' => 'Frank Sinatra',
                'El Rey del Reggae' => 'Bob Marley',
                'La Reina del Country' => 'Dolly Parton',
                'El Rey del Jazz' => 'Louis Armstrong',
                'La Reina del Jazz' => 'Ella Fitzgerald',
                'El Rey del Hip-Hop' => 'Tupac Shakur',
                'La Reina del Hip-Hop' => 'Queen Latifah',
                'El Rey del Rap' => 'Eminem',
                'La Reina del Rap' => 'Nicki Minaj',
                'El Rey del Metal' => 'Ozzy Osbourne',
                'La Reina del Metal' => 'Doro Pesch',
                'El Rey del Punk' => 'Johnny Rotten',
                'La Reina del Punk' => 'Debbie Harry',
            ],
            
            // Nombres artísticos
            'stage_name' => [
                'Lady Gaga' => 'Stefani Germanotta',
                'Bruno Mars' => 'Peter Gene Hernandez',
                'Drake' => 'Aubrey Graham',
                'Snoop Dogg' => 'Calvin Cordozar Broadus Jr.',
                '50 Cent' => 'Curtis Jackson',
                'Pitbull' => 'Armando Christian Pérez',
                'Fergie' => 'Stacy Ann Ferguson',
                'Pink' => 'Alecia Beth Moore',
                'Katy Perry' => 'Katheryn Elizabeth Hudson',
                'Rihanna' => 'Robyn Rihanna Fenty',
                'Beyoncé' => 'Beyoncé Giselle Knowles',
                'Jay-Z' => 'Shawn Corey Carter',
                'Kanye West' => 'Kanye Omari West',
                'The Weeknd' => 'Abel Makkonen Tesfaye',
                'Post Malone' => 'Austin Richard Post',
                'Billie Eilish' => 'Billie Eilish Pirate Baird O\'Connell',
                'Ariana Grande' => 'Ariana Grande-Butera',
                'Taylor Swift' => 'Taylor Alison Swift',
                'Ed Sheeran' => 'Edward Christopher Sheeran',
                'Justin Bieber' => 'Justin Drew Bieber',
            ],
            
            // Nombres de nacimiento
            'birth_name' => [
                'Stefani Germanotta' => 'Lady Gaga',
                'Peter Gene Hernandez' => 'Bruno Mars',
                'Aubrey Graham' => 'Drake',
                'Calvin Cordozar Broadus Jr.' => 'Snoop Dogg',
                'Curtis Jackson' => '50 Cent',
                'Armando Christian Pérez' => 'Pitbull',
                'Stacy Ann Ferguson' => 'Fergie',
                'Alecia Beth Moore' => 'Pink',
                'Katheryn Elizabeth Hudson' => 'Katy Perry',
                'Robyn Rihanna Fenty' => 'Rihanna',
                'Beyoncé Giselle Knowles' => 'Beyoncé',
                'Shawn Corey Carter' => 'Jay-Z',
                'Kanye Omari West' => 'Kanye West',
                'Abel Makkonen Tesfaye' => 'The Weeknd',
                'Austin Richard Post' => 'Post Malone',
                'Billie Eilish Pirate Baird O\'Connell' => 'Billie Eilish',
                'Ariana Grande-Butera' => 'Ariana Grande',
                'Taylor Alison Swift' => 'Taylor Swift',
                'Edward Christopher Sheeran' => 'Ed Sheeran',
                'Justin Drew Bieber' => 'Justin Bieber',
            ],
            
            // Otros alias
            'other' => [
                'MJ' => 'Michael Jackson',
                'Madge' => 'Madonna',
                'The King' => 'Elvis Presley',
                'MC' => 'Mariah Carey',
                'The Boss' => 'Bruce Springsteen',
                'Soul Sister #1' => 'Aretha Franklin',
                'Riley B. King' => 'B.B. King',
                'Ol\' Blue Eyes' => 'Frank Sinatra',
                'Tuff Gong' => 'Bob Marley',
                'The Iron Butterfly' => 'Dolly Parton',
                'Pops' => 'Louis Armstrong',
                'First Lady of Song' => 'Ella Fitzgerald',
                '2Pac' => 'Tupac Shakur',
                'Latifah' => 'Queen Latifah',
                'Slim Shady' => 'Eminem',
                'The Harajuku Barbie' => 'Nicki Minaj',
                'The Prince of Darkness' => 'Ozzy Osbourne',
                'Metal Queen' => 'Doro Pesch',
                'John Lydon' => 'Johnny Rotten',
                'Blondie' => 'Debbie Harry',
            ],
        ];

        $createdAliases = [];
        $aliasCount = 0;

        foreach ($aliases as $type => $typeAliases) {
            foreach ($typeAliases as $aliasName => $realName) {
                // Buscar una persona aleatoria para asignar el alias
                $person = $people->random();
                
                // Crear el alias
                $alias = Alias::firstOrCreate(
                    [
                        'name' => $aliasName,
                        'person_id' => $person->id,
                    ],
                    [
                        'type' => $type,
                        'is_primary' => false, // Por defecto no es primario
                    ]
                );
                
                $createdAliases[] = [
                    'id' => $alias->id,
                    'name' => $alias->name,
                    'type' => $alias->type,
                    'person' => $person->name ?? 'Persona #' . $person->id,
                    'is_primary' => $alias->is_primary ? 'Sí' : 'No',
                ];
                
                $aliasCount++;
            }
        }

        // Crear algunos aliases adicionales aleatorios para personas existentes
        $additionalAliases = [
            'El Grande', 'La Estrella', 'El Maestro', 'La Leyenda', 'El Ídolo',
            'La Sensación', 'El Genio', 'La Diva', 'El Rey', 'La Reina',
            'El Príncipe', 'La Princesa', 'El Héroe', 'La Heroína', 'El Campeón',
            'La Campeona', 'El Líder', 'La Líder', 'El Visionario', 'La Visionaria',
        ];

        foreach ($additionalAliases as $aliasName) {
            $person = $people->random();
            $type = ['nickname', 'stage_name', 'other'][array_rand(['nickname', 'stage_name', 'other'])];
            
            $alias = Alias::firstOrCreate(
                [
                    'name' => $aliasName,
                    'person_id' => $person->id,
                ],
                [
                    'type' => $type,
                    'is_primary' => false,
                ]
            );
            
            $createdAliases[] = [
                'id' => $alias->id,
                'name' => $alias->name,
                'type' => $alias->type,
                'person' => $person->name ?? 'Persona #' . $person->id,
                'is_primary' => $alias->is_primary ? 'Sí' : 'No',
            ];
            
            $aliasCount++;
        }

        $this->command->info("Se han creado {$aliasCount} aliases para personas famosas.");
        
        // Mostrar tabla con los aliases creados
        $this->command->table(
            ['ID', 'Alias', 'Tipo', 'Persona', 'Es Primario'],
            array_slice($createdAliases, 0, 20) // Mostrar solo los primeros 20 para no saturar
        );
        
        if (count($createdAliases) > 20) {
            $this->command->info("... y " . (count($createdAliases) - 20) . " aliases más.");
        }

        // Estadísticas
        $totalAliases = Alias::count();
        $aliasesByType = Alias::selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();
        
        $this->command->newLine();
        $this->command->info("📊 Estadísticas:");
        $this->command->info("   • Total de aliases en BD: {$totalAliases}");
        $this->command->info("   • Por tipo:");
        foreach ($aliasesByType as $type => $count) {
            $typeLabel = match($type) {
                'nickname' => 'Apodos',
                'stage_name' => 'Nombres Artísticos',
                'birth_name' => 'Nombres de Nacimiento',
                'other' => 'Otros',
                default => ucfirst($type)
            };
            $this->command->info("     - {$typeLabel}: {$count}");
        }
        
        $this->command->newLine();
        $this->command->info("✅ Seeder de Alias completado exitosamente.");
    }
}
