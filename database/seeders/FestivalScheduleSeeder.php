<?php

namespace Database\Seeders;

use App\Models\FestivalSchedule;
use App\Models\Festival;
use Illuminate\Database\Seeder;

class FestivalScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $festivals = Festival::all();

        if ($festivals->isEmpty()) {
            $this->command->info('No hay festivales disponibles. Ejecuta FestivalProgramSeeder primero.');
            return;
        }

        $schedules = [
            [
                'festival_id' => $festivals->random()->id,
                'date' => '2025-06-15',
                'opening_time' => '09:00:00',
                'closing_time' => '23:00:00',
                'main_events' => json_encode([
                    'ceremonia_apertura' => '20:00',
                    'concierto_inaugural' => '21:00',
                    'fuegos_artificiales' => '23:30'
                ]),
                'side_activities' => json_encode([
                    'exposiciones' => '10:00-18:00',
                    'talleres_infantiles' => '11:00-13:00',
                    'degustaciones' => '12:00-15:00'
                ]),
                'special_notes' => 'Día de apertura con ceremonia especial',
                'weather_forecast' => 'Soleado, 25°C',
                'expected_attendance' => 5000,
                'transportation_info' => json_encode([
                    'transporte_extra' => 'sí',
                    'horarios_ampliados' => 'sí',
                    'estaciones_cercanas' => ['Granada Centro', 'Alhambra']
                ]),
                'parking_info' => json_encode([
                    'capacidad' => 2000,
                    'gratuito' => 'sí',
                    'acceso_discapacitados' => 'mejorado'
                ]),
            ],
            [
                'festival_id' => $festivals->random()->id,
                'date' => '2025-06-16',
                'opening_time' => '10:00:00',
                'closing_time' => '22:00:00',
                'main_events' => json_encode([
                    'talleres_musica' => '11:00-13:00',
                    'conciertos_noche' => '20:00-22:00'
                ]),
                'side_activities' => json_encode([
                    'conferencias' => '12:00-14:00',
                    'exposiciones' => '10:00-18:00',
                    'actividades_familiares' => '16:00-18:00'
                ]),
                'special_notes' => 'Día de talleres y actividades educativas',
                'weather_forecast' => 'Parcialmente nublado, 23°C',
                'expected_attendance' => 3500,
                'transportation_info' => json_encode([
                    'transporte_regular' => 'sí',
                    'horarios_normales' => 'sí'
                ]),
                'parking_info' => json_encode([
                    'capacidad' => 1500,
                    'gratuito' => 'sí'
                ]),
            ],
            [
                'festival_id' => $festivals->random()->id,
                'date' => '2025-06-17',
                'opening_time' => '11:00:00',
                'closing_time' => '21:00:00',
                'main_events' => json_encode([
                    'conferencias' => '12:00-14:00',
                    'presentaciones' => '16:00-18:00'
                ]),
                'side_activities' => json_encode([
                    'exposiciones' => '11:00-19:00',
                    'debates' => '15:00-17:00'
                ]),
                'special_notes' => 'Día de conferencias y presentaciones',
                'weather_forecast' => 'Lluvia ligera, 20°C',
                'expected_attendance' => 3000,
                'transportation_info' => json_encode([
                    'transporte_regular' => 'sí',
                    'espacios_cubiertos' => 'sí'
                ]),
                'parking_info' => json_encode([
                    'capacidad' => 1200,
                    'gratuito' => 'sí'
                ]),
            ],
            [
                'festival_id' => $festivals->random()->id,
                'date' => '2025-06-18',
                'opening_time' => '12:00:00',
                'closing_time' => '23:00:00',
                'main_events' => json_encode([
                    'jazz_sunset' => '19:00-20:00',
                    'musica_camara' => '21:00-23:00'
                ]),
                'side_activities' => json_encode([
                    'degustaciones' => '18:00-19:00',
                    'exposiciones' => '12:00-20:00'
                ]),
                'special_notes' => 'Noche de música de cámara y jazz',
                'weather_forecast' => 'Despejado, 22°C',
                'expected_attendance' => 4000,
                'transportation_info' => json_encode([
                    'transporte_nocturno' => 'sí',
                    'horarios_ampliados' => 'sí'
                ]),
                'parking_info' => json_encode([
                    'capacidad' => 1800,
                    'gratuito' => 'sí'
                ]),
            ],
            [
                'festival_id' => $festivals->random()->id,
                'date' => '2025-06-19',
                'opening_time' => '14:00:00',
                'closing_time' => '23:30:00',
                'main_events' => json_encode([
                    'ensayo_general' => '16:00-18:00',
                    'opening_doors' => '19:30',
                    'opera_carmen' => '20:30-23:30'
                ]),
                'side_activities' => json_encode([
                    'exposiciones' => '14:00-20:00',
                    'charlas_previa' => '15:00-16:00'
                ]),
                'special_notes' => 'Noche de ópera al aire libre',
                'weather_forecast' => 'Despejado, 24°C',
                'expected_attendance' => 6000,
                'transportation_info' => json_encode([
                    'transporte_extra' => 'sí',
                    'horarios_ampliados' => 'sí'
                ]),
                'parking_info' => json_encode([
                    'capacidad' => 2500,
                    'gratuito' => 'sí',
                    'acceso_discapacitados' => 'mejorado'
                ]),
            ],
            [
                'festival_id' => $festivals->random()->id,
                'date' => '2025-06-20',
                'opening_time' => '09:00:00',
                'closing_time' => '20:00:00',
                'main_events' => json_encode([
                    'talleres_inicio' => '10:00-12:00',
                    'almuerzo' => '13:00-14:00',
                    'talleres_tarde' => '15:00-17:00'
                ]),
                'side_activities' => json_encode([
                    'exposiciones' => '09:00-19:00',
                    'networking' => '17:00-19:00'
                ]),
                'special_notes' => 'Día dedicado a talleres y formación',
                'weather_forecast' => 'Soleado, 26°C',
                'expected_attendance' => 2000,
                'transportation_info' => json_encode([
                    'transporte_regular' => 'sí'
                ]),
                'parking_info' => json_encode([
                    'capacidad' => 800,
                    'gratuito' => 'sí'
                ]),
            ],
            [
                'festival_id' => $festivals->random()->id,
                'date' => '2025-06-21',
                'opening_time' => '16:00:00',
                'closing_time' => '23:00:00',
                'main_events' => json_encode([
                    'flamenco_inicio' => '18:00-19:00',
                    'baile_tradicional' => '20:00-21:00',
                    'concierto_flamenco' => '21:00-23:00'
                ]),
                'side_activities' => json_encode([
                    'degustaciones' => '17:00-18:00',
                    'exposiciones' => '16:00-22:00'
                ]),
                'special_notes' => 'Noche de flamenco y música tradicional',
                'weather_forecast' => 'Despejado, 25°C',
                'expected_attendance' => 3500,
                'transportation_info' => json_encode([
                    'transporte_nocturno' => 'sí'
                ]),
                'parking_info' => json_encode([
                    'capacidad' => 1500,
                    'gratuito' => 'sí'
                ]),
            ],
            [
                'festival_id' => $festivals->random()->id,
                'date' => '2025-06-22',
                'opening_time' => '18:00:00',
                'closing_time' => '00:30:00',
                'main_events' => json_encode([
                    'ceremonia_clausura' => '21:00-21:30',
                    'espectaculo_final' => '22:30-00:00',
                    'fuegos_artificiales' => '00:00'
                ]),
                'side_activities' => json_encode([
                    'exposiciones' => '18:00-22:00',
                    'actividades_farewell' => '19:00-21:00'
                ]),
                'special_notes' => 'Ceremonia de clausura con espectáculo final',
                'weather_forecast' => 'Despejado, 23°C',
                'expected_attendance' => 8000,
                'transportation_info' => json_encode([
                    'transporte_extra' => 'sí',
                    'horarios_ampliados' => 'sí'
                ]),
                'parking_info' => json_encode([
                    'capacidad' => 3000,
                    'gratuito' => 'sí',
                    'acceso_discapacitados' => 'mejorado'
                ]),
            ],
        ];

        foreach ($schedules as $schedule) {
            FestivalSchedule::create($schedule);
        }

        $this->command->info('✅ Creados ' . count($schedules) . ' horarios de festivales');
    }
}
