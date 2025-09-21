<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SponsoredContent;
use App\Models\User;
use App\Models\TopicPost;
use App\Models\Event;
use App\Models\Cooperative;
use App\Models\NewsArticle;
use Carbon\Carbon;

class SponsoredContentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener usuarios existentes
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->command->warn('No hay usuarios disponibles. Ejecuta UsersSeeder primero.');
            return;
        }

        // Obtener contenido existente
        $topicPosts = TopicPost::all();
        $events = Event::all();
        $cooperatives = Cooperative::all();
        $newsArticles = NewsArticle::all();

        $sponsoredContents = [
            // Campaña de post promocionado
            [
                'sponsor_id' => $users->random()->id,
                'sponsorable_type' => 'App\Models\TopicPost',
                'sponsorable_id' => $topicPosts->isNotEmpty() ? $topicPosts->random()->id : 1,
                'campaign_name' => 'Campaña Solar Residencial 2024',
                'campaign_description' => 'Promoción de instalaciones solares residenciales con descuentos especiales',
                'content_type' => 'promoted_post',
                'target_audience' => ['propietarios_vivienda', 'profesionales_energia'],
                'target_topics' => ['energia_solar', 'autoconsumo', 'sostenibilidad'],
                'target_locations' => ['madrid', 'barcelona', 'valencia'],
                'target_demographics' => ['edad_35_45', 'ingresos_medios', 'propietarios_vivienda'],
                'ad_label' => 'Patrocinado',
                'call_to_action' => 'Solicita tu presupuesto gratuito',
                'destination_url' => 'https://ejemplo.com/solar-residencial',
                'creative_assets' => [
                    'banner_principal' => 'https://ejemplo.com/banner-solar.jpg',
                    'imagen_secundaria' => 'https://ejemplo.com/imagen-solar.jpg',
                    'video_promocional' => 'https://ejemplo.com/video-solar.mp4',
                    'logo_patrocinador' => 'https://ejemplo.com/logo-solar.png'
                ],
                'pricing_model' => 'cpc',
                'bid_amount' => 0.50,
                'daily_budget' => 100.00,
                'total_budget' => 3000.00,
                'spent_amount' => 1250.00,
                'start_date' => Carbon::now()->subDays(30),
                'end_date' => Carbon::now()->addDays(30),
                'schedule_config' => [
                    'lunes_09_12' => 'activo',
                    'martes_09_12' => 'activo',
                    'miercoles_09_12' => 'activo',
                    'jueves_09_12' => 'activo',
                    'viernes_09_12' => 'activo',
                    'fin_de_semana' => 'pausado'
                ],
                'status' => 'active',
                'reviewed_by' => $users->random()->id,
                'reviewed_at' => Carbon::now()->subDays(25),
                'review_notes' => 'Campaña aprobada tras revisión de contenido',
                'impressions' => 125000,
                'clicks' => 2500,
                'conversions' => 125,
                'ctr' => 2.0,
                'conversion_rate' => 5.0,
                'engagement_rate' => 3.5,
                'show_sponsor_info' => true,
                'allow_user_feedback' => true,
                'disclosure_text' => [
                    'es' => 'Contenido patrocinado',
                    'en' => 'Sponsored content',
                    'ca' => 'Contingut patrocinat'
                ],
            ],

            // Campaña de banner publicitario
            [
                'sponsor_id' => $users->random()->id,
                'sponsorable_type' => 'App\Models\Event',
                'sponsorable_id' => $events->isNotEmpty() ? $events->random()->id : 1,
                'campaign_name' => 'Banner Congreso Energía Verde',
                'campaign_description' => 'Promoción del congreso internacional de energía verde',
                'content_type' => 'banner_ad',
                'target_audience' => ['profesionales_energia', 'empresas_sostenibles'],
                'target_topics' => ['energias_renovables', 'sostenibilidad', 'innovacion'],
                'target_locations' => ['madrid', 'barcelona', 'sevilla'],
                'target_demographics' => ['edad_25_35', 'educacion_superior', 'profesionales_tecnologia'],
                'ad_label' => 'Publicidad',
                'call_to_action' => 'Regístrate ahora',
                'destination_url' => 'https://congreso-energia-verde.com',
                'creative_assets' => [
                    'banner_principal' => 'https://ejemplo.com/banner-congreso.jpg',
                    'imagen_secundaria' => 'https://ejemplo.com/imagen-congreso.jpg',
                    'logo_patrocinador' => 'https://ejemplo.com/logo-congreso.png'
                ],
                'pricing_model' => 'cpm',
                'bid_amount' => 2.50,
                'daily_budget' => 200.00,
                'total_budget' => 5000.00,
                'spent_amount' => 2100.00,
                'start_date' => Carbon::now()->subDays(45),
                'end_date' => Carbon::now()->addDays(15),
                'schedule_config' => [
                    'lunes_09_12' => 'activo',
                    'martes_09_12' => 'activo',
                    'miercoles_09_12' => 'activo',
                    'jueves_09_12' => 'activo',
                    'viernes_09_12' => 'activo',
                    'fin_de_semana' => 'activo'
                ],
                'status' => 'active',
                'reviewed_by' => $users->random()->id,
                'reviewed_at' => Carbon::now()->subDays(40),
                'review_notes' => 'Banner aprobado para evento profesional',
                'impressions' => 850000,
                'clicks' => 8500,
                'conversions' => 425,
                'ctr' => 1.0,
                'conversion_rate' => 5.0,
                'engagement_rate' => 2.8,
                'show_sponsor_info' => true,
                'allow_user_feedback' => false,
                'disclosure_text' => [
                    'es' => 'Publicidad',
                    'en' => 'Advertisement',
                    'ca' => 'Publicitat'
                ],
            ],

            // Campaña de tema patrocinado
            [
                'sponsor_id' => $users->random()->id,
                'sponsorable_type' => 'App\Models\Cooperative',
                'sponsorable_id' => $cooperatives->isNotEmpty() ? $cooperatives->random()->id : 1,
                'campaign_name' => 'Tema Patrocinado Cooperativa Solar',
                'campaign_description' => 'Promoción de cooperativa de energía solar comunitaria',
                'content_type' => 'sponsored_topic',
                'target_audience' => ['propietarios_vivienda', 'comunidades_vecinos'],
                'target_topics' => ['energia_solar', 'cooperativas', 'comunidad'],
                'target_locations' => ['madrid', 'barcelona', 'valencia', 'sevilla'],
                'target_demographics' => ['edad_35_55', 'ingresos_medios', 'propietarios_vivienda'],
                'ad_label' => 'Patrocinado',
                'call_to_action' => 'Únete a nuestra cooperativa',
                'destination_url' => 'https://cooperativa-solar.com',
                'creative_assets' => [
                    'banner_principal' => 'https://ejemplo.com/banner-cooperativa.jpg',
                    'imagen_secundaria' => 'https://ejemplo.com/imagen-cooperativa.jpg',
                    'logo_patrocinador' => 'https://ejemplo.com/logo-cooperativa.png'
                ],
                'pricing_model' => 'fixed',
                'bid_amount' => 0.0,
                'daily_budget' => 150.00,
                'total_budget' => 4000.00,
                'spent_amount' => 1800.00,
                'start_date' => Carbon::now()->subDays(20),
                'end_date' => Carbon::now()->addDays(40),
                'schedule_config' => [
                    'lunes_09_12' => 'activo',
                    'martes_09_12' => 'activo',
                    'miercoles_09_12' => 'activo',
                    'jueves_09_12' => 'activo',
                    'viernes_09_12' => 'activo',
                    'fin_de_semana' => 'activo'
                ],
                'status' => 'active',
                'reviewed_by' => $users->random()->id,
                'reviewed_at' => Carbon::now()->subDays(15),
                'review_notes' => 'Tema aprobado para cooperativa energética',
                'impressions' => 200000,
                'clicks' => 4000,
                'conversions' => 200,
                'ctr' => 2.0,
                'conversion_rate' => 5.0,
                'engagement_rate' => 4.2,
                'show_sponsor_info' => true,
                'allow_user_feedback' => true,
                'disclosure_text' => [
                    'es' => 'Contenido patrocinado',
                    'en' => 'Sponsored content',
                    'ca' => 'Contingut patrocinat'
                ],
            ],

            // Campaña de placement de producto
            [
                'sponsor_id' => $users->random()->id,
                'sponsorable_type' => 'App\Models\NewsArticle',
                'sponsorable_id' => $newsArticles->isNotEmpty() ? $newsArticles->random()->id : 1,
                'campaign_name' => 'Placement Inversores Solares',
                'campaign_description' => 'Placement de producto para inversores solares de última generación',
                'content_type' => 'product_placement',
                'target_audience' => ['profesionales_energia', 'instaladores'],
                'target_topics' => ['tecnologia', 'inversores', 'eficiencia'],
                'target_locations' => ['madrid', 'barcelona', 'valencia'],
                'target_demographics' => ['edad_25_45', 'profesionales_tecnologia', 'educacion_superior'],
                'ad_label' => 'Patrocinado',
                'call_to_action' => 'Descubre más',
                'destination_url' => 'https://inversores-solares.com',
                'creative_assets' => [
                    'banner_principal' => 'https://ejemplo.com/banner-inversores.jpg',
                    'imagen_secundaria' => 'https://ejemplo.com/imagen-inversores.jpg',
                    'logo_patrocinador' => 'https://ejemplo.com/logo-inversores.png'
                ],
                'pricing_model' => 'cpa',
                'bid_amount' => 15.00,
                'daily_budget' => 300.00,
                'total_budget' => 8000.00,
                'spent_amount' => 3200.00,
                'start_date' => Carbon::now()->subDays(60),
                'end_date' => Carbon::now()->addDays(30),
                'schedule_config' => [
                    'lunes_09_12' => 'activo',
                    'martes_09_12' => 'activo',
                    'miercoles_09_12' => 'activo',
                    'jueves_09_12' => 'activo',
                    'viernes_09_12' => 'activo',
                    'fin_de_semana' => 'pausado'
                ],
                'status' => 'active',
                'reviewed_by' => $users->random()->id,
                'reviewed_at' => Carbon::now()->subDays(55),
                'review_notes' => 'Placement aprobado para producto técnico',
                'impressions' => 500000,
                'clicks' => 10000,
                'conversions' => 200,
                'ctr' => 2.0,
                'conversion_rate' => 2.0,
                'engagement_rate' => 1.8,
                'show_sponsor_info' => true,
                'allow_user_feedback' => false,
                'disclosure_text' => [
                    'es' => 'Contenido patrocinado',
                    'en' => 'Sponsored content',
                    'ca' => 'Contingut patrocinat'
                ],
            ],

            // Campaña de contenido nativo
            [
                'sponsor_id' => $users->random()->id,
                'sponsorable_type' => 'App\Models\TopicPost',
                'sponsorable_id' => $topicPosts->isNotEmpty() ? $topicPosts->random()->id : 1,
                'campaign_name' => 'Contenido Nativo Eficiencia Energética',
                'campaign_description' => 'Contenido nativo sobre eficiencia energética en edificios',
                'content_type' => 'native_content',
                'target_audience' => ['arquitectos_sostenibles', 'propietarios_vivienda'],
                'target_topics' => ['eficiencia_energetica', 'edificios_verdes', 'sostenibilidad'],
                'target_locations' => ['madrid', 'barcelona', 'valencia', 'sevilla'],
                'target_demographics' => ['edad_30_50', 'educacion_superior', 'propietarios_vivienda'],
                'ad_label' => 'Patrocinado',
                'call_to_action' => 'Lee más',
                'destination_url' => 'https://eficiencia-energetica.com',
                'creative_assets' => [
                    'banner_principal' => 'https://ejemplo.com/banner-eficiencia.jpg',
                    'imagen_secundaria' => 'https://ejemplo.com/imagen-eficiencia.jpg',
                    'logo_patrocinador' => 'https://ejemplo.com/logo-eficiencia.png'
                ],
                'pricing_model' => 'cpm',
                'bid_amount' => 1.50,
                'daily_budget' => 250.00,
                'total_budget' => 6000.00,
                'spent_amount' => 2800.00,
                'start_date' => Carbon::now()->subDays(40),
                'end_date' => Carbon::now()->addDays(20),
                'schedule_config' => [
                    'lunes_09_12' => 'activo',
                    'martes_09_12' => 'activo',
                    'miercoles_09_12' => 'activo',
                    'jueves_09_12' => 'activo',
                    'viernes_09_12' => 'activo',
                    'fin_de_semana' => 'activo'
                ],
                'status' => 'active',
                'reviewed_by' => $users->random()->id,
                'reviewed_at' => Carbon::now()->subDays(35),
                'review_notes' => 'Contenido nativo aprobado',
                'impressions' => 1800000,
                'clicks' => 18000,
                'conversions' => 900,
                'ctr' => 1.0,
                'conversion_rate' => 5.0,
                'engagement_rate' => 3.2,
                'show_sponsor_info' => true,
                'allow_user_feedback' => true,
                'disclosure_text' => [
                    'es' => 'Contenido patrocinado',
                    'en' => 'Sponsored content',
                    'ca' => 'Contingut patrocinat'
                ],
            ],

            // Campaña de promoción de evento
            [
                'sponsor_id' => $users->random()->id,
                'sponsorable_type' => 'App\Models\Event',
                'sponsorable_id' => $events->isNotEmpty() ? $events->random()->id : 1,
                'campaign_name' => 'Promoción Feria Solar 2024',
                'campaign_description' => 'Promoción de la feria internacional de energía solar',
                'content_type' => 'event_promotion',
                'target_audience' => ['profesionales_energia', 'empresas_sostenibles', 'inversores_verdes'],
                'target_topics' => ['energia_solar', 'ferias', 'networking'],
                'target_locations' => ['madrid', 'barcelona', 'valencia'],
                'target_demographics' => ['edad_25_45', 'profesionales_tecnologia', 'educacion_superior'],
                'ad_label' => 'Patrocinado',
                'call_to_action' => 'Reserva tu entrada',
                'destination_url' => 'https://feria-solar-2024.com',
                'creative_assets' => [
                    'banner_principal' => 'https://ejemplo.com/banner-feria.jpg',
                    'imagen_secundaria' => 'https://ejemplo.com/imagen-feria.jpg',
                    'video_promocional' => 'https://ejemplo.com/video-feria.mp4',
                    'logo_patrocinador' => 'https://ejemplo.com/logo-feria.png'
                ],
                'pricing_model' => 'cpc',
                'bid_amount' => 0.75,
                'daily_budget' => 400.00,
                'total_budget' => 10000.00,
                'spent_amount' => 4500.00,
                'start_date' => Carbon::now()->subDays(90),
                'end_date' => Carbon::now()->addDays(10),
                'schedule_config' => [
                    'lunes_09_12' => 'activo',
                    'martes_09_12' => 'activo',
                    'miercoles_09_12' => 'activo',
                    'jueves_09_12' => 'activo',
                    'viernes_09_12' => 'activo',
                    'fin_de_semana' => 'activo'
                ],
                'status' => 'active',
                'reviewed_by' => $users->random()->id,
                'reviewed_at' => Carbon::now()->subDays(85),
                'review_notes' => 'Promoción de evento aprobada',
                'impressions' => 2000000,
                'clicks' => 6000,
                'conversions' => 300,
                'ctr' => 0.3,
                'conversion_rate' => 5.0,
                'engagement_rate' => 2.5,
                'show_sponsor_info' => true,
                'allow_user_feedback' => true,
                'disclosure_text' => [
                    'es' => 'Contenido patrocinado',
                    'en' => 'Sponsored content',
                    'ca' => 'Contingut patrocinat'
                ],
            ],

            // Campaña de oferta de trabajo
            [
                'sponsor_id' => $users->random()->id,
                'sponsorable_type' => 'App\Models\NewsArticle',
                'sponsorable_id' => $newsArticles->isNotEmpty() ? $newsArticles->random()->id : 1,
                'campaign_name' => 'Ofertas de Trabajo Sector Energético',
                'campaign_description' => 'Promoción de ofertas de trabajo en el sector energético',
                'content_type' => 'job_posting',
                'target_audience' => ['profesionales_energia', 'estudiantes_medioambiente'],
                'target_topics' => ['empleo', 'energia', 'sostenibilidad'],
                'target_locations' => ['madrid', 'barcelona', 'valencia', 'sevilla'],
                'target_demographics' => ['edad_22_35', 'educacion_superior', 'profesionales_tecnologia'],
                'ad_label' => 'Patrocinado',
                'call_to_action' => 'Ver ofertas',
                'destination_url' => 'https://empleos-energia.com',
                'creative_assets' => [
                    'banner_principal' => 'https://ejemplo.com/banner-empleos.jpg',
                    'imagen_secundaria' => 'https://ejemplo.com/imagen-empleos.jpg',
                    'logo_patrocinador' => 'https://ejemplo.com/logo-empleos.png'
                ],
                'pricing_model' => 'cpa',
                'bid_amount' => 8.00,
                'daily_budget' => 200.00,
                'total_budget' => 5000.00,
                'spent_amount' => 2200.00,
                'start_date' => Carbon::now()->subDays(25),
                'end_date' => Carbon::now()->addDays(35),
                'schedule_config' => [
                    'lunes_09_12' => 'activo',
                    'martes_09_12' => 'activo',
                    'miercoles_09_12' => 'activo',
                    'jueves_09_12' => 'activo',
                    'viernes_09_12' => 'activo',
                    'fin_de_semana' => 'pausado'
                ],
                'status' => 'active',
                'reviewed_by' => $users->random()->id,
                'reviewed_at' => Carbon::now()->subDays(20),
                'review_notes' => 'Ofertas de trabajo aprobadas',
                'impressions' => 800000,
                'clicks' => 8000,
                'conversions' => 275,
                'ctr' => 1.0,
                'conversion_rate' => 3.4,
                'engagement_rate' => 2.1,
                'show_sponsor_info' => true,
                'allow_user_feedback' => false,
                'disclosure_text' => [
                    'es' => 'Contenido patrocinado',
                    'en' => 'Sponsored content',
                    'ca' => 'Contingut patrocinat'
                ],
            ],

            // Campaña de destacar servicio
            [
                'sponsor_id' => $users->random()->id,
                'sponsorable_type' => 'App\Models\Cooperative',
                'sponsorable_id' => $cooperatives->isNotEmpty() ? $cooperatives->random()->id : 1,
                'campaign_name' => 'Destacar Servicio Consultoría Energética',
                'campaign_description' => 'Promoción de servicios de consultoría energética especializada',
                'content_type' => 'service_highlight',
                'target_audience' => ['empresas_sostenibles', 'consultores_energia'],
                'target_topics' => ['consultoria', 'eficiencia_energetica', 'auditorias'],
                'target_locations' => ['madrid', 'barcelona', 'valencia'],
                'target_demographics' => ['edad_30_50', 'empresas_sostenibles', 'educacion_superior'],
                'ad_label' => 'Patrocinado',
                'call_to_action' => 'Contacta con nosotros',
                'destination_url' => 'https://consultoria-energetica.com',
                'creative_assets' => [
                    'banner_principal' => 'https://ejemplo.com/banner-consultoria.jpg',
                    'imagen_secundaria' => 'https://ejemplo.com/imagen-consultoria.jpg',
                    'logo_patrocinador' => 'https://ejemplo.com/logo-consultoria.png'
                ],
                'pricing_model' => 'cpc',
                'bid_amount' => 1.25,
                'daily_budget' => 150.00,
                'total_budget' => 3500.00,
                'spent_amount' => 1600.00,
                'start_date' => Carbon::now()->subDays(35),
                'end_date' => Carbon::now()->addDays(25),
                'schedule_config' => [
                    'lunes_09_12' => 'activo',
                    'martes_09_12' => 'activo',
                    'miercoles_09_12' => 'activo',
                    'jueves_09_12' => 'activo',
                    'viernes_09_12' => 'activo',
                    'fin_de_semana' => 'pausado'
                ],
                'status' => 'active',
                'reviewed_by' => $users->random()->id,
                'reviewed_at' => Carbon::now()->subDays(30),
                'review_notes' => 'Servicio de consultoría aprobado',
                'impressions' => 600000,
                'clicks' => 12000,
                'conversions' => 120,
                'ctr' => 2.0,
                'conversion_rate' => 1.0,
                'engagement_rate' => 1.5,
                'show_sponsor_info' => true,
                'allow_user_feedback' => true,
                'disclosure_text' => [
                    'es' => 'Contenido patrocinado',
                    'en' => 'Sponsored content',
                    'ca' => 'Contingut patrocinat'
                ],
            ],

            // Campaña pausada
            [
                'sponsor_id' => $users->random()->id,
                'sponsorable_type' => 'App\Models\TopicPost',
                'sponsorable_id' => $topicPosts->isNotEmpty() ? $topicPosts->random()->id : 1,
                'campaign_name' => 'Campaña Pausada Eólica',
                'campaign_description' => 'Campaña pausada temporalmente para energía eólica',
                'content_type' => 'promoted_post',
                'target_audience' => ['profesionales_energia', 'inversores_verdes'],
                'target_topics' => ['energia_eolica', 'inversion', 'sostenibilidad'],
                'target_locations' => ['madrid', 'barcelona'],
                'target_demographics' => ['edad_35_55', 'ingresos_altos', 'inversores_verdes'],
                'ad_label' => 'Patrocinado',
                'call_to_action' => 'Más información',
                'destination_url' => 'https://energia-eolica.com',
                'creative_assets' => [
                    'banner_principal' => 'https://ejemplo.com/banner-eolica.jpg',
                    'imagen_secundaria' => 'https://ejemplo.com/imagen-eolica.jpg',
                    'logo_patrocinador' => 'https://ejemplo.com/logo-eolica.png'
                ],
                'pricing_model' => 'cpm',
                'bid_amount' => 3.00,
                'daily_budget' => 500.00,
                'total_budget' => 12000.00,
                'spent_amount' => 7500.00,
                'start_date' => Carbon::now()->subDays(100),
                'end_date' => Carbon::now()->addDays(50),
                'schedule_config' => [
                    'lunes_09_12' => 'pausado',
                    'martes_09_12' => 'pausado',
                    'miercoles_09_12' => 'pausado',
                    'jueves_09_12' => 'pausado',
                    'viernes_09_12' => 'pausado',
                    'fin_de_semana' => 'pausado'
                ],
                'status' => 'paused',
                'reviewed_by' => $users->random()->id,
                'reviewed_at' => Carbon::now()->subDays(95),
                'review_notes' => 'Campaña pausada por revisión de estrategia',
                'impressions' => 2500000,
                'clicks' => 25000,
                'conversions' => 500,
                'ctr' => 1.0,
                'conversion_rate' => 2.0,
                'engagement_rate' => 1.2,
                'show_sponsor_info' => true,
                'allow_user_feedback' => false,
                'disclosure_text' => [
                    'es' => 'Contenido patrocinado',
                    'en' => 'Sponsored content',
                    'ca' => 'Contingut patrocinat'
                ],
            ],

            // Campaña completada
            [
                'sponsor_id' => $users->random()->id,
                'sponsorable_type' => 'App\Models\Event',
                'sponsorable_id' => $events->isNotEmpty() ? $events->random()->id : 1,
                'campaign_name' => 'Campaña Completada Webinar Sostenibilidad',
                'campaign_description' => 'Campaña completada para webinar sobre sostenibilidad',
                'content_type' => 'event_promotion',
                'target_audience' => ['profesionales_energia', 'estudiantes_medioambiente'],
                'target_topics' => ['sostenibilidad', 'webinar', 'educacion'],
                'target_locations' => ['madrid', 'barcelona', 'valencia'],
                'target_demographics' => ['edad_22_40', 'educacion_superior', 'profesionales_tecnologia'],
                'ad_label' => 'Patrocinado',
                'call_to_action' => 'Regístrate gratis',
                'destination_url' => 'https://webinar-sostenibilidad.com',
                'creative_assets' => [
                    'banner_principal' => 'https://ejemplo.com/banner-webinar.jpg',
                    'imagen_secundaria' => 'https://ejemplo.com/imagen-webinar.jpg',
                    'logo_patrocinador' => 'https://ejemplo.com/logo-webinar.png'
                ],
                'pricing_model' => 'cpc',
                'bid_amount' => 0.30,
                'daily_budget' => 80.00,
                'total_budget' => 2000.00,
                'spent_amount' => 2000.00,
                'start_date' => Carbon::now()->subDays(60),
                'end_date' => Carbon::now()->subDays(10),
                'schedule_config' => [
                    'lunes_09_12' => 'activo',
                    'martes_09_12' => 'activo',
                    'miercoles_09_12' => 'activo',
                    'jueves_09_12' => 'activo',
                    'viernes_09_12' => 'activo',
                    'fin_de_semana' => 'activo'
                ],
                'status' => 'completed',
                'reviewed_by' => $users->random()->id,
                'reviewed_at' => Carbon::now()->subDays(55),
                'review_notes' => 'Campaña completada exitosamente',
                'impressions' => 1000000,
                'clicks' => 15000,
                'conversions' => 750,
                'ctr' => 1.5,
                'conversion_rate' => 5.0,
                'engagement_rate' => 3.8,
                'show_sponsor_info' => true,
                'allow_user_feedback' => true,
                'disclosure_text' => [
                    'es' => 'Contenido patrocinado',
                    'en' => 'Sponsored content',
                    'ca' => 'Contingut patrocinat'
                ],
            ],
        ];

        foreach ($sponsoredContents as $content) {
            SponsoredContent::create($content);
        }

        $this->command->info('Se han creado ' . count($sponsoredContents) . ' contenidos patrocinados.');
    }
}

