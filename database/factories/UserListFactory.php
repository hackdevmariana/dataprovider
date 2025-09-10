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

    public function definition(): array
    {
        $listTypes = ['mixed', 'users', 'posts', 'projects', 'companies', 'resources', 'events', 'custom'];
        $visibilities = ['private', 'public', 'followers', 'collaborative'];
        $curationModes = ['manual', 'auto_hashtag', 'auto_keyword', 'auto_author', 'auto_topic'];
        $colors = ['#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6', '#EC4899', '#06B6D4', '#84CC16'];
        $icons = ['📋', '⭐', '🔥', '💡', '🚀', '🎯', '📚', '🌟', '💎', '🎨', '🔧', '📊', '🎪', '🏆', '💼', '🎭'];

        $name = $this->faker->randomElement([
            'Mis Favoritos',
            'Proyectos Interesantes',
            'Recursos Útiles',
            'Personas a Seguir',
            'Eventos Importantes',
            'Empresas de Confianza',
            'Herramientas de Desarrollo',
            'Libros Recomendados',
            'Artículos Destacados',
            'Tutoriales de Calidad',
            'Noticias Relevantes',
            'Comunidades Activas',
            'Influencers del Sector',
            'Startups Prometedoras',
            'Tecnologías Emergentes',
            'Casos de Éxito',
            'Mejores Prácticas',
            'Recursos Gratuitos',
            'Herramientas Premium',
            'Comunidades Abiertas',
        ]);

        $listType = $this->faker->randomElement($listTypes);
        $visibility = $this->faker->randomElement($visibilities);
        $curationMode = $this->faker->randomElement($curationModes);

        return [
            'user_id' => User::factory(),
            'name' => $name,
            'slug' => \Illuminate\Support\Str::slug($name) . '-' . $this->faker->unique()->numberBetween(1000, 9999),
            'description' => $this->generateDescription($name, $listType),
            'icon' => $this->faker->randomElement($icons),
            'color' => $this->faker->randomElement($colors),
            'cover_image' => $this->faker->optional(0.3)->imageUrl(800, 400, 'abstract'),
            'list_type' => $listType,
            'allowed_content_types' => $this->generateAllowedContentTypes($listType),
            'visibility' => $visibility,
            'collaborator_ids' => $this->generateCollaborators($visibility),
            'allow_suggestions' => $this->faker->boolean(30),
            'allow_comments' => $this->faker->boolean(40),
            'curation_mode' => $curationMode,
            'auto_criteria' => $this->generateAutoCriteria($curationMode),
            'items_count' => $this->faker->numberBetween(0, 50),
            'followers_count' => $this->faker->numberBetween(0, 100),
            'views_count' => $this->faker->numberBetween(0, 1000),
            'shares_count' => $this->faker->numberBetween(0, 50),
            'engagement_score' => $this->faker->randomFloat(2, 0, 100),
            'is_featured' => $this->faker->boolean(10),
            'is_template' => $this->faker->boolean(5),
            'is_active' => $this->faker->boolean(95),
        ];
    }

    private function generateDescription(string $name, string $listType): string
    {
        $descriptions = [
            'mixed' => [
                'Una colección curada de contenido diverso y de calidad.',
                'Mi selección personal de los mejores recursos.',
                'Una lista cuidadosamente seleccionada de elementos interesantes.',
                'Contenido variado que considero valioso y relevante.',
            ],
            'users' => [
                'Personas influyentes y expertos en el sector.',
                'Usuarios que sigo y recomiendo por su contenido de calidad.',
                'Profesionales destacados en sus respectivos campos.',
                'Comunidad de usuarios activos y comprometidos.',
            ],
            'posts' => [
                'Artículos y publicaciones que considero esenciales.',
                'Contenido de calidad que merece ser compartido.',
                'Posts destacados sobre temas relevantes.',
                'Publicaciones que han marcado una diferencia.',
            ],
            'projects' => [
                'Proyectos innovadores y prometedores.',
                'Iniciativas que están transformando el sector.',
                'Proyectos de código abierto destacados.',
                'Emprendimientos con potencial de crecimiento.',
            ],
            'companies' => [
                'Empresas líderes en innovación y tecnología.',
                'Organizaciones que están marcando tendencias.',
                'Compañías con modelos de negocio interesantes.',
                'Empresas que valoro por su impacto social.',
            ],
            'resources' => [
                'Herramientas y recursos esenciales para el desarrollo.',
                'Enlaces útiles que uso regularmente.',
                'Recursos de calidad para aprender y crecer.',
                'Herramientas que han mejorado mi productividad.',
            ],
            'events' => [
                'Eventos importantes del sector tecnológico.',
                'Conferencias y meetups que recomiendo.',
                'Eventos que han sido transformadores.',
                'Oportunidades de networking y aprendizaje.',
            ],
            'custom' => [
                'Una lista personalizada con criterios específicos.',
                'Mi selección especial basada en preferencias únicas.',
                'Contenido curado según mis intereses particulares.',
                'Una colección única de elementos seleccionados.',
            ],
        ];

        return $this->faker->randomElement($descriptions[$listType] ?? $descriptions['mixed']);
    }

    private function generateAllowedContentTypes(string $listType): ?array
    {
        $contentTypes = [
            'mixed' => null, // null significa todos los tipos
            'users' => ['user', 'person'],
            'posts' => ['post', 'article', 'blog'],
            'projects' => ['project', 'repository'],
            'companies' => ['company', 'cooperative', 'organization'],
            'resources' => ['link', 'resource', 'tool'],
            'events' => ['event', 'conference', 'meetup'],
            'custom' => ['custom', 'mixed'],
        ];

        return $contentTypes[$listType] ?? null;
    }

    private function generateCollaborators(string $visibility): ?array
    {
        if ($visibility !== 'collaborative') {
            return null;
        }

        // Para listas colaborativas, generar algunos colaboradores
        $collaboratorCount = $this->faker->numberBetween(1, 5);
        $collaborators = [];
        
        for ($i = 0; $i < $collaboratorCount; $i++) {
            $collaborators[] = $this->faker->numberBetween(1, 100);
        }

        return array_unique($collaborators);
    }

    private function generateAutoCriteria(string $curationMode): ?array
    {
        $criteria = [
            'manual' => null,
            'auto_hashtag' => [
                'hashtags' => $this->faker->randomElements(['#tecnologia', '#innovacion', '#desarrollo', '#startup', '#ai', '#blockchain', '#sustainability'], $this->faker->numberBetween(1, 3)),
                'min_engagement' => $this->faker->numberBetween(10, 100),
            ],
            'auto_keyword' => [
                'keywords' => $this->faker->randomElements(['tecnologia', 'innovacion', 'desarrollo', 'startup', 'inteligencia artificial', 'blockchain', 'sostenibilidad'], $this->faker->numberBetween(1, 3)),
                'min_quality_score' => $this->faker->numberBetween(7, 10),
            ],
            'auto_author' => [
                'author_ids' => $this->faker->randomElements(range(1, 50), $this->faker->numberBetween(1, 5)),
                'min_followers' => $this->faker->numberBetween(100, 1000),
            ],
            'auto_topic' => [
                'topics' => $this->faker->randomElements(['tecnologia', 'innovacion', 'desarrollo', 'startup', 'ai', 'blockchain', 'sustainability'], $this->faker->numberBetween(1, 3)),
                'min_relevance_score' => $this->faker->numberBetween(0.7, 1.0),
            ],
        ];

        return $criteria[$curationMode] ?? null;
    }

    public function mixed(): static
    {
        return $this->state(fn (array $attributes) => [
            'list_type' => 'mixed',
            'allowed_content_types' => null,
        ]);
    }

    public function users(): static
    {
        return $this->state(fn (array $attributes) => [
            'list_type' => 'users',
            'allowed_content_types' => ['user', 'person'],
            'name' => $this->faker->randomElement(['Personas a Seguir', 'Influencers del Sector', 'Expertos Destacados', 'Comunidad Activa']),
        ]);
    }

    public function posts(): static
    {
        return $this->state(fn (array $attributes) => [
            'list_type' => 'posts',
            'allowed_content_types' => ['post', 'article', 'blog'],
            'name' => $this->faker->randomElement(['Artículos Destacados', 'Posts de Calidad', 'Contenido Relevante', 'Publicaciones Esenciales']),
        ]);
    }

    public function projects(): static
    {
        return $this->state(fn (array $attributes) => [
            'list_type' => 'projects',
            'allowed_content_types' => ['project', 'repository'],
            'name' => $this->faker->randomElement(['Proyectos Interesantes', 'Repositorios Destacados', 'Iniciativas Innovadoras', 'Proyectos de Código Abierto']),
        ]);
    }

    public function companies(): static
    {
        return $this->state(fn (array $attributes) => [
            'list_type' => 'companies',
            'allowed_content_types' => ['company', 'cooperative', 'organization'],
            'name' => $this->faker->randomElement(['Empresas de Confianza', 'Startups Prometedoras', 'Organizaciones Líderes', 'Compañías Innovadoras']),
        ]);
    }

    public function resources(): static
    {
        return $this->state(fn (array $attributes) => [
            'list_type' => 'resources',
            'allowed_content_types' => ['link', 'resource', 'tool'],
            'name' => $this->faker->randomElement(['Recursos Útiles', 'Herramientas de Desarrollo', 'Enlaces de Calidad', 'Recursos Gratuitos']),
        ]);
    }

    public function events(): static
    {
        return $this->state(fn (array $attributes) => [
            'list_type' => 'events',
            'allowed_content_types' => ['event', 'conference', 'meetup'],
            'name' => $this->faker->randomElement(['Eventos Importantes', 'Conferencias Destacadas', 'Meetups Relevantes', 'Eventos del Sector']),
        ]);
    }

    public function custom(): static
    {
        return $this->state(fn (array $attributes) => [
            'list_type' => 'custom',
            'allowed_content_types' => ['custom', 'mixed'],
            'name' => $this->faker->randomElement(['Mi Lista Personal', 'Selección Especial', 'Colección Única', 'Lista Personalizada']),
        ]);
    }

    public function private(): static
    {
        return $this->state(fn (array $attributes) => [
            'visibility' => 'private',
            'collaborator_ids' => null,
        ]);
    }

    public function public(): static
    {
        return $this->state(fn (array $attributes) => [
            'visibility' => 'public',
            'collaborator_ids' => null,
        ]);
    }

    public function followers(): static
    {
        return $this->state(fn (array $attributes) => [
            'visibility' => 'followers',
            'collaborator_ids' => null,
        ]);
    }

    public function collaborative(): static
    {
        return $this->state(fn (array $attributes) => [
            'visibility' => 'collaborative',
            'collaborator_ids' => $this->generateCollaborators('collaborative'),
        ]);
    }

    public function manual(): static
    {
        return $this->state(fn (array $attributes) => [
            'curation_mode' => 'manual',
            'auto_criteria' => null,
        ]);
    }

    public function autoHashtag(): static
    {
        return $this->state(fn (array $attributes) => [
            'curation_mode' => 'auto_hashtag',
            'auto_criteria' => $this->generateAutoCriteria('auto_hashtag'),
        ]);
    }

    public function autoKeyword(): static
    {
        return $this->state(fn (array $attributes) => [
            'curation_mode' => 'auto_keyword',
            'auto_criteria' => $this->generateAutoCriteria('auto_keyword'),
        ]);
    }

    public function autoAuthor(): static
    {
        return $this->state(fn (array $attributes) => [
            'curation_mode' => 'auto_author',
            'auto_criteria' => $this->generateAutoCriteria('auto_author'),
        ]);
    }

    public function autoTopic(): static
    {
        return $this->state(fn (array $attributes) => [
            'curation_mode' => 'auto_topic',
            'auto_criteria' => $this->generateAutoCriteria('auto_topic'),
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
            'visibility' => 'public',
            'engagement_score' => $this->faker->randomFloat(2, 50, 100),
            'followers_count' => $this->faker->numberBetween(50, 200),
            'views_count' => $this->faker->numberBetween(500, 2000),
        ]);
    }

    public function template(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_template' => true,
            'visibility' => 'public',
            'name' => $this->faker->randomElement(['Plantilla: Recursos de Desarrollo', 'Plantilla: Eventos del Sector', 'Plantilla: Empresas de Confianza', 'Plantilla: Herramientas Útiles']),
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function withItems(int $count): static
    {
        return $this->state(fn (array $attributes) => [
            'items_count' => $count,
            'engagement_score' => $count * 1.5 + $this->faker->randomFloat(2, 0, 20),
        ]);
    }

    public function withFollowers(int $count): static
    {
        return $this->state(fn (array $attributes) => [
            'followers_count' => $count,
            'engagement_score' => $count * 2 + $this->faker->randomFloat(2, 0, 20),
        ]);
    }

    public function withViews(int $count): static
    {
        return $this->state(fn (array $attributes) => [
            'views_count' => $count,
            'engagement_score' => $count * 0.1 + $this->faker->randomFloat(2, 0, 20),
        ]);
    }

    public function withShares(int $count): static
    {
        return $this->state(fn (array $attributes) => [
            'shares_count' => $count,
            'engagement_score' => $count * 5 + $this->faker->randomFloat(2, 0, 20),
        ]);
    }

    public function highEngagement(): static
    {
        return $this->state(fn (array $attributes) => [
            'engagement_score' => $this->faker->randomFloat(2, 80, 100),
            'followers_count' => $this->faker->numberBetween(100, 500),
            'views_count' => $this->faker->numberBetween(1000, 5000),
            'shares_count' => $this->faker->numberBetween(50, 200),
            'is_featured' => $this->faker->boolean(80),
        ]);
    }

    public function lowEngagement(): static
    {
        return $this->state(fn (array $attributes) => [
            'engagement_score' => $this->faker->randomFloat(2, 0, 20),
            'followers_count' => $this->faker->numberBetween(0, 10),
            'views_count' => $this->faker->numberBetween(0, 50),
            'shares_count' => $this->faker->numberBetween(0, 5),
            'is_featured' => false,
        ]);
    }

    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    public function withCustomName(string $name): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => $name,
        ]);
    }

    public function withCustomDescription(string $description): static
    {
        return $this->state(fn (array $attributes) => [
            'description' => $description,
        ]);
    }

    public function withCustomIcon(string $icon): static
    {
        return $this->state(fn (array $attributes) => [
            'icon' => $icon,
        ]);
    }

    public function withCustomColor(string $color): static
    {
        return $this->state(fn (array $attributes) => [
            'color' => $color,
        ]);
    }
}