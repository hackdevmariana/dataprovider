<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Profession;

class ProfessionSeeder extends Seeder
{
    public function run()
    {
        $professions = [
            [
                'name' => 'Actor',
                'slug' => 'actor',
                'category' => 'Artes escénicas',
                'is_public_facing' => true,
            ],
            [
                'name' => 'Escritor',
                'slug' => 'escritor',
                'category' => 'Literatura',
                'is_public_facing' => true,
            ],
            [
                'name' => 'Director de cine',
                'slug' => 'director-de-cine',
                'category' => 'Cine',
                'is_public_facing' => true,
            ],
            [
                'name' => 'Modelo',
                'slug' => 'modelo',
                'category' => 'Moda',
                'is_public_facing' => true,
            ],
            [
                'name' => 'Político',
                'slug' => 'politico',
                'category' => 'Política',
                'is_public_facing' => true,
            ],
            [
                'name' => 'Economista',
                'slug' => 'economista',
                'category' => 'Ciencias sociales',
                'is_public_facing' => true,
            ],
            [
                'name' => 'Cantante',
                'slug' => 'cantante',
                'category' => 'Música',
                'is_public_facing' => true,
            ],
            [
                'name' => 'Periodista',
                'slug' => 'periodista',
                'category' => 'Medios',
                'is_public_facing' => true,
            ],
            [
                'name' => 'Artista visual',
                'slug' => 'artista-visual',
                'category' => 'Artes visuales',
                'is_public_facing' => true,
            ],
            [
                'name' => 'Científico',
                'slug' => 'cientifico',
                'category' => 'Ciencia',
                'is_public_facing' => true,
            ],
        ];

        foreach ($professions as $profession) {
            Profession::firstOrCreate(['slug' => $profession['slug']], $profession);
        }
    }
}
