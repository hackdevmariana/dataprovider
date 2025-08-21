<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CalendarHolidayLocation;
use App\Http\Resources\V1\CalendarHolidayLocationResource;
use App\Http\Requests\StoreCalendarHolidayLocationRequest;
use App\Http\Requests\UpdateCalendarHolidayLocationRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @group Calendar Holiday Locations
 *
 * APIs para la gestión de ubicaciones de festivos del calendario.
 * Permite crear, consultar y gestionar ubicaciones específicas para festivos.
 */
class CalendarHolidayLocationController extends Controller
{
    /**
     * Display a listing of calendar holiday locations
     *
     * Obtiene una lista paginada de todas las ubicaciones de festivos.
     *
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 20
     * @queryParam country string Filtrar por país. Example: Spain
     * @queryParam region string Filtrar por región. Example: Madrid
     * @queryParam is_active boolean Filtrar por ubicaciones activas. Example: true
     * @queryParam search string Buscar por nombre o descripción. Example: madrid
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Madrid",
     *       "country": "Spain",
     *       "region": "Madrid",
     *       "is_active": true,
     *       "timezone": "Europe/Madrid"
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\CalendarHolidayLocationResource
     * @apiResourceModel App\Models\CalendarHolidayLocation
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'country' => 'sometimes|string|max:255',
            'region' => 'sometimes|string|max:255',
            'is_active' => 'sometimes|boolean',
            'search' => 'sometimes|string|max:255'
        ]);

        $query = CalendarHolidayLocation::query();

        if ($request->has('country')) {
            $query->where('country', $request->country);
        }

        if ($request->has('region')) {
            $query->where('region', $request->region);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $locations = $query->orderBy('country')
                          ->orderBy('region')
                          ->orderBy('name')
                          ->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => CalendarHolidayLocationResource::collection($locations),
            'meta' => [
                'current_page' => $locations->currentPage(),
                'last_page' => $locations->lastPage(),
                'per_page' => $locations->perPage(),
                'total' => $locations->total(),
            ]
        ]);
    }

    /**
     * Store a newly created calendar holiday location
     *
     * Crea una nueva ubicación de festivo en el sistema.
     *
     * @bodyParam name string required Nombre de la ubicación. Example: Madrid
     * @bodyParam country string required País. Example: Spain
     * @bodyParam region string Región o provincia. Example: Madrid
     * @bodyParam description string Descripción de la ubicación. Example: Capital de España
     * @bodyParam timezone string Zona horaria. Example: Europe/Madrid
     * @bodyParam is_active boolean Si la ubicación está activa. Example: true
     * @bodyParam metadata object Metadatos adicionales (JSON). Example: {"population": "3.2M", "capital": true}
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *       "name": "Madrid",
     *       "country": "Spain",
     *       "region": "Madrid",
     *       "description": "Capital de España",
     *       "timezone": "Europe/Madrid",
     *       "is_active": true,
     *       "created_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\CalendarHolidayLocation
     * @authenticated
     */
    public function store(StoreCalendarHolidayLocationRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        $location = CalendarHolidayLocation::create($data);

        return response()->json([
            'data' => new CalendarHolidayLocationResource($location)
        ], 201);
    }

    /**
     * Display the specified calendar holiday location
     *
     * Obtiene los detalles de una ubicación de festivo específica.
     *
     * @urlParam calendarHolidayLocation integer ID de la ubicación. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *       "name": "Madrid",
     *       "country": "Spain",
     *       "region": "Madrid",
     *       "description": "Capital de España",
     *       "timezone": "Europe/Madrid",
     *       "is_active": true,
     *       "metadata": {
     *         "population": "3.2M",
     *         "capital": true
     *       }
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Ubicación de festivo no encontrada"
     * }
     *
     * @apiResourceModel App\Models\CalendarHolidayLocation
     */
    public function show(CalendarHolidayLocation $calendarHolidayLocation): JsonResponse
    {
        return response()->json([
            'data' => new CalendarHolidayLocationResource($calendarHolidayLocation)
        ]);
    }

    /**
     * Update the specified calendar holiday location
     *
     * Actualiza una ubicación de festivo existente.
     *
     * @urlParam calendarHolidayLocation integer ID de la ubicación. Example: 1
     * @bodyParam name string Nombre de la ubicación. Example: Madrid Capital
     * @bodyParam country string País. Example: Spain
     * @bodyParam region string Región o provincia. Example: Madrid
     * @bodyParam description string Descripción de la ubicación. Example: Capital de España y sede del gobierno
     * @bodyParam timezone string Zona horaria. Example: Europe/Madrid
     * @bodyParam is_active boolean Si la ubicación está activa. Example: true
     * @bodyParam metadata object Metadatos adicionales (JSON). Example: {"population": "3.2M", "capital": true, "government": true}
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *       "name": "Madrid Capital",
     *       "country": "Spain",
     *       "region": "Madrid",
     *       "description": "Capital de España y sede del gobierno",
     *       "timezone": "Europe/Madrid",
     *       "is_active": true,
     *       "updated_at": "2024-01-02T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Ubicación de festivo no encontrada"
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\CalendarHolidayLocation
     * @authenticated
     */
    public function update(UpdateCalendarHolidayLocationRequest $request, CalendarHolidayLocation $calendarHolidayLocation): JsonResponse
    {
        $data = $request->validated();
        
        $calendarHolidayLocation->update($data);

        return response()->json([
            'data' => new CalendarHolidayLocationResource($calendarHolidayLocation)
        ]);
    }

    /**
     * Remove the specified calendar holiday location
     *
     * Elimina una ubicación de festivo del sistema.
     *
     * @urlParam calendarHolidayLocation integer ID de la ubicación. Example: 1
     *
     * @response 204 {
     *   "message": "Ubicación de festivo eliminada exitosamente"
     * }
     *
     * @response 404 {
     *   "message": "Ubicación de festivo no encontrada"
     * }
     *
     * @authenticated
     */
    public function destroy(CalendarHolidayLocation $calendarHolidayLocation): JsonResponse
    {
        $calendarHolidayLocation->delete();

        return response()->json([
            'message' => 'Ubicación de festivo eliminada exitosamente'
        ], 204);
    }
}
