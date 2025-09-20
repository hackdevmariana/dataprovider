<?php

namespace Database\Seeders;

use App\Models\Devotion;
use App\Models\CatholicSaint;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DevotionSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creando devociones...');

        // Verificar que existen santos
        $saints = CatholicSaint::limit(10)->get();
        if ($saints->isEmpty()) {
            $this->command->warn('No hay santos disponibles. Creando santos de prueba...');
            for ($i = 1; $i <= 5; $i++) {
                CatholicSaint::create([
                    'name' => 'Santo de Prueba ' . $i,
                    'slug' => 'santo-de-prueba-' . $i,
                    'feast_date' => fake()->date(),
                    'description' => 'Descripción del santo de prueba ' . $i,
                ]);
            }
            $saints = CatholicSaint::limit(10)->get();
        }

        $devotions = [
            [
                'saint_id' => $saints->random()->id,
                'name' => 'Devoción al Sagrado Corazón de Jesús',
                'description' => 'Devoción centrada en el amor infinito de Jesucristo manifestado a través de su corazón.',
                'prayer_text' => 'Sagrado Corazón de Jesús, en Vos confío. Sagrado Corazón de Jesús, misericordioso y lleno de amor, ten piedad de nosotros.',
                'novena_days' => 9,
                'special_intentions' => ['Conversión de pecadores', 'Paz en el mundo', 'Sanación espiritual'],
                'miracles' => ['Apariciones a Santa Margarita María', 'Promesas del Sagrado Corazón'],
                'origin' => 'Siglo XVII, Francia',
                'popularity_level' => 'very_high',
                'practices' => ['Consagración al Sagrado Corazón', 'Primeros viernes', 'Hora santa'],
                'traditions' => ['Coronación del Sagrado Corazón', 'Procesiones del Sagrado Corazón'],
                'is_approved' => true,
            ],
            [
                'saint_id' => $saints->random()->id,
                'name' => 'Devoción a la Virgen de Guadalupe',
                'description' => 'Devoción mariana originada en México tras las apariciones de la Virgen María a San Juan Diego.',
                'prayer_text' => 'Virgen de Guadalupe, Madre de los mexicanos, ruega por nosotros que recurrimos a ti.',
                'novena_days' => 9,
                'special_intentions' => ['Protección de México', 'Unidad familiar', 'Sanación'],
                'miracles' => ['Aparición en el Tepeyac', 'Imagen milagrosa en la tilma'],
                'origin' => '1531, México',
                'popularity_level' => 'very_high',
                'practices' => ['Peregrinación al Tepeyac', 'Rosario de Guadalupe'],
                'traditions' => ['Mañanitas a la Virgen', 'Danza de los concheros'],
                'is_approved' => true,
            ],
            [
                'saint_id' => $saints->random()->id,
                'name' => 'Devoción a San José',
                'description' => 'Devoción al esposo de la Virgen María y padre adoptivo de Jesús, patrono de la Iglesia universal.',
                'prayer_text' => 'San José, esposo de María y padre adoptivo de Jesús, ruega por nosotros.',
                'novena_days' => 9,
                'special_intentions' => ['Protección de la familia', 'Trabajo digno', 'Muerte santa'],
                'miracles' => ['Protección de la Sagrada Familia', 'Intercesión en momentos difíciles'],
                'origin' => 'Siglos I-III, Palestina',
                'popularity_level' => 'high',
                'practices' => ['Novena a San José', 'Consagración a San José'],
                'traditions' => ['Altar de San José', 'Pan de San José'],
                'is_approved' => true,
            ],
            [
                'saint_id' => $saints->random()->id,
                'name' => 'Devoción a San Antonio de Padua',
                'description' => 'Devoción al santo franciscano conocido como el santo de los milagros y protector de los objetos perdidos.',
                'prayer_text' => 'San Antonio, santo de los milagros, ayúdanos a encontrar lo que hemos perdido.',
                'novena_days' => 9,
                'special_intentions' => ['Encontrar objetos perdidos', 'Buen matrimonio', 'Protección de los niños'],
                'miracles' => ['Milagro del pez', 'Predicación a los peces'],
                'origin' => 'Siglo XIII, Portugal/Italia',
                'popularity_level' => 'high',
                'practices' => ['Trece martes de San Antonio', 'Pan de San Antonio'],
                'traditions' => ['Bendición de los niños', 'Procesión de San Antonio'],
                'is_approved' => true,
            ],
            [
                'saint_id' => $saints->random()->id,
                'name' => 'Devoción a San Judas Tadeo',
                'description' => 'Devoción al apóstol conocido como el santo de las causas imposibles y desesperadas.',
                'prayer_text' => 'San Judas Tadeo, santo de las causas imposibles, ruega por nosotros en nuestras necesidades.',
                'novena_days' => 9,
                'special_intentions' => ['Causas imposibles', 'Situaciones desesperadas', 'Esperanza'],
                'miracles' => ['Intercesión en casos desesperados'],
                'origin' => 'Siglo I, Palestina',
                'popularity_level' => 'high',
                'practices' => ['Novena de los nueve días', 'Oración de los tres días'],
                'traditions' => ['Vigilia de San Judas', 'Ofrendas de flores'],
                'is_approved' => true,
            ],
        ];

        $count = 0;
        foreach ($devotions as $devotionData) {
            Devotion::create($devotionData);
            $count++;
        }

        // Crear devociones adicionales aleatorias
        $popularityLevels = ['very_high', 'high', 'medium', 'low', 'very_low'];
        $origins = ['Siglo I', 'Siglo III', 'Siglo XIII', 'Siglo XVII', 'Siglo XIX', 'Siglo XX'];

        for ($i = 0; $i < 10; $i++) {
            Devotion::create([
                'saint_id' => fake()->randomElement($saints->pluck('id')->toArray()),
                'name' => 'Devoción a ' . fake()->firstName() . ' ' . fake()->lastName(),
                'description' => fake()->paragraph(2),
                'prayer_text' => fake()->sentence(10),
                'novena_days' => fake()->numberBetween(1, 30),
                'special_intentions' => fake()->randomElements(['Salud', 'Trabajo', 'Familia', 'Paz'], rand(1, 3)),
                'miracles' => fake()->randomElements(['Milagro de curación', 'Protección divina'], rand(0, 2)),
                'origin' => fake()->randomElement($origins) . ', ' . fake()->country(),
                'popularity_level' => fake()->randomElement($popularityLevels),
                'practices' => fake()->randomElements(['Novena', 'Rosario', 'Oración diaria'], rand(1, 3)),
                'traditions' => fake()->randomElements(['Procesión', 'Fiesta patronal', 'Bendición'], rand(1, 2)),
                'is_approved' => fake()->boolean(80),
            ]);
            $count++;
        }

        $this->command->info("✅ Creadas {$count} devociones");
        $this->showStatistics();
    }

    private function showStatistics(): void
    {
        $total = Devotion::count();
        $approved = Devotion::where('is_approved', true)->count();
        
        $this->command->info("\n📊 Estadísticas:");
        $this->command->info("   Total devociones: {$total}");
        $this->command->info("   Aprobadas: {$approved}");
        
        $popularity = Devotion::selectRaw('popularity_level, COUNT(*) as count')->groupBy('popularity_level')->get();
        $this->command->info("\n⭐ Por popularidad:");
        foreach ($popularity as $pop) {
            $this->command->info("   {$pop->popularity_level}: {$pop->count}");
        }
        
        $withMiracles = Devotion::whereNotNull('miracles')->where('miracles', '!=', '[]')->count();
        $this->command->info("\n✨ Devociones con milagros: {$withMiracles}");
    }
}