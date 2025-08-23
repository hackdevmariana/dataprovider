<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CalendarHoliday;
use App\Models\Municipality;
use Carbon\Carbon;

class CalendarHolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener municipios disponibles
        $municipalities = Municipality::all();
        
        // Festivos nacionales de España 2024
        $nationalHolidays2024 = [
            ['name' => 'Año Nuevo', 'date' => '2024-01-01', 'slug' => 'ano-nuevo-2024'],
            ['name' => 'Epifanía del Señor (Reyes Magos)', 'date' => '2024-01-06', 'slug' => 'reyes-magos-2024'],
            ['name' => 'Viernes Santo', 'date' => '2024-03-29', 'slug' => 'viernes-santo-2024'],
            ['name' => 'Lunes de Pascua', 'date' => '2024-04-01', 'slug' => 'lunes-pascua-2024'],
            ['name' => 'Día del Trabajo', 'date' => '2024-05-01', 'slug' => 'dia-trabajo-2024'],
            ['name' => 'Asunción de la Virgen', 'date' => '2024-08-15', 'slug' => 'asuncion-virgen-2024'],
            ['name' => 'Día de la Hispanidad', 'date' => '2024-10-12', 'slug' => 'dia-hispanidad-2024'],
            ['name' => 'Todos los Santos', 'date' => '2024-11-01', 'slug' => 'todos-santos-2024'],
            ['name' => 'Día de la Constitución', 'date' => '2024-12-06', 'slug' => 'dia-constitucion-2024'],
            ['name' => 'Inmaculada Concepción', 'date' => '2024-12-08', 'slug' => 'inmaculada-concepcion-2024'],
            ['name' => 'Navidad', 'date' => '2024-12-25', 'slug' => 'navidad-2024'],
        ];

        // Festivos nacionales de España 2025
        $nationalHolidays2025 = [
            ['name' => 'Año Nuevo', 'date' => '2025-01-01', 'slug' => 'ano-nuevo-2025'],
            ['name' => 'Epifanía del Señor (Reyes Magos)', 'date' => '2025-01-06', 'slug' => 'reyes-magos-2025'],
            ['name' => 'Viernes Santo', 'date' => '2025-04-18', 'slug' => 'viernes-santo-2025'],
            ['name' => 'Lunes de Pascua', 'date' => '2025-04-21', 'slug' => 'lunes-pascua-2025'],
            ['name' => 'Día del Trabajo', 'date' => '2025-05-01', 'slug' => 'dia-trabajo-2025'],
            ['name' => 'Asunción de la Virgen', 'date' => '2025-08-15', 'slug' => 'asuncion-virgen-2025'],
            ['name' => 'Día de la Hispanidad', 'date' => '2025-10-12', 'slug' => 'dia-hispanidad-2025'],
            ['name' => 'Todos los Santos', 'date' => '2025-11-01', 'slug' => 'todos-santos-2025'],
            ['name' => 'Día de la Constitución', 'date' => '2025-12-06', 'slug' => 'dia-constitucion-2025'],
            ['name' => 'Inmaculada Concepción', 'date' => '2025-12-08', 'slug' => 'inmaculada-concepcion-2025'],
            ['name' => 'Navidad', 'date' => '2025-12-25', 'slug' => 'navidad-2025'],
        ];

        // Festivos regionales de Madrid
        $madridHolidays = [
            ['name' => 'San Isidro Labrador', 'date' => '2024-05-15', 'slug' => 'san-isidro-2024', 'municipality_id' => 1],
            ['name' => 'San Isidro Labrador', 'date' => '2025-05-15', 'slug' => 'san-isidro-2025', 'municipality_id' => 1],
            ['name' => 'Día de la Comunidad de Madrid', 'date' => '2024-05-02', 'slug' => 'dia-comunidad-madrid-2024', 'municipality_id' => 1],
            ['name' => 'Día de la Comunidad de Madrid', 'date' => '2025-05-02', 'slug' => 'dia-comunidad-madrid-2025', 'municipality_id' => 1],
        ];

        // Festivos regionales de Cataluña
        $catalunyaHolidays = [
            ['name' => 'San Jorge (Diada de Sant Jordi)', 'date' => '2024-04-23', 'slug' => 'san-jorge-2024', 'municipality_id' => 4],
            ['name' => 'San Jorge (Diada de Sant Jordi)', 'date' => '2025-04-23', 'slug' => 'san-jorge-2025', 'municipality_id' => 4],
            ['name' => 'Día Nacional de Cataluña', 'date' => '2024-09-11', 'slug' => 'diada-catalunya-2024', 'municipality_id' => 4],
            ['name' => 'Día Nacional de Cataluña', 'date' => '2025-09-11', 'slug' => 'diada-catalunya-2025', 'municipality_id' => 4],
            ['name' => 'San Jorge (Diada de Sant Jordi)', 'date' => '2024-04-23', 'slug' => 'san-jorge-girona-2024', 'municipality_id' => 5],
            ['name' => 'San Jorge (Diada de Sant Jordi)', 'date' => '2025-04-23', 'slug' => 'san-jorge-girona-2025', 'municipality_id' => 5],
            ['name' => 'Día Nacional de Cataluña', 'date' => '2024-09-11', 'slug' => 'diada-catalunya-girona-2024', 'municipality_id' => 5],
            ['name' => 'Día Nacional de Cataluña', 'date' => '2025-09-11', 'slug' => 'diada-catalunya-girona-2025', 'municipality_id' => 5],
        ];

        // Festivos locales específicos
        $localHolidays = [
            // Alcalá de Henares
            ['name' => 'Santos Justo y Pastor', 'date' => '2024-08-06', 'slug' => 'santos-justo-pastor-alcala-2024', 'municipality_id' => 2],
            ['name' => 'Santos Justo y Pastor', 'date' => '2025-08-06', 'slug' => 'santos-justo-pastor-alcala-2025', 'municipality_id' => 2],
            
            // Móstoles
            ['name' => 'Virgen de los Santos', 'date' => '2024-09-08', 'slug' => 'virgen-santos-mostoles-2024', 'municipality_id' => 3],
            ['name' => 'Virgen de los Santos', 'date' => '2025-09-08', 'slug' => 'virgen-santos-mostoles-2025', 'municipality_id' => 3],
        ];

        // Festivos especiales y culturales
        $specialHolidays = [
            ['name' => 'Día de San Valentín', 'date' => '2024-02-14', 'slug' => 'san-valentin-2024'],
            ['name' => 'Día de San Valentín', 'date' => '2025-02-14', 'slug' => 'san-valentin-2025'],
            ['name' => 'Día de la Madre', 'date' => '2024-05-05', 'slug' => 'dia-madre-2024'],
            ['name' => 'Día de la Madre', 'date' => '2025-05-05', 'slug' => 'dia-madre-2025'],
            ['name' => 'Día del Padre', 'date' => '2024-03-19', 'slug' => 'dia-padre-2024'],
            ['name' => 'Día del Padre', 'date' => '2025-03-19', 'slug' => 'dia-padre-2025'],
            ['name' => 'Halloween', 'date' => '2024-10-31', 'slug' => 'halloween-2024'],
            ['name' => 'Halloween', 'date' => '2025-10-31', 'slug' => 'halloween-2025'],
            ['name' => 'Nochevieja', 'date' => '2024-12-31', 'slug' => 'nochevieja-2024'],
            ['name' => 'Nochevieja', 'date' => '2025-12-31', 'slug' => 'nochevieja-2025'],
        ];

        // Festivos ambientales y sostenibilidad
        $environmentalHolidays = [
            ['name' => 'Día Mundial del Medio Ambiente', 'date' => '2024-06-05', 'slug' => 'dia-medio-ambiente-2024'],
            ['name' => 'Día Mundial del Medio Ambiente', 'date' => '2025-06-05', 'slug' => 'dia-medio-ambiente-2025'],
            ['name' => 'Día Mundial de la Tierra', 'date' => '2024-04-22', 'slug' => 'dia-tierra-2024'],
            ['name' => 'Día Mundial de la Tierra', 'date' => '2025-04-22', 'slug' => 'dia-tierra-2025'],
            ['name' => 'Día Mundial del Agua', 'date' => '2024-03-22', 'slug' => 'dia-agua-2024'],
            ['name' => 'Día Mundial del Agua', 'date' => '2025-03-22', 'slug' => 'dia-agua-2025'],
            ['name' => 'Día Mundial de la Energía', 'date' => '2024-10-14', 'slug' => 'dia-energia-2024'],
            ['name' => 'Día Mundial de la Energía', 'date' => '2025-10-14', 'slug' => 'dia-energia-2025'],
        ];

        // Combinar todos los festivos
        $allHolidays = array_merge(
            $nationalHolidays2024,
            $nationalHolidays2025,
            $madridHolidays,
            $catalunyaHolidays,
            $localHolidays,
            $specialHolidays,
            $environmentalHolidays
        );

        // Crear los festivos
        foreach ($allHolidays as $holidayData) {
            CalendarHoliday::create($holidayData);
        }

        $this->command->info('✅ Se han creado ' . count($allHolidays) . ' festivos en el calendario.');
        $this->command->info('📅 Festivos nacionales, regionales, locales y especiales incluidos.');
        $this->command->info('🌍 Festivos ambientales y de sostenibilidad agregados.');
        $this->command->info('🏛️ Festivos específicos por municipio configurados.');
    }
}
