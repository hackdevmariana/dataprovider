<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\AppSettingResource;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @group App Settings
 *
 * APIs para la gestión de configuraciones globales de la aplicación.
 * Permite consultar configuraciones del sistema.
 */
class AppSettingController extends Controller
{
    /**
     * Display a listing of app settings
     *
     * Obtiene la configuración global de la aplicación.
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "app_name": "DataProvider",
     *     "app_version": "1.0.0",
     *     "maintenance_mode": false,
     *     "organization": {...}
     *   }
     * }
     *
     * @apiResourceModel App\Models\AppSetting
     * @authenticated
     */
    public function index(): JsonResponse
    {
        $settings = AppSetting::with('organization')->first(); // asumiendo que sólo hay uno
        
        return response()->json([
            'data' => new AppSettingResource($settings)
        ]);
    }

    /**
     * Display the specified app setting
     *
     * Obtiene los detalles de una configuración específica por ID.
     *
     * @urlParam id integer ID de la configuración. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *       "app_name": "DataProvider",
     *       "app_version": "1.0.0",
     *       "maintenance_mode": false,
     *       "organization": {...}
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Configuración no encontrada"
     * }
     *
     * @apiResourceModel App\Models\AppSetting
     * @authenticated
     */
    public function show($id): JsonResponse
    {
        $setting = AppSetting::with('organization')->findOrFail($id);
        
        return response()->json([
            'data' => new AppSettingResource($setting)
        ]);
    }
}
