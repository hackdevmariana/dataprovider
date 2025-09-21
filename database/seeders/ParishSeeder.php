<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Parish;
use App\Models\Bishopric;

class ParishSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener los obispados para asignar las parroquias
        $bishoprics = Bishopric::all();
        
        if ($bishoprics->isEmpty()) {
            $this->command->warn('No hay obispados disponibles. Ejecuta primero BishopricSeeder.');
            return;
        }

        $parishes = [
            // Parroquias de Madrid
            [
                'name' => 'Parroquia de San Isidro',
                'slug' => 'parroquia-san-isidro-madrid',
                'description' => 'Parroquia dedicada a San Isidro Labrador, patrón de Madrid.',
                'address' => 'Calle Toledo, 37',
                'city' => 'Madrid',
                'province' => 'Madrid',
                'postal_code' => '28005',
                'latitude' => 40.4154,
                'longitude' => -3.7074,
                'bishopric_id' => $bishoprics->where('slug', 'archidiocesis-madrid')->first()->id,
                'patron_saint' => 'San Isidro Labrador',
                'foundation_date' => '1620-05-15',
                'current_priest' => 'P. José María García',
                'phone' => '+34 915 36 00 00',
                'email' => 'sanisidro@archimadrid.es',
                'website' => 'https://www.sanisidromadrid.es',
                'mass_schedules' => [
                    'weekdays' => ['08:00', '19:30'],
                    'saturday' => ['09:00', '19:30'],
                    'sunday' => ['09:00', '11:00', '12:30', '19:30']
                ],
                'confession_schedules' => [
                    'weekdays' => ['19:00-19:30'],
                    'saturday' => ['18:30-19:30'],
                    'sunday' => ['18:30-19:30']
                ],
                'services' => ['Bautismos', 'Matrimonios', 'Comuniones', 'Confesiones', 'Catequesis'],
                'is_active' => true,
                'is_cathedral' => false,
                'capacity' => 800,
                'notes' => 'Parroquia histórica en el centro de Madrid.',
            ],
            [
                'name' => 'Catedral de la Almudena',
                'slug' => 'catedral-almudena-madrid',
                'description' => 'Catedral de Santa María la Real de la Almudena, sede de la archidiócesis de Madrid.',
                'address' => 'Calle Bailén, 10',
                'city' => 'Madrid',
                'province' => 'Madrid',
                'postal_code' => '28013',
                'latitude' => 40.4159,
                'longitude' => -3.7142,
                'bishopric_id' => $bishoprics->where('slug', 'archidiocesis-madrid')->first()->id,
                'patron_saint' => 'Virgen de la Almudena',
                'foundation_date' => '1993-06-15',
                'current_priest' => 'P. José Luis Montes',
                'phone' => '+34 915 42 22 00',
                'email' => 'catedral@archimadrid.es',
                'website' => 'https://www.catedraldelaalmudena.es',
                'mass_schedules' => [
                    'weekdays' => ['09:00', '19:00'],
                    'saturday' => ['09:00', '19:00'],
                    'sunday' => ['09:00', '11:00', '12:30', '19:00']
                ],
                'confession_schedules' => [
                    'weekdays' => ['18:30-19:00'],
                    'saturday' => ['18:30-19:00'],
                    'sunday' => ['18:30-19:00']
                ],
                'services' => ['Bautismos', 'Matrimonios', 'Comuniones', 'Confesiones', 'Catequesis', 'Consagraciones'],
                'is_active' => true,
                'is_cathedral' => true,
                'capacity' => 2000,
                'notes' => 'Catedral de la archidiócesis de Madrid.',
            ],
            // Parroquias de Barcelona
            [
                'name' => 'Basílica de Santa María del Mar',
                'slug' => 'basilica-santa-maria-mar-barcelona',
                'description' => 'Basílica gótica dedicada a Santa María del Mar en el barrio de la Ribera.',
                'address' => 'Plaça de Santa Maria, 1',
                'city' => 'Barcelona',
                'province' => 'Barcelona',
                'postal_code' => '08003',
                'latitude' => 41.3840,
                'longitude' => 2.1825,
                'bishopric_id' => $bishoprics->where('slug', 'diocesis-barcelona')->first()->id,
                'patron_saint' => 'Santa María del Mar',
                'foundation_date' => '1329-03-25',
                'current_priest' => 'P. Joan Martí',
                'phone' => '+34 933 10 23 90',
                'email' => 'info@santamariadelmar.cat',
                'website' => 'https://www.santamariadelmar.cat',
                'mass_schedules' => [
                    'weekdays' => ['08:30', '19:30'],
                    'saturday' => ['09:00', '19:30'],
                    'sunday' => ['09:00', '11:00', '12:30', '19:30']
                ],
                'confession_schedules' => [
                    'weekdays' => ['19:00-19:30'],
                    'saturday' => ['18:30-19:30'],
                    'sunday' => ['18:30-19:30']
                ],
                'services' => ['Bautismos', 'Matrimonios', 'Comuniones', 'Confesiones', 'Catequesis'],
                'is_active' => true,
                'is_cathedral' => false,
                'capacity' => 1200,
                'notes' => 'Basílica gótica histórica de Barcelona.',
            ],
            // Parroquias de Sevilla
            [
                'name' => 'Parroquia de San Lorenzo',
                'slug' => 'parroquia-san-lorenzo-sevilla',
                'description' => 'Parroquia barroca dedicada a San Lorenzo en el centro histórico de Sevilla.',
                'address' => 'Calle San Lorenzo, 1',
                'city' => 'Sevilla',
                'province' => 'Sevilla',
                'postal_code' => '41002',
                'latitude' => 37.3925,
                'longitude' => -5.9922,
                'bishopric_id' => $bishoprics->where('slug', 'diocesis-sevilla')->first()->id,
                'patron_saint' => 'San Lorenzo',
                'foundation_date' => '1699-08-10',
                'current_priest' => 'P. Antonio Ruiz',
                'phone' => '+34 954 22 00 00',
                'email' => 'sanlorenzo@archisevilla.org',
                'website' => 'https://www.sanlorenzosevilla.es',
                'mass_schedules' => [
                    'weekdays' => ['08:30', '19:30'],
                    'saturday' => ['09:00', '19:30'],
                    'sunday' => ['09:00', '11:00', '12:30', '19:30']
                ],
                'confession_schedules' => [
                    'weekdays' => ['19:00-19:30'],
                    'saturday' => ['18:30-19:30'],
                    'sunday' => ['18:30-19:30']
                ],
                'services' => ['Bautismos', 'Matrimonios', 'Comuniones', 'Confesiones', 'Catequesis'],
                'is_active' => true,
                'is_cathedral' => false,
                'capacity' => 600,
                'notes' => 'Parroquia barroca en el centro histórico de Sevilla.',
            ],
            // Parroquias de Valencia
            [
                'name' => 'Parroquia de San Nicolás',
                'slug' => 'parroquia-san-nicolas-valencia',
                'description' => 'Parroquia gótica con frescos barrocos dedicada a San Nicolás de Bari.',
                'address' => 'Calle Caballeros, 35',
                'city' => 'Valencia',
                'province' => 'Valencia',
                'postal_code' => '46001',
                'latitude' => 39.4749,
                'longitude' => -0.3756,
                'bishopric_id' => $bishoprics->where('slug', 'diocesis-valencia')->first()->id,
                'patron_saint' => 'San Nicolás de Bari',
                'foundation_date' => '1242-01-01',
                'current_priest' => 'P. Vicente Ferrer',
                'phone' => '+34 963 91 00 00',
                'email' => 'sannicolas@archivalencia.org',
                'website' => 'https://www.sannicolasvalencia.es',
                'mass_schedules' => [
                    'weekdays' => ['08:30', '19:30'],
                    'saturday' => ['09:00', '19:30'],
                    'sunday' => ['09:00', '11:00', '12:30', '19:30']
                ],
                'confession_schedules' => [
                    'weekdays' => ['19:00-19:30'],
                    'saturday' => ['18:30-19:30'],
                    'sunday' => ['18:30-19:30']
                ],
                'services' => ['Bautismos', 'Matrimonios', 'Comuniones', 'Confesiones', 'Catequesis'],
                'is_active' => true,
                'is_cathedral' => false,
                'capacity' => 500,
                'notes' => 'Parroquia gótica con frescos barrocos únicos.',
            ],
            // Parroquias de Bilbao
            [
                'name' => 'Parroquia de San Antón',
                'slug' => 'parroquia-san-anton-bilbao',
                'description' => 'Parroquia dedicada a San Antón en el casco viejo de Bilbao.',
                'address' => 'Calle Ronda, 24',
                'city' => 'Bilbao',
                'province' => 'Vizcaya',
                'postal_code' => '48005',
                'latitude' => 43.2609,
                'longitude' => -2.9234,
                'bishopric_id' => $bishoprics->where('slug', 'diocesis-bilbao')->first()->id,
                'patron_saint' => 'San Antón',
                'foundation_date' => '1506-01-17',
                'current_priest' => 'P. Iñaki Urrutia',
                'phone' => '+34 944 15 00 00',
                'email' => 'sananton@diocesisbilbao.org',
                'website' => 'https://www.sanantonbilbao.es',
                'mass_schedules' => [
                    'weekdays' => ['08:30', '19:30'],
                    'saturday' => ['09:00', '19:30'],
                    'sunday' => ['09:00', '11:00', '12:30', '19:30']
                ],
                'confession_schedules' => [
                    'weekdays' => ['19:00-19:30'],
                    'saturday' => ['18:30-19:30'],
                    'sunday' => ['18:30-19:30']
                ],
                'services' => ['Bautismos', 'Matrimonios', 'Comuniones', 'Confesiones', 'Catequesis'],
                'is_active' => true,
                'is_cathedral' => false,
                'capacity' => 400,
                'notes' => 'Parroquia histórica del casco viejo de Bilbao.',
            ],
        ];

        foreach ($parishes as $parishData) {
            Parish::updateOrCreate(
                ['slug' => $parishData['slug']],
                $parishData
            );
        }
        
        $this->command->info('✅ Parroquias creadas/actualizadas: ' . count($parishes));
    }
}
