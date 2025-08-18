<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->call([
            // Core system data
            RolesAndAdminSeeder::class,
            AppSettingSeeder::class,
            
            // Reference data - Geographic
            TimezoneSeeder::class,
            LanguageSeeder::class,
            CountrySeeder::class,
            AutonomousCommunitySeeder::class,
            ProvinceSeeder::class,
            \Database\Seeders\Municipality\MadridMunicipalitiesSeeder::class,
            
            // Reference data - Economic
            CurrencySeeder::class,
            PriceUnitSeeder::class,
            CompanyTypeSeeder::class,
            
            // Reference data - Cultural
            EventTypeSeeder::class,
            VenueTypeSeeder::class,
            TagSeeder::class,
            
            // Reference data - People
            ProfessionSeeder::class,
            FamilyMemberSeeder::class,
            
            // Energy sector data
            EnergyCompanySeeder::class,
            ElectricityPriceSeeder::class,
            EnergyInstallationSeeder::class,
            CooperativeSeeder::class,
            
            // Sustainability data
            CarbonEquivalenceSeeder::class,
            PlantSpeciesSeeder::class,
            WeatherAndSolarDataSeeder::class,
            
            // Media and communication data
            MediaOutletSeeder::class,
            MediaContactSeeder::class,
            NewsArticleSeeder::class,
            UserGeneratedContentSeeder::class,
        ]);
    }
}
