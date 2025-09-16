<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Image>
 */
class ImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'filename' => fake()->word() . '.jpg',
            'original_filename' => fake()->word() . '.jpg',
            'file_path' => fake()->filePath(),
            'file_size_bytes' => fake()->numberBetween(1000, 10000000),
            'mime_type' => fake()->randomElement(['image/jpeg', 'image/png', 'image/gif', 'image/webp']),
            'width' => fake()->numberBetween(100, 4000),
            'height' => fake()->numberBetween(100, 4000),
            'alt_text' => fake()->optional()->sentence(),
            'caption' => fake()->optional()->sentence(),
            'title' => fake()->optional()->sentence(),
            'description' => fake()->optional()->text(200),
            'tags' => fake()->words(3),
            'is_public' => fake()->boolean(80),
            'upload_source' => fake()->randomElement(['user_upload', 'admin_upload', 'api_import', 'system_generated']),
            'storage_driver' => fake()->randomElement(['local', 's3', 'cloudinary', 'gcs']),
            'checksum' => fake()->sha256(),
            'metadata' => [
                'camera' => fake()->optional()->word(),
                'lens' => fake()->optional()->word(),
                'iso' => fake()->optional()->numberBetween(100, 6400),
                'aperture' => fake()->optional()->randomFloat(1, 1.4, 22),
                'shutter_speed' => fake()->optional()->randomFloat(2, 1/4000, 30),
            ],
        ];
    }

    /**
     * Indicate that the image is public.
     */
    public function public(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_public' => true,
        ]);
    }
}


