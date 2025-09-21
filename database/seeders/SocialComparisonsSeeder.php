<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SocialComparison;
use App\Models\User;
use Carbon\Carbon;

class SocialComparisonsSeeder extends Seeder
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

        $comparisonTypes = ['energy_consumption', 'carbon_footprint', 'renewable_usage', 'efficiency_score', 'savings_amount'];
        $periods = ['monthly', 'quarterly', 'yearly'];
        $scopes = ['global', 'regional', 'local', 'community'];
        $units = ['kWh', 'kg CO2', '%', '€', 'points'];
        
        for ($i = 0; $i < 40; $i++) {
            $userValue = rand(100, 2000);
            $averageValue = $userValue + rand(-500, 500);
            $medianValue = $userValue + rand(-300, 300);
            $bestValue = rand(50, $userValue);
            $totalParticipants = rand(100, 10000);
            $userRank = rand(1, $totalParticipants);
            $percentile = round(($totalParticipants - $userRank + 1) / $totalParticipants * 100, 2);
            
            SocialComparison::create([
                'user_id' => $users->random()->id,
                'comparison_type' => $comparisonTypes[array_rand($comparisonTypes)],
                'period' => $periods[array_rand($periods)],
                'scope' => $scopes[array_rand($scopes)],
                'scope_id' => rand(1, 10),
                'user_value' => $userValue,
                'unit' => $units[array_rand($units)],
                'average_value' => $averageValue,
                'median_value' => $medianValue,
                'best_value' => $bestValue,
                'user_rank' => $userRank,
                'total_participants' => $totalParticipants,
                'percentile' => $percentile,
                'breakdown' => [
                    'category_1' => rand(20, 80),
                    'category_2' => rand(10, 50),
                    'category_3' => rand(5, 30),
                ],
                'metadata' => [
                    'region' => 'España',
                    'industry' => 'Residencial',
                    'building_type' => 'Apartamento',
                    'size_sqm' => rand(50, 200),
                ],
                'is_public' => rand(0, 1) == 1,
                'comparison_date' => Carbon::now()->subDays(rand(0, 365)),
            ]);
        }
    }
}
