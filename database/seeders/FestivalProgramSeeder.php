<?php

namespace Database\Seeders;

use App\Models\FestivalProgram;
use App\Models\Festival;
use App\Models\Artist;
use App\Models\Group;
use Illuminate\Database\Seeder;

class FestivalProgramSeeder extends Seeder
{
    public function run(): void
    {
        $festivals = Festival::all();
        $artists = Artist::limit(20)->get();
        $groups = Group::limit(20)->get();

        if ($festivals->isEmpty()) {
            $this->command->info('No hay festivales disponibles. Ejecuta FestivalSeeder primero.');
            return;
        }

        $programs = [
            [
                'festival_id' => $festivals->random()->id,
                'day' => '2025-06-15',
                'start_time' => '20:00:00',
                'end_time' => '22:00:00',
                'event_name' => 'Concierto de Apertura - Orquesta Sinfónica de Granada',
                'description' => 'Concierto inaugural con obras de Mozart, Beethoven y compositores españoles',
                'location' => 'Teatro del Generalife, Alhambra',
                'artist_id' => $artists->isNotEmpty() ? $artists->random()->id : null,
                'group_id' => null,
                'event_type' => 'performance',
                'is_free' => false,
                'ticket_price' => 45.00,
                'capacity' => 800,
                'current_attendance' => 0,
                'additional_info' => json_encode([
                    'category' => 'música clásica',
                    'duration' => '120 minutos',
                    'intermission' => '15 minutos',
                    'dress_code' => 'formal',
                    'parking' => 'disponible',
                    'accessibility' => 'sí'
                ]),
            ],
            [
                'festival_id' => $festivals->random()->id,
                'day' => '2025-06-16',
                'start_time' => '19:30:00',
                'end_time' => '21:30:00',
                'event_name' => 'Recital de Piano - María del Pilar',
                'description' => 'Recital dedicado a compositores españoles del siglo XIX',
                'location' => 'Palacio de Carlos V, Alhambra',
                'artist_id' => $artists->isNotEmpty() ? $artists->random()->id : null,
                'group_id' => null,
                'event_type' => 'performance',
                'is_free' => false,
                'ticket_price' => 35.00,
                'capacity' => 400,
                'current_attendance' => 0,
                'additional_info' => json_encode([
                    'category' => 'piano',
                    'duration' => '120 minutos',
                    'intermission' => '20 minutos',
                    'dress_code' => 'smart casual',
                    'parking' => 'disponible',
                    'accessibility' => 'sí'
                ]),
            ],
            [
                'festival_id' => $festivals->random()->id,
                'day' => '2025-06-17',
                'start_time' => '18:00:00',
                'end_time' => '20:00:00',
                'event_name' => 'Conferencia: La Música en la Alhambra',
                'description' => 'Conferencia sobre la historia musical de la Alhambra',
                'location' => 'Sala de Conferencias, Museo de la Alhambra',
                'artist_id' => null,
                'group_id' => null,
                'event_type' => 'lecture',
                'is_free' => true,
                'ticket_price' => null,
                'capacity' => 150,
                'current_attendance' => 0,
                'additional_info' => json_encode([
                    'category' => 'conferencia',
                    'duration' => '120 minutos',
                    'idioma' => 'español',
                    'traducción' => 'inglés disponible',
                    'parking' => 'disponible',
                    'accessibility' => 'sí'
                ]),
            ],
            [
                'festival_id' => $festivals->random()->id,
                'day' => '2025-06-18',
                'start_time' => '21:00:00',
                'end_time' => '23:00:00',
                'event_name' => 'Concierto de Cámara - Cuarteto Alhambra',
                'description' => 'Música de cámara en el Patio de los Arrayanes',
                'location' => 'Patio de los Arrayanes, Alhambra',
                'artist_id' => null,
                'group_id' => $groups->isNotEmpty() ? $groups->random()->id : null,
                'event_type' => 'performance',
                'is_free' => false,
                'ticket_price' => 25.00,
                'capacity' => 200,
                'current_attendance' => 0,
                'additional_info' => json_encode([
                    'category' => 'música de cámara',
                    'duration' => '120 minutos',
                    'intermission' => '15 minutos',
                    'dress_code' => 'casual',
                    'parking' => 'disponible',
                    'accessibility' => 'sí'
                ]),
            ],
            [
                'festival_id' => $festivals->random()->id,
                'day' => '2025-06-19',
                'start_time' => '20:30:00',
                'end_time' => '23:30:00',
                'event_name' => 'Ópera: Carmen de Bizet',
                'description' => 'Producción especial al aire libre en la Alhambra',
                'location' => 'Plaza de los Aljibes, Alhambra',
                'artist_id' => $artists->isNotEmpty() ? $artists->random()->id : null,
                'group_id' => null,
                'event_type' => 'performance',
                'is_free' => false,
                'ticket_price' => 75.00,
                'capacity' => 1200,
                'current_attendance' => 0,
                'additional_info' => json_encode([
                    'category' => 'ópera',
                    'duration' => '180 minutos',
                    'intermission' => '30 minutos',
                    'dress_code' => 'formal',
                    'parking' => 'disponible',
                    'accessibility' => 'sí',
                    'subtítulos' => 'español e inglés'
                ]),
            ],
            [
                'festival_id' => $festivals->random()->id,
                'day' => '2025-06-20',
                'start_time' => '17:00:00',
                'end_time' => '19:00:00',
                'event_name' => 'Taller de Composición Musical',
                'description' => 'Taller práctico para compositores emergentes',
                'location' => 'Centro de Formación Musical, Granada',
                'artist_id' => $artists->isNotEmpty() ? $artists->random()->id : null,
                'group_id' => null,
                'event_type' => 'workshop',
                'is_free' => false,
                'ticket_price' => 50.00,
                'capacity' => 30,
                'current_attendance' => 0,
                'additional_info' => json_encode([
                    'category' => 'taller',
                    'duration' => '120 minutos',
                    'nivel' => 'intermedio-avanzado',
                    'materiales' => 'incluidos',
                    'parking' => 'disponible',
                    'accessibility' => 'sí'
                ]),
            ],
            [
                'festival_id' => $festivals->random()->id,
                'day' => '2025-06-21',
                'start_time' => '19:00:00',
                'end_time' => '21:00:00',
                'event_name' => 'Concierto de Guitarra Flamenca',
                'description' => 'Guitarristas flamencos en el Patio de la Acequia',
                'location' => 'Patio de la Acequia, Generalife',
                'artist_id' => $artists->isNotEmpty() ? $artists->random()->id : null,
                'group_id' => null,
                'event_type' => 'performance',
                'is_free' => false,
                'ticket_price' => 30.00,
                'capacity' => 300,
                'current_attendance' => 0,
                'additional_info' => json_encode([
                    'category' => 'flamenco',
                    'duration' => '120 minutos',
                    'intermission' => '15 minutos',
                    'dress_code' => 'casual',
                    'parking' => 'disponible',
                    'accessibility' => 'sí'
                ]),
            ],
            [
                'festival_id' => $festivals->random()->id,
                'day' => '2025-06-22',
                'start_time' => '20:00:00',
                'end_time' => '22:30:00',
                'event_name' => 'Concierto de Cierre - Orquesta Filarmónica de Granada',
                'description' => 'Concierto de clausura con fuegos artificiales',
                'location' => 'Plaza de los Aljibes, Alhambra',
                'artist_id' => null,
                'group_id' => $groups->isNotEmpty() ? $groups->random()->id : null,
                'event_type' => 'performance',
                'is_free' => false,
                'ticket_price' => 60.00,
                'capacity' => 1500,
                'current_attendance' => 0,
                'additional_info' => json_encode([
                    'category' => 'música clásica',
                    'duration' => '150 minutos',
                    'intermission' => '20 minutos',
                    'dress_code' => 'formal',
                    'parking' => 'disponible',
                    'accessibility' => 'sí',
                    'fuegos_artificiales' => 'sí'
                ]),
            ],
        ];

        foreach ($programs as $program) {
            FestivalProgram::create($program);
        }

        $this->command->info('✅ Creados ' . count($programs) . ' programas de festivales');
    }
}
