<?php

namespace Database\Seeders;

use App\Models\UserSubscription;
use App\Models\User;
use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class UserSubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('👥 Sembrando suscripciones de usuarios...');

        // Obtener datos necesarios
        $users = User::take(30)->get();
        $plans = SubscriptionPlan::where('is_active', true)->get();

        if ($users->isEmpty()) {
            $this->command->error('❌ No hay usuarios disponibles. Ejecuta UserSeeder primero.');
            return;
        }

        if ($plans->isEmpty()) {
            $this->command->error('❌ No hay planes de suscripción disponibles. Ejecuta SubscriptionPlanSeeder primero.');
            return;
        }

        $statuses = ['active', 'trial', 'cancelled', 'expired', 'suspended'];
        $paymentMethods = ['credit_card', 'bank_transfer', 'paypal', 'sepa', 'stripe'];
        $currencies = ['EUR', 'USD', 'GBP'];

        $createdCount = 0;

        // Crear suscripciones para usuarios
        foreach ($users as $user) {
            // 70% de usuarios tienen suscripción
            if (fake()->boolean(70)) {
                $plan = $plans->random();
                $status = fake()->randomElement($statuses);
                $billingCycle = $plan->billing_cycle;
                
                // Calcular fechas basadas en el estado
                $startsAt = fake()->dateTimeBetween('-2 years', 'now');
                $endsAt = $this->calculateEndDate($startsAt, $billingCycle, $status);
                $trialEndsAt = $plan->trial_days > 0 ? fake()->dateTimeBetween($startsAt, $startsAt->modify('+30 days')) : null;
                $nextBillingAt = $status === 'active' ? $this->calculateNextBilling($endsAt, $billingCycle) : null;
                $cancelledAt = $status === 'cancelled' ? fake()->dateTimeBetween($startsAt, 'now') : null;

                $subscription = UserSubscription::create([
                    'user_id' => $user->id,
                    'subscription_plan_id' => $plan->id,
                    'status' => $status,
                    'amount_paid' => $status === 'active' || $status === 'trial' ? $plan->price : 0,
                    'currency' => fake()->randomElement($currencies),
                    'billing_cycle' => $billingCycle,
                    'starts_at' => $startsAt,
                    'ends_at' => $endsAt,
                    'trial_ends_at' => $trialEndsAt,
                    'cancelled_at' => $cancelledAt,
                    'next_billing_at' => $nextBillingAt,
                    'payment_method' => fake()->randomElement($paymentMethods),
                    'external_subscription_id' => fake()->uuid(),
                    'usage_stats' => $this->generateUsageStats($plan),
                    'metadata' => $this->generateMetadata($plan, $status),
                    'cancellation_reason' => $status === 'cancelled' ? fake()->randomElement([
                        'too_expensive', 'not_using_features', 'found_alternative', 
                        'technical_issues', 'customer_service', 'other'
                    ]) : null,
                    'auto_renew' => $status === 'active' ? true : false,
                ]);

                $createdCount++;
            }
        }

        $this->command->info("✅ Creadas {$createdCount} suscripciones de usuarios");
        $this->showStatistics();
    }

    private function calculateEndDate(\DateTime $startsAt, string $billingCycle, string $status): ?\DateTime
    {
        if ($status === 'cancelled' || $status === 'expired') {
            return fake()->dateTimeBetween($startsAt, 'now');
        }

        return match($billingCycle) {
            'monthly' => fake()->dateTimeBetween($startsAt->modify('+1 month'), '+2 years'),
            'yearly' => fake()->dateTimeBetween($startsAt->modify('+1 year'), '+3 years'),
            'one_time' => null,
            default => fake()->dateTimeBetween($startsAt->modify('+1 month'), '+2 years')
        };
    }

    private function calculateNextBilling(?\DateTime $endsAt, string $billingCycle): ?\DateTime
    {
        if (!$endsAt) {
            return null;
        }

        return match($billingCycle) {
            'monthly' => fake()->dateTimeBetween($endsAt, $endsAt->modify('+1 month')),
            'yearly' => fake()->dateTimeBetween($endsAt, $endsAt->modify('+1 year')),
            default => null
        };
    }

    private function generateUsageStats($plan): array
    {
        $stats = [];
        
        // Generar estadísticas basadas en los límites del plan
        if ($plan->max_projects) {
            $stats['projects_created'] = fake()->numberBetween(0, min($plan->max_projects, 20));
        }
        
        if ($plan->max_investments) {
            $stats['investments_made'] = fake()->numberBetween(0, min($plan->max_investments, 15));
        }
        
        if ($plan->max_consultations) {
            $stats['consultations_requested'] = fake()->numberBetween(0, min($plan->max_consultations, 10));
        }
        
        if ($plan->max_cooperatives) {
            $stats['cooperatives_joined'] = fake()->numberBetween(0, min($plan->max_cooperatives, 5));
        }

        // Estadísticas adicionales
        $stats['api_calls_this_month'] = fake()->numberBetween(0, 1000);
        $stats['storage_used_mb'] = fake()->numberBetween(10, 500);
        $stats['emails_sent'] = fake()->numberBetween(0, 50);
        $stats['support_tickets'] = fake()->numberBetween(0, 5);

        return $stats;
    }

    private function generateMetadata($plan, string $status): array
    {
        return [
            'signup_source' => fake()->randomElement(['website', 'referral', 'social_media', 'advertisement', 'organic']),
            'referral_code' => fake()->optional(0.3)->bothify('REF-####'),
            'marketing_consent' => fake()->boolean(80),
            'newsletter_subscription' => fake()->boolean(70),
            'first_payment_method' => fake()->randomElement(['credit_card', 'bank_transfer', 'paypal']),
            'plan_features_used' => $this->getUsedFeatures($plan),
            'support_requests' => fake()->numberBetween(0, 3),
            'upgrade_history' => fake()->boolean(20) ? ['basic', 'premium'] : [],
            'cancellation_attempts' => $status === 'cancelled' ? fake()->numberBetween(1, 3) : 0,
            'retention_score' => fake()->randomFloat(2, 0, 100),
            'engagement_level' => fake()->randomElement(['low', 'medium', 'high']),
            'preferred_language' => fake()->randomElement(['es', 'en', 'ca', 'eu']),
            'timezone' => fake()->timezone(),
            'last_login' => fake()->dateTimeBetween('-30 days', 'now')->format('Y-m-d H:i:s'),
            'device_info' => [
                'platform' => fake()->randomElement(['web', 'mobile_ios', 'mobile_android']),
                'browser' => fake()->randomElement(['chrome', 'firefox', 'safari', 'edge']),
                'version' => fake()->randomFloat(1, 1.0, 5.0)
            ]
        ];
    }

    private function getUsedFeatures($plan): array
    {
        $usedFeatures = [];
        $planFeatures = $plan->features ?? [];
        
        foreach ($planFeatures as $feature) {
            if (fake()->boolean(70)) { // 70% de probabilidad de usar cada feature
                $usedFeatures[] = $feature;
            }
        }
        
        return $usedFeatures;
    }

    private function showStatistics(): void
    {
        $total = UserSubscription::count();
        $active = UserSubscription::where('status', 'active')->count();
        $trial = UserSubscription::where('status', 'trial')->count();
        $cancelled = UserSubscription::where('status', 'cancelled')->count();
        
        $byPlan = UserSubscription::selectRaw('subscription_plan_id, COUNT(*) as count')
            ->groupBy('subscription_plan_id')
            ->with('subscriptionPlan')
            ->get()
            ->pluck('count', 'subscriptionPlan.name');
        
        $byCycle = UserSubscription::selectRaw('billing_cycle, COUNT(*) as count')
            ->groupBy('billing_cycle')
            ->pluck('count', 'billing_cycle');

        $totalRevenue = UserSubscription::sum('amount_paid');
        $avgRevenuePerUser = $total > 0 ? $totalRevenue / $total : 0;

        $this->command->info("\n📊 Estadísticas de suscripciones:");
        $this->command->info("   • Total de suscripciones: {$total}");
        $this->command->info("   • Suscripciones activas: {$active}");
        $this->command->info("   • En período de prueba: {$trial}");
        $this->command->info("   • Canceladas: {$cancelled}");
        $this->command->info("   • Ingresos totales: €" . number_format($totalRevenue, 2));
        $this->command->info("   • Ingreso promedio por usuario: €" . number_format($avgRevenuePerUser, 2));

        $this->command->info("\n📋 Por plan:");
        foreach ($byPlan as $planName => $count) {
            $this->command->info("   • {$planName}: {$count}");
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

        // Estadísticas de uso
        $this->command->info("\n📈 Estadísticas de uso promedio:");
        $avgProjects = UserSubscription::where('status', 'active')
            ->get()
            ->avg(function($sub) {
                return $sub->usage_stats['projects_created'] ?? 0;
            });
        
        $avgInvestments = UserSubscription::where('status', 'active')
            ->get()
            ->avg(function($sub) {
                return $sub->usage_stats['investments_made'] ?? 0;
            });

        $this->command->info("   • Proyectos creados promedio: " . round($avgProjects, 1));
        $this->command->info("   • Inversiones realizadas promedio: " . round($avgInvestments, 1));
    }
}