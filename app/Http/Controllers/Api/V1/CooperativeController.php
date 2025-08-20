<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CooperativeResource;
use App\Models\Cooperative;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @group Cooperatives
 *
 * APIs para la gestión de cooperativas energéticas y de otros tipos.
 * Permite crear, consultar, actualizar y gestionar cooperativas.
 */
class CooperativeController extends Controller
{
    /**
     * Display a listing of cooperatives
     *
     * Obtiene una lista de cooperativas con opciones de paginación.
     *
     * @queryParam per_page int Cantidad por página (máx 100). Example: 15
     * @queryParam page int Número de página. Example: 1
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Som Energia",
     *       "slug": "som-energia",
     *       "cooperative_type": "energy",
     *       "scope": "national",
     *       "municipality": {
     *         "id": 1,
     *         "name": "Girona"
     *       }
     *     }
     *   ],
     *   "meta": {
     *     "current_page": 1,
     *     "last_page": 5,
     *     "per_page": 15,
     *     "total": 75
     *   }
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\CooperativeResource
     * @apiResourceModel App\Models\Cooperative
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'per_page' => 'sometimes|integer|min:1|max:100',
            'page' => 'sometimes|integer|min:1'
        ]);

        $perPage = min($request->get('per_page', 15), 100);
        $cooperatives = Cooperative::with(['municipality', 'image'])
            ->paginate($perPage);

        return response()->json([
            'data' => CooperativeResource::collection($cooperatives),
            'meta' => [
                'current_page' => $cooperatives->currentPage(),
                'last_page' => $cooperatives->lastPage(),
                'per_page' => $cooperatives->perPage(),
                'total' => $cooperatives->total(),
            ]
        ]);
    }

    /**
     * Store a newly created cooperative
     *
     * Crea una nueva cooperativa con los datos validados.
     *
     * @bodyParam name string required Nombre de la cooperativa. Example: Som Energia
     * @bodyParam slug string required Slug único de la cooperativa. Example: som-energia
     * @bodyParam legal_name string Nombre legal de la cooperativa. Example: Som Energia SCCL
     * @bodyParam cooperative_type string required Tipo de cooperativa (energy, housing, agriculture, etc). Example: energy
     * @bodyParam scope string required Alcance (local, regional, national). Example: national
     * @bodyParam nif string NIF/CIF de la cooperativa. Example: F12345678
     * @bodyParam founded_at date Fecha de fundación. Example: 2010-01-01
     * @bodyParam phone string required Teléfono de contacto. Example: +34 972 123 456
     * @bodyParam email string required Email de contacto. Example: info@somenergia.coop
     * @bodyParam website string required Sitio web. Example: https://somenergia.coop
     * @bodyParam logo_url string URL del logo. Example: https://example.com/logo.png
     * @bodyParam municipality_id int required ID del municipio. Example: 1
     * @bodyParam address string required Dirección física. Example: Carrer de la Pau 1
     * @bodyParam latitude number Latitud geográfica. Example: 41.9833
     * @bodyParam longitude number Longitud geográfica. Example: 2.8167
     * @bodyParam description string Descripción de la cooperativa. Example: Cooperativa de energía renovable
     * @bodyParam number_of_members int Número de miembros. Example: 1000
     * @bodyParam main_activity string required Actividad principal. Example: Producción de energía renovable
     * @bodyParam is_open_to_new_members boolean Abierta a nuevos miembros. Example: true
     * @bodyParam source string Fuente de los datos. Example: api
     * @bodyParam has_energy_market_access boolean Acceso al mercado energético. Example: true
     * @bodyParam legal_form string Forma legal. Example: SCCL
     * @bodyParam statutes_url string URL de los estatutos. Example: https://example.com/estatutos.pdf
     * @bodyParam accepts_new_installations boolean Acepta nuevas instalaciones. Example: true
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "name": "Som Energia",
     *     "slug": "som-energia",
     *     "cooperative_type": "energy",
     *     "scope": "national"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {
     *     "name": ["El campo nombre es obligatorio."]
     *   }
     * }
     *
     * @apiResourceModel App\Models\Cooperative
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:cooperatives,slug',
            'legal_name' => 'nullable|string|max:255',
            'cooperative_type' => 'required|string|in:energy,housing,agriculture,etc',
            'scope' => 'required|string|in:local,regional,national',
            'nif' => 'nullable|string|max:20',
            'founded_at' => 'nullable|date',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'website' => 'required|url|max:255',
            'logo_url' => 'nullable|url|max:255',
            'municipality_id' => 'required|integer|exists:municipalities,id',
            'address' => 'required|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'description' => 'nullable|string|max:2000',
            'number_of_members' => 'nullable|integer|min:1',
            'main_activity' => 'required|string|max:255',
            'is_open_to_new_members' => 'sometimes|boolean',
            'source' => 'nullable|string|max:100',
            'has_energy_market_access' => 'sometimes|boolean',
            'legal_form' => 'nullable|string|max:100',
            'statutes_url' => 'nullable|url|max:255',
            'accepts_new_installations' => 'sometimes|boolean',
        ]);

        $validated['source'] = $validated['source'] ?? 'api';

        $cooperative = Cooperative::create($validated);
        $cooperative->load(['municipality', 'image']);

        return response()->json([
            'data' => new CooperativeResource($cooperative)
        ], 201);
    }

    /**
     * Display the specified cooperative
     *
     * Obtiene los detalles de una cooperativa específica por ID o slug.
     *
     * @urlParam idOrSlug mixed ID o slug de la cooperativa. Example: som-energia
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Som Energia",
     *     "slug": "som-energia",
     *     "cooperative_type": "energy",
     *     "scope": "national",
     *     "description": "Cooperativa de energía renovable",
     *     "municipality": {
     *       "id": 1,
     *       "name": "Girona"
     *     }
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Cooperativa no encontrada"
     * }
     *
     * @apiResourceModel App\Models\Cooperative
     */
    public function show($idOrSlug): JsonResponse
    {
        $cooperative = Cooperative::with(['municipality', 'image', 'userMemberships', 'users'])
            ->where('id', $idOrSlug)
            ->orWhere('slug', $idOrSlug)
            ->firstOrFail();

        return response()->json([
            'data' => new CooperativeResource($cooperative)
        ]);
    }

    /**
     * Update the specified cooperative
     *
     * Actualiza una cooperativa existente con los datos proporcionados.
     *
     * @urlParam cooperative int ID de la cooperativa. Example: 1
     * @bodyParam name string Nombre de la cooperativa. Example: Som Energia
     * @bodyParam slug string Slug único de la cooperativa. Example: som-energia
     * @bodyParam legal_name string Nombre legal de la cooperativa. Example: Som Energia SCCL
     * @bodyParam cooperative_type string Tipo de cooperativa (energy, housing, agriculture, etc). Example: energy
     * @bodyParam scope string Alcance (local, regional, national). Example: national
     * @bodyParam nif string NIF/CIF de la cooperativa. Example: F12345678
     * @bodyParam founded_at date Fecha de fundación. Example: 2010-01-01
     * @bodyParam phone string Teléfono de contacto. Example: +34 972 123 456
     * @bodyParam email string Email de contacto. Example: info@somenergia.coop
     * @bodyParam website string Sitio web. Example: https://somenergia.coop
     * @bodyParam description string Descripción de la cooperativa. Example: Cooperativa de energía renovable
     * @bodyParam is_open_to_new_members boolean Abierta a nuevos miembros. Example: true
     * @bodyParam accepts_new_installations boolean Acepta nuevas instalaciones. Example: true
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Som Energia",
     *     "slug": "som-energia",
     *     "cooperative_type": "energy"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Cooperativa no encontrada"
     * }
     *
     * @apiResourceModel App\Models\Cooperative
     */
    public function update(Request $request, Cooperative $cooperative): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:cooperatives,slug,' . $cooperative->id,
            'legal_name' => 'nullable|string|max:255',
            'cooperative_type' => 'sometimes|string|in:energy,housing,agriculture,etc',
            'scope' => 'sometimes|string|in:local,regional,national',
            'nif' => 'nullable|string|max:20',
            'founded_at' => 'nullable|date',
            'phone' => 'sometimes|string|max:20',
            'email' => 'sometimes|email|max:255',
            'website' => 'sometimes|url|max:255',
            'description' => 'nullable|string|max:2000',
            'is_open_to_new_members' => 'sometimes|boolean',
            'accepts_new_installations' => 'sometimes|boolean',
        ]);

        $cooperative->update($validated);
        $cooperative->load(['municipality', 'image']);

        return response()->json([
            'data' => new CooperativeResource($cooperative)
        ]);
    }

    /**
     * Remove the specified cooperative
     *
     * Elimina una cooperativa específica del sistema.
     *
     * @urlParam cooperative int ID de la cooperativa. Example: 1
     *
     * @response 204 {
     *   "message": "Cooperativa eliminada exitosamente"
     * }
     *
     * @response 404 {
     *   "message": "Cooperativa no encontrada"
     * }
     */
    public function destroy(Cooperative $cooperative): JsonResponse
    {
        $cooperative->delete();
        
        return response()->json([
            'message' => 'Cooperativa eliminada exitosamente'
        ], 204);
    }

    /**
     * Filter cooperatives by type
     *
     * Filtra cooperativas por tipo específico.
     *
     * @urlParam type string Tipo de cooperativa. Example: energy
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Som Energia",
     *       "cooperative_type": "energy"
     *     }
     *   ]
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\CooperativeResource
     * @apiResourceModel App\Models\Cooperative
     */
    public function filterByType($type): JsonResponse
    {
        $cooperatives = Cooperative::with(['municipality', 'image'])
            ->ofType($type)
            ->paginate(15);

        return response()->json([
            'data' => CooperativeResource::collection($cooperatives)
        ]);
    }

    /**
     * Get energy cooperatives
     *
     * Obtiene una lista de cooperativas energéticas.
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Som Energia",
     *       "cooperative_type": "energy"
     *     }
     *   ]
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\CooperativeResource
     * @apiResourceModel App\Models\Cooperative
     */
    public function energy(): JsonResponse
    {
        $cooperatives = Cooperative::with(['municipality', 'image'])
            ->energy()
            ->paginate(15);

        return response()->json([
            'data' => CooperativeResource::collection($cooperatives)
        ]);
    }

    /**
     * Get cooperatives open to new members
     *
     * Obtiene una lista de cooperativas abiertas a nuevos miembros.
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Som Energia",
     *       "is_open_to_new_members": true
     *     }
     *   ]
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\CooperativeResource
     * @apiResourceModel App\Models\Cooperative
     */
    public function openToMembers(): JsonResponse
    {
        $cooperatives = Cooperative::with(['municipality', 'image'])
            ->openToNewMembers()
            ->paginate(15);

        return response()->json([
            'data' => CooperativeResource::collection($cooperatives)
        ]);
    }

    /**
     * Search cooperatives
     *
     * Busca cooperativas por nombre, nombre legal, descripción o actividad principal.
     *
     * @queryParam q string required Término de búsqueda. Example: energia
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 15
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Som Energia",
     *       "main_activity": "Producción de energía renovable"
     *     }
     *   ]
     * }
     *
     * @response 400 {
     *   "error": "El parámetro de consulta q es obligatorio"
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\CooperativeResource
     * @apiResourceModel App\Models\Cooperative
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|max:255',
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100'
        ]);

        $query = $request->get('q');
        $perPage = min($request->get('per_page', 15), 100);

        $cooperatives = Cooperative::with(['municipality', 'image'])
            ->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('legal_name', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%")
                  ->orWhere('main_activity', 'LIKE', "%{$query}%");
            })
            ->paginate($perPage);

        return response()->json([
            'data' => CooperativeResource::collection($cooperatives),
            'meta' => [
                'current_page' => $cooperatives->currentPage(),
                'last_page' => $cooperatives->lastPage(),
                'per_page' => $cooperatives->perPage(),
                'total' => $cooperatives->total(),
            ]
        ]);
    }

    /**
     * Get cooperative statistics
     *
     * Obtiene estadísticas generales sobre las cooperativas del sistema.
     *
     * @response 200 {
     *   "data": {
     *     "total_cooperatives": 75,
     *     "energy_cooperatives": 25,
     *     "open_to_members": 60,
     *     "by_type": [
     *       {
     *         "cooperative_type": "energy",
     *         "count": 25
     *       }
     *     ],
     *     "by_scope": [
     *       {
     *         "scope": "national",
     *         "count": 30
     *       }
     *     ],
     *     "total_members": 15000,
     *     "average_members": 200.0
     *   }
     * }
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_cooperatives' => Cooperative::count(),
            'energy_cooperatives' => Cooperative::energy()->count(),
            'open_to_members' => Cooperative::openToNewMembers()->count(),
            'by_type' => Cooperative::selectRaw('cooperative_type, COUNT(*) as count')
                ->groupBy('cooperative_type')
                ->get(),
            'by_scope' => Cooperative::selectRaw('scope, COUNT(*) as count')
                ->groupBy('scope')
                ->get(),
            'total_members' => Cooperative::sum('number_of_members'),
            'average_members' => round(Cooperative::avg('number_of_members'), 1),
        ];

        return response()->json(['data' => $stats]);
    }
}