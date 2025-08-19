<?php

namespace Database\Factories;

use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Topic>
 */
class TopicFactory extends Factory
{
    protected $model = Topic::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->sentence(3);
        $categories = [
            'technology', 'legislation', 'financing', 'installation',
            'cooperative', 'market', 'diy', 'news', 'beginners',
            'professional', 'regional', 'general'
        ];

        $colors = [
            '#3B82F6', '#10B981', '#F59E0B', '#EF4444', 
            '#8B5CF6', '#06B6D4', '#84CC16', '#F97316'
        ];

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->paragraph(3),
            'icon' => fake()->randomElement([
                'solar-panel', 'lightning-bolt', 'cog', 'book-open',
                'users', 'chart-bar', 'wrench', 'newspaper'
            ]),
            'color' => fake()->randomElement($colors),
            'banner_image' => fake()->optional(0.3)->imageUrl(800, 200, 'energy'),
            'creator_id' => User::factory(),
            'moderator_ids' => null,
            'rules' => fake()->optional(0.5)->randomElements([
                'Mantén las discusiones relevantes al tema',
                'Sé respetuoso con otros miembros',
                'No spam ni autopromoción',
                'Proporciona fuentes cuando sea posible',
                'Usa las etiquetas apropiadas'
            ], fake()->numberBetween(2, 4)),
            'visibility' => fake()->randomElement(['public', 'private', 'restricted']),
            'post_permission' => fake()->randomElement(['everyone', 'members', 'moderators']),
            'comment_permission' => fake()->randomElement(['everyone', 'members', 'verified']),
            'category' => fake()->randomElement($categories),
            'members_count' => fake()->numberBetween(1, 1000),
            'posts_count' => fake()->numberBetween(0, 500),
            'comments_count' => fake()->numberBetween(0, 2000),
            'activity_score' => fake()->randomFloat(2, 0, 1000),
            'is_featured' => fake()->boolean(10), // 10% probabilidad
            'is_active' => fake()->boolean(95), // 95% activos
            'requires_approval' => fake()->boolean(20), // 20% requieren aprobación
            'allow_polls' => fake()->boolean(80),
            'allow_images' => fake()->boolean(90),
            'allow_links' => fake()->boolean(85),
        ];
    }

    /**
     * Indica que el tema está destacado.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
            'activity_score' => fake()->randomFloat(2, 500, 1000),
        ]);
    }

    /**
     * Indica que el tema es privado.
     */
    public function private(): static
    {
        return $this->state(fn (array $attributes) => [
            'visibility' => 'private',
        ]);
    }

    /**
     * Indica que el tema es para principiantes.
     */
    public function beginner(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'beginners',
            'name' => 'Energía Solar para Principiantes',
            'description' => 'Un espacio acogedor para quienes se inician en el mundo de la energía solar.',
        ]);
    }

    /**
     * Indica que el tema es muy activo.
     */
    public function highActivity(): static
    {
        return $this->state(fn (array $attributes) => [
            'members_count' => fake()->numberBetween(500, 2000),
            'posts_count' => fake()->numberBetween(200, 1000),
            'comments_count' => fake()->numberBetween(1000, 5000),
            'activity_score' => fake()->randomFloat(2, 800, 1500),
        ]);
    }

    /**
     * Indica que el tema es tecnológico.
     */
    public function technology(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'technology',
            'icon' => 'cog',
            'color' => '#3B82F6',
            'allow_images' => true,
            'allow_links' => true,
        ]);
    }

    /**
     * Indica que el tema es sobre legislación.
     */
    public function legislation(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'legislation',
            'icon' => 'book-open',
            'color' => '#10B981',
            'requires_approval' => true, // Legislación requiere más moderación
        ]);
    }

    /**
     * Indica que el tema es sobre cooperativas.
     */
    public function cooperative(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'cooperative',
            'icon' => 'users',
            'color' => '#8B5CF6',
            'allow_polls' => true, // Las cooperativas usan mucho las encuestas
        ]);
    }
}

