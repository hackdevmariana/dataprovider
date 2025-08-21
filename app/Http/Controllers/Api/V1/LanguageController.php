<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Http\Resources\V1\LanguageResource;
use Illuminate\Http\JsonResponse;

/**
 * @group Languages
 *
 * APIs para la gestión de idiomas y lenguas.
 * Permite consultar información de idiomas y países donde se hablan.
 */
class LanguageController extends Controller
{
    /**
     * Display a listing of languages
     *
     * Obtiene una lista de todos los idiomas disponibles con sus países.
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Español",
     *       "slug": "espanol",
     *       "iso_639_1": "es",
     *       "iso_639_2": "spa",
     *       "countries": [
     *         {
     *           "id": 1,
     *           "name": "España",
     *           "slug": "espana"
     *         }
     *       ]
     *     }
     *   ]
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\LanguageResource
     * @apiResourceModel App\Models\Language
     */
    public function index(): JsonResponse
    {
        $languages = Language::with('countries')->get();
        
        return response()->json([
            'data' => LanguageResource::collection($languages)
        ]);
    }

    /**
     * Display the specified language
     *
     * Obtiene los detalles de un idioma específico por ID o slug.
     *
     * @urlParam idOrSlug integer|string ID o slug del idioma. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *       "name": "Español",
     *       "slug": "espanol",
     *       "iso_639_1": "es",
     *       "iso_639_2": "spa",
     *       "countries": [
     *         {
     *           "id": 1,
     *           "name": "España",
     *           "slug": "espana"
     *         }
     *       ]
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Idioma no encontrado"
     * }
     *
     * @apiResourceModel App\Models\Language
     */
    public function show($idOrSlug): JsonResponse
    {
        $language = Language::with('countries')
            ->where('id', $idOrSlug)
            ->orWhere('slug', $idOrSlug)
            ->firstOrFail();

        return response()->json([
            'data' => new LanguageResource($language)
        ]);
    }
}
