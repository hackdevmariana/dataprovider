<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CalendarHoliday;
use App\Http\Resources\V1\CalendarHolidayResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @group Calendar Holidays
 *
 * APIs para la gestión de festivos y días festivos del calendario.
 * Permite consultar información de festivos y días especiales.
 */
/**
 * @OA\Tag(
 *     name="Fiestas del Calendario",
 *     description="APIs para la gestión de Fiestas del Calendario"
 * )
 */
class CalendarHolidayController extends Controller
{
    /**
     * Display a listing of calendar holidays
     *
     * Obtiene una lista de todos los festivos del calendario.
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Navidad",
     *       "slug": "navidad",
     *       "description": "Celebración de la Navidad",
     *       "date": "2024-12-25",
     *       "type": "national"
     *     }
     *   ]
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\CalendarHolidayResource
     * @apiResourceModel App\Models\CalendarHoliday
     */
    public function index(): JsonResponse
    {
        $holidays = CalendarHoliday::all();
        
        return response()->json([
            'data' => CalendarHolidayResource::collection($holidays)
        ]);
    }

    /**
     * Display the specified calendar holiday
     *

     * Obtiene los detalles de un festivo específico por ID o slug.
     *
     * @urlParam idOrSlug integer|string ID o slug del festivo. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *       "name": "Navidad",
     *       "slug": "navidad",
     *       "description": "Celebración de la Navidad",
     *       "date": "2024-12-25",
     *       "type": "national"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Festivo no encontrado"
     * }
     *
     * @apiResourceModel App\Models\CalendarHoliday
     */
    public function show($idOrSlug): JsonResponse
    {
        $holiday = CalendarHoliday::where('slug', $idOrSlug)
            ->orWhere('id', $idOrSlug)
            ->firstOrFail();
        
        return response()->json([
            'data' => new CalendarHolidayResource($holiday)
        ]);
    }

    /**
     * Get holidays by date
     *

     * Obtiene festivos para una fecha específica (formato YYYY-MM-DD).
     *
     * @urlParam date string Fecha en formato YYYY-MM-DD. Example: 2024-12-25
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Navidad",
     *       "slug": "navidad",
     *       "description": "Celebración de la Navidad",
     *       "date": "2024-12-25",
     *       "type": "national"
     *     }
     *   ]
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\CalendarHolidayResource
     * @apiResourceModel App\Models\CalendarHoliday
     */
    public function byDate($date): JsonResponse
    {
        $request = new Request();
        $request->validate([
            'date' => 'required|date_format:Y-m-d'
        ]);

        $holidays = CalendarHoliday::where('date', $date)->get();
        
        return response()->json([
            'data' => CalendarHolidayResource::collection($holidays)
        ]);
    }
}
