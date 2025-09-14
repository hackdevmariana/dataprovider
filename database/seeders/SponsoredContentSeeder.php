<?php

namespace Database\Seeders;

use App\Models\SponsoredContent;
use App\Models\User;
use App\Models\Post;
use App\Models\Topic;
use App\Models\Event;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SponsoredContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener usuarios patrocinadores
        $sponsors = User::take(10)->get();
        
        if ($sponsors->isEmpty()) {
            $this->command->warn('No hay usuarios disponibles. Ejecuta UserSeeder primero.');
            return;
        }

        // Crear diferentes tipos de contenido patrocinado
        $this->createPromotedPosts($sponsors);
        $this->createBannerAds($sponsors);
        $this->createSponsoredTopics($sponsors);
        $this->createEventPromotions($sponsors);
        $this->createJobPostings($sponsors);
        $this->createServiceHighlights($sponsors);
    }

    /**
     * Crear posts promocionados.
     */
    private function createPromotedPosts($sponsors): void
    {
        foreach ($sponsors->take(3) as $sponsor) {
            SponsoredContent::create([
                'sponsor_id' => $sponsor->id,
                'sponsorable_type' => 'App\\Models\\TopicPost',
                'sponsorable_id' => fake()->numberBetween(1, 100),
                'campaign_name' => 'Promoción Post - ' . fake()->words(2, true),
                'campaign_description' => fake()->paragraph(),
                'content_type' => 'promoted_post',
                'target_audience' => $this->getTargetAudience(),
                'target_topics' => $this->getTargetTopics(),
                'target_locations' => $this->getTargetLocations(),
                'target_demographics' => $this->getTargetDemographics(),
                'ad_label' => 'Patrocinado',
                'call_to_action' => fake()->randomElement(['Más información', 'Leer más', 'Ver detalles']),
                'destination_url' => fake()->url(),
                'creative_assets' => $this->getCreativeAssets(),
                'pricing_model' => fake()->randomElement(['cpm', 'cpc']),
                'bid_amount' => fake()->randomFloat(4, 0.50, 5.00),
                'daily_budget' => fake()->randomFloat(2, 50, 500),
                'total_budget' => fake()->randomFloat(2, 500, 5000),
                'spent_amount' => fake()->randomFloat(2, 0, 1000),
                'start_date' => fake()->dateTimeBetween('-30 days', '+7 days'),
                'end_date' => fake()->optional(0.8)->dateTimeBetween('+7 days', '+60 days'),
                'schedule_config' => $this->getScheduleConfig(),
                'status' => fake()->randomElement(['active', 'approved', 'pending_review']),
                'reviewed_by' => fake()->optional(0.7)->randomElement($sponsors->pluck('id')->toArray()),
                'reviewed_at' => fake()->optional(0.7)->dateTimeBetween('-30 days', 'now'),
                'review_notes' => fake()->optional(0.5)->sentence(),
                'impressions' => fake()->numberBetween(1000, 50000),
                'clicks' => fake()->numberBetween(50, 2500),
                'conversions' => fake()->numberBetween(5, 250),
                'ctr' => fake()->randomFloat(2, 1.0, 8.0),
                'conversion_rate' => fake()->randomFloat(2, 2.0, 15.0),
                'engagement_rate' => fake()->randomFloat(2, 3.0, 12.0),
                'show_sponsor_info' => fake()->boolean(80),
                'allow_user_feedback' => fake()->boolean(70),
                'disclosure_text' => $this->getDisclosureText(),
            ]);
        }
    }

    /**
     * Crear banners publicitarios.
     */
    private function createBannerAds($sponsors): void
    {
        foreach ($sponsors->take(3) as $sponsor) {
            SponsoredContent::create([
                'sponsor_id' => $sponsor->id,
                'sponsorable_type' => 'App\\Models\\NewsArticle',
                'sponsorable_id' => fake()->numberBetween(1, 50),
                'campaign_name' => 'Banner Ad - ' . fake()->words(2, true),
                'campaign_description' => fake()->paragraph(),
                'content_type' => 'banner_ad',
                'target_audience' => $this->getTargetAudience(),
                'target_topics' => $this->getTargetTopics(),
                'target_locations' => $this->getTargetLocations(),
                'target_demographics' => $this->getTargetDemographics(),
                'ad_label' => 'Publicidad',
                'call_to_action' => fake()->randomElement(['Comprar ahora', 'Obtener oferta', 'Registrarse']),
                'destination_url' => fake()->url(),
                'creative_assets' => $this->getCreativeAssets(),
                'pricing_model' => fake()->randomElement(['cpm', 'fixed']),
                'bid_amount' => fake()->randomFloat(4, 1.00, 10.00),
                'daily_budget' => fake()->randomFloat(2, 100, 1000),
                'total_budget' => fake()->randomFloat(2, 1000, 10000),
                'spent_amount' => fake()->randomFloat(2, 0, 2000),
                'start_date' => fake()->dateTimeBetween('-15 days', '+3 days'),
                'end_date' => fake()->optional(0.9)->dateTimeBetween('+3 days', '+90 days'),
                'schedule_config' => $this->getScheduleConfig(),
                'status' => fake()->randomElement(['active', 'approved', 'paused']),
                'reviewed_by' => fake()->optional(0.8)->randomElement($sponsors->pluck('id')->toArray()),
                'reviewed_at' => fake()->optional(0.8)->dateTimeBetween('-15 days', 'now'),
                'review_notes' => fake()->optional(0.4)->sentence(),
                'impressions' => fake()->numberBetween(5000, 100000),
                'clicks' => fake()->numberBetween(100, 5000),
                'conversions' => fake()->numberBetween(10, 500),
                'ctr' => fake()->randomFloat(2, 0.5, 5.0),
                'conversion_rate' => fake()->randomFloat(2, 1.0, 10.0),
                'engagement_rate' => fake()->randomFloat(2, 2.0, 8.0),
                'show_sponsor_info' => true,
                'allow_user_feedback' => fake()->boolean(60),
                'disclosure_text' => $this->getDisclosureText(),
            ]);
        }
    }

    /**
     * Crear temas patrocinados.
     */
    private function createSponsoredTopics($sponsors): void
    {
        foreach ($sponsors->take(2) as $sponsor) {
            SponsoredContent::create([
                'sponsor_id' => $sponsor->id,
                'sponsorable_type' => 'App\\Models\\Topic',
                'sponsorable_id' => fake()->numberBetween(1, 50),
                'campaign_name' => 'Tema Patrocinado - ' . fake()->words(2, true),
                'campaign_description' => fake()->paragraph(),
                'content_type' => 'sponsored_topic',
                'target_audience' => $this->getTargetAudience(),
                'target_topics' => $this->getTargetTopics(),
                'target_locations' => $this->getTargetLocations(),
                'target_demographics' => $this->getTargetDemographics(),
                'ad_label' => 'Contenido Patrocinado',
                'call_to_action' => fake()->randomElement(['Participar', 'Seguir tema', 'Más información']),
                'destination_url' => fake()->url(),
                'creative_assets' => $this->getCreativeAssets(),
                'pricing_model' => fake()->randomElement(['cpm', 'fixed']),
                'bid_amount' => fake()->randomFloat(4, 2.00, 8.00),
                'daily_budget' => fake()->randomFloat(2, 75, 750),
                'total_budget' => fake()->randomFloat(2, 750, 7500),
                'spent_amount' => fake()->randomFloat(2, 0, 1500),
                'start_date' => fake()->dateTimeBetween('-20 days', '+5 days'),
                'end_date' => fake()->optional(0.7)->dateTimeBetween('+5 days', '+45 days'),
                'schedule_config' => $this->getScheduleConfig(),
                'status' => fake()->randomElement(['active', 'approved']),
                'reviewed_by' => fake()->optional(0.9)->randomElement($sponsors->pluck('id')->toArray()),
                'reviewed_at' => fake()->optional(0.9)->dateTimeBetween('-20 days', 'now'),
                'review_notes' => fake()->optional(0.3)->sentence(),
                'impressions' => fake()->numberBetween(2000, 30000),
                'clicks' => fake()->numberBetween(80, 1500),
                'conversions' => fake()->numberBetween(8, 150),
                'ctr' => fake()->randomFloat(2, 2.0, 6.0),
                'conversion_rate' => fake()->randomFloat(2, 3.0, 12.0),
                'engagement_rate' => fake()->randomFloat(2, 4.0, 10.0),
                'show_sponsor_info' => fake()->boolean(90),
                'allow_user_feedback' => fake()->boolean(80),
                'disclosure_text' => $this->getDisclosureText(),
            ]);
        }
    }

    /**
     * Crear promociones de eventos.
     */
    private function createEventPromotions($sponsors): void
    {
        foreach ($sponsors->take(2) as $sponsor) {
            SponsoredContent::create([
                'sponsor_id' => $sponsor->id,
                'sponsorable_type' => 'App\\Models\\Event',
                'sponsorable_id' => fake()->numberBetween(1, 30),
                'campaign_name' => 'Promoción Evento - ' . fake()->words(2, true),
                'campaign_description' => fake()->paragraph(),
                'content_type' => 'event_promotion',
                'target_audience' => $this->getTargetAudience(),
                'target_topics' => $this->getTargetTopics(),
                'target_locations' => $this->getTargetLocations(),
                'target_demographics' => $this->getTargetDemographics(),
                'ad_label' => 'Evento Patrocinado',
                'call_to_action' => fake()->randomElement(['Registrarse', 'Comprar entrada', 'Más información']),
                'destination_url' => fake()->url(),
                'creative_assets' => $this->getCreativeAssets(),
                'pricing_model' => fake()->randomElement(['cpc', 'cpa', 'fixed']),
                'bid_amount' => fake()->randomFloat(4, 1.50, 6.00),
                'daily_budget' => fake()->randomFloat(2, 60, 600),
                'total_budget' => fake()->randomFloat(2, 600, 6000),
                'spent_amount' => fake()->randomFloat(2, 0, 1200),
                'start_date' => fake()->dateTimeBetween('-10 days', '+2 days'),
                'end_date' => fake()->optional(0.6)->dateTimeBetween('+2 days', '+30 days'),
                'schedule_config' => $this->getScheduleConfig(),
                'status' => fake()->randomElement(['active', 'approved', 'completed']),
                'reviewed_by' => fake()->optional(0.6)->randomElement($sponsors->pluck('id')->toArray()),
                'reviewed_at' => fake()->optional(0.6)->dateTimeBetween('-10 days', 'now'),
                'review_notes' => fake()->optional(0.2)->sentence(),
                'impressions' => fake()->numberBetween(1500, 25000),
                'clicks' => fake()->numberBetween(60, 1200),
                'conversions' => fake()->numberBetween(6, 120),
                'ctr' => fake()->randomFloat(2, 1.5, 5.5),
                'conversion_rate' => fake()->randomFloat(2, 2.5, 11.0),
                'engagement_rate' => fake()->randomFloat(2, 3.5, 9.0),
                'show_sponsor_info' => fake()->boolean(85),
                'allow_user_feedback' => fake()->boolean(75),
                'disclosure_text' => $this->getDisclosureText(),
            ]);
        }
    }

    /**
     * Crear ofertas de trabajo.
     */
    private function createJobPostings($sponsors): void
    {
        foreach ($sponsors->take(2) as $sponsor) {
            SponsoredContent::create([
                'sponsor_id' => $sponsor->id,
                'sponsorable_type' => 'App\\Models\\EnergyService',
                'sponsorable_id' => fake()->numberBetween(1, 30),
                'campaign_name' => 'Oferta Trabajo - ' . fake()->words(2, true),
                'campaign_description' => fake()->paragraph(),
                'content_type' => 'job_posting',
                'target_audience' => $this->getTargetAudience(),
                'target_topics' => $this->getTargetTopics(),
                'target_locations' => $this->getTargetLocations(),
                'target_demographics' => $this->getTargetDemographics(),
                'ad_label' => 'Oferta de Trabajo',
                'call_to_action' => fake()->randomElement(['Aplicar', 'Ver oferta', 'Enviar CV']),
                'destination_url' => fake()->url(),
                'creative_assets' => $this->getCreativeAssets(),
                'pricing_model' => fake()->randomElement(['cpc', 'cpa', 'fixed']),
                'bid_amount' => fake()->randomFloat(4, 3.00, 12.00),
                'daily_budget' => fake()->randomFloat(2, 80, 800),
                'total_budget' => fake()->randomFloat(2, 800, 8000),
                'spent_amount' => fake()->randomFloat(2, 0, 1600),
                'start_date' => fake()->dateTimeBetween('-25 days', '+10 days'),
                'end_date' => fake()->optional(0.5)->dateTimeBetween('+10 days', '+60 days'),
                'schedule_config' => $this->getScheduleConfig(),
                'status' => fake()->randomElement(['active', 'approved', 'paused']),
                'reviewed_by' => fake()->optional(0.5)->randomElement($sponsors->pluck('id')->toArray()),
                'reviewed_at' => fake()->optional(0.5)->dateTimeBetween('-25 days', 'now'),
                'review_notes' => fake()->optional(0.1)->sentence(),
                'impressions' => fake()->numberBetween(3000, 40000),
                'clicks' => fake()->numberBetween(120, 2000),
                'conversions' => fake()->numberBetween(12, 200),
                'ctr' => fake()->randomFloat(2, 2.5, 6.5),
                'conversion_rate' => fake()->randomFloat(2, 4.0, 14.0),
                'engagement_rate' => fake()->randomFloat(2, 5.0, 11.0),
                'show_sponsor_info' => fake()->boolean(95),
                'allow_user_feedback' => fake()->boolean(65),
                'disclosure_text' => $this->getDisclosureText(),
            ]);
        }
    }

    /**
     * Crear destacados de servicios.
     */
    private function createServiceHighlights($sponsors): void
    {
        foreach ($sponsors->take(2) as $sponsor) {
            SponsoredContent::create([
                'sponsor_id' => $sponsor->id,
                'sponsorable_type' => 'App\\Models\\ConsultationService',
                'sponsorable_id' => fake()->numberBetween(1, 40),
                'campaign_name' => 'Servicio Destacado - ' . fake()->words(2, true),
                'campaign_description' => fake()->paragraph(),
                'content_type' => 'service_highlight',
                'target_audience' => $this->getTargetAudience(),
                'target_topics' => $this->getTargetTopics(),
                'target_locations' => $this->getTargetLocations(),
                'target_demographics' => $this->getTargetDemographics(),
                'ad_label' => 'Servicio Patrocinado',
                'call_to_action' => fake()->randomElement(['Contactar', 'Solicitar info', 'Ver servicios']),
                'destination_url' => fake()->url(),
                'creative_assets' => $this->getCreativeAssets(),
                'pricing_model' => fake()->randomElement(['cpm', 'cpc', 'fixed']),
                'bid_amount' => fake()->randomFloat(4, 2.50, 7.50),
                'daily_budget' => fake()->randomFloat(2, 70, 700),
                'total_budget' => fake()->randomFloat(2, 700, 7000),
                'spent_amount' => fake()->randomFloat(2, 0, 1400),
                'start_date' => fake()->dateTimeBetween('-18 days', '+8 days'),
                'end_date' => fake()->optional(0.4)->dateTimeBetween('+8 days', '+50 days'),
                'schedule_config' => $this->getScheduleConfig(),
                'status' => fake()->randomElement(['active', 'approved', 'draft']),
                'reviewed_by' => fake()->optional(0.4)->randomElement($sponsors->pluck('id')->toArray()),
                'reviewed_at' => fake()->optional(0.4)->dateTimeBetween('-18 days', 'now'),
                'review_notes' => fake()->optional(0.05)->sentence(),
                'impressions' => fake()->numberBetween(2500, 35000),
                'clicks' => fake()->numberBetween(100, 1750),
                'conversions' => fake()->numberBetween(10, 175),
                'ctr' => fake()->randomFloat(2, 2.0, 6.0),
                'conversion_rate' => fake()->randomFloat(2, 3.5, 13.0),
                'engagement_rate' => fake()->randomFloat(2, 4.5, 10.5),
                'show_sponsor_info' => fake()->boolean(88),
                'allow_user_feedback' => fake()->boolean(72),
                'disclosure_text' => $this->getDisclosureText(),
            ]);
        }
    }

    /**
     * Obtener audiencia objetivo.
     */
    private function getTargetAudience(): array
    {
        return fake()->randomElements([
            'profesionales', 'empresarios', 'estudiantes', 'inversores',
            'consumidores', 'tecnólogos', 'sostenibilidad', 'energía'
        ], fake()->numberBetween(1, 3));
    }

    /**
     * Obtener temas objetivo.
     */
    private function getTargetTopics(): array
    {
        return fake()->randomElements([
            'energía_renovable', 'sostenibilidad', 'tecnología', 'innovación',
            'medio_ambiente', 'economía_circular', 'eficiencia_energética'
        ], fake()->numberBetween(1, 2));
    }

    /**
     * Obtener ubicaciones objetivo.
     */
    private function getTargetLocations(): array
    {
        return fake()->randomElements([
            'Madrid', 'Barcelona', 'Valencia', 'Sevilla', 'Bilbao',
            'Málaga', 'Zaragoza', 'Murcia', 'Palma', 'Las Palmas'
        ], fake()->numberBetween(1, 3));
    }

    /**
     * Obtener demografía objetivo.
     */
    private function getTargetDemographics(): array
    {
        return [
            'age_range' => fake()->randomElement(['25-35', '35-45', '45-55', '25-55']),
            'gender' => fake()->randomElement(['all', 'male', 'female']),
            'income_level' => fake()->randomElement(['medium', 'high', 'all']),
            'education' => fake()->randomElement(['university', 'all', 'professional'])
        ];
    }

    /**
     * Obtener activos creativos.
     */
    private function getCreativeAssets(): array
    {
        return [
            'images' => [
                fake()->imageUrl(800, 600),
                fake()->imageUrl(1200, 400)
            ],
            'videos' => fake()->optional(0.3)->randomElements([
                fake()->url() . '/video1.mp4',
                fake()->url() . '/video2.mp4'
            ], 1),
            'logos' => [fake()->imageUrl(200, 200)]
        ];
    }

    /**
     * Obtener configuración de horarios.
     */
    private function getScheduleConfig(): array
    {
        return [
            'days_of_week' => fake()->randomElements(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'], fake()->numberBetween(3, 7)),
            'hours' => [
                'start' => fake()->randomElement(['08:00', '09:00', '10:00']),
                'end' => fake()->randomElement(['18:00', '19:00', '20:00'])
            ],
            'timezone' => 'Europe/Madrid'
        ];
    }

    /**
     * Obtener texto de divulgación.
     */
    private function getDisclosureText(): array
    {
        return [
            'es' => fake()->randomElement([
                'Contenido patrocinado',
                'Publicidad',
                'Contenido promocional',
                'Anuncio'
            ]),
            'en' => fake()->randomElement([
                'Sponsored content',
                'Advertisement',
                'Promotional content',
                'Ad'
            ])
        ];
    }
}