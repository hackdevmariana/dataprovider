<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Anniversary;
use App\Http\Resources\V1\AnniversaryResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @group Anniversaries
 *
 * APIs para la gestión de aniversarios y efemérides del sistema.
 * Permite consultar aniversarios por fecha y obtener detalles específicos.
 */
class AnniversaryController extends Controller
{
    /**
     * Display a listing of anniversaries
     *
     * Obtiene una lista de todos los aniversarios disponibles.
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "title": "Día Mundial de la Energía",
     *       "slug": "dia-mundial-energia",
     *       "description": "Celebración anual para concienciar sobre el uso racional de la energía",
     *       "day": 14,
     *       "month": 2,
     *       "year": 1949,
     *       "category": "energy"
     *     }
     *   ]
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\AnniversaryResource
     * @apiResourceModel App\Models\Anniversary
     */
    public function index(): JsonResponse
    {
        $anniversaries = Anniversary::all();
        
        return response()->json([
            'data' => AnniversaryResource::collection($anniversaries)
        ]);
    }

    /**
     * Display the specified anniversary
     *
     * Obtiene los detalles de un aniversario específico por ID o slug.
     *
     * @urlParam idOrSlug mixed ID o slug del aniversario. Example: dia-mundial-energia
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "title": "Día Mundial de la Energía",
     *     "slug": "dia-mundial-energia",
     *     "description": "Celebración anual para concienciar sobre el uso racional de la energía",
     *     "day": 14,
     *     "month": 2,
     *     "year": 1949,
     *     "category": "energy",
     *     "source_url": "https://example.com/energia"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Aniversario no encontrado"
     * }
     *
     * @apiResourceModel App\Models\Anniversary
     */
    public function show($idOrSlug): JsonResponse
    {
        $anniversary = Anniversary::where('slug', $idOrSlug)
            ->orWhere('id', $idOrSlug)
            ->firstOrFail();
            
        return response()->json([
            'data' => new AnniversaryResource($anniversary)
        ]);
    }

    /**
     * Get anniversaries by month and day
     *
     * Obtiene aniversarios para una fecha específica (mes y día).
     *
     * @urlParam month int Mes (1-12). Example: 2
     * @urlParam day int Día (1-31). Example: 14
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "title": "Día Mundial de la Energía",
     *       "slug": "dia-mundial-energia",
     *       "description": "Celebración anual para concienciar sobre el uso racional de la energía",
     *       "day": 14,
     *       "month": 2,
     *       "year": 1949
     *     }
     *   ]
     * }
     *
     * @response 400 {
     *   "message": "Fecha inválida proporcionada"
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\AnniversaryResource
     * @apiResourceModel App\Models\Anniversary
     */
    public function byDay($month, $day): JsonResponse
    {
        // Validar que mes y día sean válidos
        if (!is_numeric($month) || !is_numeric($day) || 
            $month < 1 || $month > 12 || $day < 1 || $day > 31) {
            return response()->json([
                'message' => 'Fecha inválida proporcionada'
            ], 400);
        }

        $anniversaries = Anniversary::where('month', $month)
            ->where('day', $day)
            ->get();
            
        return response()->json([
            'data' => AnniversaryResource::collection($anniversaries)
        ]);
    }
}
