<?php

namespace Database\Seeders;

use App\Models\Festival;
use Illuminate\Database\Seeder;

class FestivalProgramSeeder extends Seeder
{
    public function run(): void
    {
        $festivals = [
            [
                'name' => 'Festival de Música Clásica de Granada',
                'slug' => 'festival-musica-clasica-granada',
                'description' => 'Festival internacional de música clásica en los monumentos de Granada',
                'month' => 6,
                'usual_days' => '15-30',
                'recurring' => true,
                'location_id' => 1, // Asumiendo que existe
                'logo_url' => 'https://festivalgranada.es/logo.png',
                'color_theme' => '#8B4513',
            ],
            [
                'name' => 'Festival de Jazz de San Sebastián',
                'slug' => 'festival-jazz-san-sebastian',
                'description' => 'Festival de jazz en la hermosa ciudad costera de San Sebastián',
                'month' => 7,
                'usual_days' => '20-26',
                'recurring' => true,
                'location_id' => 1, // Asumiendo que existe
                'logo_url' => 'https://jazzaldia.eus/logo.png',
                'color_theme' => '#1E90FF',
            ],
            [
                'name' => 'Festival de Cine de San Sebastián',
                'slug' => 'festival-cine-san-sebastian',
                'description' => 'Festival internacional de cine con premios y proyecciones',
                'month' => 9,
                'usual_days' => '20-28',
                'recurring' => true,
                'location_id' => 1, // Asumiendo que existe
                'logo_url' => 'https://sansebastianfestival.com/logo.png',
                'color_theme' => '#FF4500',
            ],
            [
                'name' => 'Festival de Teatro Clásico de Mérida',
                'slug' => 'festival-teatro-clasico-merida',
                'description' => 'Festival de teatro clásico en el impresionante Teatro Romano de Mérida',
                'month' => 7,
                'usual_days' => '01-25',
                'recurring' => true,
                'location_id' => 1, // Asumiendo que existe
                'logo_url' => 'https://festivaldemerida.es/logo.png',
                'color_theme' => '#8B0000',
            ],
            [
                'name' => 'Festival de Flamenco de Jerez',
                'slug' => 'festival-flamenco-jerez',
                'description' => 'Festival dedicado al arte flamenco en la cuna del flamenco',
                'month' => 2,
                'usual_days' => '20-02',
                'recurring' => true,
                'location_id' => 1, // Asumiendo que existe
                'logo_url' => 'https://festivaldejerez.es/logo.png',
                'color_theme' => '#FF1493',
            ],
            [
                'name' => 'Festival de Gastronomía de San Sebastián',
                'slug' => 'festival-gastronomia-san-sebastian',
                'description' => 'Festival de alta cocina y gastronomía vasca',
                'month' => 9,
                'usual_days' => '15-22',
                'recurring' => true,
                'location_id' => 1, // Asumiendo que existe
                'logo_url' => 'https://sansebastiangastronomika.com/logo.png',
                'color_theme' => '#32CD32',
            ],
            [
                'name' => 'Festival de Música Electrónica Sónar',
                'slug' => 'festival-musica-electronica-sonar',
                'description' => 'Festival internacional de música electrónica y arte digital',
                'month' => 6,
                'usual_days' => '19-21',
                'recurring' => true,
                'location_id' => 1, // Asumiendo que existe
                'logo_url' => 'https://sonar.es/logo.png',
                'color_theme' => '#FFD700',
            ],
            [
                'name' => 'Festival de Literatura de Hay',
                'slug' => 'festival-literatura-hay',
                'description' => 'Festival de literatura y pensamiento en la ciudad de Segovia',
                'month' => 9,
                'usual_days' => '25-29',
                'recurring' => true,
                'location_id' => 1, // Asumiendo que existe
                'logo_url' => 'https://hayfestival.com/logo.png',
                'color_theme' => '#4B0082',
            ],
            [
                'name' => 'Festival de Cine Independiente de Málaga',
                'slug' => 'festival-cine-independiente-malaga',
                'description' => 'Festival de cine independiente y de autor',
                'month' => 3,
                'usual_days' => '15-24',
                'recurring' => true,
                'location_id' => 1, // Asumiendo que existe
                'logo_url' => 'https://festivaldemalaga.com/logo.png',
                'color_theme' => '#FF6347',
            ],
            [
                'name' => 'Festival de Música Antigua de Úbeda y Baeza',
                'slug' => 'festival-musica-antigua-ubeda-baeza',
                'description' => 'Festival de música antigua en ciudades patrimonio de la humanidad',
                'month' => 12,
                'usual_days' => '01-15',
                'recurring' => true,
                'location_id' => 1, // Asumiendo que existe
                'logo_url' => 'https://festivalubedaybaeza.es/logo.png',
                'color_theme' => '#8FBC8F',
            ]
        ];

        foreach ($festivals as $festival) {
            Festival::create($festival);
        }
    }
}
