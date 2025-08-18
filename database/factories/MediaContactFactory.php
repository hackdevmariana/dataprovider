<?php

namespace Database\Factories;

use App\Models\MediaContact;
use App\Models\MediaOutlet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para generar contactos de medios realistas.
 */
class MediaContactFactory extends Factory
{
    protected $model = MediaContact::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $contactData = $this->getRealisticContactData();
        $selectedContact = fake()->randomElement($contactData);
        
        return [
            'contact_name' => $selectedContact['name'],
            'job_title' => $selectedContact['job_title'],
            'department' => $selectedContact['department'],
            'type' => fake()->randomElement(['editorial', 'commercial', 'general']),
            
            // Información de contacto
            'phone' => fake()->optional(0.8)->phoneNumber(),
            'mobile_phone' => fake()->optional(0.6)->phoneNumber(),
            'email' => strtolower(str_replace(' ', '.', $selectedContact['name'])) . '@' . fake()->randomElement([
                'elpais.es', 'elmundo.es', 'lavanguardia.com', 'eldiario.es', 'abc.es'
            ]),
            'secondary_email' => fake()->optional(0.3)->email(),
            'preferred_contact_method' => fake()->randomElement(['email', 'phone', 'whatsapp', 'social_media']),
            
            // Especialización
            'specializations' => json_encode($selectedContact['specializations']),
            'coverage_areas' => json_encode($selectedContact['coverage_areas']),
            'language_preference' => 'es',
            
            // Preferencias de contenido
            'accepts_press_releases' => fake()->boolean(85),
            'accepts_interviews' => fake()->boolean(70),
            'accepts_events_invitations' => fake()->boolean(60),
            
            // Características profesionales
            'is_freelancer' => $selectedContact['is_freelancer'],
            'is_active' => fake()->boolean(90),
            'is_verified' => fake()->boolean(75),
            'priority_level' => fake()->numberBetween(1, 4), // 1=low, 2=medium, 3=high, 4=critical
            
            // Disponibilidad
            'availability_schedule' => json_encode([
                'monday' => ['09:00-18:00'],
                'tuesday' => ['09:00-18:00'],
                'wednesday' => ['09:00-18:00'],
                'thursday' => ['09:00-18:00'],
                'friday' => ['09:00-17:00'],
                'weekend' => fake()->boolean(20) ? ['10:00-14:00'] : []
            ]),
            
            // Métricas de interacción
            'contacts_count' => fake()->numberBetween(0, 200),
            'successful_contacts' => fake()->numberBetween(0, 150),
            'response_rate' => fake()->randomFloat(2, 0.3, 0.95),
            
            // Información adicional
            'social_media_profiles' => json_encode([
                'twitter' => fake()->optional(0.7)->userName(),
                'linkedin' => fake()->optional(0.9)->userName(),
                'instagram' => fake()->optional(0.3)->userName(),
            ]),
            'bio' => $selectedContact['bio'],
            'recent_articles' => json_encode($this->generateRecentArticles()),
            'notes' => fake()->optional(0.4)->sentence(10),
            'interaction_history' => json_encode($this->generateInteractionHistory()),
            
            // Fechas
            'last_contacted_at' => fake()->optional(0.6)->dateTimeBetween('-3 months', 'now'),
            'last_response_at' => fake()->optional(0.5)->dateTimeBetween('-2 months', 'now'),
            'verified_at' => fake()->optional(0.7)->dateTimeBetween('-1 year', 'now'),
            
            // Relación con medio (se asignará en el seeder)
            'media_outlet_id' => null,
        ];
    }

    /**
     * Datos realistas de contactos de medios.
     */
    private function getRealisticContactData(): array
    {
        return [
            // Editores jefe
            [
                'name' => 'María González Redondo',
                'job_title' => 'Editora Jefe',
                'department' => 'Redacción',
                'type' => 'editorial',
                'specializations' => ['política', 'nacional', 'coordinación editorial'],
                'coverage_areas' => ['España', 'política nacional', 'elecciones'],
                'is_freelancer' => false,
                'bio' => 'Editora con más de 15 años de experiencia en medios nacionales. Especializada en política y coordinación editorial.'
            ],
            [
                'name' => 'Carlos Martín Silva',
                'job_title' => 'Editor de Sostenibilidad',
                'department' => 'Medio Ambiente',
                'type' => 'editorial',
                'specializations' => ['sostenibilidad', 'cambio climático', 'energías renovables'],
                'coverage_areas' => ['medio ambiente', 'energía', 'políticas verdes'],
                'is_freelancer' => false,
                'bio' => 'Editor especializado en temas ambientales y sostenibilidad. Licenciado en Ciencias Ambientales.'
            ],
            
            // Periodistas especializados
            [
                'name' => 'Ana Rodríguez López',
                'job_title' => 'Periodista Ambiental',
                'department' => 'Medio Ambiente',
                'type' => 'editorial',
                'specializations' => ['biodiversidad', 'conservación', 'cambio climático'],
                'coverage_areas' => ['espacios naturales', 'especies protegidas', 'políticas ambientales'],
                'is_freelancer' => false,
                'bio' => 'Periodista especializada en medio ambiente con 10 años de experiencia cubriendo temas de biodiversidad.'
            ],
            [
                'name' => 'Javier Fernández Ruiz',
                'job_title' => 'Corresponsal de Energía',
                'department' => 'Economía',
                'type' => 'correspondent',
                'specializations' => ['energías renovables', 'sector eléctrico', 'política energética'],
                'coverage_areas' => ['mercado energético', 'empresas eléctricas', 'regulación'],
                'is_freelancer' => false,
                'bio' => 'Corresponsal especializado en el sector energético español. Ingeniero Industrial de formación.'
            ],
            [
                'name' => 'Laura Sánchez Torres',
                'job_title' => 'Redactora de Ciencia',
                'department' => 'Ciencia y Tecnología',
                'type' => 'editorial',
                'specializations' => ['investigación científica', 'innovación', 'tecnología verde'],
                'coverage_areas' => ['CSIC', 'universidades', 'centros de investigación'],
                'is_freelancer' => false,
                'bio' => 'Periodista científica con doctorado en Biología. Especializada en divulgación científica.'
            ],
            
            // Responsables de comunicación
            [
                'name' => 'David López Moreno',
                'job_title' => 'Director de Comunicación',
                'department' => 'Comunicación',
                'type' => 'press_contact',
                'specializations' => ['relaciones públicas', 'estrategia comunicativa', 'crisis'],
                'coverage_areas' => ['comunicación corporativa', 'eventos', 'notas de prensa'],
                'is_freelancer' => false,
                'bio' => 'Director de comunicación con amplia experiencia en gestión de crisis y relaciones con medios.'
            ],
            [
                'name' => 'Isabel Martínez Vega',
                'job_title' => 'Responsable de Prensa',
                'department' => 'Comunicación',
                'type' => 'press_contact',
                'specializations' => ['notas de prensa', 'eventos', 'entrevistas'],
                'coverage_areas' => ['medios nacionales', 'coordinación eventos', 'agenda mediática'],
                'is_freelancer' => false,
                'bio' => 'Responsable de prensa especializada en coordinación con medios y organización de eventos.'
            ],
            
            // Freelancers
            [
                'name' => 'Roberto García Jiménez',
                'job_title' => 'Periodista Freelance',
                'department' => 'Freelance',
                'type' => 'freelancer',
                'specializations' => ['sostenibilidad', 'economía circular', 'startups verdes'],
                'coverage_areas' => ['emprendimiento sostenible', 'innovación verde', 'economía circular'],
                'is_freelancer' => true,
                'bio' => 'Periodista freelance especializado en economía sostenible y emprendimiento verde.'
            ],
            [
                'name' => 'Carmen Ruiz Delgado',
                'job_title' => 'Colaboradora Especializada',
                'department' => 'Freelance',
                'type' => 'freelancer',
                'specializations' => ['agricultura ecológica', 'alimentación sostenible', 'rural'],
                'coverage_areas' => ['sector agrario', 'desarrollo rural', 'alimentación'],
                'is_freelancer' => true,
                'bio' => 'Colaboradora especializada en temas rurales y agricultura sostenible. Ingeniera Agrónoma.'
            ],
            
            // Corresponsales
            [
                'name' => 'Miguel Ángel Torres',
                'job_title' => 'Corresponsal en Bruselas',
                'department' => 'Internacional',
                'type' => 'correspondent',
                'specializations' => ['política europea', 'Green Deal', 'legislación ambiental'],
                'coverage_areas' => ['Unión Europea', 'política climática', 'normativa europea'],
                'is_freelancer' => false,
                'bio' => 'Corresponsal en Bruselas especializado en políticas ambientales europeas y Green Deal.'
            ],
            [
                'name' => 'Beatriz Herrera Campos',
                'job_title' => 'Corresponsal Regional',
                'department' => 'Regional',
                'type' => 'correspondent',
                'specializations' => ['política regional', 'medio ambiente local', 'desarrollo sostenible'],
                'coverage_areas' => ['Andalucía', 'políticas regionales', 'proyectos locales'],
                'is_freelancer' => false,
                'bio' => 'Corresponsal regional cubriendo políticas ambientales y proyectos de desarrollo sostenible.'
            ]
        ];
    }

    /**
     * Generar artículos recientes ficticios.
     */
    private function generateRecentArticles(): array
    {
        $articles = [];
        for ($i = 0; $i < fake()->numberBetween(2, 6); $i++) {
            $articles[] = [
                'title' => fake()->sentence(8),
                'published_at' => fake()->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
                'url' => fake()->url(),
            ];
        }
        return $articles;
    }

    /**
     * Generar historial de interacciones.
     */
    private function generateInteractionHistory(): array
    {
        $interactions = [];
        for ($i = 0; $i < fake()->numberBetween(0, 10); $i++) {
            $interactions[] = [
                'date' => fake()->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
                'type' => fake()->randomElement(['email', 'phone', 'meeting', 'event']),
                'description' => fake()->sentence(6),
                'successful' => fake()->boolean(70),
            ];
        }
        return $interactions;
    }

    /**
     * Contacto verificado.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => true,
            'verified_at' => fake()->dateTimeBetween('-2 years', 'now'),
            'priority_level' => fake()->numberBetween(3, 4),
        ]);
    }

    /**
     * Contacto especializado en sostenibilidad.
     */
    public function sustainabilityFocused(): static
    {
        return $this->state(fn (array $attributes) => [
            'specializations' => json_encode([
                'sostenibilidad', 'cambio climático', 'energías renovables', 'medio ambiente'
            ]),
            'coverage_areas' => json_encode([
                'políticas ambientales', 'energía verde', 'conservación', 'biodiversidad'
            ]),
        ]);
    }

    /**
     * Contacto de alta prioridad.
     */
    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority_level' => fake()->numberBetween(3, 4), // high o critical
            'response_rate' => fake()->randomFloat(2, 0.8, 0.98),
            'is_verified' => true,
        ]);
    }

    /**
     * Freelancer.
     */
    public function freelancer(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_freelancer' => true,
            'type' => 'general',
            'department' => 'Freelance',
        ]);
    }
}