<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            TopicCommentSeeder::class,
            UserGeneratedContentSeeder::class,
            GroupSeeder::class,
            FestivalProgramSeeder::class,
            QuoteSeeder::class,
            RealTimePriceSeeder::class,
            NewsSourceSeeder::class,
            FestivalActivitySeeder::class,
            FestivalScheduleSeeder::class,
            NewsAggregationSeeder::class,
            TrendingTopicSeeder::class,
            DevotionSeeder::class,
            OfferHistorySeeder::class,
            PriceForecastSeeder::class,
        ]);
    }
}
