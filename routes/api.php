<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AppSettingController;
use App\Http\Controllers\Api\V1\ProvinceController;
use App\Http\Controllers\Api\V1\CountryController;
use App\Http\Controllers\Api\V1\LanguageController;
use App\Http\Controllers\Api\V1\TimezoneController;
use App\Http\Controllers\Api\V1\MunicipalityController;
use App\Http\Controllers\Api\V1\PointOfInterestController;
use App\Http\Controllers\Api\V1\AutonomousCommunityController;
use App\Http\Controllers\Api\V1\PersonController;
use App\Http\Controllers\Api\V1\ImageController;
use App\Http\Controllers\Api\V1\ProfessionController;
use App\Http\Controllers\Api\V1\WorkController;
use App\Http\Controllers\Api\V1\LinkController;
use App\Http\Controllers\Api\V1\AwardController;
use App\Http\Controllers\Api\V1\AwardWinnerController;
use App\Http\Controllers\Api\V1\FamilyMemberController;
use App\Http\Controllers\Api\V1\ElectricityPriceController;
use App\Http\Controllers\Api\V1\EnergyCompanyController;
use App\Http\Controllers\Api\V1\EnergyInstallationController;
use App\Http\Controllers\Api\V1\CooperativeController;
use App\Http\Controllers\Api\V1\CarbonEquivalenceController;
use App\Http\Controllers\Api\V1\PlantSpeciesController;
use App\Http\Controllers\Api\V1\WeatherAndSolarDataController;
use App\Http\Controllers\Api\V1\NewsArticleController;
use App\Http\Controllers\Api\V1\MediaOutletController;
use App\Http\Controllers\Api\V1\MediaContactController;
use App\Http\Controllers\Api\V1\ScrapingSourceController;
use App\Http\Controllers\Api\V1\UserGeneratedContentController;

// Nuevos controladores implementados
use App\Http\Controllers\Api\V1\AchievementController;
use App\Http\Controllers\Api\V1\ChallengeController;
use App\Http\Controllers\Api\V1\ColorController;
use App\Http\Controllers\Api\V1\FontController;
use App\Http\Controllers\Api\V1\ElectricityOfferController;
use App\Http\Controllers\Api\V1\NotificationSettingController;
use App\Http\Controllers\Api\V1\ExchangeRateController;
use App\Http\Controllers\Api\V1\CurrencyController;
use App\Http\Controllers\Api\V1\PriceUnitController;
use App\Http\Controllers\Api\V1\UserDeviceController;
use App\Http\Controllers\Api\V1\SocialAccountController;
use App\Http\Controllers\Api\V1\ApiKeyController;

// Controladores restantes implementados
use App\Http\Controllers\Api\V1\AliasController;
use App\Http\Controllers\Api\V1\AppearanceController;
use App\Http\Controllers\Api\V1\OrganizationController;
use App\Http\Controllers\Api\V1\OrganizationFeatureController;
use App\Http\Controllers\Api\V1\CarbonCalculationController;
use App\Http\Controllers\Api\V1\CarbonSavingLogController;
use App\Http\Controllers\Api\V1\CarbonSavingRequestController;
use App\Http\Controllers\Api\V1\EmissionFactorController;
use App\Http\Controllers\Api\V1\EnergyCertificateController;
use App\Http\Controllers\Api\V1\EnergyTransactionController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\CompanyTypeController;
use App\Http\Controllers\Api\V1\DataSourceController;
use App\Http\Controllers\Api\V1\ElectricityPriceIntervalController;
use App\Http\Controllers\Api\V1\PlatformController;
use App\Http\Controllers\Api\V1\StatController;
use App\Http\Controllers\Api\V1\SyncLogController;
use App\Http\Controllers\Api\V1\TagController;
use App\Http\Controllers\Api\V1\TagGroupController;
use App\Http\Controllers\Api\V1\RelationshipTypeController;
use App\Http\Controllers\Api\V1\VenueTypeController;
use App\Http\Controllers\Api\V1\VisualIdentityController;
use App\Http\Controllers\Api\V1\CalendarHolidayLocationController;
use App\Http\Controllers\Api\V1\CooperativeUserMemberController;
use App\Http\Controllers\Api\V1\PersonProfessionController;
use App\Http\Controllers\Api\V1\PersonWorkController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\UserAchievementController;
use App\Http\Controllers\Api\V1\UserChallengeController;
use App\Http\Controllers\Api\V1\ZoneClimateController;

// Social Features Controllers
use App\Http\Controllers\Api\V1\TopicController;
use App\Http\Controllers\Api\V1\HashtagController;
use App\Http\Controllers\Api\V1\ActivityFeedController;
use App\Http\Controllers\Api\V1\SocialInteractionController;
use App\Http\Controllers\Api\V1\UserFollowController;
use App\Http\Controllers\Api\V1\UserListController;

// Collaborative Projects Controllers
use App\Http\Controllers\Api\V1\ProjectProposalController;
use App\Http\Controllers\Api\V1\RoofMarketplaceController;

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::apiResource('app-settings', AppSettingController::class)->only(['index', 'show']);
    Route::post('/points-of-interest', [PointOfInterestController::class, 'store']);
    Route::put('/points-of-interest/{id}', [PointOfInterestController::class, 'update']);
    // Route::delete('/points-of-interest/{id}', [PointOfInterestController::class, 'destroy']);
    Route::post('/images', [ImageController::class, 'store']);
    Route::put('/images/{id}', [ImageController::class, 'update']);
    Route::delete('/images/{id}', [ImageController::class, 'destroy']);
    Route::post('/professions', [ProfessionController::class, 'store']);
    Route::post('/works', [WorkController::class, 'store']);
    Route::post('/links', [LinkController::class, 'store']);
    Route::post('/awards', [AwardController::class, 'store']);
    Route::post('/award-winners', [AwardWinnerController::class, 'store']);
    
    // Energy Installations (authenticated routes - only update/delete)
    Route::put('/energy-installations/{energyInstallation}', [EnergyInstallationController::class, 'update']);
    Route::delete('/energy-installations/{energyInstallation}', [EnergyInstallationController::class, 'destroy']);
    
    // Cooperatives (authenticated routes - only update/delete)
    Route::put('/cooperatives/{cooperative}', [CooperativeController::class, 'update']);
    Route::delete('/cooperatives/{cooperative}', [CooperativeController::class, 'destroy']);
});

// Rutas públicas sin autenticación
Route::prefix('v1')->group(function () {
    Route::get('/provinces', [ProvinceController::class, 'index']);
    Route::get('/provinces/with-municipalities-count', [ProvinceController::class, 'withMunicipalitiesCount']);
    Route::get('/provinces/filter/by-area', [ProvinceController::class, 'filterByArea']);
    Route::get('/provinces/search', [ProvinceController::class, 'search']);
    Route::get('/provinces/largest/{limit}', [ProvinceController::class, 'largest']);
    Route::get('/provinces/by-autonomous-community/{slug}', [ProvinceController::class, 'byAutonomousCommunity']);
    Route::get('/provinces/{idOrSlug}', [ProvinceController::class, 'show']);
    Route::get('/countries', [CountryController::class, 'index']);
    Route::get('/countries/{idOrSlug}', [CountryController::class, 'show']);
    Route::get('/languages', [LanguageController::class, 'index']);
    Route::get('/languages/{idOrSlug}', [LanguageController::class, 'show']);
    Route::get('/timezones', [TimezoneController::class, 'index']);
    Route::get('/timezones/{idOrName}', [TimezoneController::class, 'show']);
    Route::get('/municipalities', [MunicipalityController::class, 'index']);
    Route::get('/municipalities/filter/by-population', [MunicipalityController::class, 'filterByPopulation']);
    Route::get('/municipalities/filter/by-area', [MunicipalityController::class, 'filterByArea']);
    Route::get('/municipalities/search', [MunicipalityController::class, 'search']);
    Route::get('/municipalities/largest/{limit?}', [MunicipalityController::class, 'largest'])->where('limit', '[0-9]+');
    Route::get('/municipalities/province/{slug}', [MunicipalityController::class, 'byProvince']);
    Route::get('/municipalities/country/{slug}', [MunicipalityController::class, 'byCountry']);
    Route::get('/municipalities/{idOrSlug}', [MunicipalityController::class, 'show']);
    Route::get('/points-of-interest', [PointOfInterestController::class, 'index']);
    Route::get('/points-of-interest/{idOrSlug}', [PointOfInterestController::class, 'show']);
    Route::get('/points-of-interest/municipality/{slug}', [PointOfInterestController::class, 'byMunicipality']);
    Route::get('/points-of-interest/type/{type}', [PointOfInterestController::class, 'byType']);
    Route::get('/points-of-interest/tag/{tagSlug}', [PointOfInterestController::class, 'byTag']);
    Route::get('/autonomous-communities', [AutonomousCommunityController::class, 'index']);
    Route::get('/autonomous-communities/{slug}', [AutonomousCommunityController::class, 'show']);
    Route::get('/autonomous-communities-with-provinces', [AutonomousCommunityController::class, 'withProvinces']);
    Route::get('/autonomous-communities-with-provinces-and-municipalities', [AutonomousCommunityController::class, 'withProvincesAndMunicipalities']);
    Route::get('/persons', [PersonController::class, 'index']);
    Route::get('/persons/{idOrSlug}', [PersonController::class, 'show']);
    Route::get('/images', [ImageController::class, 'index']);
    Route::get('/images/{id}', [ImageController::class, 'show']);
    Route::get('/professions', [ProfessionController::class, 'index']);
    Route::get('/professions/{idOrSlug}', [ProfessionController::class, 'show']);
    Route::get('/works', [WorkController::class, 'index']);
    Route::get('/works/{idOrSlug}', [WorkController::class, 'show']);
    Route::get('/links', [LinkController::class, 'index']);
    Route::get('/links/{id}', [LinkController::class, 'show']);
    Route::get('/awards', [AwardController::class, 'index']);
    Route::get('/awards/{idOrSlug}', [AwardController::class, 'show']);
    Route::get('/award-winners', [AwardWinnerController::class, 'index']);
    Route::get('/award-winners/{id}', [AwardWinnerController::class, 'show']);
    Route::get('/family-members', [FamilyMemberController::class, 'index']);
    Route::get('/family-members/{id}', [FamilyMemberController::class, 'show']);
    Route::get('/regions', [\App\Http\Controllers\Api\V1\RegionController::class, 'index']);
    Route::get('/regions/{idOrSlug}', [\App\Http\Controllers\Api\V1\RegionController::class, 'show']);
    Route::get('/provinces/{slug}/regions', [\App\Http\Controllers\Api\V1\RegionController::class, 'byProvince']);
    Route::get('/autonomous-communities/{slug}/regions', [\App\Http\Controllers\Api\V1\RegionController::class, 'byAutonomousCommunity']);
    Route::get('/countries/{slug}/regions', [\App\Http\Controllers\Api\V1\RegionController::class, 'byCountry']);
    Route::get('/anniversaries', [\App\Http\Controllers\Api\V1\AnniversaryController::class, 'index']);
    Route::get('/anniversaries/{idOrSlug}', [\App\Http\Controllers\Api\V1\AnniversaryController::class, 'show']);
    Route::get('/anniversaries/day/{month}/{day}', [\App\Http\Controllers\Api\V1\AnniversaryController::class, 'byDay']);
    Route::get('/calendar-holidays', [\App\Http\Controllers\Api\V1\CalendarHolidayController::class, 'index']);
    Route::get('/calendar-holidays/{idOrSlug}', [\App\Http\Controllers\Api\V1\CalendarHolidayController::class, 'show']);
    Route::get('/calendar-holidays/date/{date}', [\App\Http\Controllers\Api\V1\CalendarHolidayController::class, 'byDate']);
    Route::get('/events', [\App\Http\Controllers\Api\V1\EventController::class, 'index']);
    Route::get('/events/filter/by-date-range', [\App\Http\Controllers\Api\V1\EventController::class, 'filterByDateRange']);
    Route::get('/events/filter/by-location', [\App\Http\Controllers\Api\V1\EventController::class, 'filterByLocation']);
    Route::get('/events/filter/by-type', [\App\Http\Controllers\Api\V1\EventController::class, 'filterByType']);
    Route::get('/events/search', [\App\Http\Controllers\Api\V1\EventController::class, 'search']);
    Route::get('/events/upcoming/{days?}', [\App\Http\Controllers\Api\V1\EventController::class, 'upcoming'])->where('days', '[0-9]+');
    Route::get('/events/{idOrSlug}', [\App\Http\Controllers\Api\V1\EventController::class, 'show']);
    Route::get('/event-types', [\App\Http\Controllers\Api\V1\EventTypeController::class, 'index']);
    Route::get('/event-types/{idOrSlug}', [\App\Http\Controllers\Api\V1\EventTypeController::class, 'show']);
    Route::get('/venues', [\App\Http\Controllers\Api\V1\VenueController::class, 'index']);
    Route::get('/venues/{idOrSlug}', [\App\Http\Controllers\Api\V1\VenueController::class, 'show']);
    Route::post('/venues', [\App\Http\Controllers\Api\V1\VenueController::class, 'store']);
    Route::get('/artists', [\App\Http\Controllers\Api\V1\ArtistController::class, 'index']);
    Route::get('/artists/{idOrSlug}', [\App\Http\Controllers\Api\V1\ArtistController::class, 'show']);
    Route::post('/artists', [\App\Http\Controllers\Api\V1\ArtistController::class, 'store']);
    Route::get('/groups', [\App\Http\Controllers\Api\V1\GroupController::class, 'index']);
    Route::get('/groups/{id}', [\App\Http\Controllers\Api\V1\GroupController::class, 'show']);
    Route::post('/groups', [\App\Http\Controllers\Api\V1\GroupController::class, 'store']);
    Route::get('/festivals', [\App\Http\Controllers\Api\V1\FestivalController::class, 'index']);
    Route::get('/festivals/filter/by-location', [\App\Http\Controllers\Api\V1\FestivalController::class, 'filterByLocation']);
    Route::get('/festivals/search', [\App\Http\Controllers\Api\V1\FestivalController::class, 'search']);
    Route::post('/festivals', [\App\Http\Controllers\Api\V1\FestivalController::class, 'store']);
    // Specific festival routes must come before {id} routes
    Route::get('/festivals/today', [\App\Http\Controllers\Api\V1\FestivalController::class, 'today']);
    Route::get('/festivals/this-week', [\App\Http\Controllers\Api\V1\FestivalController::class, 'thisWeek']);
    Route::get('/festivals/this-month', [\App\Http\Controllers\Api\V1\FestivalController::class, 'thisMonth']);
    Route::get('/festivals/this-year', [\App\Http\Controllers\Api\V1\FestivalController::class, 'thisYear']);
    Route::get('/festivals/municipality/{idOrSlug}', [\App\Http\Controllers\Api\V1\FestivalController::class, 'byMunicipality']);
    Route::get('/festivals/region/{idOrSlug}', [\App\Http\Controllers\Api\V1\FestivalController::class, 'byRegion']);
    Route::get('/festivals/province/{idOrSlug}', [\App\Http\Controllers\Api\V1\FestivalController::class, 'byProvince']);
    Route::get('/festivals/autonomous-community/{idOrSlug}', [\App\Http\Controllers\Api\V1\FestivalController::class, 'byAutonomousCommunity']);
    // Generic {id} routes come last
    Route::get('/festivals/{id}', [\App\Http\Controllers\Api\V1\FestivalController::class, 'show']);
    Route::get('/festivals/{id}/events', [\App\Http\Controllers\Api\V1\FestivalController::class, 'events']);
    Route::get('/festivals/{id}/artists', [\App\Http\Controllers\Api\V1\FestivalController::class, 'artists']);
    Route::get('/festivals-and-unassigned-events', [\App\Http\Controllers\Api\V1\FestivalController::class, 'festivalsAndUnassignedEvents']);
    Route::get('/festivals-with-events-and-unassigned', [\App\Http\Controllers\Api\V1\FestivalController::class, 'festivalsWithEventsAndUnassigned']);
    
    // Energy APIs
    Route::get('/electricity-prices', [ElectricityPriceController::class, 'index']);
    Route::get('/electricity-prices/today', [ElectricityPriceController::class, 'today']);
    Route::get('/electricity-prices/current-hour', [ElectricityPriceController::class, 'currentHour']);
    Route::get('/electricity-prices/cheapest-hours', [ElectricityPriceController::class, 'cheapestHours']);
    Route::get('/electricity-prices/daily-summary', [ElectricityPriceController::class, 'dailySummary']);
    Route::get('/electricity-prices/{id}', [ElectricityPriceController::class, 'show']);
    
    Route::get('/energy-companies', [EnergyCompanyController::class, 'index']);
    Route::get('/energy-companies/filter/by-location', [EnergyCompanyController::class, 'filterByLocation']);
    Route::get('/energy-companies/search', [EnergyCompanyController::class, 'search']);
    Route::get('/energy-companies/commercializers', [EnergyCompanyController::class, 'commercializers']);
    Route::get('/energy-companies/cooperatives', [EnergyCompanyController::class, 'cooperatives']);
    Route::get('/energy-companies/{idOrSlug}', [EnergyCompanyController::class, 'show']);

    // Energy Installations API (public read and create, authenticated update/delete)
    Route::get('/energy-installations', [EnergyInstallationController::class, 'index']);
    Route::post('/energy-installations', [EnergyInstallationController::class, 'store']); // Public create
    Route::get('/energy-installations/filter/by-type/{type}', [EnergyInstallationController::class, 'filterByType']);
    Route::get('/energy-installations/filter/by-capacity', [EnergyInstallationController::class, 'filterByCapacity']);
    Route::get('/energy-installations/commissioned', [EnergyInstallationController::class, 'commissioned']);
    Route::get('/energy-installations/in-development', [EnergyInstallationController::class, 'inDevelopment']);
    Route::get('/energy-installations/search', [EnergyInstallationController::class, 'search']);
    Route::get('/energy-installations/statistics', [EnergyInstallationController::class, 'statistics']);
    Route::get('/energy-installations/{energyInstallation}', [EnergyInstallationController::class, 'show']);

    // Cooperatives API (public read and create, authenticated update/delete)
    Route::get('/cooperatives', [CooperativeController::class, 'index']);
    Route::post('/cooperatives', [CooperativeController::class, 'store']); // Public create
    Route::get('/cooperatives/filter/by-type/{type}', [CooperativeController::class, 'filterByType']);
    Route::get('/cooperatives/energy', [CooperativeController::class, 'energy']);
    Route::get('/cooperatives/open-to-members', [CooperativeController::class, 'openToMembers']);
    Route::get('/cooperatives/search', [CooperativeController::class, 'search']);
    Route::get('/cooperatives/statistics', [CooperativeController::class, 'statistics']);
    Route::get('/cooperatives/{idOrSlug}', [CooperativeController::class, 'show']);

    // Carbon Equivalences API (calculadora de huella de carbono)
    Route::get('/carbon-equivalences', [CarbonEquivalenceController::class, 'index']);
    Route::get('/carbon-equivalences/filter/energy', [CarbonEquivalenceController::class, 'energy']);
    Route::get('/carbon-equivalences/filter/transport', [CarbonEquivalenceController::class, 'transport']);
    Route::post('/carbon-equivalences/calculate', [CarbonEquivalenceController::class, 'calculate']);
    Route::get('/carbon-equivalences/statistics', [CarbonEquivalenceController::class, 'statistics']);
    Route::get('/carbon-equivalences/{carbonEquivalence}', [CarbonEquivalenceController::class, 'show']);

    // Plant Species API (catálogo de especies vegetales para reforestación)
    Route::get('/plant-species', [PlantSpeciesController::class, 'index']);
    Route::get('/plant-species/filter/trees', [PlantSpeciesController::class, 'trees']);
    Route::get('/plant-species/filter/reforestation', [PlantSpeciesController::class, 'forReforestation']);
    Route::get('/plant-species/filter/high-co2', [PlantSpeciesController::class, 'highCO2Absorption']);
    Route::get('/plant-species/filter/drought-resistant', [PlantSpeciesController::class, 'droughtResistant']);
    Route::post('/plant-species/calculate-compensation', [PlantSpeciesController::class, 'calculateCompensation']);
    Route::get('/plant-species/statistics', [PlantSpeciesController::class, 'statistics']);
    Route::get('/plant-species/{plantSpecies}', [PlantSpeciesController::class, 'show']);

    // Weather & Solar Data API (optimización energética con datos meteorológicos)
    Route::get('/weather-solar-data', [WeatherAndSolarDataController::class, 'index']);
    Route::get('/weather-solar-data/current', [WeatherAndSolarDataController::class, 'current']);
    Route::get('/weather-solar-data/forecast', [WeatherAndSolarDataController::class, 'forecast']);
    Route::get('/weather-solar-data/optimal-solar', [WeatherAndSolarDataController::class, 'optimalSolar']);
    Route::get('/weather-solar-data/optimal-wind', [WeatherAndSolarDataController::class, 'optimalWind']);
    Route::get('/weather-solar-data/near-location', [WeatherAndSolarDataController::class, 'nearLocation']);
    Route::post('/weather-solar-data/calculate-production', [WeatherAndSolarDataController::class, 'calculateProduction']);
    Route::get('/weather-solar-data/daily-optimization', [WeatherAndSolarDataController::class, 'dailyOptimization']);
    Route::get('/weather-solar-data/statistics', [WeatherAndSolarDataController::class, 'statistics']);
    Route::get('/weather-solar-data/{weatherAndSolarData}', [WeatherAndSolarDataController::class, 'show']);

    // === MEDIOS Y COMUNICACIÓN ===

    // News Articles API (gestión completa de noticias con análisis sostenibilidad)
    Route::get('/news-articles', [NewsArticleController::class, 'index']);
    Route::get('/news-articles/featured', [NewsArticleController::class, 'featured']);
    Route::get('/news-articles/breaking', [NewsArticleController::class, 'breaking']);
    Route::get('/news-articles/sustainability', [NewsArticleController::class, 'sustainability']);
    Route::get('/news-articles/popular', [NewsArticleController::class, 'popular']);
    Route::get('/news-articles/near-location', [NewsArticleController::class, 'nearLocation']);
    Route::get('/news-articles/statistics', [NewsArticleController::class, 'statistics']);
    Route::post('/news-articles/{article}/analyze-sustainability', [NewsArticleController::class, 'analyzeSustainability']);
    Route::post('/news-articles/{article}/increment-shares', [NewsArticleController::class, 'incrementShares']);
    Route::get('/news-articles/{idOrSlug}', [NewsArticleController::class, 'show']);

    // Media Outlets API (análisis de credibilidad e influencia de medios)
    Route::get('/media-outlets', [MediaOutletController::class, 'index']);
    Route::get('/media-outlets/verified', [MediaOutletController::class, 'verified']);
    Route::get('/media-outlets/sustainability-focused', [MediaOutletController::class, 'sustainabilityFocused']);
    Route::get('/media-outlets/high-credibility', [MediaOutletController::class, 'highCredibility']);
    Route::get('/media-outlets/influential', [MediaOutletController::class, 'influential']);
    Route::get('/media-outlets/digital-native', [MediaOutletController::class, 'digitalNative']);
    Route::get('/media-outlets/local', [MediaOutletController::class, 'local']);
    Route::get('/media-outlets/national', [MediaOutletController::class, 'national']);
    Route::get('/media-outlets/reference', [MediaOutletController::class, 'reference']);
    Route::get('/media-outlets/statistics', [MediaOutletController::class, 'statistics']);
    Route::get('/media-outlets/credibility-ranking', [MediaOutletController::class, 'credibilityRanking']);
    Route::get('/media-outlets/influence-ranking', [MediaOutletController::class, 'influenceRanking']);
    Route::post('/media-outlets/{outlet}/calculate-scores', [MediaOutletController::class, 'calculateScores']);
    Route::get('/media-outlets/{idOrSlug}', [MediaOutletController::class, 'show']);

    // Media Contacts API (gestión de contactos de prensa con tracking interacciones)
    Route::get('/media-contacts', [MediaContactController::class, 'index']);
    Route::get('/media-contacts/press-contacts', [MediaContactController::class, 'pressContacts']);
    Route::get('/media-contacts/sustainability-focused', [MediaContactController::class, 'sustainabilityFocused']);
    Route::get('/media-contacts/statistics', [MediaContactController::class, 'statistics']);
    Route::post('/media-contacts/{contact}/record-interaction', [MediaContactController::class, 'recordInteraction']);
    Route::get('/media-contacts/{contact}', [MediaContactController::class, 'show']);

    // User Generated Content API (contenido usuarios con moderación automática)
    // Scraping Sources
    Route::get('/scraping-sources', [ScrapingSourceController::class, 'index']);
    Route::get('/scraping-sources/active', [ScrapingSourceController::class, 'active']);
    Route::get('/scraping-sources/sustainability', [ScrapingSourceController::class, 'sustainability']);
    Route::get('/scraping-sources/type/{type}', [ScrapingSourceController::class, 'byType']);
    Route::get('/scraping-sources/statistics', [ScrapingSourceController::class, 'statistics']);
    Route::get('/scraping-sources/needs-scraping', [ScrapingSourceController::class, 'needsScraping']);
    Route::post('/scraping-sources/{scrapingSource}/update-scraped', [ScrapingSourceController::class, 'updateLastScraped']);
    Route::post('/scraping-sources/{scrapingSource}/toggle-active', [ScrapingSourceController::class, 'toggleActive']);
    Route::get('/scraping-sources/{scrapingSource}', [ScrapingSourceController::class, 'show']);

    // User Generated Content
    Route::get('/user-content', [UserGeneratedContentController::class, 'index']);
    Route::post('/user-content', [UserGeneratedContentController::class, 'store']);
    Route::get('/user-content/comments', [UserGeneratedContentController::class, 'comments']);
    Route::get('/user-content/reviews', [UserGeneratedContentController::class, 'reviews']);
    Route::get('/user-content/featured', [UserGeneratedContentController::class, 'featured']);
    Route::get('/user-content/popular', [UserGeneratedContentController::class, 'popular']);
    Route::get('/user-content/statistics', [UserGeneratedContentController::class, 'statistics']);
    Route::post('/user-content/{content}/like', [UserGeneratedContentController::class, 'like']);
    Route::post('/user-content/{content}/dislike', [UserGeneratedContentController::class, 'dislike']);
    Route::get('/user-content/{content}', [UserGeneratedContentController::class, 'show']);

    // ========== NUEVOS ENDPOINTS IMPLEMENTADOS ==========
    
    // Gamificación
    Route::apiResource('achievements', AchievementController::class)->only(['index', 'show']);
    Route::apiResource('challenges', ChallengeController::class)->only(['index', 'show']);
    
    // Identidad Visual
    Route::apiResource('colors', ColorController::class)->only(['index', 'show']);
    Route::apiResource('fonts', FontController::class)->only(['index', 'show']);
    
    // Datos Económicos
    Route::apiResource('electricity-offers', ElectricityOfferController::class)->only(['index', 'show']);
    Route::apiResource('exchange-rates', ExchangeRateController::class)->only(['index', 'show']);
    Route::apiResource('currencies', CurrencyController::class)->only(['index', 'show']);
    Route::apiResource('price-units', PriceUnitController::class)->only(['index', 'show']);
    
    // Configuración de Usuario
    Route::apiResource('notification-settings', NotificationSettingController::class);
    Route::apiResource('user-devices', UserDeviceController::class);
    Route::apiResource('social-accounts', SocialAccountController::class);
    Route::apiResource('api-keys', ApiKeyController::class)->only(['index', 'show', 'store', 'destroy']);

    // ========== TODOS LOS CONTROLADORES RESTANTES ==========
    
    // Personas y Organizaciones
    Route::apiResource('aliases', AliasController::class)->only(['index', 'show']);
    Route::apiResource('appearances', AppearanceController::class)->only(['index', 'show']);
    Route::apiResource('organizations', OrganizationController::class)->only(['index', 'show']);
    Route::apiResource('organization-features', OrganizationFeatureController::class)->only(['index', 'show']);
    
    // Energía y Sostenibilidad
    Route::apiResource('carbon-calculations', CarbonCalculationController::class)->only(['index', 'show']);
    Route::apiResource('carbon-saving-logs', CarbonSavingLogController::class)->only(['index', 'show']);
    Route::apiResource('carbon-saving-requests', CarbonSavingRequestController::class);
    Route::apiResource('emission-factors', EmissionFactorController::class)->only(['index', 'show']);
    Route::apiResource('energy-certificates', EnergyCertificateController::class)->only(['index', 'show']);
    Route::apiResource('energy-transactions', EnergyTransactionController::class);
    
    // Configuración y Datos
    Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
    Route::apiResource('company-types', CompanyTypeController::class)->only(['index', 'show']);
    Route::apiResource('data-sources', DataSourceController::class)->only(['index', 'show']);
    Route::apiResource('electricity-price-intervals', ElectricityPriceIntervalController::class)->only(['index', 'show']);
    Route::apiResource('platforms', PlatformController::class)->only(['index', 'show']);
    Route::apiResource('stats', StatController::class)->only(['index', 'show']);
    Route::apiResource('sync-logs', SyncLogController::class)->only(['index', 'show']);
    
    // Contenido y Etiquetas
    Route::apiResource('tags', TagController::class);
    Route::apiResource('tag-groups', TagGroupController::class)->only(['index', 'show']);
    Route::apiResource('relationship-types', RelationshipTypeController::class)->only(['index', 'show']);
    Route::apiResource('venue-types', VenueTypeController::class)->only(['index', 'show']);
    Route::apiResource('visual-identities', VisualIdentityController::class)->only(['index', 'show']);
    
    // Especiales y Pivot
    Route::apiResource('calendar-holiday-locations', CalendarHolidayLocationController::class)->only(['index', 'show']);
    Route::apiResource('cooperative-user-members', CooperativeUserMemberController::class);
    Route::apiResource('person-professions', PersonProfessionController::class)->only(['index', 'show']);
    Route::apiResource('person-works', PersonWorkController::class)->only(['index', 'show']);
    Route::apiResource('users', UserController::class)->only(['index', 'show', 'update']);
    Route::apiResource('user-achievements', UserAchievementController::class);
    Route::apiResource('user-challenges', UserChallengeController::class);
    Route::apiResource('zone-climates', ZoneClimateController::class)->only(['index', 'show']);
    
    // ===== SOCIAL FEATURES ROUTES =====
    
    // Topics (Thematic Communities)
    Route::get('topics/trending', [TopicController::class, 'trending']); // Debe ir ANTES del apiResource
    Route::apiResource('topics', TopicController::class)->except(['update', 'destroy']);
    Route::post('topics/{slug}/join', [TopicController::class, 'join']);
    Route::post('topics/{slug}/leave', [TopicController::class, 'leave']);
    Route::get('topics/{slug}/stats', [TopicController::class, 'stats']);
    
    // Hashtags (Sistema de etiquetas inteligente)
    Route::get('hashtags/trending', [HashtagController::class, 'trending']);
    Route::get('hashtags/suggest', [HashtagController::class, 'suggest']);
    Route::get('hashtags/extract', [HashtagController::class, 'extract']);
    Route::apiResource('hashtags', HashtagController::class)->except(['update', 'destroy']);
    Route::get('hashtags/{hashtag}/related', [HashtagController::class, 'related']);
    
    // User Lists (Listas personalizadas)
    Route::get('user-lists/featured', [UserListController::class, 'featured']);
    Route::get('user-lists/search', [UserListController::class, 'search']);
    Route::apiResource('user-lists', UserListController::class);
    
    // ===== COLLABORATIVE PROJECTS ROUTES =====
    
    // Project Proposals (Propuestas de proyectos)
    Route::get('project-proposals/featured', [ProjectProposalController::class, 'featured']);
    Route::get('project-proposals/funding', [ProjectProposalController::class, 'funding']);
    Route::get('project-proposals/nearby', [ProjectProposalController::class, 'nearby']);
    Route::apiResource('project-proposals', ProjectProposalController::class)->except(['update', 'destroy']);
    Route::post('project-proposals/{slug}/invest', [ProjectProposalController::class, 'invest']);
    Route::get('project-proposals/{slug}/stats', [ProjectProposalController::class, 'stats']);
    
    // Roof Marketplace (Marketplace de techos)
    Route::get('roof-marketplace/featured', [RoofMarketplaceController::class, 'featured']);
    Route::get('roof-marketplace/nearby', [RoofMarketplaceController::class, 'nearby']);
    Route::get('roof-marketplace/stats', [RoofMarketplaceController::class, 'stats']);
    Route::apiResource('roof-marketplace', RoofMarketplaceController::class)->except(['update', 'destroy']);
    Route::post('roof-marketplace/{slug}/inquire', [RoofMarketplaceController::class, 'inquire']);
    Route::get('roof-marketplace/{slug}/energy-potential', [RoofMarketplaceController::class, 'energyPotential']);
    
    // ========================================
    // FASE 5: FEED DE ACTIVIDAD SOCIAL
    // ========================================
    
    // Activity Feed (Feed de actividades energéticas)
    Route::get('activity-feed', [ActivityFeedController::class, 'index']);
    Route::get('activity-feed/public', [ActivityFeedController::class, 'public']);
    Route::get('activity-feed/featured', [ActivityFeedController::class, 'featured']);
    Route::get('activity-feed/milestones', [ActivityFeedController::class, 'milestones']);
    Route::get('activity-feed/nearby', [ActivityFeedController::class, 'nearby']);
    Route::get('activity-feed/stats', [ActivityFeedController::class, 'stats']);
    Route::get('activity-feed/{activityFeed}', [ActivityFeedController::class, 'show']);
    
    // Social Interactions (Interacciones sociales)
    Route::post('social-interactions', [SocialInteractionController::class, 'store']);
    Route::delete('social-interactions/{socialInteraction}', [SocialInteractionController::class, 'destroy']);
    Route::get('social-interactions/for-object', [SocialInteractionController::class, 'forObject']);
    Route::get('social-interactions/my-interactions', [SocialInteractionController::class, 'myInteractions']);
    Route::get('social-interactions/stats', [SocialInteractionController::class, 'stats']);
    
    // User Following (Seguimiento de usuarios)
    Route::post('users/{user}/follow', [UserFollowController::class, 'follow']);
    Route::delete('users/{user}/unfollow', [UserFollowController::class, 'unfollow']);
    Route::get('user-follows/following', [UserFollowController::class, 'following']);
    Route::get('user-follows/followers', [UserFollowController::class, 'followers']);
    Route::get('user-follows/suggestions', [UserFollowController::class, 'suggestions']);
    Route::put('user-follows/{userFollow}/configure', [UserFollowController::class, 'configure']);
    Route::get('user-follows/stats', [UserFollowController::class, 'stats']);
    
    // ========================================
    // FASE 6: COMUNIDADES TEMÁTICAS
    // ========================================
    
    // Topics (Comunidades temáticas especializadas)
    Route::get('topics/trending', [TopicController::class, 'trending']);
    Route::get('topics/stats', [TopicController::class, 'stats']);
    Route::post('topics/{topic}/join', [TopicController::class, 'join']);
    Route::delete('topics/{topic}/leave', [TopicController::class, 'leave']);
    Route::apiResource('topics', TopicController::class)->except(['update', 'destroy']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
