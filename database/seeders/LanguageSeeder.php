<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Language;

class LanguageSeeder extends Seeder
{
    public function run()
    {
        $languages = [
            [
                'language' => 'Español',
                'slug' => 'espanol',
                'native_name' => 'español',
                'iso_639_1' => 'es',
                'iso_639_2' => 'spa',
                'rtl' => false,
            ],
            [
                'language' => 'Catalán',
                'slug' => 'catalan',
                'native_name' => 'català',
                'iso_639_1' => 'ca',
                'iso_639_2' => 'cat',
                'rtl' => false,
            ],
            [
                'language' => 'Gallego',
                'slug' => 'gallego',
                'native_name' => 'galego',
                'iso_639_1' => 'gl',
                'iso_639_2' => 'glg',
                'rtl' => false,
            ],
            [
                'language' => 'Vasco',
                'slug' => 'vasco',
                'native_name' => 'euskara',
                'iso_639_1' => 'eu',
                'iso_639_2' => 'eus',
                'rtl' => false,
            ],
            [
                'language' => 'Francés',
                'slug' => 'frances',
                'native_name' => 'français',
                'iso_639_1' => 'fr',
                'iso_639_2' => 'fra',
                'rtl' => false,
            ],
            [
                'language' => 'Portugués',
                'slug' => 'portugues',
                'native_name' => 'português',
                'iso_639_1' => 'pt',
                'iso_639_2' => 'por',
                'rtl' => false,
            ],
            [
                'language' => 'Quechua',
                'slug' => 'quechua',
                'native_name' => 'Runa Simi',
                'iso_639_1' => 'qu',
                'iso_639_2' => 'que',
                'rtl' => false,
            ],
            [
                'language' => 'Guaraní',
                'slug' => 'guarani',
                'native_name' => 'Avañe\'ẽ',
                'iso_639_1' => 'gn',
                'iso_639_2' => 'grn',
                'rtl' => false,
            ],
            [
                'language' => 'Aymara',
                'slug' => 'aymara',
                'native_name' => 'Aymar aru',
                'iso_639_1' => 'ay',
                'iso_639_2' => 'aym',
                'rtl' => false,
            ],
            [
                'language' => 'Náhuatl',
                'slug' => 'nahuatl',
                'native_name' => 'Nāhuatl',
                'iso_639_1' => '', 
                'iso_639_2' => 'nah',
                'rtl' => false,
            ],
        ];

        foreach ($languages as $lang) {
            Language::firstOrCreate(['iso_639_1' => $lang['iso_639_1']], $lang);
        }
    }
}
