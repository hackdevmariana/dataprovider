<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AppSettingController;
use App\Http\Controllers\Api\ProvinceController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\LanguageController;
use App\Http\Controllers\Api\TimezoneController;




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
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
