<?php

namespace Database\Seeders;

use App\Models\FestivalActivity;
use App\Models\Festival;
use App\Models\Artist;
use App\Models\Group;
use Illuminate\Database\Seeder;

class FestivalActivitySeeder extends Seeder
{
    public function run(): void
    {
        $festivals = Festival::all();
        $artists = Artist::limit(20)->get();
        $groups = Group::limit(20)->get();

        if ($festivals->isEmpty()) {
            $this->command->info('No hay festivales disponibles. Ejecuta FestivalProgramSeeder primero.');
            return;
        }

        $activities = [
            [
                'festival_id' => $festivals->random()->id,
                'name' => 'Concierto de Apertura - Orquesta Sinfónica de Granada',
                'type' => 'performance',
                'description' => 'Concierto inaugural con obras de Mozart, Beethoven y compositores españoles',
                'start_time' => '20:00:00',
                'duration_minutes' => 120,
                'location' => 'Teatro del Generalife, Alhambra',
                'organizer' => 'Festival de Música de Granada',
                'max_participants' => 800,
                'age_restriction' => 'Todas las edades',
                'requirements' => json_encode(['Entrada', 'Vestimenta formal']),
                'materials_provided' => json_encode(['Programa', 'Asiento numerado']),
                'requires_registration' => true,
                'participation_fee' => 45.00,
            ],
            [
                'festival_id' => $festivals->random()->id,
                'name' => 'Recital de Piano - María del Pilar',
                'type' => 'performance',
                'description' => 'Recital dedicado a compositores españoles del siglo XIX',
                'start_time' => '19:30:00',
                'duration_minutes' => 120,
                'location' => 'Palacio de Carlos V, Alhambra',
                'organizer' => 'Conservatorio Superior de Granada',
                'max_participants' => 400,
                'age_restriction' => 'Todas las edades',
                'requirements' => json_encode(['Entrada', 'Vestimenta smart casual']),
                'materials_provided' => json_encode(['Programa', 'Asiento numerado']),
                'requires_registration' => true,
                'participation_fee' => 35.00,
            ],
            [
                'festival_id' => $festivals->random()->id,
                'name' => 'Conferencia: La Música en la Alhambra',
                'type' => 'lecture',
                'description' => 'Conferencia sobre la historia musical de la Alhambra',
                'start_time' => '18:00:00',
                'duration_minutes' => 120,
                'location' => 'Sala de Conferencias, Museo de la Alhambra',
                'organizer' => 'Patronato de la Alhambra',
                'max_participants' => 150,
                'age_restriction' => 'Mayores de 16 años',
                'requirements' => json_encode(['Inscripción gratuita']),
                'materials_provided' => json_encode(['Material de apoyo', 'Certificado de asistencia']),
                'requires_registration' => true,
                'participation_fee' => null,
            ],
            [
                'festival_id' => $festivals->random()->id,
                'name' => 'Concierto de Cámara - Cuarteto Alhambra',
                'type' => 'performance',
                'description' => 'Música de cámara en el Patio de los Arrayanes',
                'start_time' => '21:00:00',
                'duration_minutes' => 120,
                'location' => 'Patio de los Arrayanes, Alhambra',
                'organizer' => 'Cuarteto Alhambra',
                'max_participants' => 200,
                'age_restriction' => 'Todas las edades',
                'requirements' => json_encode(['Entrada', 'Vestimenta casual']),
                'materials_provided' => json_encode(['Programa', 'Asiento']),
                'requires_registration' => true,
                'participation_fee' => 25.00,
            ],
            [
                'festival_id' => $festivals->random()->id,
                'name' => 'Ópera: Carmen de Bizet',
                'type' => 'performance',
                'description' => 'Producción especial al aire libre en la Alhambra',
                'start_time' => '20:30:00',
                'duration_minutes' => 180,
                'location' => 'Plaza de los Aljibes, Alhambra',
                'organizer' => 'Teatro de la Ópera de Granada',
                'max_participants' => 1200,
                'age_restriction' => 'Mayores de 12 años',
                'requirements' => json_encode(['Entrada', 'Vestimenta formal']),
                'materials_provided' => json_encode(['Programa', 'Asiento numerado', 'Subtítulos']),
                'requires_registration' => true,
                'participation_fee' => 75.00,
            ],
            [
                'festival_id' => $festivals->random()->id,
                'name' => 'Taller de Composición Musical',
                'type' => 'workshop',
                'description' => 'Taller práctico para compositores emergentes',
                'start_time' => '17:00:00',
                'duration_minutes' => 120,
                'location' => 'Centro de Formación Musical, Granada',
                'organizer' => 'Escuela de Música Moderna',
                'max_participants' => 30,
                'age_restriction' => 'Mayores de 18 años',
                'requirements' => json_encode(['Conocimientos básicos de música', 'Instrumento propio']),
                'materials_provided' => json_encode(['Partituras', 'Equipos de grabación', 'Instrumentos de percusión']),
                'requires_registration' => true,
                'participation_fee' => 50.00,
            ],
            [
                'festival_id' => $festivals->random()->id,
                'name' => 'Concierto de Guitarra Flamenca',
                'type' => 'performance',
                'description' => 'Guitarristas flamencos en el Patio de la Acequia',
                'start_time' => '19:00:00',
                'duration_minutes' => 120,
                'location' => 'Patio de la Acequia, Generalife',
                'organizer' => 'Peña Flamenca de Granada',
                'max_participants' => 300,
                'age_restriction' => 'Todas las edades',
                'requirements' => json_encode(['Entrada', 'Vestimenta casual']),
                'materials_provided' => json_encode(['Programa', 'Asiento']),
                'requires_registration' => true,
                'participation_fee' => 30.00,
            ],
            [
                'festival_id' => $festivals->random()->id,
                'name' => 'Concierto de Cierre - Orquesta Filarmónica de Granada',
                'type' => 'performance',
                'description' => 'Concierto de clausura con fuegos artificiales',
                'start_time' => '20:00:00',
                'duration_minutes' => 150,
                'location' => 'Plaza de los Aljibes, Alhambra',
                'organizer' => 'Orquesta Filarmónica de Granada',
                'max_participants' => 1500,
                'age_restriction' => 'Todas las edades',
                'requirements' => json_encode(['Entrada', 'Vestimenta formal']),
                'materials_provided' => json_encode(['Programa', 'Asiento numerado']),
                'requires_registration' => true,
                'participation_fee' => 60.00,
            ],
        ];

        foreach ($activities as $activity) {
            FestivalActivity::create($activity);
        }

        $this->command->info('✅ Creadas ' . count($activities) . ' actividades de festivales');
    }
}
