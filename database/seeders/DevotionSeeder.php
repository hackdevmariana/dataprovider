<?php

namespace Database\Seeders;

use App\Models\Devotion;
use App\Models\CatholicSaint;
use Illuminate\Database\Seeder;

class DevotionSeeder extends Seeder
{
    public function run(): void
    {
        $saints = CatholicSaint::all();

        if ($saints->isEmpty()) {
            $this->command->info('No hay santos católicos disponibles. Ejecuta CatholicSaintSeeder primero.');
            return;
        }

        $devotions = [
            [
                'saint_id' => $saints->random()->id,
                'name' => 'Rosario',
                'description' => 'Oración mariana que consiste en meditar los misterios de la vida de Cristo',
                'prayer_text' => 'Dios te salve, María, llena eres de gracia...',
                'novena_days' => null,
                'special_intentions' => json_encode([
                    'paz_mundial' => 'Por la paz en el mundo',
                    'conversiones' => 'Por las conversiones',
                    'familia' => 'Por las familias'
                ]),
                'miracles' => json_encode([
                    'fatima' => 'Apariciones de Fátima',
                    'lourdes' => 'Milagros de Lourdes'
                ]),
                'origin' => 'Medieval',
                'popularity_level' => 'very_high',
                'practices' => json_encode([
                    'daily' => 'Rezo diario',
                    'sundays' => 'Misterios gloriosos los domingos',
                    'groups' => 'Rezo en grupo'
                ]),
                'traditions' => json_encode([
                    'may' => 'Mes de María',
                    'october' => 'Mes del Rosario'
                ]),
                'is_approved' => true,
            ],
            [
                'saint_id' => $saints->random()->id,
                'name' => 'Divina Misericordia',
                'description' => 'Devoción a la misericordia de Dios revelada a Santa Faustina',
                'prayer_text' => 'Jesús, en ti confío...',
                'novena_days' => 9,
                'special_intentions' => json_encode([
                    'misericordia' => 'Por la misericordia divina',
                    'conversiones' => 'Por las conversiones de los pecadores',
                    'almas' => 'Por las almas del purgatorio'
                ]),
                'miracles' => json_encode([
                    'faustina' => 'Revelaciones a Santa Faustina',
                    'imagen' => 'Imagen de la Divina Misericordia'
                ]),
                'origin' => 'Modern',
                'popularity_level' => 'high',
                'practices' => json_encode([
                    'coronilla' => 'Rezo de la coronilla',
                    'hour' => 'Hora de la misericordia (3 PM)',
                    'feast' => 'Domingo de la Divina Misericordia'
                ]),
                'traditions' => json_encode([
                    'sunday' => 'Domingo después de Pascua',
                    'prayer' => 'Oración de la hora de la misericordia'
                ]),
                'is_approved' => true,
            ],
            [
                'saint_id' => $saints->random()->id,
                'name' => 'Via Crucis',
                'description' => 'Meditación de las 14 estaciones del camino de la cruz',
                'prayer_text' => 'Te adoramos, oh Cristo, y te bendecimos...',
                'novena_days' => null,
                'special_intentions' => json_encode([
                    'pasion' => 'Por la pasión de Cristo',
                    'conversion' => 'Por la conversión de los pecadores',
                    'suffering' => 'Por los que sufren'
                ]),
                'miracles' => json_encode([
                    'jerusalem' => 'Camino original en Jerusalén',
                    'indulgencias' => 'Indulgencias plenarias'
                ]),
                'origin' => 'Medieval',
                'popularity_level' => 'high',
                'practices' => json_encode([
                    'fridays' => 'Viernes de Cuaresma',
                    'stations' => 'Recorrer las 14 estaciones',
                    'meditation' => 'Meditar cada estación'
                ]),
                'traditions' => json_encode([
                    'lent' => 'Especialmente en Cuaresma',
                    'good_friday' => 'Viernes Santo'
                ]),
                'is_approved' => true,
            ],
            [
                'saint_id' => $saints->random()->id,
                'name' => 'Adoración Eucarística',
                'description' => 'Adoración del Santísimo Sacramento expuesto',
                'prayer_text' => 'Bendito sea Dios...',
                'novena_days' => null,
                'special_intentions' => json_encode([
                    'eucharist' => 'Por la Eucaristía',
                    'priests' => 'Por los sacerdotes',
                    'vocations' => 'Por las vocaciones'
                ]),
                'miracles' => json_encode([
                    'eucharistic' => 'Milagros eucarísticos',
                    'apparitions' => 'Apariciones del Sagrado Corazón'
                ]),
                'origin' => 'Medieval',
                'popularity_level' => 'high',
                'practices' => json_encode([
                    'exposition' => 'Exposición del Santísimo',
                    'benediction' => 'Bendición',
                    'procession' => 'Procesión eucarística'
                ]),
                'traditions' => json_encode([
                    'corpus_christi' => 'Fiesta del Corpus Christi',
                    'thursday' => 'Jueves eucarístico'
                ]),
                'is_approved' => true,
            ],
            [
                'saint_id' => $saints->random()->id,
                'name' => 'Novena a la Inmaculada',
                'description' => 'Nueve días de oración a la Inmaculada Concepción',
                'prayer_text' => 'Oh María, concebida sin pecado...',
                'novena_days' => 9,
                'special_intentions' => json_encode([
                    'immaculate' => 'Por la Inmaculada Concepción',
                    'protection' => 'Por la protección de María',
                    'grace' => 'Por las gracias especiales'
                ]),
                'miracles' => json_encode([
                    'lourdes' => 'Apariciones de Lourdes',
                    'medal' => 'Medalla Milagrosa'
                ]),
                'origin' => 'Modern',
                'popularity_level' => 'moderate',
                'practices' => json_encode([
                    'nine_days' => 'Rezo durante 9 días',
                    'december' => 'Especialmente en diciembre',
                    'rosary' => 'Rezo del rosario'
                ]),
                'traditions' => json_encode([
                    'december_8' => 'Fiesta de la Inmaculada',
                    'novena' => 'Novenas marianas'
                ]),
                'is_approved' => true,
            ],
        ];

        foreach ($devotions as $devotion) {
            Devotion::create($devotion);
        }

        $this->command->info('✅ Creadas ' . count($devotions) . ' devociones religiosas');
    }
}
