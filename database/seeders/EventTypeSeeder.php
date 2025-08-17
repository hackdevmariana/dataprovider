<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EventType;

class EventTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $eventTypes = [
            // Música
            [
                'name' => 'Concierto',
                'slug' => 'concierto',
                'description' => 'Espectáculo musical en vivo con uno o más artistas. Duración típica: 2 horas.',
            ],
            [
                'name' => 'Festival de Música',
                'slug' => 'festival-musica',
                'description' => 'Evento musical de varios días con múltiples artistas y escenarios. Duración: 1-4 días.',
            ],
            [
                'name' => 'Recital',
                'slug' => 'recital',
                'description' => 'Actuación musical íntima, generalmente de música clásica o acústica. Duración: 1-2 horas.',
            ],
            [
                'name' => 'Jam Session',
                'slug' => 'jam-session',
                'description' => 'Sesión musical improvisada donde músicos tocan juntos espontáneamente. Duración: 2-4 horas.',
            ],

            // Teatro y Artes Escénicas
            [
                'name' => 'Obra de Teatro',
                'slug' => 'obra-teatro',
                'description' => 'Representación teatral con actores en vivo. Duración típica: 1.5-3 horas.',
            ],
            [
                'name' => 'Musical',
                'slug' => 'musical',
                'description' => 'Espectáculo teatral que combina actuación, canto y danza. Duración: 2-3 horas.',
            ],
            [
                'name' => 'Danza',
                'slug' => 'danza',
                'description' => 'Espectáculo de baile y expresión corporal. Duración: 1-2 horas.',
            ],
            [
                'name' => 'Monólogo',
                'slug' => 'monologo',
                'description' => 'Espectáculo teatral de una sola persona, generalmente cómico. Duración: 1-1.5 horas.',
            ],

            // Cultura y Tradición
            [
                'name' => 'Fiesta Popular',
                'slug' => 'fiesta-popular',
                'description' => 'Celebración tradicional local con múltiples actividades. Duración: todo el día.',
            ],
            [
                'name' => 'Procesión',
                'slug' => 'procesion',
                'description' => 'Desfile religioso o ceremonial por las calles. Duración: 1-3 horas.',
            ],
            [
                'name' => 'Feria',
                'slug' => 'feria',
                'description' => 'Evento comercial y social con puestos, atracciones y actividades. Duración: varios días.',
            ],
            [
                'name' => 'Romería',
                'slug' => 'romeria',
                'description' => 'Peregrinación o excursión religiosa tradicional. Duración: medio día o día completo.',
            ],

            // Deportes
            [
                'name' => 'Competición Deportiva',
                'slug' => 'competicion-deportiva',
                'description' => 'Evento deportivo competitivo entre equipos o individuos. Duración: 1-3 horas.',
            ],
            [
                'name' => 'Torneo',
                'slug' => 'torneo',
                'description' => 'Serie de competiciones deportivas eliminatorias. Duración: varios días.',
            ],
            [
                'name' => 'Carrera Popular',
                'slug' => 'carrera-popular',
                'description' => 'Competición de running abierta al público general. Duración: 2-6 horas.',
            ],

            // Educación y Conferencias
            [
                'name' => 'Conferencia',
                'slug' => 'conferencia',
                'description' => 'Charla educativa o informativa sobre un tema específico. Duración: 1-2 horas.',
            ],
            [
                'name' => 'Seminario',
                'slug' => 'seminario',
                'description' => 'Evento educativo interactivo con múltiples sesiones. Duración: medio día o día completo.',
            ],
            [
                'name' => 'Taller',
                'slug' => 'taller',
                'description' => 'Actividad práctica de aprendizaje en grupo pequeño. Duración: 2-4 horas.',
            ],

            // Exposiciones y Arte
            [
                'name' => 'Exposición',
                'slug' => 'exposicion',
                'description' => 'Muestra de arte, historia o ciencia abierta al público. Duración: semanas o meses.',
            ],
            [
                'name' => 'Vernissage',
                'slug' => 'vernissage',
                'description' => 'Inauguración de una exposición artística. Duración: 2-3 horas.',
            ],

            // Gastronomía
            [
                'name' => 'Festival Gastronómico',
                'slug' => 'festival-gastronomico',
                'description' => 'Evento dedicado a la comida y bebida regional o temática. Duración: todo el día.',
            ],
            [
                'name' => 'Cata',
                'slug' => 'cata',
                'description' => 'Degustación dirigida de vinos, cervezas u otros productos. Duración: 1-3 horas.',
            ],

            // Otros
            [
                'name' => 'Mercado',
                'slug' => 'mercado',
                'description' => 'Evento comercial con puestos de venta y productos locales. Duración: todo el día.',
            ],
            [
                'name' => 'Otro',
                'slug' => 'otro',
                'description' => 'Tipo de evento no especificado en las categorías anteriores.',
            ],
        ];

        foreach ($eventTypes as $eventType) {
            EventType::firstOrCreate(
                ['slug' => $eventType['slug']],
                $eventType
            );
        }

        $this->command->info('EventType seeder completed: ' . count($eventTypes) . ' event types created/updated.');
    }
}