<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TagGroup>
 */
class TagGroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->randomElement([
            'Categorías de Contenido',
            'Etiquetas de Ubicación',
            'Tipos de Eventos',
            'Clasificación por Edad',
            'Géneros Musicales',
            'Estilos Artísticos',
            'Niveles de Dificultad',
            'Idiomas Disponibles',
            'Formatos de Presentación',
            'Temas de Interés',
            'Estados de Proyecto',
            'Prioridades',
            'Departamentos',
            'Funciones del Personal',
            'Tipos de Documentos',
            'Clasificación Legal',
            'Estados de Aprobación',
            'Niveles de Acceso',
            'Tipos de Recurso',
            'Categorías de Noticias'
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->optional(0.8)->paragraph(2),
        ];
    }

    /**
     * Indicate that the tag group is for content categories.
     */
    public function contentCategories(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Categorías de Contenido',
            'slug' => 'categorias-contenido',
            'description' => 'Grupo de etiquetas para categorizar diferentes tipos de contenido como artículos, videos, imágenes, etc.',
        ]);
    }

    /**
     * Indicate that the tag group is for location tags.
     */
    public function locationTags(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Etiquetas de Ubicación',
            'slug' => 'etiquetas-ubicacion',
            'description' => 'Etiquetas para identificar ubicaciones geográficas, ciudades, regiones y países.',
        ]);
    }

    /**
     * Indicate that the tag group is for event types.
     */
    public function eventTypes(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Tipos de Eventos',
            'slug' => 'tipos-eventos',
            'description' => 'Clasificación de diferentes tipos de eventos como conferencias, talleres, conciertos, etc.',
        ]);
    }

    /**
     * Indicate that the tag group is for age classification.
     */
    public function ageClassification(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Clasificación por Edad',
            'slug' => 'clasificacion-edad',
            'description' => 'Etiquetas para clasificar contenido según la edad recomendada del público objetivo.',
        ]);
    }

    /**
     * Indicate that the tag group is for music genres.
     */
    public function musicGenres(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Géneros Musicales',
            'slug' => 'generos-musicales',
            'description' => 'Clasificación de diferentes géneros y estilos musicales.',
        ]);
    }
}
