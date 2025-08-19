<?php

namespace Database\Factories;

use App\Models\UserList;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserList>
 */
class UserListFactory extends Factory
{
    protected $model = UserList::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $listNames = [
            'Instaladores de confianza',
            'Proyectos interesantes',
            'Mejores cooperativas',
            'Ofertas de electricidad',
            'Recursos de autoconsumo',
            'Eventos de energía solar',
            'Empresas recomendadas',
            'Artículos imprescindibles',
            'Herramientas útiles',
            'Contactos del sector',
            'Subvenciones disponibles',
            'Normativas importantes',
            'Casos de éxito',
            'Proveedores verificados',
            'Comunidades activas'
        ];

        $name = fake()->randomElement($listNames);
        $userId = fake()->randomElement(User::pluck('id')->toArray());

        return [
            'user_id' => $userId,
            'name' => $name,
            'slug' => UserList::generateUniqueSlug($name, $userId),
            'description' => fake()->optional(0.8)->paragraph(),
            'icon' => fake()->optional(0.6)->randomElement([
                'users', 'bookmark', 'star', 'heart', 'collection',
                'folder', 'list', 'grid', 'tag', 'flag'
            ]),
            'color' => fake()->hexColor(),
            'cover_image' => fake()->optional(0.3)->imageUrl(800, 200, 'nature'),
            'list_type' => fake()->randomElement([
                'mixed', 'users', 'posts', 'projects', 
                'companies', 'resources', 'events'
            ]),
            'allowed_content_types' => fake()->optional(0.4)->randomElements([
                'App\\Models\\User',
                'App\\Models\\Post',
                'App\\Models\\Project',
                'App\\Models\\Cooperative',
                'App\\Models\\NewsArticle',
                'App\\Models\\Event'
            ], 2),
            'visibility' => fake()->randomElement(['private', 'public', 'followers', 'collaborative']),
            'collaborator_ids' => fake()->optional(0.2)->randomElements(
                User::pluck('id')->toArray(), 
                fake()->numberBetween(1, 3)
            ),
            'allow_suggestions' => fake()->boolean(30),
            'allow_comments' => fake()->boolean(40),
            'curation_mode' => fake()->randomElement([
                'manual', 'auto_hashtag', 'auto_keyword', 'auto_author', 'auto_topic'
            ]),
            'auto_criteria' => fake()->optional(0.3)->randomElements([
                'energiasolar', 'autoconsumo', 'cooperativa', 'instalacion'
            ], 2),
            'items_count' => fake()->numberBetween(0, 50),
            'followers_count' => fake()->numberBetween(0, 100),
            'views_count' => fake()->numberBetween(0, 1000),
            'shares_count' => fake()->numberBetween(0, 50),
            'engagement_score' => fake()->randomFloat(2, 0, 500),
            'is_featured' => fake()->boolean(10), // 10% featured
            'is_template' => fake()->boolean(5), // 5% templates
            'is_active' => fake()->boolean(95), // 95% active
        ];
    }

    /**
     * Lista pública.
     */
    public function public(): static
    {
        return $this->state(fn (array $attributes) => [
            'visibility' => 'public',
            'views_count' => fake()->numberBetween(50, 2000),
            'followers_count' => fake()->numberBetween(10, 200),
            'engagement_score' => fake()->randomFloat(2, 50, 800),
        ]);
    }

    /**
     * Lista destacada.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
            'visibility' => 'public',
            'items_count' => fake()->numberBetween(10, 100),
            'followers_count' => fake()->numberBetween(50, 500),
            'views_count' => fake()->numberBetween(500, 5000),
            'engagement_score' => fake()->randomFloat(2, 200, 1000),
            'description' => 'Lista destacada curada por expertos de la comunidad KiroLux',
        ]);
    }

    /**
     * Lista colaborativa.
     */
    public function collaborative(): static
    {
        return $this->state(fn (array $attributes) => [
            'visibility' => 'collaborative',
            'collaborator_ids' => fake()->randomElements(
                User::pluck('id')->toArray(), 
                fake()->numberBetween(2, 5)
            ),
            'allow_suggestions' => true,
            'allow_comments' => true,
        ]);
    }

    /**
     * Lista por tipo específico.
     */
    public function type(string $type): static
    {
        $typeNames = [
            'users' => ['Instaladores recomendados', 'Expertos en energía', 'Miembros activos'],
            'posts' => ['Artículos destacados', 'Mejores publicaciones', 'Contenido popular'],
            'projects' => ['Proyectos innovadores', 'Casos de éxito', 'Propuestas interesantes'],
            'companies' => ['Empresas confiables', 'Proveedores verificados', 'Cooperativas activas'],
            'resources' => ['Recursos útiles', 'Herramientas esenciales', 'Documentación'],
            'events' => ['Eventos importantes', 'Conferencias', 'Talleres formativos'],
        ];

        $names = $typeNames[$type] ?? $typeNames['mixed'];
        $name = fake()->randomElement($names);

        return $this->state(fn (array $attributes) => [
            'list_type' => $type,
            'name' => $name,
        ]);
    }

    /**
     * Plantilla de lista.
     */
    public function template(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_template' => true,
            'visibility' => 'public',
            'description' => 'Plantilla que puedes usar para crear tu propia lista personalizada',
            'allow_suggestions' => false,
            'allow_comments' => false,
        ]);
    }
}
