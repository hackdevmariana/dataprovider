<?php

namespace Database\Factories;

use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;

class GroupFactory extends Factory
{
    protected $model = Group::class;

    public function definition(): array
    {
        $genres = ['Pop', 'Rock', 'Electrónica', 'Hip-Hop', 'Jazz', 'Clásica', 'Folk', 'Reggae', 'Blues', 'Country'];
        $countries = ['España', 'Estados Unidos', 'Reino Unido', 'Francia', 'Alemania', 'Italia', 'México', 'Argentina', 'Brasil', 'Japón'];
        $cities = ['Madrid', 'Barcelona', 'Valencia', 'Sevilla', 'Bilbao', 'Londres', 'París', 'Nueva York', 'Los Ángeles', 'Tokio'];
        $statuses = ['active', 'inactive', 'disbanded', 'on_hiatus'];
        
        return [
            'name' => $this->faker->company . ' Group',
            'slug' => $this->faker->unique()->slug(),
            'description' => $this->faker->optional()->sentence(8),
            'genre' => $this->faker->randomElement($genres),
            'formed_at' => $this->faker->optional(0.8)->dateTimeBetween('-50 years', '-1 year'),
            'disbanded_at' => $this->faker->optional(0.2)->dateTimeBetween('-20 years', 'now'),
            'active_status' => $this->faker->randomElement($statuses),
            'website' => $this->faker->optional(0.7)->url(),
            'social_media' => $this->faker->optional(0.6)->randomElements([
                'facebook' => $this->faker->url(),
                'twitter' => $this->faker->url(),
                'instagram' => $this->faker->url(),
                'youtube' => $this->faker->url(),
                'spotify' => $this->faker->url(),
            ], $this->faker->numberBetween(1, 3)),
            'contact_email' => $this->faker->optional(0.5)->companyEmail(),
            'management_company' => $this->faker->optional(0.4)->company(),
            'origin_country' => $this->faker->randomElement($countries),
            'origin_city' => $this->faker->randomElement($cities),
            'current_location' => $this->faker->optional(0.8)->city(),
            'record_label' => $this->faker->optional(0.6)->company(),
            'albums_count' => $this->faker->numberBetween(0, 20),
            'songs_count' => $this->faker->numberBetween(0, 200),
            'awards' => $this->faker->optional(0.3)->randomElements([
                'Grammy', 'MTV Video Music Award', 'Billboard Music Award', 'Premio Grammy Latino',
                'Premio Lo Nuestro', 'Premio Ondas', 'Premio Nacional de Música'
            ], $this->faker->numberBetween(1, 3)),
            'certifications' => $this->faker->optional(0.2)->randomElements([
                'Disco de Oro', 'Disco de Platino', 'Disco de Diamante', 'Multi-Platino'
            ], $this->faker->numberBetween(1, 2)),
            'biography' => $this->faker->optional(0.7)->paragraphs(3, true),
            'tags' => $this->faker->optional(0.5)->randomElements([
                'legendario', 'influyente', 'pionero', 'experimental', 'tradicional',
                'moderno', 'clásico', 'innovador', 'underground', 'mainstream'
            ], $this->faker->numberBetween(1, 4)),
            'search_boost' => $this->faker->randomFloat(2, 0.50, 9.99),
            'official_fan_club' => $this->faker->optional(0.3)->url(),
            'is_verified' => $this->faker->boolean(20),
            'is_featured' => $this->faker->boolean(10),
            'source' => 'factory',
            'metadata' => [
                'factory_created' => true,
                'last_updated' => now()->toISOString(),
            ],
        ];
    }
}
