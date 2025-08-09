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
});

// Rutas públicas sin autenticación
Route::prefix('v1')->group(function () {
    Route::apiResource('provinces', ProvinceController::class)->only(['index', 'show']);
    Route::get('/countries', [CountryController::class, 'index']);
    Route::get('/countries/{idOrSlug}', [CountryController::class, 'show']);
    Route::get('/languages', [LanguageController::class, 'index']);
    Route::get('/languages/{idOrSlug}', [LanguageController::class, 'show']);
    Route::get('/timezones', [TimezoneController::class, 'index']);
    Route::get('/timezones/{idOrName}', [TimezoneController::class, 'show']);
    Route::get('/municipalities', [MunicipalityController::class, 'index']);
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
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
