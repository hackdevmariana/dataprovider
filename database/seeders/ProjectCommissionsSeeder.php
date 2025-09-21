<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProjectCommission;
use App\Models\User;
use Carbon\Carbon;

class ProjectCommissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::take(20)->get();
        
        if ($users->isEmpty()) {
            $this->command->warn('No hay usuarios disponibles. Ejecuta primero UserSeeder.');
            return;
        }

        $types = ['success_fee', 'listing_fee', 'verification_fee', 'premium_fee'];
        $statuses = ['pending', 'paid', 'waived', 'disputed', 'refunded'];
        $currencies = ['EUR', 'USD', 'GBP'];
        $paymentMethods = ['bank_transfer', 'credit_card', 'paypal', 'crypto'];

        $created = 0;
        $maxAttempts = 200;
        $attempts = 0;
        
        while ($created < 35 && $attempts < $maxAttempts) {
            $attempts++;
            $baseAmount = rand(1000, 50000);
            $rate = rand(1, 15) / 100; // 1% a 15%
            $amount = $baseAmount * $rate;
            $currency = $currencies[array_rand($currencies)];
            
            $userId = $users->random()->id;
            $projectId = rand(1, 5);
            
            ProjectCommission::create([
                'project_proposal_id' => $projectId,
                'user_id' => $userId,
                'type' => $types[array_rand($types)],
                'amount' => $amount,
                'rate' => $rate,
                'base_amount' => $baseAmount,
                'currency' => $currency,
                'status' => $statuses[array_rand($statuses)],
                'due_date' => Carbon::now()->addDays(rand(1, 90)),
                'paid_at' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 30)) : null,
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'transaction_id' => 'TXN-' . strtoupper(uniqid()),
                'description' => 'Comisión por ' . $types[array_rand($types)] . ' - Proyecto ' . ($created + 1),
                'calculation_details' => [
                    'base_amount' => $baseAmount,
                    'commission_rate' => $rate,
                    'calculated_amount' => $amount,
                    'currency' => $currency,
                    'calculation_date' => Carbon::now()->subDays(rand(1, 30)),
                    'project_value' => $baseAmount,
                    'commission_type' => $types[array_rand($types)],
                    'tier_level' => ['bronze', 'silver', 'gold'][array_rand(['bronze', 'silver', 'gold'])],
                ],
                'notes' => 'Notas sobre la comisión: ' . ($created + 1) . ' - Proyecto de energía renovable',
            ]);
            $created++;
        }
    }
}