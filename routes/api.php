<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AppSettingController;
use App\Http\Controllers\Api\ProvinceController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\LanguageController;
use App\Http\Controllers\Api\TimezoneController;
use App\Http\Controllers\Api\MunicipalityController;
use App\Http\Controllers\Api\PointOfInterestController;

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::apiResource('app-settings', AppSettingController::class)->only(['index', 'show']);
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
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
