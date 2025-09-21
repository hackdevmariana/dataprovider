<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('💳 Sembrando planes de suscripción...');

        // Planes de suscripción para la plataforma energética
        $plans = [
            [
                'name' => 'Plan Básico',
                'slug' => 'plan-basico',
                'description' => 'Ideal para usuarios individuales que quieren participar en proyectos energéticos locales',
                'type' => 'individual',
                'billing_cycle' => 'monthly',
                'price' => 0.00,
                'setup_fee' => 0.00,
                'trial_days' => 0,
                'max_projects' => 5,
                'max_cooperatives' => 1,
                'max_investments' => 3,
                'max_consultations' => 2,
                'features' => [
                    'participar_en_proyectos',
                    'seguimiento_inversiones',
                    'notificaciones_basicas',
                    'soporte_email'
                ],
                'limits' => [
                    'inversion_maxima' => 1000,
                    'proyectos_simultaneos' => 2,
                    'consultas_mensuales' => 2
                ],
                'commission_rate' => 0.05,
                'priority_support' => false,
                'verified_badge' => false,
                'analytics_access' => false,
                'api_access' => false,
                'white_label' => false,
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 1
            ],
            [
                'name' => 'Plan Cooperativo',
                'slug' => 'plan-cooperativo',
                'description' => 'Perfecto para cooperativas energéticas que gestionan múltiples proyectos',
                'type' => 'cooperative',
                'billing_cycle' => 'monthly',
                'price' => 49.99,
                'setup_fee' => 0.00,
                'trial_days' => 14,
                'max_projects' => 25,
                'max_cooperatives' => 5,
                'max_investments' => 50,
                'max_consultations' => 10,
                'features' => [
                    'participar_en_proyectos',
                    'seguimiento_inversiones',
                    'notificaciones_basicas',
                    'soporte_email',
                    'gestion_cooperativas',
                    'herramientas_colaboracion',
                    'reportes_avanzados',
                    'soporte_telefonico'
                ],
                'limits' => [
                    'inversion_maxima' => 10000,
                    'proyectos_simultaneos' => 10,
                    'consultas_mensuales' => 10,
                    'miembros_cooperativa' => 100
                ],
                'commission_rate' => 0.035,
                'priority_support' => true,
                'verified_badge' => true,
                'analytics_access' => true,
                'api_access' => false,
                'white_label' => false,
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 2
            ],
            [
                'name' => 'Plan Empresarial',
                'slug' => 'plan-empresarial',
                'description' => 'Para empresas que quieren invertir en proyectos energéticos a gran escala',
                'type' => 'business',
                'billing_cycle' => 'yearly',
                'price' => 299.99,
                'setup_fee' => 99.99,
                'trial_days' => 30,
                'max_projects' => 100,
                'max_cooperatives' => 20,
                'max_investments' => 200,
                'max_consultations' => 50,
                'features' => [
                    'participar_en_proyectos',
                    'seguimiento_inversiones',
                    'notificaciones_basicas',
                    'soporte_email',
                    'gestion_cooperativas',
                    'herramientas_colaboracion',
                    'reportes_avanzados',
                    'soporte_telefonico',
                    'analytics_avanzados',
                    'api_completa',
                    'integraciones_crm',
                    'soporte_dedicado'
                ],
                'limits' => [
                    'inversion_maxima' => 100000,
                    'proyectos_simultaneos' => 50,
                    'consultas_mensuales' => 50,
                    'miembros_empresa' => 500
                ],
                'commission_rate' => 0.025,
                'priority_support' => true,
                'verified_badge' => true,
                'analytics_access' => true,
                'api_access' => true,
                'white_label' => false,
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 3
            ],
            [
                'name' => 'Plan Enterprise',
                'slug' => 'plan-enterprise',
                'description' => 'Solución completa para grandes organizaciones y utilities',
                'type' => 'enterprise',
                'billing_cycle' => 'yearly',
                'price' => 999.99,
                'setup_fee' => 299.99,
                'trial_days' => 60,
                'max_projects' => null, // Ilimitado
                'max_cooperatives' => null, // Ilimitado
                'max_investments' => null, // Ilimitado
                'max_consultations' => null, // Ilimitado
                'features' => [
                    'participar_en_proyectos',
                    'seguimiento_inversiones',
                    'notificaciones_basicas',
                    'soporte_email',
                    'gestion_cooperativas',
                    'herramientas_colaboracion',
                    'reportes_avanzados',
                    'soporte_telefonico',
                    'analytics_avanzados',
                    'api_completa',
                    'integraciones_crm',
                    'soporte_dedicado',
                    'white_label_completo',
                    'customizacion_total',
                    'sla_garantizado',
                    'consultoria_incluida'
                ],
                'limits' => [
                    'inversion_maxima' => null, // Ilimitado
                    'proyectos_simultaneos' => null, // Ilimitado
                    'consultas_mensuales' => null, // Ilimitado
                    'miembros_organizacion' => null // Ilimitado
                ],
                'commission_rate' => 0.015,
                'priority_support' => true,
                'verified_badge' => true,
                'analytics_access' => true,
                'api_access' => true,
                'white_label' => true,
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 4
            ],
            [
                'name' => 'Plan Desarrollador',
                'slug' => 'plan-desarrollador',
                'description' => 'Para desarrolladores que quieren integrar la API de la plataforma',
                'type' => 'individual',
                'billing_cycle' => 'monthly',
                'price' => 19.99,
                'setup_fee' => 0.00,
                'trial_days' => 30,
                'max_projects' => 10,
                'max_cooperatives' => 2,
                'max_investments' => 10,
                'max_consultations' => 5,
                'features' => [
                    'participar_en_proyectos',
                    'seguimiento_inversiones',
                    'notificaciones_basicas',
                    'soporte_email',
                    'api_completa',
                    'documentacion_avanzada',
                    'sandbox_testing'
                ],
                'limits' => [
                    'inversion_maxima' => 5000,
                    'proyectos_simultaneos' => 5,
                    'consultas_mensuales' => 5,
                    'api_calls_mensuales' => 10000
                ],
                'commission_rate' => 0.04,
                'priority_support' => false,
                'verified_badge' => true,
                'analytics_access' => false,
                'api_access' => true,
                'white_label' => false,
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 5
            ]
        ];

        $createdCount = 0;

        foreach ($plans as $planData) {
            $plan = SubscriptionPlan::updateOrCreate(
                ['slug' => $planData['slug']],
                $planData
            );

            if ($plan->wasRecentlyCreated) {
                $createdCount++;
            }
        }

        $this->command->info("✅ Creados {$createdCount} planes de suscripción");
        $this->showStatistics();
    }

    private function showStatistics(): void
    {
        $total = SubscriptionPlan::count();
        $active = SubscriptionPlan::where('is_active', true)->count();
        $featured = SubscriptionPlan::where('is_featured', true)->count();
        
        $byType = SubscriptionPlan::selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type');
        
        $byCycle = SubscriptionPlan::selectRaw('billing_cycle, COUNT(*) as count')
            ->groupBy('billing_cycle')
            ->pluck('count', 'billing_cycle');

        $freePlans = SubscriptionPlan::where('price', 0)->count();
        $paidPlans = SubscriptionPlan::where('price', '>', 0)->count();

        $this->command->info("\n📊 Estadísticas de planes de suscripción:");
        $this->command->info("   • Total de planes: {$total}");
        $this->command->info("   • Planes activos: {$active}");
        $this->command->info("   • Planes destacados: {$featured}");
        $this->command->info("   • Planes gratuitos: {$freePlans}");
        $this->command->info("   • Planes de pago: {$paidPlans}");

        $this->command->info("\n👥 Por tipo de usuario:");
        foreach ($byType as $type => $count) {
            $typeLabel = match($type) {
                'individual' => 'Individual',
                'cooperative' => 'Cooperativa',
                'business' => 'Empresa',
                'enterprise' => 'Enterprise',
                default => ucfirst($type)
            };
            $this->command->info("   • {$typeLabel}: {$count}");
        }

        $this->command->info("\n💰 Por ciclo de facturación:");
        foreach ($byCycle as $cycle => $count) {
            $cycleLabel = match($cycle) {
                'monthly' => 'Mensual',
                'yearly' => 'Anual',
                'one_time' => 'Pago único',
                default => ucfirst($cycle)
            };
            $this->command->info("   • {$cycleLabel}: {$count}");
        }

        // Mostrar algunos planes destacados
        $featuredPlans = SubscriptionPlan::where('is_featured', true)->get();
        if ($featuredPlans->isNotEmpty()) {
            $this->command->info("\n⭐ Planes destacados:");
            foreach ($featuredPlans as $plan) {
                $price = $plan->getFormattedPrice();
                $this->command->info("   • {$plan->name}: {$price}");
            }
        }

        // Estadísticas de precios
        $avgPrice = SubscriptionPlan::where('price', '>', 0)->avg('price');
        $minPrice = SubscriptionPlan::where('price', '>', 0)->min('price');
        $maxPrice = SubscriptionPlan::max('price');

        $this->command->info("\n💵 Estadísticas de precios:");
        $this->command->info("   • Precio promedio: €" . number_format($avgPrice, 2));
        $this->command->info("   • Precio mínimo: €" . number_format($minPrice, 2));
        $this->command->info("   • Precio máximo: €" . number_format($maxPrice, 2));
    }
}