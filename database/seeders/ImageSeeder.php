<?php

namespace Database\Seeders;

use App\Models\Image;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Imágenes del sistema
        $data = [
            [
                'slug' => 'default-image',
                'url' => 'https://via.placeholder.com/300x200/1e40af/ffffff?text=Default+Image',
                'alt_text' => 'Imagen por defecto',
                'source' => 'placeholder',
                'width' => 300,
                'height' => 200,
            ],
            [
                'slug' => 'energy-image',
                'url' => 'https://via.placeholder.com/300x200/059669/ffffff?text=Energy+Image',
                'alt_text' => 'Imagen energética',
                'source' => 'placeholder',
                'width' => 300,
                'height' => 200,
            ],
            [
                'slug' => 'company-logo',
                'url' => 'https://via.placeholder.com/300x200/dc2626/ffffff?text=Company+Logo',
                'alt_text' => 'Logo de empresa',
                'source' => 'placeholder',
                'width' => 300,
                'height' => 200,
            ],
            [
                'slug' => 'user-avatar',
                'url' => 'https://via.placeholder.com/300x200/7c3aed/ffffff?text=User+Avatar',
                'alt_text' => 'Avatar de usuario',
                'source' => 'placeholder',
                'width' => 300,
                'height' => 200,
            ],
            [
                'slug' => 'event-image',
                'url' => 'https://via.placeholder.com/300x200/b45309/ffffff?text=Event+Image',
                'alt_text' => 'Imagen de evento',
                'source' => 'placeholder',
                'width' => 300,
                'height' => 200,
            ],
        ];

        foreach ($data as $item) {
            Image::updateOrCreate(
                ['slug' => $item['slug']],
                $item
            );
        }

        $this->command->info('Imágenes del sistema creadas exitosamente.');
    }
}