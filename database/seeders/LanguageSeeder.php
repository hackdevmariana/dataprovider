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
        ];

        foreach ($languages as $lang) {
            Language::firstOrCreate(['iso_639_1' => $lang['iso_639_1']], $lang);
        }
    }
}
