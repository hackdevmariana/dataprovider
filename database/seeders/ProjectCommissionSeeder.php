<?php

namespace Database\Seeders;

use App\Models\ProjectCommission;
use App\Models\ProjectProposal;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectCommissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('💼 Sembrando comisiones de proyectos...');

        // Obtener datos necesarios
        $projects = ProjectProposal::take(15)->get();
        $users = User::take(25)->get();

        if ($projects->isEmpty()) {
            $this->command->error('❌ No hay proyectos disponibles. Ejecuta ProjectProposalSeeder primero.');
            return;
        }

        if ($users->isEmpty()) {
            $this->command->error('❌ No hay usuarios disponibles. Ejecuta UserSeeder primero.');
            return;
        }

        $commissionTypes = ['success_fee', 'listing_fee', 'verification_fee', 'premium_fee'];
        $statuses = ['pending', 'paid', 'waived', 'disputed', 'refunded'];
        $paymentMethods = ['bank_transfer', 'credit_card', 'paypal', 'stripe'];

        $createdCount = 0;

        // Crear comisiones para proyectos
        foreach ($projects as $project) {
            $numCommissions = fake()->numberBetween(1, 4);
            
            for ($i = 0; $i < $numCommissions; $i++) {
                $user = $users->random();
                $type = fake()->randomElement($commissionTypes);
                $status = fake()->randomElement($statuses);
                
                $baseAmount = $this->getBaseAmountForType($type, $project);
                $rate = $this->getRateForType($type);
                $amount = ProjectCommission::calculateCommission($baseAmount, $rate);

                $commission = ProjectCommission::create([
                    'project_proposal_id' => $project->id,
                    'user_id' => $user->id,
                    'type' => $type,
                    'amount' => $amount,
                    'rate' => $rate,
                    'base_amount' => $baseAmount,
                    'currency' => 'EUR',
                    'status' => $status,
                    'due_date' => fake()->dateTimeBetween('now', '+90 days'),
                    'paid_at' => $status === 'paid' ? fake()->dateTimeBetween('-6 months', 'now') : null,
                    'payment_method' => $status === 'paid' ? fake()->randomElement($paymentMethods) : null,
                    'transaction_id' => $status === 'paid' ? fake()->uuid() : null,
                    'description' => $this->generateDescription($type, $project->title, $amount),
                    'calculation_details' => $this->generateCalculationDetails($baseAmount, $rate, $amount, $project, $type),
                    'notes' => fake()->optional(0.3)->sentence(),
                ]);

                $createdCount++;
            }
        }

        $this->command->info("✅ Creadas {$createdCount} comisiones de proyectos");
        $this->showStatistics();
    }

    private function getBaseAmountForType(string $type, $project): float
    {
        return match($type) {
            'success_fee' => $project->total_investment_required,
            'listing_fee' => fake()->randomFloat(2, 100, 500),
            'verification_fee' => fake()->randomFloat(2, 50, 200),
            'premium_fee' => fake()->randomFloat(2, 200, 1000),
            default => $project->total_investment_required
        };
    }

    private function getRateForType(string $type): float
    {
        return match($type) {
            'success_fee' => fake()->randomFloat(4, 0.02, 0.05), // 2-5%
            'listing_fee' => fake()->randomFloat(4, 0.01, 0.03), // 1-3%
            'verification_fee' => fake()->randomFloat(4, 0.005, 0.02), // 0.5-2%
            'premium_fee' => fake()->randomFloat(4, 0.01, 0.04), // 1-4%
            default => 0.03 // 3% por defecto
        };
    }

    private function generateDescription(string $type, string $projectTitle, float $amount): string
    {
        return match($type) {
            'success_fee' => "Comisión de éxito por financiación completa del proyecto '{$projectTitle}'. Monto: €" . number_format($amount, 2),
            'listing_fee' => "Tarifa de listado premium para el proyecto '{$projectTitle}' en la plataforma. Monto: €" . number_format($amount, 2),
            'verification_fee' => "Tarifa de verificación técnica y legal para el proyecto '{$projectTitle}'. Monto: €" . number_format($amount, 2),
            'premium_fee' => "Tarifa premium por servicios adicionales para el proyecto '{$projectTitle}'. Monto: €" . number_format($amount, 2),
            default => "Comisión del proyecto '{$projectTitle}'. Monto: €" . number_format($amount, 2)
        };
    }

    private function generateCalculationDetails(float $baseAmount, float $rate, float $amount, $project, string $type): array
    {
        return [
            'calculation_type' => $type,
            'base_amount' => $baseAmount,
            'rate_percentage' => $rate * 100,
            'rate_decimal' => $rate,
            'calculated_amount' => $amount,
            'calculation_formula' => "€{$baseAmount} × {$rate} = €{$amount}",
            'project_id' => $project->id,
            'project_title' => $project->title,
            'project_status' => $project->status,
            'project_investment_total' => $project->total_investment_required,
            'project_investment_raised' => $project->investment_raised,
            'commission_tier' => $this->getCommissionTier($amount),
            'calculated_at' => now()->toISOString(),
            'calculated_by' => 'system',
            'validation_status' => 'validated',
            'approval_required' => $amount > 1000,
            'tax_included' => false,
            'tax_rate' => 0.21, // IVA español
            'tax_amount' => round($amount * 0.21, 2),
            'net_amount' => round($amount - ($amount * 0.21), 2),
            'currency' => 'EUR',
            'exchange_rate' => 1.0,
            'payment_terms' => '30 días',
            'late_fee_rate' => 0.05, // 5% por mora
            'discount_applied' => fake()->boolean(20) ? fake()->randomFloat(4, 0.05, 0.15) : 0,
            'special_conditions' => fake()->optional(0.1)->sentence()
        ];
    }

    private function getCommissionTier(float $amount): string
    {
        return match(true) {
            $amount < 100 => 'tier_1',
            $amount < 500 => 'tier_2',
            $amount < 1000 => 'tier_3',
            $amount < 5000 => 'tier_4',
            default => 'tier_5'
        };
    }

    private function showStatistics(): void
    {
        $total = ProjectCommission::count();
        $totalAmount = ProjectCommission::sum('amount');
        $paidAmount = ProjectCommission::where('status', 'paid')->sum('amount');
        $pendingAmount = ProjectCommission::where('status', 'pending')->sum('amount');
        
        $byStatus = ProjectCommission::selectRaw('status, COUNT(*) as count, SUM(amount) as total_amount')
            ->groupBy('status')
            ->get()
            ->pluck('total_amount', 'status');
        
        $byType = ProjectCommission::selectRaw('type, COUNT(*) as count, AVG(amount) as avg_amount, SUM(amount) as total_amount')
            ->groupBy('type')
            ->get();

        $avgCommission = ProjectCommission::avg('amount');
        $highestCommission = ProjectCommission::max('amount');
        $lowestCommission = ProjectCommission::min('amount');

        $this->command->info("\n📊 Estadísticas de comisiones:");
        $this->command->info("   • Total de comisiones: {$total}");
        $this->command->info("   • Monto total: €" . number_format($totalAmount, 2));
        $this->command->info("   • Monto pagado: €" . number_format($paidAmount, 2));
        $this->command->info("   • Monto pendiente: €" . number_format($pendingAmount, 2));
        $this->command->info("   • Comisión promedio: €" . number_format($avgCommission, 2));
        $this->command->info("   • Comisión más alta: €" . number_format($highestCommission, 2));
        $this->command->info("   • Comisión más baja: €" . number_format($lowestCommission, 2));

        $this->command->info("\n📈 Por estado:");
        foreach ($byStatus as $status => $amount) {
            $statusLabel = match($status) {
                'pending' => 'Pendiente',
                'paid' => 'Pagada',
                'waived' => 'Exonerada',
                'disputed' => 'En disputa',
                'refunded' => 'Reembolsada',
                default => ucfirst($status)
            };
            $this->command->info("   • {$statusLabel}: €" . number_format($amount, 2));
        }

        $this->command->info("\n💼 Por tipo de comisión:");
        foreach ($byType as $type) {
            $typeLabel = match($type->type) {
                'success_fee' => 'Comisión de éxito',
                'listing_fee' => 'Tarifa de listado',
                'verification_fee' => 'Tarifa de verificación',
                'premium_fee' => 'Tarifa premium',
                default => ucfirst($type->type)
            };
            $this->command->info("   • {$typeLabel}: {$type->count} comisiones, €" . number_format($type->total_amount, 2) . " total, €" . number_format($type->avg_amount, 2) . " promedio");
        }

        // Estadísticas de tiempo
        $avgDaysToPayment = ProjectCommission::where('status', 'paid')
            ->whereNotNull('paid_at')
            ->whereNotNull('due_date')
            ->get()
            ->avg(function($commission) {
                return $commission->due_date->diffInDays($commission->paid_at);
            });

        $this->command->info("\n⏰ Estadísticas de tiempo:");
        $this->command->info("   • Días promedio para pago: " . round($avgDaysToPayment, 1));
        
        $overdueCommissions = ProjectCommission::where('status', 'pending')
            ->where('due_date', '<', now())
            ->count();
        
        $this->command->info("   • Comisiones vencidas: {$overdueCommissions}");
    }
}