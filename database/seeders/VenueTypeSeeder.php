<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\VenueType;

class VenueTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $venueTypes = [
            [
                'name' => 'Auditorio',
                'slug' => 'auditorio',
                'description' => 'Espacio cerrado diseñado específicamente para espectáculos y conciertos con buena acústica. Capacidad típica: 100-5000 personas.',
            ],
            [
                'name' => 'Parque',
                'slug' => 'parque',
                'description' => 'Espacio verde al aire libre, ideal para festivales y eventos familiares. Capacidad: 50-50000 personas.',
            ],
            [
                'name' => 'Plaza',
                'slug' => 'plaza',
                'description' => 'Espacio urbano abierto, tradicionalmente usado para eventos públicos y celebraciones. Capacidad típica: 100-10000 personas.',
            ],
            [
                'name' => 'Club',
                'slug' => 'club',
                'description' => 'Local nocturno cerrado, diseñado para música en vivo y eventos nocturnos. Capacidad típica: 50-2000 personas.',
            ],
            [
                'name' => 'Teatro',
                'slug' => 'teatro',
                'description' => 'Edificio específicamente diseñado para representaciones teatrales y espectáculos escénicos. Capacidad típica: 100-3000 personas.',
            ],
            [
                'name' => 'Estadio',
                'slug' => 'estadio',
                'description' => 'Gran recinto deportivo que puede albergar eventos musicales y espectáculos masivos. Capacidad: 5000-100000 personas.',
            ],
            [
                'name' => 'Pabellón',
                'slug' => 'pabellon',
                'description' => 'Edificio multiusos cerrado, frecuentemente usado para eventos deportivos y espectáculos. Capacidad: 1000-20000 personas.',
            ],
            [
                'name' => 'Sala de Conciertos',
                'slug' => 'sala-conciertos',
                'description' => 'Espacio cerrado específicamente diseñado para música en vivo con excelente acústica. Capacidad: 50-3000 personas.',
            ],
            [
                'name' => 'Centro Cultural',
                'slug' => 'centro-cultural',
                'description' => 'Edificio multiuso dedicado a actividades culturales y artísticas. Capacidad típica: 100-1000 personas.',
            ],
            [
                'name' => 'Recinto Ferial',
                'slug' => 'recinto-ferial',
                'description' => 'Espacio dedicado a ferias, exposiciones y grandes eventos. Capacidad: 1000-50000 personas.',
            ],
            [
                'name' => 'Playa',
                'slug' => 'playa',
                'description' => 'Espacio costero natural ideal para festivales de verano y eventos al aire libre. Capacidad: 100-20000 personas.',
            ],
            [
                'name' => 'Iglesia',
                'slug' => 'iglesia',
                'description' => 'Edificio religioso que ocasionalmente alberga conciertos de música clásica y eventos culturales. Capacidad: 50-1000 personas.',
            ],
            [
                'name' => 'Online',
                'slug' => 'online',
                'description' => 'Evento virtual transmitido por internet sin ubicación física. Capacidad: ilimitada.',
            ],
            [
                'name' => 'Otro',
                'slug' => 'otro',
                'description' => 'Tipo de venue no especificado en las categorías anteriores.',
            ],
        ];

        foreach ($venueTypes as $venueType) {
            VenueType::firstOrCreate(
                ['slug' => $venueType['slug']],
                $venueType
            );
        }

        $this->command->info('VenueType seeder completed: ' . count($venueTypes) . ' venue types created/updated.');
    }
}