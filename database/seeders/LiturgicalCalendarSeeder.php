<?php

namespace Database\Seeders;

use App\Models\LiturgicalCalendar;
use Illuminate\Database\Seeder;

class LiturgicalCalendarSeeder extends Seeder
{
    public function run(): void
    {
        $events = [
            [
                'date' => '2025-01-06',
                'event_name' => 'Epifanía del Señor',
                'liturgical_season' => 'Christmas',
                'celebration_type' => 'Solemnity',
                'color' => 'white',
                'rank' => 'Solemnity',
                'is_holy_day' => true,
                'is_optional' => false,
                'country_specific' => 'ES',
                'description' => 'Celebración de la manifestación de Cristo a los Magos',
                'readings' => json_encode([
                    'first_reading' => 'Isaías 60:1-6',
                    'psalm' => 'Salmo 72:1-2, 7-8, 10-11, 12-13',
                    'second_reading' => 'Efesios 3:2-3a, 5-6',
                    'gospel' => 'Mateo 2:1-12'
                ]),
                'prayers' => json_encode([
                    'collect' => 'Dios todopoderoso, que en este día revelaste a tu Hijo a las naciones por medio de la estrella...',
                    'prayer_over_offerings' => 'Acepta, Señor, las ofrendas de tu Iglesia...',
                    'post_communion' => 'Dios todopoderoso, que nos has alimentado con el pan de la vida...'
                ]),
                'traditions' => json_encode([
                    'spain' => 'Cabalgata de Reyes, Rosca de Reyes',
                    'general' => 'Bendición de las casas, Adoración de los Magos'
                ]),
                'additional_info' => json_encode([
                    'notes' => 'En España es festivo nacional',
                    'customs' => 'Intercambio de regalos, comidas familiares',
                    'liturgical_notes' => 'Se puede celebrar el domingo más próximo'
                ]),
            ],
            [
                'date' => '2025-02-14',
                'event_name' => 'Miércoles de Ceniza',
                'liturgical_season' => 'Lent',
                'celebration_type' => 'Fast',
                'color' => 'purple',
                'rank' => 'Ash Wednesday',
                'is_holy_day' => false,
                'is_optional' => false,
                'country_specific' => null,
                'description' => 'Inicio de la Cuaresma, tiempo de penitencia y conversión',
                'readings' => json_encode([
                    'first_reading' => 'Joel 2:12-18',
                    'psalm' => 'Salmo 51:3-4, 5-6ab, 12-13, 14, 17',
                    'second_reading' => '2 Corintios 5:20-6:2',
                    'gospel' => 'Mateo 6:1-6, 16-18'
                ]),
                'prayers' => json_encode([
                    'collect' => 'Concédenos, Señor, comenzar con este ayuno cuaresmal...',
                    'prayer_over_offerings' => 'Acepta, Señor, estas ofrendas...',
                    'post_communion' => 'Que este sacramento, Señor, nos fortalezca...'
                ]),
                'traditions' => json_encode([
                    'general' => 'Imposición de cenizas, ayuno y abstinencia',
                    'spain' => 'Procesiones penitenciales en algunas regiones'
                ]),
                'additional_info' => json_encode([
                    'notes' => 'Día de ayuno y abstinencia obligatorios',
                    'customs' => 'Imposición de cenizas en la frente',
                    'liturgical_notes' => 'Las cenizas provienen de los ramos del año anterior'
                ]),
            ],
            [
                'date' => '2025-04-20',
                'event_name' => 'Domingo de Resurrección',
                'liturgical_season' => 'Easter',
                'celebration_type' => 'Solemnity',
                'color' => 'white',
                'rank' => 'Easter Sunday',
                'is_holy_day' => true,
                'is_optional' => false,
                'country_specific' => null,
                'description' => 'Celebración de la Resurrección de Jesucristo',
                'readings' => json_encode([
                    'first_reading' => 'Hechos 10:34a, 37-43',
                    'psalm' => 'Salmo 118:1-2, 16-17, 22-23',
                    'second_reading' => 'Colosenses 3:1-4',
                    'gospel' => 'Juan 20:1-9'
                ]),
                'prayers' => json_encode([
                    'collect' => 'Dios todopoderoso, que en este día nos has abierto las puertas de la vida...',
                    'prayer_over_offerings' => 'Acepta, Señor, las ofrendas de tu pueblo...',
                    'post_communion' => 'Que este sacramento pascual, Señor...'
                ]),
                'traditions' => json_encode([
                    'general' => 'Vigilia Pascual, Misa del día',
                    'spain' => 'Procesiones de Resurrección, comidas familiares'
                ]),
                'additional_info' => json_encode([
                    'notes' => 'Fiesta más importante del año litúrgico',
                    'customs' => 'Huevos de Pascua, comidas festivas',
                    'liturgical_notes' => 'Se celebra la Vigilia Pascual la noche anterior'
                ]),
            ],
            [
                'date' => '2025-05-29',
                'event_name' => 'Ascensión del Señor',
                'liturgical_season' => 'Easter',
                'celebration_type' => 'Solemnity',
                'color' => 'white',
                'rank' => 'Solemnity',
                'is_holy_day' => true,
                'is_optional' => false,
                'country_specific' => 'ES',
                'description' => 'Celebración de la Ascensión de Jesucristo al cielo',
                'readings' => json_encode([
                    'first_reading' => 'Hechos 1:1-11',
                    'psalm' => 'Salmo 47:2-3, 6-7, 8-9',
                    'second_reading' => 'Efesios 1:17-23',
                    'gospel' => 'Marcos 16:15-20'
                ]),
                'prayers' => json_encode([
                    'collect' => 'Dios todopoderoso, que en este día has elevado a tu Hijo...',
                    'prayer_over_offerings' => 'Acepta, Señor, las ofrendas de tu pueblo...',
                    'post_communion' => 'Que este sacramento, Señor, nos eleve...'
                ]),
                'traditions' => json_encode([
                    'general' => 'Misa solemne, procesiones',
                    'spain' => 'Festivo en algunas comunidades autónomas'
                ]),
                'additional_info' => json_encode([
                    'notes' => 'En España se celebra el domingo siguiente',
                    'customs' => 'Procesiones, celebraciones al aire libre',
                    'liturgical_notes' => 'Se puede trasladar al domingo VII de Pascua'
                ]),
            ],
            [
                'date' => '2025-06-08',
                'event_name' => 'Pentecostés',
                'liturgical_season' => 'Easter',
                'celebration_type' => 'Solemnity',
                'color' => 'red',
                'rank' => 'Solemnity',
                'is_holy_day' => true,
                'is_optional' => false,
                'country_specific' => null,
                'description' => 'Celebración de la venida del Espíritu Santo',
                'readings' => json_encode([
                    'first_reading' => 'Hechos 2:1-11',
                    'psalm' => 'Salmo 104:1, 24, 29-30, 31, 34',
                    'second_reading' => '1 Corintios 12:3b-7, 12-13',
                    'gospel' => 'Juan 20:19-23'
                ]),
                'prayers' => json_encode([
                    'collect' => 'Dios todopoderoso, que en este día has santificado a tu Iglesia...',
                    'prayer_over_offerings' => 'Acepta, Señor, las ofrendas de tu pueblo...',
                    'post_communion' => 'Que este sacramento, Señor, nos fortalezca...'
                ]),
                'traditions' => json_encode([
                    'general' => 'Misa solemne, confirmaciones',
                    'spain' => 'Celebraciones especiales en algunas parroquias'
                ]),
                'additional_info' => json_encode([
                    'notes' => 'Cierre del tiempo pascual',
                    'customs' => 'Confirmaciones, celebraciones del Espíritu Santo',
                    'liturgical_notes' => 'Se usa el color rojo, símbolo del Espíritu Santo'
                ]),
            ],
            [
                'date' => '2025-08-15',
                'event_name' => 'Asunción de la Virgen María',
                'liturgical_season' => 'Ordinary Time',
                'celebration_type' => 'Solemnity',
                'color' => 'white',
                'rank' => 'Solemnity',
                'is_holy_day' => true,
                'is_optional' => false,
                'country_specific' => 'ES',
                'description' => 'Celebración de la Asunción de María al cielo',
                'readings' => json_encode([
                    'first_reading' => 'Apocalipsis 11:19a; 12:1-6a, 10ab',
                    'psalm' => 'Salmo 45:10, 11, 12, 16',
                    'second_reading' => '1 Corintios 15:20-27a',
                    'gospel' => 'Lucas 1:39-56'
                ]),
                'prayers' => json_encode([
                    'collect' => 'Dios todopoderoso, que has elevado a la Virgen María...',
                    'prayer_over_offerings' => 'Acepta, Señor, las ofrendas de tu pueblo...',
                    'post_communion' => 'Que este sacramento, Señor, nos fortalezca...'
                ]),
                'traditions' => json_encode([
                    'general' => 'Misa solemne, procesiones marianas',
                    'spain' => 'Festivo nacional, grandes celebraciones'
                ]),
                'additional_info' => json_encode([
                    'notes' => 'Festivo nacional en España',
                    'customs' => 'Procesiones marianas, romerías',
                    'liturgical_notes' => 'Una de las principales fiestas marianas'
                ]),
            ],
            [
                'date' => '2025-11-01',
                'event_name' => 'Todos los Santos',
                'liturgical_season' => 'Ordinary Time',
                'celebration_type' => 'Solemnity',
                'color' => 'white',
                'rank' => 'Solemnity',
                'is_holy_day' => true,
                'is_optional' => false,
                'country_specific' => 'ES',
                'description' => 'Celebración de todos los santos del cielo',
                'readings' => json_encode([
                    'first_reading' => 'Apocalipsis 7:2-4, 9-14',
                    'psalm' => 'Salmo 24:1-2, 3-4, 5-6',
                    'second_reading' => '1 Juan 3:1-3',
                    'gospel' => 'Mateo 5:1-12a'
                ]),
                'prayers' => json_encode([
                    'collect' => 'Dios todopoderoso, que nos has concedido celebrar...',
                    'prayer_over_offerings' => 'Acepta, Señor, las ofrendas de tu pueblo...',
                    'post_communion' => 'Que este sacramento, Señor, nos fortalezca...'
                ]),
                'traditions' => json_encode([
                    'general' => 'Misa solemne, visitas a cementerios',
                    'spain' => 'Festivo nacional, visitas a cementerios'
                ]),
                'additional_info' => json_encode([
                    'notes' => 'Festivo nacional en España',
                    'customs' => 'Visitas a cementerios, flores en las tumbas',
                    'liturgical_notes' => 'Se celebra el día siguiente a Halloween'
                ]),
            ],
            [
                'date' => '2025-12-25',
                'event_name' => 'Natividad del Señor',
                'liturgical_season' => 'Christmas',
                'celebration_type' => 'Solemnity',
                'color' => 'white',
                'rank' => 'Christmas',
                'is_holy_day' => true,
                'is_optional' => false,
                'country_specific' => null,
                'description' => 'Celebración del nacimiento de Jesucristo',
                'readings' => json_encode([
                    'first_reading' => 'Isaías 9:1-6',
                    'psalm' => 'Salmo 96:1-2, 2-3, 11-12, 13',
                    'second_reading' => 'Tito 2:11-14',
                    'gospel' => 'Lucas 2:1-14'
                ]),
                'prayers' => json_encode([
                    'collect' => 'Dios todopoderoso, que has iluminado esta noche santa...',
                    'prayer_over_offerings' => 'Acepta, Señor, las ofrendas de tu pueblo...',
                    'post_communion' => 'Que este sacramento, Señor, nos fortalezca...'
                ]),
                'traditions' => json_encode([
                    'general' => 'Misa de Medianoche, Misa del día',
                    'spain' => 'Festivo nacional, comidas familiares, regalos'
                ]),
                'additional_info' => json_encode([
                    'notes' => 'Festivo nacional en España',
                    'customs' => 'Árbol de Navidad, belenes, comidas familiares',
                    'liturgical_notes' => 'Se celebra la Misa de Medianoche la noche anterior'
                ]),
            ],
        ];

        foreach ($events as $event) {
            LiturgicalCalendar::create($event);
        }

        $this->command->info('✅ Creados ' . count($events) . ' eventos del calendario litúrgico');
    }
}


