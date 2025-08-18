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
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
