<?php

namespace Database\Factories;

use App\Models\Hashtag;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Hashtag>
 */
class HashtagFactory extends Factory
{
    protected $model = Hashtag::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $energyHashtags = [
            'energiasolar', 'autoconsumo', 'panelessolares', 'renovables',
            'fotovoltaica', 'sostenibilidad', 'ahorro', 'electricidad',
            'cooperativa', 'comunidadenergetica', 'instalacion', 'mantenimiento',
            'subvenciones', 'ayudas', 'financiacion', 'medioambiente',
            'madrid', 'barcelona', 'andalucia', 'valencia', 'tecnologia',
            'inversor', 'bateria', 'smart', 'iot', 'rd244', 'normativa',
            'mercado', 'precio', 'tarifa', 'factura', 'verde', 'eco'
        ];

        $name = fake()->unique()->randomElement($energyHashtags);
        
        return [
            'name' => $name,
            'slug' => \Str::slug($name),
            'description' => fake()->optional(0.7)->sentence(),
            'color' => fake()->hexColor(),
            'icon' => fake()->optional(0.5)->randomElement([
                'solar-panel', 'leaf', 'lightning-bolt', 'home', 'users',
                'chart-bar', 'cog', 'map-pin', 'book-open', 'shield-check'
            ]),
            'category' => Hashtag::detectCategory($name),
            'usage_count' => fake()->numberBetween(1, 1000),
            'posts_count' => fake()->numberBetween(1, 500),
            'followers_count' => fake()->numberBetween(0, 200),
            'trending_score' => fake()->randomFloat(2, 0, 500),
            'is_trending' => fake()->boolean(20), // 20% trending
            'is_verified' => fake()->boolean(30), // 30% verified
            'is_blocked' => false,
            'created_by' => fake()->optional(0.8)->randomElement(User::pluck('id')->toArray()),
            'related_hashtags' => fake()->optional(0.3)->randomElements($energyHashtags, 3),
            'synonyms' => fake()->optional(0.2)->randomElements($energyHashtags, 2),
            'auto_suggest' => fake()->boolean(90), // 90% auto-suggest enabled
        ];
    }

    /**
     * Hashtag trending.
     */
    public function trending(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_trending' => true,
            'trending_score' => fake()->randomFloat(2, 100, 1000),
            'usage_count' => fake()->numberBetween(500, 2000),
            'posts_count' => fake()->numberBetween(200, 1000),
            'followers_count' => fake()->numberBetween(50, 500),
        ]);
    }

    /**
     * Hashtag verificado.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => true,
            'description' => 'Hashtag oficial verificado por KiroLux',
            'usage_count' => fake()->numberBetween(100, 1500),
        ]);
    }

    /**
     * Hashtag por categoría específica.
     */
    public function category(string $category): static
    {
        $hashtagsByCategory = [
            'technology' => ['panelessolares', 'inversor', 'bateria', 'smart', 'iot', 'tecnologia', 'monitorizacion', 'optimizador', 'string', 'microinversor', 'estructura', 'cableado', 'protecciones', 'contador', 'medidor'],
            'legislation' => ['normativa', 'ley', 'regulacion', 'legal', 'decreto', 'boe', 'cnmc', 'idae', 'certificacion', 'homologacion', 'tramites', 'licencias'],
            'financing' => ['subvencion', 'ayuda', 'credito', 'financiacion', 'inversion', 'leasing', 'renting', 'plan', 'descuento', 'bonificacion', 'deduccion', 'iva'],
            'installation' => ['instalacion', 'montaje', 'mantenimiento', 'reparacion', 'revision', 'limpieza', 'diagnostico', 'puesta', 'marcha', 'conexion', 'cableado', 'estructura'],
            'cooperative' => ['comunidad', 'colectivo', 'compartido', 'asociacion', 'agrupacion', 'cluster', 'red', 'plataforma', 'union', 'federacion'],
            'market' => ['precio', 'mercado', 'tarifa', 'factura', 'ahorro', 'coste', 'rentabilidad', 'roi', 'amortizacion', 'pool', 'pvpc', 'omie'],
            'sustainability' => ['sostenible', 'verde', 'eco', 'medioambiente', 'co2', 'huella', 'carbono', 'emisiones', 'renovable', 'limpia', 'circular', 'eficiencia'],
            'location' => ['sevilla', 'valencia', 'bilbao', 'murcia', 'palma', 'canarias', 'asturias', 'galicia', 'castilla', 'leon', 'aragon', 'cataluna', 'extremadura', 'navarra', 'rioja'],
            'general' => ['energia', 'renovables', 'fotovoltaica', 'solar', 'electrica', 'generacion', 'produccion', 'consumo', 'excedentes', 'balance', 'neto', 'compensacion'],
        ];

        $names = $hashtagsByCategory[$category] ?? $hashtagsByCategory['general'];
        $name = fake()->unique()->randomElement($names);

        return $this->state(fn (array $attributes) => [
            'name' => $name,
            'slug' => \Str::slug($name),
            'category' => $category,
        ]);
    }
}
