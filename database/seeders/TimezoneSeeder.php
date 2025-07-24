<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Timezone;

class TimezoneSeeder extends Seeder
{
    public function run()
    {
        $timezones = [
            [
                'name' => 'America/Argentina/Buenos_Aires',
                'offset' => '-03:00',
                'dst_offset' => '-03:00',
            ],
            [
                'name' => 'America/Mexico_City',
                'offset' => '-06:00',
                'dst_offset' => '-05:00',
            ],
            [
                'name' => 'America/Bogota',
                'offset' => '-05:00',
                'dst_offset' => '-05:00',
            ],
            [
                'name' => 'America/Lima',
                'offset' => '-05:00',
                'dst_offset' => '-05:00',
            ],
            [
                'name' => 'America/Santiago',
                'offset' => '-04:00',
                'dst_offset' => '-03:00',
            ],
            [
                'name' => 'America/Montevideo',
                'offset' => '-03:00',
                'dst_offset' => '-03:00',
            ],
            [
                'name' => 'America/Caracas',
                'offset' => '-04:00',
                'dst_offset' => '-04:00',
            ],
        ];

        foreach ($timezones as $tz) {
            Timezone::firstOrCreate(['name' => $tz['name']], $tz);
        }
    }
}
