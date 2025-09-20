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
        $this->command->info('🚀 Iniciando proceso de seeding completo...');
        
        // ===== CONFIGURACIÓN BÁSICA =====
        $this->command->info('📋 Configurando datos básicos...');
        $this->call([
            TimezoneSeeder::class,
            CountrySeeder::class,
            LanguageSeeder::class,
            AutonomousCommunitySeeder::class,
            ProvinceSeeder::class,
            RegionSeeder::class,
            MunicipalitySeeder::class,
            CurrencySeeder::class,
            CompanyTypeSeeder::class,
            RelationshipTypeSeeder::class,
            EventTypeSeeder::class,
            VenueTypeSeeder::class,
            PriceUnitSeeder::class,
            TagGroupSeeder::class,
            CategorySeeder::class,
            PlatformSeeder::class,
        ]);

        // ===== USUARIOS Y PERMISOS =====
        $this->command->info('👥 Configurando usuarios y permisos...');
        $this->call([
            UserSeeder::class,
            RolesAndAdminSeeder::class,
            UserReputationSeeder::class,
        ]);

        // ===== DATOS DE ENERGÍA Y SOSTENIBILIDAD =====
        $this->command->info('⚡ Configurando datos de energía y sostenibilidad...');
        $this->call([
            EmissionFactorSeeder::class,
            PlantSpeciesSeeder::class,
            ZoneClimateSeeder::class,
            EnergyCompanySeeder::class,
            EnergyInstallationSeeder::class,
            ElectricityPriceSeeder::class,
            ElectricityPriceIntervalSeeder::class,
            ElectricityOfferSeeder::class,
            EnergyCertificateSeeder::class,
            CarbonEquivalenceSeeder::class,
            CarbonSavingRequestSeeder::class,
            CarbonSavingRequestsSeeder::class,
            CarbonCalculationSeeder::class,
            CarbonCalculationsSeeder::class,
            WeatherAndSolarDataSeeder::class,
            EnergyTransactionSeeder::class,
            EnergyServiceSeeder::class,
        ]);

        // ===== COOPERATIVAS Y PROYECTOS =====
        $this->command->info('🏢 Configurando cooperativas y proyectos...');
        $this->call([
            CooperativeSeeder::class,
            CooperativeUserMemberSeeder::class,
            CooperativePostSeeder::class,
            ProjectProposalSeeder::class,
            ProjectInvestmentSeeder::class,
            ProjectInvestmentsSeeder::class,
            ProjectUpdateSeeder::class,
            ProjectCommissionSeeder::class,
            ProjectCommissionsSeeder::class,
            ProjectVerificationSeeder::class,
            ProductionRightsSeeder::class,
            RoofMarketplaceSeeder::class,
            CarbonSavingLogSeeder::class,
        ]);

        // ===== CONTENIDO Y TEMAS =====
        $this->command->info('📝 Configurando contenido y temas...');
        $this->call([
            TopicSeeder::class,
            TopicPostSeeder::class,
            TopicCommentSeeder::class,
            TopicMembershipSeeder::class,
            TopicFollowingSeeder::class,
            TopicFollowingsSeeder::class,
            HashtagSeeder::class,
            TagSeeder::class,
            TrendingTopicSeeder::class,
        ]);

        // ===== PERSONAS Y ORGANIZACIONES =====
        $this->command->info('👤 Configurando personas y organizaciones...');
        $this->call([
            ProfessionSeeder::class,
            PersonSeeder::class,
            AliasSeeder::class,
            WorkSeeder::class,
            PersonWorkSeeder::class,
            PersonProfessionSeeder::class,
            AwardSeeder::class,
            AwardWinnerSeeder::class,
            AppearanceSeeder::class,
            FamilyMemberSeeder::class,
            OrganizationSeeder::class,
            OrganizationFeatureSeeder::class,
            SocialEntitySeeder::class,
        ]);

        // ===== EVENTOS Y CULTURA =====
        $this->command->info('🎭 Configurando eventos y cultura...');
        $this->call([
            VenueSeeder::class,
            FestivalSeeder::class,
            FestivalProgramSeeder::class,
            FestivalActivitySeeder::class,
            FestivalScheduleSeeder::class,
            EventSeeder::class,
            ArtistSeeder::class,
            GroupSeeder::class,
            CalendarHolidaySeeder::class,
            CalendarHolidayLocationSeeder::class,
            AnniversarySeeder::class,
            DailyAnniversarySeeder::class,
            HistoricalEventSeeder::class,
            TimelineSeeder::class,
            ContentHashtagSeeder::class,
        ]);

        // ===== RELIGIÓN Y ESPIRITUALIDAD =====
        $this->command->info('⛪ Configurando contenido religioso...');
        $this->call([
            CatholicSaintSeeder::class,
            DevotionSeeder::class,
            PilgrimageSiteSeeder::class,
            LiturgicalCalendarSeeder::class,
            BishopricSeeder::class,
            ParishSeeder::class,
        ]);

        // ===== MEDIOS Y NOTICIAS =====
        $this->command->info('📰 Configurando medios y noticias...');
        $this->call([
            MediaOutletSeeder::class,
            MediaContactSeeder::class,
            MediaContactsSeeder::class,
            NewsSourceSeeder::class,
            NewsArticleSeeder::class,
            NewsAggregationSeeder::class,
            ScrapingSourceSeeder::class,
            DataSourceSeeder::class,
            SyncLogSeeder::class,
        ]);

        // ===== PRECIOS Y SERVICIOS =====
        $this->command->info('💰 Configurando precios y servicios...');
        $this->call([
            RealTimePriceSeeder::class,
            PriceForecastSeeder::class,
            PriceAlertSeeder::class,
            OfferComparisonSeeder::class,
            OfferHistorySeeder::class,
            BillSimulatorSeeder::class,
            ExchangeRateSeeder::class,
        ]);

        // ===== SISTEMA SOCIAL =====
        $this->command->info('🤝 Configurando sistema social...');
        $this->call([
            UserFollowSeeder::class,
            UserBookmarkSeeder::class,
            UserBookmarksSeeder::class,
            UserListSeeder::class,
            ListItemSeeder::class,
            ListItemsSeeder::class,
            UserEndorsementSeeder::class,
            UserReviewSeeder::class,
            ContentVoteSeeder::class,
            SocialInteractionSeeder::class,
            ActivityFeedSeeder::class,
            ActivityFeedsSeeder::class,
            ReputationTransactionSeeder::class,
        ]);

        // ===== GAMIFICACIÓN =====
        $this->command->info('🎮 Configurando sistema de gamificación...');
        $this->call([
            AchievementSeeder::class,
            AchievementsSeeder::class,
            ChallengeSeeder::class,
            ChallengesSeeder::class,
            UserAchievementSeeder::class,
            UserAchievementsSeeder::class,
            UserChallengeSeeder::class,
            UserChallengesSeeder::class,
            UserBadgeSeeder::class,
            UserPrivilegeSeeder::class,
            UserPrivilegesSeeder::class,
            ExpertVerificationSeeder::class,
            ExpertVerificationsSeeder::class,
            LeaderboardSeeder::class,
            LeaderboardsSeeder::class,
            SocialComparisonSeeder::class,
            SocialComparisonsSeeder::class,
        ]);

        // ===== SUSCRIPCIONES Y PAGOS =====
        $this->command->info('💳 Configurando suscripciones y pagos...');
        $this->call([
            SubscriptionPlanSeeder::class,
            UserSubscriptionSeeder::class,
            UserSubscriptionsSeeder::class,
            ConsultationServiceSeeder::class,
            PaymentSeeder::class,
            CompanyCertificationSeeder::class,
        ]);

        // ===== CONTENIDO GENERADO POR USUARIOS =====
        $this->command->info('✍️ Configurando contenido generado por usuarios...');
        $this->call([
            UserGeneratedContentSeeder::class,
            CooperativePostSeeder::class,
            CooperativePostsSeeder::class,
            SponsoredContentSeeder::class,
            SponsoredContentsSeeder::class,
        ]);

        // ===== CULTURA Y ARTE =====
        $this->command->info('🎨 Configurando cultura y arte...');
        $this->call([
            QuoteSeeder::class,
            QuoteCategorySeeder::class,
            QuoteCollectionSeeder::class,
            BookSeeder::class,
            BookEditionSeeder::class,
            BookReviewSeeder::class,
            LinkSeeder::class,
            PointOfInterestSeeder::class,
            SpanishRapSeeder::class,
        ]);

        // ===== CONFIGURACIÓN VISUAL =====
        $this->command->info('🎨 Configurando elementos visuales...');
        $this->call([
            ColorSeeder::class,
            ColorsSeeder::class,
            FontSeeder::class,
            FontsSeeder::class,
            VisualIdentitySeeder::class,
            ImageSeeder::class,
        ]);

        // ===== CONFIGURACIÓN TÉCNICA =====
        $this->command->info('⚙️ Configurando elementos técnicos...');
        $this->call([
            ApiKeySeeder::class,
            UserDeviceSeeder::class,
            NotificationSettingSeeder::class,
            SocialAccountSeeder::class,
            AppSettingSeeder::class,
            StatSeeder::class,
            StatsSeeder::class,
            PlatformSeeder::class,
            PlatformsSeeder::class,
            UserRolesSeeder::class,
        ]);

        $this->command->info('✅ Proceso de seeding completado exitosamente!');
    }
}
