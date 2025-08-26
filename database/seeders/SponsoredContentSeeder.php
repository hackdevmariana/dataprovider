<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SponsoredContent;
use App\Models\User;
use App\Models\TopicPost;
use App\Models\Event;
use App\Models\Cooperative;
use App\Models\NewsArticle;
use Illuminate\Support\Str;

class SponsoredContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar si ya existen contenidos patrocinados
        if (SponsoredContent::count() > 0) {
            $this->command->info('Ya existen contenidos patrocinados en la base de datos. Creando contenidos adicionales...');
        }

        // Crear contenidos patrocinados para diferentes tipos
        $this->createPromotedPosts();
        $this->createBannerAds();
        $this->createSponsoredTopics();
        $this->createProductPlacements();
        $this->createNativeContent();
        $this->createEventPromotions();
        $this->createJobPostings();
        $this->createServiceHighlights();

        $this->command->info('✅ Se han creado/actualizado los contenidos patrocinados del sistema.');
    }

    private function createPromotedPosts(): void
    {
        $sponsors = User::take(3)->get();
        $posts = TopicPost::take(5)->get();

        foreach ($sponsors as $sponsor) {
            foreach ($posts->take(2) as $post) {
                $this->createOrUpdateSponsoredContent([
                    'sponsor_id' => $sponsor->id,
                    'sponsorable_type' => TopicPost::class,
                    'sponsorable_id' => $post->id,
                    'campaign_name' => 'Promoción Post: ' . Str::limit($post->title, 30),
                    'campaign_description' => 'Campaña para promocionar contenido relevante sobre ' . Str::limit($post->title, 50),
                    'content_type' => 'promoted_post',
                    'target_audience' => ['profesionales_energia', 'interesados_sostenibilidad'],
                    'target_topics' => ['energia_renovable', 'sostenibilidad'],
                    'target_locations' => ['España', 'Europa'],
                    'target_demographics' => ['25-45', 'universitarios'],
                    'ad_label' => 'Patrocinado',
                    'call_to_action' => 'Leer más',
                    'destination_url' => 'https://example.com/posts/' . $post->id,
                    'creative_assets' => [
                        'banner' => 'https://example.com/banners/post-' . $post->id . '.jpg',
                        'thumbnail' => 'https://example.com/thumbnails/post-' . $post->id . '.jpg',
                    ],
                    'pricing_model' => 'cpc',
                    'bid_amount' => rand(50, 200) / 100,
                    'daily_budget' => rand(50, 200),
                    'total_budget' => rand(500, 2000),
                    'spent_amount' => rand(0, 100),
                    'start_date' => now()->subDays(rand(1, 30)),
                    'end_date' => now()->addDays(rand(30, 90)),
                    'schedule_config' => [
                        'monday' => ['09:00-18:00'],
                        'tuesday' => ['09:00-18:00'],
                        'wednesday' => ['09:00-18:00'],
                        'thursday' => ['09:00-18:00'],
                        'friday' => ['09:00-18:00'],
                    ],
                    'status' => $this->getRandomStatus(),
                    'impressions' => rand(1000, 10000),
                    'clicks' => rand(50, 500),
                    'conversions' => rand(5, 50),
                    'ctr' => rand(20, 80) / 100,
                    'conversion_rate' => rand(5, 15) / 100,
                    'engagement_rate' => rand(10, 30) / 100,
                    'show_sponsor_info' => true,
                    'allow_user_feedback' => true,
                    'disclosure_text' => [
                        'es' => 'Contenido patrocinado por ' . $sponsor->name,
                        'en' => 'Sponsored content by ' . $sponsor->name,
                    ],
                ]);
            }
        }
    }

    private function createBannerAds(): void
    {
        $sponsors = User::take(2)->get();
        $cooperatives = Cooperative::take(3)->get();

        foreach ($sponsors as $sponsor) {
            foreach ($cooperatives->take(2) as $cooperative) {
                $this->createOrUpdateSponsoredContent([
                    'sponsor_id' => $sponsor->id,
                    'sponsorable_type' => Cooperative::class,
                    'sponsorable_id' => $cooperative->id,
                    'campaign_name' => 'Banner Cooperativa: ' . Str::limit($cooperative->name, 30),
                    'campaign_description' => 'Campaña de banner para promocionar ' . $cooperative->name,
                    'content_type' => 'banner_ad',
                    'target_audience' => ['miembros_cooperativas', 'interesados_energia_comunitaria'],
                    'target_topics' => ['energia_comunitaria', 'cooperativas'],
                    'target_locations' => [$cooperative->region->name ?? 'España'],
                    'target_demographics' => ['30-60', 'propietarios_vivienda'],
                    'ad_label' => 'Anuncio',
                    'call_to_action' => 'Unirse',
                    'destination_url' => 'https://example.com/cooperatives/' . $cooperative->id,
                    'creative_assets' => [
                        'banner_728x90' => 'https://example.com/banners/728x90-' . $cooperative->id . '.jpg',
                        'banner_300x250' => 'https://example.com/banners/300x250-' . $cooperative->id . '.jpg',
                    ],
                    'pricing_model' => 'cpm',
                    'bid_amount' => rand(2, 8) / 1000,
                    'daily_budget' => rand(100, 500),
                    'total_budget' => rand(2000, 10000),
                    'spent_amount' => rand(0, 500),
                    'start_date' => now()->subDays(rand(1, 15)),
                    'end_date' => now()->addDays(rand(60, 120)),
                    'schedule_config' => [
                        'all_days' => ['00:00-23:59'],
                    ],
                    'status' => $this->getRandomStatus(),
                    'impressions' => rand(50000, 200000),
                    'clicks' => rand(200, 1500),
                    'conversions' => rand(20, 100),
                    'ctr' => rand(10, 50) / 1000,
                    'conversion_rate' => rand(8, 20) / 100,
                    'engagement_rate' => rand(15, 40) / 100,
                    'show_sponsor_info' => true,
                    'allow_user_feedback' => false,
                    'disclosure_text' => [
                        'es' => 'Anuncio de ' . $cooperative->name,
                        'en' => 'Ad by ' . $cooperative->name,
                    ],
                ]);
            }
        }
    }

    private function createSponsoredTopics(): void
    {
        $sponsors = User::take(2)->get();
        $topics = ['energia_solar', 'energia_eolica', 'eficiencia_energetica', 'movilidad_electrica'];

        foreach ($sponsors as $sponsor) {
            foreach ($topics as $topic) {
                $this->createOrUpdateSponsoredContent([
                    'sponsor_id' => $sponsor->id,
                    'sponsorable_type' => TopicPost::class,
                    'sponsorable_id' => TopicPost::inRandomOrder()->first()->id,
                    'campaign_name' => 'Tema Patrocinado: ' . Str::title(str_replace('_', ' ', $topic)),
                    'campaign_description' => 'Campaña para destacar contenido sobre ' . str_replace('_', ' ', $topic),
                    'content_type' => 'sponsored_topic',
                    'target_audience' => ['profesionales_energia', 'estudiantes', 'inversores'],
                    'target_topics' => [$topic, 'sostenibilidad'],
                    'target_locations' => ['España', 'Europa', 'Latinoamérica'],
                    'target_demographics' => ['18-65', 'universitarios', 'profesionales'],
                    'ad_label' => 'Tema Patrocinado',
                    'call_to_action' => 'Explorar',
                    'destination_url' => 'https://example.com/topics/' . $topic,
                    'creative_assets' => [
                        'hero_image' => 'https://example.com/topics/' . $topic . '/hero.jpg',
                        'icon' => 'https://example.com/topics/' . $topic . '/icon.svg',
                    ],
                    'pricing_model' => 'fixed',
                    'bid_amount' => rand(100, 500) / 100,
                    'daily_budget' => rand(200, 800),
                    'total_budget' => rand(5000, 15000),
                    'spent_amount' => rand(0, 1000),
                    'start_date' => now()->subDays(rand(1, 10)),
                    'end_date' => now()->addDays(rand(90, 180)),
                    'schedule_config' => [
                        'weekdays' => ['08:00-20:00'],
                        'weekends' => ['10:00-18:00'],
                    ],
                    'status' => $this->getRandomStatus(),
                    'impressions' => rand(20000, 80000),
                    'clicks' => rand(500, 2000),
                    'conversions' => rand(50, 200),
                    'ctr' => rand(15, 60) / 1000,
                    'conversion_rate' => rand(10, 25) / 100,
                    'engagement_rate' => rand(20, 45) / 100,
                    'show_sponsor_info' => true,
                    'allow_user_feedback' => true,
                    'disclosure_text' => [
                        'es' => 'Tema patrocinado por ' . $sponsor->name,
                        'en' => 'Sponsored topic by ' . $sponsor->name,
                    ],
                ]);
            }
        }
    }

    private function createProductPlacements(): void
    {
        $sponsors = User::take(2)->get();
        $products = ['paneles_solares', 'baterias_litio', 'aerogeneradores', 'cargadores_electricos'];

        foreach ($sponsors as $sponsor) {
            foreach ($products as $product) {
                $this->createOrUpdateSponsoredContent([
                    'sponsor_id' => $sponsor->id,
                    'sponsorable_type' => TopicPost::class,
                    'sponsorable_id' => TopicPost::inRandomOrder()->first()->id,
                    'campaign_name' => 'Producto: ' . Str::title(str_replace('_', ' ', $product)),
                    'campaign_description' => 'Placement de producto para ' . str_replace('_', ' ', $product),
                    'content_type' => 'product_placement',
                    'target_audience' => ['instaladores', 'propietarios_vivienda', 'empresas'],
                    'target_topics' => [$product, 'tecnologia_verde'],
                    'target_locations' => ['España', 'Portugal', 'Francia'],
                    'target_demographics' => ['25-55', 'propietarios', 'profesionales'],
                    'ad_label' => 'Producto Destacado',
                    'call_to_action' => 'Ver Producto',
                    'destination_url' => 'https://example.com/products/' . $product,
                    'creative_assets' => [
                        'product_image' => 'https://example.com/products/' . $product . '/main.jpg',
                        'gallery' => 'https://example.com/products/' . $product . '/gallery.json',
                    ],
                    'pricing_model' => 'cpa',
                    'bid_amount' => rand(500, 2000) / 100,
                    'daily_budget' => rand(300, 1000),
                    'total_budget' => rand(8000, 25000),
                    'spent_amount' => rand(0, 2000),
                    'start_date' => now()->subDays(rand(1, 5)),
                    'end_date' => now()->addDays(rand(120, 365)),
                    'schedule_config' => [
                        'business_hours' => ['09:00-17:00'],
                        'timezone' => 'Europe/Madrid',
                    ],
                    'status' => $this->getRandomStatus(),
                    'impressions' => rand(15000, 60000),
                    'clicks' => rand(300, 1200),
                    'conversions' => rand(30, 150),
                    'ctr' => rand(20, 70) / 1000,
                    'conversion_rate' => rand(15, 30) / 100,
                    'engagement_rate' => rand(25, 50) / 100,
                    'show_sponsor_info' => true,
                    'allow_user_feedback' => true,
                    'disclosure_text' => [
                        'es' => 'Producto destacado por ' . $sponsor->name,
                        'en' => 'Featured product by ' . $sponsor->name,
                    ],
                ]);
            }
        }
    }

    private function createNativeContent(): void
    {
        $sponsors = User::take(2)->get();
        $articles = NewsArticle::take(3)->get();

        foreach ($sponsors as $sponsor) {
            foreach ($articles->take(2) as $article) {
                $this->createOrUpdateSponsoredContent([
                    'sponsor_id' => $sponsor->id,
                    'sponsorable_type' => NewsArticle::class,
                    'sponsorable_id' => $article->id,
                    'campaign_name' => 'Contenido Nativo: ' . Str::limit($article->title, 30),
                    'campaign_description' => 'Contenido nativo integrado sobre ' . Str::limit($article->title, 50),
                    'content_type' => 'native_content',
                    'target_audience' => ['lectores_energia', 'profesionales_sector'],
                    'target_topics' => ['noticias_energia', 'innovacion'],
                    'target_locations' => ['España', 'Europa'],
                    'target_demographics' => ['25-60', 'universitarios', 'profesionales'],
                    'ad_label' => 'Contenido Patrocinado',
                    'call_to_action' => 'Leer Artículo',
                    'destination_url' => 'https://example.com/news/' . $article->id,
                    'creative_assets' => [
                        'featured_image' => 'https://example.com/news/' . $article->id . '/featured.jpg',
                        'inline_content' => 'https://example.com/news/' . $article->id . '/inline.json',
                    ],
                    'pricing_model' => 'cpm',
                    'bid_amount' => rand(3, 12) / 1000,
                    'daily_budget' => rand(150, 600),
                    'total_budget' => rand(3000, 12000),
                    'spent_amount' => rand(0, 800),
                    'start_date' => now()->subDays(rand(1, 7)),
                    'end_date' => now()->addDays(rand(45, 90)),
                    'schedule_config' => [
                        'content_placement' => ['header', 'sidebar', 'inline'],
                        'frequency' => 'daily',
                    ],
                    'status' => $this->getRandomStatus(),
                    'impressions' => rand(25000, 100000),
                    'clicks' => rand(800, 3000),
                    'conversions' => rand(100, 400),
                    'ctr' => rand(25, 80) / 1000,
                    'conversion_rate' => rand(12, 28) / 100,
                    'engagement_rate' => rand(30, 55) / 100,
                    'show_sponsor_info' => false,
                    'allow_user_feedback' => true,
                    'disclosure_text' => [
                        'es' => 'Contenido patrocinado',
                        'en' => 'Sponsored content',
                    ],
                ]);
            }
        }
    }

    private function createEventPromotions(): void
    {
        $sponsors = User::take(2)->get();
        $events = Event::take(4)->get();

        foreach ($sponsors as $sponsor) {
            foreach ($events->take(2) as $event) {
                $this->createOrUpdateSponsoredContent([
                    'sponsor_id' => $sponsor->id,
                    'sponsorable_type' => Event::class,
                    'sponsorable_id' => $event->id,
                    'campaign_name' => 'Evento: ' . Str::limit($event->name, 30),
                    'campaign_description' => 'Promoción del evento ' . $event->name,
                    'content_type' => 'event_promotion',
                    'target_audience' => ['asistentes_eventos', 'profesionales_energia'],
                    'target_topics' => ['eventos_energia', 'networking'],
                    'target_locations' => [$event->venue->city ?? 'España'],
                    'target_demographics' => ['25-65', 'profesionales', 'empresarios'],
                    'ad_label' => 'Evento Patrocinado',
                    'call_to_action' => 'Registrarse',
                    'destination_url' => 'https://example.com/events/' . $event->id . '/register',
                    'creative_assets' => [
                        'event_banner' => 'https://example.com/events/' . $event->id . '/banner.jpg',
                        'event_logo' => 'https://example.com/events/' . $event->id . '/logo.png',
                    ],
                    'pricing_model' => 'cpc',
                    'bid_amount' => rand(100, 400) / 100,
                    'daily_budget' => rand(200, 800),
                    'total_budget' => rand(4000, 15000),
                    'spent_amount' => rand(0, 1200),
                    'start_date' => now()->subDays(rand(1, 3)),
                    'end_date' => $event->start_date ?? now()->addDays(rand(30, 60)),
                    'schedule_config' => [
                        'urgency' => 'high',
                        'reminder_frequency' => 'daily',
                    ],
                    'status' => $this->getRandomStatus(),
                    'impressions' => rand(30000, 120000),
                    'clicks' => rand(1000, 4000),
                    'conversions' => rand(150, 600),
                    'ctr' => rand(30, 90) / 1000,
                    'conversion_rate' => rand(18, 35) / 100,
                    'engagement_rate' => rand(35, 60) / 100,
                    'show_sponsor_info' => true,
                    'allow_user_feedback' => false,
                    'disclosure_text' => [
                        'es' => 'Evento patrocinado por ' . $sponsor->name,
                        'en' => 'Event sponsored by ' . $sponsor->name,
                    ],
                ]);
            }
        }
    }

    private function createJobPostings(): void
    {
        $sponsors = User::take(2)->get();
        $jobTitles = ['Ingeniero Solar', 'Técnico Eólico', 'Consultor Sostenibilidad', 'Instalador Fotovoltaico'];

        foreach ($sponsors as $sponsor) {
            foreach ($jobTitles as $jobTitle) {
                $this->createOrUpdateSponsoredContent([
                    'sponsor_id' => $sponsor->id,
                    'sponsorable_type' => TopicPost::class,
                    'sponsorable_id' => TopicPost::inRandomOrder()->first()->id,
                    'campaign_name' => 'Oferta de Trabajo: ' . $jobTitle,
                    'campaign_description' => 'Oferta de trabajo para ' . $jobTitle,
                    'content_type' => 'job_posting',
                    'target_audience' => ['profesionales_energia', 'desempleados', 'cambio_carrera'],
                    'target_topics' => ['empleo_energia', 'carrera_profesional'],
                    'target_locations' => ['España', 'Madrid', 'Barcelona', 'Valencia'],
                    'target_demographics' => ['22-45', 'universitarios', 'profesionales'],
                    'ad_label' => 'Oferta de Trabajo',
                    'call_to_action' => 'Aplicar',
                    'destination_url' => 'https://example.com/jobs/' . Str::slug($jobTitle),
                    'creative_assets' => [
                        'job_banner' => 'https://example.com/jobs/' . Str::slug($jobTitle) . '/banner.jpg',
                        'company_logo' => 'https://example.com/companies/' . $sponsor->id . '/logo.png',
                    ],
                    'pricing_model' => 'cpa',
                    'bid_amount' => rand(800, 3000) / 100,
                    'daily_budget' => rand(300, 1000),
                    'total_budget' => rand(6000, 20000),
                    'spent_amount' => rand(0, 1500),
                    'start_date' => now()->subDays(rand(1, 2)),
                    'end_date' => now()->addDays(rand(45, 90)),
                    'schedule_config' => [
                        'priority' => 'high',
                        'targeting' => 'active_job_seekers',
                    ],
                    'status' => $this->getRandomStatus(),
                    'impressions' => rand(20000, 80000),
                    'clicks' => rand(600, 2500),
                    'conversions' => rand(80, 300),
                    'ctr' => rand(25, 75) / 1000,
                    'conversion_rate' => rand(20, 40) / 100,
                    'engagement_rate' => rand(40, 65) / 100,
                    'show_sponsor_info' => true,
                    'allow_user_feedback' => true,
                    'disclosure_text' => [
                        'es' => 'Oferta de trabajo de ' . $sponsor->name,
                        'en' => 'Job posting by ' . $sponsor->name,
                    ],
                ]);
            }
        }
    }

    private function createServiceHighlights(): void
    {
        $sponsors = User::take(2)->get();
        $services = ['auditoria_energetica', 'instalacion_solar', 'mantenimiento_eolico', 'consultoria_sostenibilidad'];

        foreach ($sponsors as $sponsor) {
            foreach ($services as $service) {
                $this->createOrUpdateSponsoredContent([
                    'sponsor_id' => $sponsor->id,
                    'sponsorable_type' => TopicPost::class,
                    'sponsorable_id' => TopicPost::inRandomOrder()->first()->id,
                    'campaign_name' => 'Servicio: ' . Str::title(str_replace('_', ' ', $service)),
                    'campaign_description' => 'Destacar el servicio de ' . str_replace('_', ' ', $service),
                    'content_type' => 'service_highlight',
                    'target_audience' => ['empresas', 'propietarios_vivienda', 'administradores'],
                    'target_topics' => [$service, 'servicios_energia'],
                    'target_locations' => ['España', 'Portugal'],
                    'target_demographics' => ['30-65', 'propietarios', 'empresarios'],
                    'ad_label' => 'Servicio Destacado',
                    'call_to_action' => 'Contactar',
                    'destination_url' => 'https://example.com/services/' . $service,
                    'creative_assets' => [
                        'service_image' => 'https://example.com/services/' . $service . '/main.jpg',
                        'testimonials' => 'https://example.com/services/' . $service . '/testimonials.json',
                    ],
                    'pricing_model' => 'fixed',
                    'bid_amount' => rand(200, 800) / 100,
                    'daily_budget' => rand(250, 900),
                    'total_budget' => rand(5000, 18000),
                    'spent_amount' => rand(0, 1000),
                    'start_date' => now()->subDays(rand(1, 4)),
                    'end_date' => now()->addDays(rand(75, 150)),
                    'schedule_config' => [
                        'business_hours' => ['08:00-18:00'],
                        'weekdays_only' => true,
                    ],
                    'status' => $this->getRandomStatus(),
                    'impressions' => rand(18000, 70000),
                    'clicks' => rand(400, 1800),
                    'conversions' => rand(60, 250),
                    'ctr' => rand(22, 68) / 1000,
                    'conversion_rate' => rand(16, 32) / 100,
                    'engagement_rate' => rand(28, 52) / 100,
                    'show_sponsor_info' => true,
                    'allow_user_feedback' => true,
                    'disclosure_text' => [
                        'es' => 'Servicio destacado por ' . $sponsor->name,
                        'en' => 'Featured service by ' . $sponsor->name,
                    ],
                ]);
            }
        }
    }

    private function createOrUpdateSponsoredContent(array $contentData): void
    {
        // Buscar contenido existente por campaña y patrocinador
        $existingContent = SponsoredContent::where('campaign_name', $contentData['campaign_name'])
                                         ->where('sponsor_id', $contentData['sponsor_id'])
                                         ->first();

        if ($existingContent) {
            // Actualizar contenido existente
            $existingContent->update($contentData);
            $this->command->info("✅ Contenido patrocinado actualizado: {$contentData['campaign_name']}");
        } else {
            // Crear nuevo contenido
            SponsoredContent::create($contentData);
            $this->command->info("🆕 Contenido patrocinado creado: {$contentData['campaign_name']}");
        }
    }

    private function getRandomStatus(): string
    {
        $statuses = ['draft', 'pending_review', 'approved', 'active', 'paused', 'completed'];
        $weights = [10, 20, 25, 30, 10, 5]; // Probabilidades relativas
        
        $random = rand(1, 100);
        $cumulative = 0;
        
        foreach ($statuses as $index => $status) {
            $cumulative += $weights[$index];
            if ($random <= $cumulative) {
                return $status;
            }
        }
        
        return 'draft'; // Fallback
    }
}

