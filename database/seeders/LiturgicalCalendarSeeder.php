<?php

namespace Database\Seeders;

use App\Models\LiturgicalCalendar;
use App\Models\CatholicSaint;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class LiturgicalCalendarSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Sembrando calendario litúrgico católico...');

        // Obtener algunos santos para asignar a las celebraciones
        $saints = CatholicSaint::take(20)->get();
        $defaultSaint = $saints->first();

        $liturgicalEvents = [
            // ===== TIEMPO DE NAVIDAD =====
            [
                'date' => '2025-01-06',
                'liturgical_season' => 'Christmas',
                'feast_day' => 'Epifanía del Señor',
                'celebration_level' => 'solemnity',
                'color' => 'white',
                'description' => 'Celebración de la manifestación de Cristo a los Magos de Oriente',
                'readings' => [
                    'primera_lectura' => 'Isaías 60:1-6',
                    'salmo' => 'Salmo 72:1-2, 7-8, 10-11, 12-13',
                    'segunda_lectura' => 'Efesios 3:2-3a, 5-6',
                    'evangelio' => 'Mateo 2:1-12'
                ],
                'prayers' => [
                    'colecta' => 'Dios todopoderoso, que en este día revelaste a tu Hijo a las naciones por medio de la estrella...',
                    'oracion_sobre_las_ofrendas' => 'Acepta, Señor, las ofrendas de tu Iglesia...',
                    'poscomunion' => 'Dios todopoderoso, que nos has alimentado con el pan de la vida...'
                ],
                'traditions' => [
                    'espana' => 'Cabalgata de Reyes, Rosca de Reyes',
                    'general' => 'Bendición de las casas, Adoración de los Magos'
                ],
                'special_observances' => [
                    'festivo_nacional' => true,
                    'costumbres' => 'Intercambio de regalos, comidas familiares',
                    'notas_liturgicas' => 'Se puede celebrar el domingo más próximo'
                ],
                'is_holiday' => true,
            ],

            // ===== TIEMPO DE CUARESMA =====
            [
                'date' => '2025-02-14',
                'liturgical_season' => 'Lent',
                'feast_day' => 'Miércoles de Ceniza',
                'celebration_level' => 'weekday',
                'color' => 'purple',
                'description' => 'Inicio de la Cuaresma, tiempo de penitencia y conversión',
                'readings' => [
                    'primera_lectura' => 'Joel 2:12-18',
                    'salmo' => 'Salmo 51:3-4, 5-6ab, 12-13, 14, 17',
                    'segunda_lectura' => '2 Corintios 5:20-6:2',
                    'evangelio' => 'Mateo 6:1-6, 16-18'
                ],
                'prayers' => [
                    'colecta' => 'Concédenos, Señor, comenzar con este ayuno cuaresmal...',
                    'oracion_sobre_las_ofrendas' => 'Acepta, Señor, estas ofrendas...',
                    'poscomunion' => 'Que este sacramento, Señor, nos fortalezca...'
                ],
                'traditions' => [
                    'general' => 'Imposición de cenizas, ayuno y abstinencia',
                    'espana' => 'Procesiones penitenciales en algunas regiones'
                ],
                'special_observances' => [
                    'ayuno_obligatorio' => true,
                    'abstinencia_obligatoria' => true,
                    'costumbres' => 'Imposición de cenizas en la frente',
                    'notas_liturgicas' => 'Las cenizas provienen de los ramos del año anterior'
                ],
                'is_holiday' => false,
            ],

            [
                'date' => '2025-03-05',
                'liturgical_season' => 'Lent',
                'feast_day' => 'Miércoles de la I Semana de Cuaresma',
                'celebration_level' => 'weekday',
                'color' => 'purple',
                'description' => 'Primera semana de Cuaresma',
                'readings' => [
                    'primera_lectura' => 'Jonás 3:1-10',
                    'salmo' => 'Salmo 51:3-4, 12-13, 18-19',
                    'evangelio' => 'Lucas 11:29-32'
                ],
                'prayers' => [
                    'colecta' => 'Dios todopoderoso, que nos concedes la gracia de la conversión...',
                    'oracion_sobre_las_ofrendas' => 'Acepta, Señor, estas ofrendas...',
                    'poscomunion' => 'Que este sacramento, Señor, nos fortalezca...'
                ],
                'traditions' => [
                    'general' => 'Tiempo de penitencia y conversión'
                ],
                'special_observances' => [
                    'tiempo_cuaresmal' => true
                ],
                'is_holiday' => false,
            ],

            // ===== TIEMPO PASCUAL =====
            [
                'date' => '2025-04-20',
                'liturgical_season' => 'Easter',
                'feast_day' => 'Domingo de Resurrección',
                'celebration_level' => 'solemnity',
                'color' => 'white',
                'description' => 'Celebración de la Resurrección de Jesucristo',
                'readings' => [
                    'primera_lectura' => 'Hechos 10:34a, 37-43',
                    'salmo' => 'Salmo 118:1-2, 16-17, 22-23',
                    'segunda_lectura' => 'Colosenses 3:1-4',
                    'evangelio' => 'Juan 20:1-9'
                ],
                'prayers' => [
                    'colecta' => 'Dios todopoderoso, que en este día nos has abierto las puertas de la vida...',
                    'oracion_sobre_las_ofrendas' => 'Acepta, Señor, las ofrendas de tu pueblo...',
                    'poscomunion' => 'Que este sacramento pascual, Señor...'
                ],
                'traditions' => [
                    'general' => 'Vigilia Pascual, Misa del día',
                    'espana' => 'Procesiones de Resurrección, comidas familiares'
                ],
                'special_observances' => [
                    'fiesta_principal' => true,
                    'costumbres' => 'Huevos de Pascua, comidas festivas',
                    'notas_liturgicas' => 'Se celebra la Vigilia Pascual la noche anterior'
                ],
                'is_holiday' => true,
            ],

            [
                'date' => '2025-05-29',
                'liturgical_season' => 'Easter',
                'feast_day' => 'Ascensión del Señor',
                'celebration_level' => 'solemnity',
                'color' => 'white',
                'description' => 'Celebración de la Ascensión de Jesucristo al cielo',
                'readings' => [
                    'primera_lectura' => 'Hechos 1:1-11',
                    'salmo' => 'Salmo 47:2-3, 6-7, 8-9',
                    'segunda_lectura' => 'Efesios 1:17-23',
                    'evangelio' => 'Marcos 16:15-20'
                ],
                'prayers' => [
                    'colecta' => 'Dios todopoderoso, que en este día has elevado a tu Hijo...',
                    'oracion_sobre_las_ofrendas' => 'Acepta, Señor, las ofrendas de tu pueblo...',
                    'poscomunion' => 'Que este sacramento, Señor, nos eleve...'
                ],
                'traditions' => [
                    'general' => 'Misa solemne, procesiones',
                    'espana' => 'Festivo en algunas comunidades autónomas'
                ],
                'special_observances' => [
                    'festivo_regional' => true,
                    'costumbres' => 'Procesiones, celebraciones al aire libre',
                    'notas_liturgicas' => 'Se puede trasladar al domingo VII de Pascua'
                ],
                'is_holiday' => true,
            ],

            [
                'date' => '2025-06-08',
                'liturgical_season' => 'Easter',
                'feast_day' => 'Pentecostés',
                'celebration_level' => 'solemnity',
                'color' => 'red',
                'description' => 'Celebración de la venida del Espíritu Santo',
                'readings' => [
                    'primera_lectura' => 'Hechos 2:1-11',
                    'salmo' => 'Salmo 104:1, 24, 29-30, 31, 34',
                    'segunda_lectura' => '1 Corintios 12:3b-7, 12-13',
                    'evangelio' => 'Juan 20:19-23'
                ],
                'prayers' => [
                    'colecta' => 'Dios todopoderoso, que en este día has santificado a tu Iglesia...',
                    'oracion_sobre_las_ofrendas' => 'Acepta, Señor, las ofrendas de tu pueblo...',
                    'poscomunion' => 'Que este sacramento, Señor, nos fortalezca...'
                ],
                'traditions' => [
                    'general' => 'Misa solemne, confirmaciones',
                    'espana' => 'Celebraciones especiales en algunas parroquias'
                ],
                'special_observances' => [
                    'cierre_tiempo_pascual' => true,
                    'costumbres' => 'Confirmaciones, celebraciones del Espíritu Santo',
                    'notas_liturgicas' => 'Se usa el color rojo, símbolo del Espíritu Santo'
                ],
                'is_holiday' => true,
            ],

            // ===== TIEMPO ORDINARIO - FIESTAS MARIANAS =====
            [
                'date' => '2025-08-15',
                'liturgical_season' => 'Ordinary Time',
                'feast_day' => 'Asunción de la Virgen María',
                'celebration_level' => 'solemnity',
                'color' => 'white',
                'description' => 'Celebración de la Asunción de María al cielo',
                'readings' => [
                    'primera_lectura' => 'Apocalipsis 11:19a; 12:1-6a, 10ab',
                    'salmo' => 'Salmo 45:10, 11, 12, 16',
                    'segunda_lectura' => '1 Corintios 15:20-27a',
                    'evangelio' => 'Lucas 1:39-56'
                ],
                'prayers' => [
                    'colecta' => 'Dios todopoderoso, que has elevado a la Virgen María...',
                    'oracion_sobre_las_ofrendas' => 'Acepta, Señor, las ofrendas de tu pueblo...',
                    'poscomunion' => 'Que este sacramento, Señor, nos fortalezca...'
                ],
                'traditions' => [
                    'general' => 'Misa solemne, procesiones marianas',
                    'espana' => 'Festivo nacional, grandes celebraciones'
                ],
                'special_observances' => [
                    'festivo_nacional' => true,
                    'costumbres' => 'Procesiones marianas, romerías',
                    'notas_liturgicas' => 'Una de las principales fiestas marianas'
                ],
                'is_holiday' => true,
            ],

            [
                'date' => '2025-11-01',
                'liturgical_season' => 'Ordinary Time',
                'feast_day' => 'Todos los Santos',
                'celebration_level' => 'solemnity',
                'color' => 'white',
                'description' => 'Celebración de todos los santos del cielo',
                'readings' => [
                    'primera_lectura' => 'Apocalipsis 7:2-4, 9-14',
                    'salmo' => 'Salmo 24:1-2, 3-4, 5-6',
                    'segunda_lectura' => '1 Juan 3:1-3',
                    'evangelio' => 'Mateo 5:1-12a'
                ],
                'prayers' => [
                    'colecta' => 'Dios todopoderoso, que nos has concedido celebrar...',
                    'oracion_sobre_las_ofrendas' => 'Acepta, Señor, las ofrendas de tu pueblo...',
                    'poscomunion' => 'Que este sacramento, Señor, nos fortalezca...'
                ],
                'traditions' => [
                    'general' => 'Misa solemne, visitas a cementerios',
                    'espana' => 'Festivo nacional, visitas a cementerios'
                ],
                'special_observances' => [
                    'festivo_nacional' => true,
                    'costumbres' => 'Visitas a cementerios, flores en las tumbas',
                    'notas_liturgicas' => 'Se celebra el día siguiente a Halloween'
                ],
                'is_holiday' => true,
            ],

            [
                'date' => '2025-12-25',
                'liturgical_season' => 'Christmas',
                'feast_day' => 'Natividad del Señor',
                'celebration_level' => 'solemnity',
                'color' => 'white',
                'description' => 'Celebración del nacimiento de Jesucristo',
                'readings' => [
                    'primera_lectura' => 'Isaías 9:1-6',
                    'salmo' => 'Salmo 96:1-2, 2-3, 11-12, 13',
                    'segunda_lectura' => 'Tito 2:11-14',
                    'evangelio' => 'Lucas 2:1-14'
                ],
                'prayers' => [
                    'colecta' => 'Dios todopoderoso, que has iluminado esta noche santa...',
                    'oracion_sobre_las_ofrendas' => 'Acepta, Señor, las ofrendas de tu pueblo...',
                    'poscomunion' => 'Que este sacramento, Señor, nos fortalezca...'
                ],
                'traditions' => [
                    'general' => 'Misa de Medianoche, Misa del día',
                    'espana' => 'Festivo nacional, comidas familiares, regalos'
                ],
                'special_observances' => [
                    'festivo_nacional' => true,
                    'costumbres' => 'Árbol de Navidad, belenes, comidas familiares',
                    'notas_liturgicas' => 'Se celebra la Misa de Medianoche la noche anterior'
                ],
                'is_holiday' => true,
            ],

            // ===== MEMORIAS DE SANTOS =====
            [
                'date' => '2025-01-20',
                'liturgical_season' => 'Ordinary Time',
                'feast_day' => 'San Fabián, Papa y Mártir',
                'saint_id' => $defaultSaint ? $defaultSaint->id : null,
                'celebration_level' => 'memorial',
                'color' => 'red',
                'description' => 'Memoria de San Fabián, Papa y Mártir del siglo III',
                'readings' => [
                    'primera_lectura' => 'Hebreos 7:1-3, 15-17',
                    'salmo' => 'Salmo 110:1, 2, 3, 4',
                    'evangelio' => 'Marcos 3:1-6'
                ],
                'prayers' => [
                    'colecta' => 'Dios todopoderoso, que has elegido a San Fabián...',
                    'oracion_sobre_las_ofrendas' => 'Acepta, Señor, las ofrendas...',
                    'poscomunion' => 'Que este sacramento, Señor, nos fortalezca...'
                ],
                'traditions' => [
                    'general' => 'Memoria del santo'
                ],
                'special_observances' => [
                    'memoria_santo' => true
                ],
                'is_holiday' => false,
            ],

            [
                'date' => '2025-02-14',
                'liturgical_season' => 'Ordinary Time',
                'feast_day' => 'Santos Cirilo y Metodio',
                'saint_id' => $defaultSaint ? $defaultSaint->id : null,
                'celebration_level' => 'memorial',
                'color' => 'white',
                'description' => 'Memoria de los Santos Cirilo y Metodio, apóstoles de los eslavos',
                'readings' => [
                    'primera_lectura' => 'Hechos 13:46-49',
                    'salmo' => 'Salmo 117:1, 2',
                    'evangelio' => 'Marcos 16:15-20'
                ],
                'prayers' => [
                    'colecta' => 'Dios todopoderoso, que has enviado a los Santos Cirilo y Metodio...',
                    'oracion_sobre_las_ofrendas' => 'Acepta, Señor, las ofrendas...',
                    'poscomunion' => 'Que este sacramento, Señor, nos fortalezca...'
                ],
                'traditions' => [
                    'general' => 'Memoria de los santos'
                ],
                'special_observances' => [
                    'memoria_santos' => true
                ],
                'is_holiday' => false,
            ],

            // ===== FERIAS ORDINARIAS =====
            [
                'date' => '2025-01-15',
                'liturgical_season' => 'Ordinary Time',
                'feast_day' => 'Miércoles de la I Semana del Tiempo Ordinario',
                'celebration_level' => 'weekday',
                'color' => 'green',
                'description' => 'Feria del Tiempo Ordinario',
                'readings' => [
                    'primera_lectura' => 'Hebreos 2:14-18',
                    'salmo' => 'Salmo 105:1-2, 3-4, 6-7, 8-9',
                    'evangelio' => 'Marcos 1:29-39'
                ],
                'prayers' => [
                    'colecta' => 'Dios todopoderoso, que gobiernas el cielo y la tierra...',
                    'oracion_sobre_las_ofrendas' => 'Acepta, Señor, las ofrendas...',
                    'poscomunion' => 'Que este sacramento, Señor, nos fortalezca...'
                ],
                'traditions' => [
                    'general' => 'Feria ordinaria'
                ],
                'special_observances' => [
                    'tiempo_ordinario' => true
                ],
                'is_holiday' => false,
            ],

            [
                'date' => '2025-06-15',
                'liturgical_season' => 'Ordinary Time',
                'feast_day' => 'Domingo XI del Tiempo Ordinario',
                'celebration_level' => 'weekday',
                'color' => 'green',
                'description' => 'Domingo del Tiempo Ordinario',
                'readings' => [
                    'primera_lectura' => 'Ezequiel 17:22-24',
                    'salmo' => 'Salmo 92:2-3, 13-14, 15-16',
                    'segunda_lectura' => '2 Corintios 5:6-10',
                    'evangelio' => 'Marcos 4:26-34'
                ],
                'prayers' => [
                    'colecta' => 'Dios todopoderoso, que gobiernas el cielo y la tierra...',
                    'oracion_sobre_las_ofrendas' => 'Acepta, Señor, las ofrendas...',
                    'poscomunion' => 'Que este sacramento, Señor, nos fortalezca...'
                ],
                'traditions' => [
                    'general' => 'Domingo ordinario'
                ],
                'special_observances' => [
                    'domingo_ordinario' => true
                ],
                'is_holiday' => false,
            ],
        ];

        $createdCount = 0;
        $updatedCount = 0;

        foreach ($liturgicalEvents as $eventData) {
            $event = LiturgicalCalendar::updateOrCreate(
                [
                    'date' => $eventData['date'],
                ],
                $eventData
            );

            if ($event->wasRecentlyCreated) {
                $createdCount++;
            } else {
                $updatedCount++;
            }
        }

        // Mostrar estadísticas
        $this->command->info("✅ Eventos creados: {$createdCount}");
        $this->command->info("🔄 Eventos actualizados: {$updatedCount}");
        $this->command->info("📊 Total de eventos: " . LiturgicalCalendar::count());

        // Mostrar resumen por temporada litúrgica
        $this->command->info("\n📋 Resumen por temporada litúrgica:");
        $seasons = LiturgicalCalendar::all()->groupBy('liturgical_season');
        foreach ($seasons as $season => $events) {
            $this->command->info("  {$season}: {$events->count()} eventos");
        }

        // Mostrar resumen por nivel de celebración
        $this->command->info("\n🎭 Resumen por nivel de celebración:");
        $levels = LiturgicalCalendar::all()->groupBy('celebration_level');
        foreach ($levels as $level => $events) {
            $this->command->info("  {$level}: {$events->count()} eventos");
        }

        // Mostrar resumen por color litúrgico
        $this->command->info("\n🎨 Resumen por color litúrgico:");
        $colors = LiturgicalCalendar::all()->groupBy('color');
        foreach ($colors as $color => $events) {
            $this->command->info("  {$color}: {$events->count()} eventos");
        }

        // Mostrar algunos eventos destacados
        $this->command->info("\n🔬 Eventos destacados:");
        $highlightedEvents = LiturgicalCalendar::where('celebration_level', 'solemnity')->take(3)->get();
        foreach ($highlightedEvents as $event) {
            $this->command->info("  🎉 {$event->feast_day}");
            $this->command->info("     📅 {$event->formatted_date} ({$event->day_of_week})");
            $this->command->info("     🎭 {$event->celebration_level_label}");
            $this->command->info("     🎨 {$event->color_label}");
            $this->command->info("     📖 {$event->getReadingsCount()} lecturas");
            $this->command->info("     ---");
        }

        $this->command->info("\n🎯 Seeder de LiturgicalCalendar completado exitosamente!");
    }
}