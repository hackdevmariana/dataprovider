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
            UserRolesSeeder::class,
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
            PersonSeeder::class,
            RelationshipTypeSeeder::class,
            FamilyMemberSeeder::class,
            AliasSeeder::class,
            AppearanceSeeder::class,
            PersonProfessionSeeder::class,
            CooperativeUserMemberSeeder::class,
            
            // Cultural and artistic data
            ArtistSeeder::class,
            EventSeeder::class,
            AwardSeeder::class,
            AnniversarySeeder::class,
            
            // Energy sector data
            EnergyCompanySeeder::class,
            ElectricityPriceSeeder::class,
            ElectricityOfferSeeder::class,
            EnergyInstallationSeeder::class,
            CooperativeSeeder::class,
            ExchangeRateSeeder::class,
            
            // Geographic data
            PointOfInterestSeeder::class,
            ZoneClimateSeeder::class,
            
            // Sustainability data
            CarbonEquivalenceSeeder::class,
            CarbonCalculationSeeder::class,
            PlantSpeciesSeeder::class,
            WeatherAndSolarDataSeeder::class,
            
            // Media and communication data
            MediaOutletSeeder::class,
            MediaContactSeeder::class,
            ScrapingSourceSeeder::class,
            
            // Technology and analytics data
            UserDeviceSeeder::class,
            NotificationSettingSeeder::class,
            StatSeeder::class,
            ApiKeySeeder::class,
            
            // Visual identity and branding data
            ColorSeeder::class,
            FontSeeder::class,
            VisualIdentitySeeder::class,
            TagGroupSeeder::class,
            
            // Gamification data
            AchievementSeeder::class,
            ChallengeSeeder::class,
            NewsArticleSeeder::class,
            UserGeneratedContentSeeder::class,
        ]);
    }
}
