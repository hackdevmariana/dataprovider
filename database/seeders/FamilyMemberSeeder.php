<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FamilyMember;
use App\Models\Person;

class FamilyMemberSeeder extends Seeder
{
    public function run(): void
    {
        $people = Person::all();

        if ($people->count() < 2) {
            $this->command->warn('No hay suficientes personas para crear relaciones familiares.');
            return;
        }

        $relationshipTypes = [
            'padre',
            'madre',
            'hermano',
            'hermana',
            'pareja',
            'hijo',
            'hija',
            'marido',
            'esposa',
            'abuelo',
            'abuela',
            'tío',
            'tía',

        ];


        // Creamos entre 1 y 3 relaciones por persona
        foreach ($people as $person) {
            $relatives = $people->where('id', '!=', $person->id)->random(rand(1, 3));

            foreach ($relatives as $relative) {
                FamilyMember::updateOrCreate([
                    'person_id'         => $person->id,
                    'relative_id'       => $relative->id,
                ], [
                    'relationship_type' => fake()->randomElement($relationshipTypes),
                    'is_biological'     => fake()->boolean(80), // 80% probabilidad de que sea biológico
                ]);
            }
        }
    }
}
