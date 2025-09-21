<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProjectInvestment;
use App\Models\User;
use Carbon\Carbon;

class ProjectInvestmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::take(25)->get();
        
        if ($users->isEmpty()) {
            $this->command->warn('No hay usuarios disponibles. Ejecuta primero UserSeeder.');
            return;
        }

        $investmentTypes = ['monetary', 'in_kind', 'labor', 'materials', 'expertise', 'equipment', 'land_use', 'mixed'];
        $returnFrequencies = ['monthly', 'quarterly', 'biannual', 'annual', 'at_completion', 'custom'];
        $statuses = ['pending', 'confirmed', 'paid', 'active', 'completed', 'cancelled', 'refunded', 'disputed'];
        $paymentMethods = ['bank_transfer', 'credit_card', 'paypal', 'crypto', 'check'];

        $created = 0;
        $maxAttempts = 200;
        $attempts = 0;
        
        while ($created < 60 && $attempts < $maxAttempts) {
            $attempts++;
            $investmentAmount = rand(1000, 100000);
            $investmentPercentage = rand(1, 25);
            $expectedReturn = rand(5, 20);
            $termYears = rand(1, 10);
            
            $userId = $users->random()->id;
            $projectId = rand(1, 5);
            
            if (!ProjectInvestment::where('project_proposal_id', $projectId)
                ->where('investor_id', $userId)
                ->exists()) {
                
                ProjectInvestment::create([
                'project_proposal_id' => $projectId,
                'investor_id' => $userId,
                'investment_amount' => $investmentAmount,
                'investment_percentage' => $investmentPercentage,
                'investment_type' => $investmentTypes[array_rand($investmentTypes)],
                'investment_details' => [
                    'energy_type' => ['solar', 'wind', 'hydro', 'biomass'][array_rand(['solar', 'wind', 'hydro', 'biomass'])],
                    'project_size' => ['small', 'medium', 'large'][array_rand(['small', 'medium', 'large'])],
                    'location' => 'Ubicación del proyecto ' . ($created + 1),
                    'technology' => 'Tecnología avanzada',
                ],
                'investment_description' => 'Inversión en proyecto de energía renovable número ' . ($created + 1),
                'expected_return_percentage' => $expectedReturn,
                'investment_term_years' => $termYears,
                'return_frequency' => $returnFrequencies[array_rand($returnFrequencies)],
                'return_schedule' => [
                    'first_payment' => Carbon::now()->addMonths(6),
                    'payment_intervals' => 'quarterly',
                    'final_payment' => Carbon::now()->addYears($termYears),
                ],
                'reinvest_returns' => rand(0, 1) == 1,
                'status' => $statuses[array_rand($statuses)],
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'payment_reference' => 'REF-' . strtoupper(uniqid()),
                'payment_date' => Carbon::now()->subDays(rand(1, 30)),
                'payment_confirmed_at' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 25)) : null,
                'payment_confirmed_by' => rand(0, 1) ? $users->random()->id : null,
                'legal_documents' => [
                    'contract' => 'contract_' . ($created + 1) . '.pdf',
                    'terms' => 'terms_' . ($created + 1) . '.pdf',
                    'disclosure' => 'disclosure_' . ($created + 1) . '.pdf',
                ],
                'terms_accepted' => rand(0, 1) == 1,
                'terms_accepted_at' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 30)) : null,
                'digital_signature' => 'signature_' . ($created + 1) . '_hash',
                'contract_details' => [
                    'contract_number' => 'CONTRACT-' . ($created + 1),
                    'version' => '1.0',
                    'effective_date' => Carbon::now()->subDays(rand(1, 30)),
                ],
                'total_returns_received' => rand(0, $investmentAmount * 0.3),
                'pending_returns' => rand(0, $investmentAmount * 0.2),
                'last_return_date' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 90)) : null,
                'next_return_date' => Carbon::now()->addDays(rand(30, 120)),
                'has_voting_rights' => rand(0, 1) == 1,
                'voting_weight' => rand(1, 10),
                'can_participate_decisions' => rand(0, 1) == 1,
                'receives_project_updates' => rand(0, 1) == 1,
                'notification_preferences' => [
                    'email_updates' => rand(0, 1) == 1,
                    'sms_notifications' => rand(0, 1) == 1,
                    'push_notifications' => rand(0, 1) == 1,
                    'monthly_reports' => rand(0, 1) == 1,
                ],
                'public_investor' => rand(0, 1) == 1,
                'investor_alias' => rand(0, 1) ? 'Inversor ' . ($created + 1) : null,
                'current_roi' => rand(5, 25),
                'projected_final_roi' => rand(15, 35),
                'months_invested' => rand(1, 60),
                'performance_metrics' => [
                    'energy_produced' => rand(1000, 100000),
                    'co2_saved' => rand(100, 10000),
                    'revenue_generated' => rand(1000, 50000),
                ],
                'exit_requested' => rand(0, 1) == 1,
                'exit_requested_at' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 30)) : null,
                'exit_value' => rand(0, 1) ? rand(1000, 100000) : null,
                'exit_terms' => rand(0, 1) ? 'Términos de salida del proyecto' : null,
                ]);
                $created++;
            }
        }
    }
}
