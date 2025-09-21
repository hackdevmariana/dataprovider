<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SocialEntity;

class SocialEntitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $socialEntities = [
            [
                'name' => 'Cáritas Española',
                'slug' => 'caritas-espanola',
                'description' => 'Confederación de organizaciones de la Iglesia Católica para la promoción y desarrollo social.',
                'mission' => 'Promover el desarrollo integral de las personas en situación de pobreza y exclusión social.',
                'type' => 'religious_organization',
                'scope' => 'national',
                'city' => 'Madrid',
                'province' => 'Madrid',
                'country' => 'España',
                'phone' => '+34 915 88 00 00',
                'email' => 'info@caritas.es',
                'website' => 'https://www.caritas.es',
                'president' => 'Manuel Bretón Romero',
                'members_count' => 50000,
                'volunteers_count' => 80000,
                'focus_areas' => ['Pobreza', 'Exclusión social', 'Inmigración'],
                'target_groups' => ['Personas en situación de pobreza', 'Inmigrantes'],
                'services' => ['Atención social', 'Formación', 'Empleo'],
                'annual_budget' => 50000000.00,
                'accepts_donations' => true,
                'is_active' => true,
                'is_verified' => true,
                'founded_date' => '1947-01-01',
            ],
            [
                'name' => 'Cruz Roja Española',
                'slug' => 'cruz-roja-espanola',
                'description' => 'Organización humanitaria de carácter voluntario y de interés público.',
                'mission' => 'Prevenir y aliviar el sufrimiento humano en todas las circunstancias.',
                'type' => 'ngo',
                'scope' => 'national',
                'city' => 'Madrid',
                'province' => 'Madrid',
                'country' => 'España',
                'phone' => '+34 915 35 00 00',
                'email' => 'info@cruzroja.es',
                'website' => 'https://www.cruzroja.es',
                'president' => 'Juan Manuel Suárez del Toro',
                'members_count' => 200000,
                'volunteers_count' => 150000,
                'focus_areas' => ['Emergencias', 'Salud', 'Inclusión social'],
                'target_groups' => ['Víctimas de emergencias', 'Personas mayores'],
                'services' => ['Emergencias', 'Salud', 'Inclusión social'],
                'annual_budget' => 200000000.00,
                'accepts_donations' => true,
                'is_active' => true,
                'is_verified' => true,
                'founded_date' => '1864-07-05',
            ],
            [
                'name' => 'Fundación ONCE',
                'slug' => 'fundacion-once',
                'description' => 'Fundación para la integración laboral de personas con discapacidad.',
                'mission' => 'Mejorar la calidad de vida de las personas con discapacidad.',
                'type' => 'foundation',
                'scope' => 'national',
                'city' => 'Madrid',
                'province' => 'Madrid',
                'country' => 'España',
                'phone' => '+34 915 06 00 00',
                'email' => 'info@fundaciononce.es',
                'website' => 'https://www.fundaciononce.es',
                'president' => 'Miguel Carballeda Piñeiro',
                'members_count' => 10000,
                'volunteers_count' => 5000,
                'focus_areas' => ['Discapacidad', 'Inclusión laboral', 'Accesibilidad'],
                'target_groups' => ['Personas con discapacidad', 'Empresas'],
                'services' => ['Formación', 'Empleo', 'Accesibilidad'],
                'annual_budget' => 100000000.00,
                'accepts_donations' => true,
                'is_active' => true,
                'is_verified' => true,
                'founded_date' => '1988-02-13',
            ],
        ];

        foreach ($socialEntities as $entityData) {
            SocialEntity::create($entityData);
        }
    }
}
