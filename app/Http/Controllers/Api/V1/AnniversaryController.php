<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Anniversary;
use App\Http\Resources\V1\AnniversaryResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @group Anniversaries
 *
 * APIs para la gestión de aniversarios y efemérides.
 * Permite consultar información de fechas importantes y conmemoraciones.
 */
/**
 * @OA\Tag(
 *     name="Aniversarios",
 *     description="APIs para la gestión de Aniversarios"
 * )
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
     *       "title": "Día del Libro",
     *       "slug": "dia-del-libro",
     *       "description": "Celebración mundial del libro y los derechos de autor",
     *       "month": 4,
     *       "day": 23,
     *       "year": 1616,
     *       "type": "cultural"
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
     * @urlParam idOrSlug integer|string ID o slug del aniversario. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *       "title": "Día del Libro",
     *       "slug": "dia-del-libro",
     *       "description": "Celebración mundial del libro y los derechos de autor",
     *       "month": 4,
     *       "day": 23,
     *       "year": 1616,
     *       "type": "cultural"
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
     * @urlParam month integer Mes (1-12). Example: 4
     * @urlParam day integer Día (1-31). Example: 23
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "title": "Día del Libro",
     *       "slug": "dia-del-libro",
     *       "description": "Celebración mundial del libro y los derechos de autor",
     *       "month": 4,
     *       "day": 23,
     *       "year": 1616,
     *       "type": "cultural"
     *     }
     *   ]
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\AnniversaryResource
     * @apiResourceModel App\Models\Anniversary
     */
    public function byDay($month, $day): JsonResponse
    {
        $request = new Request();
        $request->validate([
            'month' => 'required|integer|between:1,12',
            'day' => 'required|integer|between:1,31'
        ]);

        $anniversaries = Anniversary::where('month', $month)->where('day', $day)->get();
        
        return response()->json([
            'data' => AnniversaryResource::collection($anniversaries)
        ]);
    }
}
