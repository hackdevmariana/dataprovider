<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserSubscription;
use App\Models\User;
use Carbon\Carbon;

class UserSubscriptionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::take(30)->get();
        
        if ($users->isEmpty()) {
            $this->command->warn('No hay usuarios disponibles. Ejecuta primero UserSeeder.');
            return;
        }

        $billingCycles = ['monthly', 'yearly', 'one_time'];
        $statuses = ['active', 'cancelled', 'expired', 'trial', 'pending'];
        $paymentMethods = ['credit_card', 'bank_transfer', 'paypal', 'crypto'];
        $currencies = ['EUR', 'USD', 'GBP'];

        $created = 0;
        $maxAttempts = 200;
        $attempts = 0;
        
        while ($created < 45 && $attempts < $maxAttempts) {
            $attempts++;
            $amount = rand(10, 500);
            $billingCycle = $billingCycles[array_rand($billingCycles)];
            $currency = $currencies[array_rand($currencies)];
            $status = $statuses[array_rand($statuses)];
            
            // Calcular fechas basadas en el ciclo de facturación
            $startsAt = Carbon::now()->subDays(rand(1, 365));
            $endsAt = match($billingCycle) {
                'monthly' => $startsAt->copy()->addMonth(),
                'yearly' => $startsAt->copy()->addYear(),
                'one_time' => $startsAt->copy()->addYear(),
            };
            
            $userId = $users->random()->id;
            $planId = rand(1, 3);
            
            if (!UserSubscription::where('user_id', $userId)
                ->where('subscription_plan_id', $planId)
                ->where('status', $status)
                ->exists()) {
                
                UserSubscription::create([
                'user_id' => $userId,
                'subscription_plan_id' => $planId,
                'status' => $status,
                'amount_paid' => $amount,
                'currency' => $currency,
                'billing_cycle' => $billingCycle,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'trial_ends_at' => rand(0, 1) ? $startsAt->copy()->addDays(14) : null,
                'cancelled_at' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 30)) : null,
                'next_billing_at' => $billingCycle !== 'one_time' ? Carbon::now()->addDays(rand(1, 30)) : null,
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'external_subscription_id' => 'sub_' . strtoupper(uniqid()),
                'usage_stats' => [
                    'api_calls' => rand(100, 10000),
                    'storage_used' => rand(100, 1000),
                    'projects_created' => rand(1, 50),
                    'investments_made' => rand(0, 20),
                    'features_used' => rand(5, 25),
                ],
                'metadata' => [
                    'plan_name' => ['Basic', 'Pro', 'Enterprise'][array_rand(['Basic', 'Pro', 'Enterprise'])],
                    'features' => [
                        'unlimited_projects' => rand(0, 1) == 1,
                        'priority_support' => rand(0, 1) == 1,
                        'advanced_analytics' => rand(0, 1) == 1,
                        'custom_integrations' => rand(0, 1) == 1,
                    ],
                    'promo_code' => rand(0, 1) ? 'PROMO' . rand(100, 999) : null,
                    'referral_source' => ['organic', 'social', 'email', 'partner'][array_rand(['organic', 'social', 'email', 'partner'])],
                ],
                'cancellation_reason' => rand(0, 1) ? [
                    'too_expensive',
                    'not_using_features',
                    'found_alternative',
                    'technical_issues',
                    'other'
                ][array_rand(['too_expensive', 'not_using_features', 'found_alternative', 'technical_issues', 'other'])] : null,
                'auto_renew' => rand(0, 1) == 1,
                ]);
                $created++;
            }
        }
    }
}
