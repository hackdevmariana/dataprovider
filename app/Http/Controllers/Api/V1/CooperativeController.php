<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CooperativeResource;
use App\Models\Cooperative;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * @group Cooperatives
 *
 * APIs para la gestión de cooperativas energéticas y de otros tipos.
 * Permite crear, consultar y gestionar cooperativas y sus miembros.
 */
class CooperativeController extends Controller
{
    /**
     * Display a listing of cooperatives
     *
     * Obtiene una lista de cooperativas con opciones de filtrado.
     *
     * @queryParam cooperative_type string Filtrar por tipo de cooperativa (energy, housing, agriculture, etc). Example: energy
     * @queryParam scope string Filtrar por alcance (local, regional, national). Example: local
     * @queryParam municipality_id int ID del municipio para filtrar. Example: 1
     * @queryParam is_open_to_new_members boolean Filtrar por cooperativas que aceptan nuevos miembros. Example: true
     * @queryParam has_energy_market_access boolean Filtrar por acceso al mercado energético. Example: true
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 15
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Som Energia",
     *       "slug": "som-energia",
     *       "cooperative_type": "energy",
     *       "scope": "national",
     *       "municipality": {...},
     *       "image": {...}
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\CooperativeResource
     * @apiResourceModel App\Models\Cooperative
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'cooperative_type' => 'sometimes|string|in:energy,housing,agriculture,etc',
            'scope' => 'sometimes|string|in:local,regional,national',
            'municipality_id' => 'sometimes|integer|exists:municipalities,id',
            'is_open_to_new_members' => 'sometimes|boolean',
            'has_energy_market_access' => 'sometimes|boolean',
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100'
        ]);

        $query = Cooperative::with(['municipality', 'image']);

        if ($request->has('cooperative_type')) {
            $query->where('cooperative_type', $request->cooperative_type);
        }

        if ($request->has('scope')) {
            $query->where('scope', $request->scope);
        }

        if ($request->has('municipality_id')) {
            $query->where('municipality_id', $request->municipality_id);
        }

        if ($request->has('is_open_to_new_members')) {
            $query->where('is_open_to_new_members', $request->is_open_to_new_members);
        }

        if ($request->has('has_energy_market_access')) {
            $query->where('has_energy_market_access', $request->has_energy_market_access);
        }

        $perPage = min($request->get('per_page', 15), 100);
        $cooperatives = $query->orderBy('name')->paginate($perPage);

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
     * Crea una nueva cooperativa.
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
     * @bodyParam website string required Sitio web. Example: https://www.somenergia.coop
     * @bodyParam logo_url string URL del logo. Example: https://example.com/logo.png
     * @bodyParam municipality_id integer required ID del municipio. Example: 1
     * @bodyParam address string required Dirección física. Example: Carrer de la Pau, 123
     * @bodyParam latitude number Latitud geográfica. Example: 41.3851
     * @bodyParam longitude number Longitud geográfica. Example: 2.1734
     * @bodyParam description string Descripción de la cooperativa. Example: Cooperativa de energía renovable
     * @bodyParam number_of_members integer Número de miembros. Example: 1000
     * @bodyParam main_activity string required Actividad principal. Example: Producción y comercialización de energía renovable
     * @bodyParam is_open_to_new_members boolean Abierta a nuevos miembros. Example: true
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
     *     "scope": "national",
     *     "municipality": {...},
     *     "image": {...}
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
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
            'is_open_to_new_members' => 'boolean',
            'source' => 'nullable|string|max:100',
            'has_energy_market_access' => 'boolean',
            'legal_form' => 'nullable|string|max:100',
            'statutes_url' => 'nullable|url|max:255',
            'accepts_new_installations' => 'boolean',
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
     * @urlParam idOrSlug integer|string ID o slug de la cooperativa. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Som Energia",
     *     "slug": "som-energia",
     *     "cooperative_type": "energy",
     *     "scope": "national",
     *     "municipality": {...},
     *     "image": {...},
     *     "user_memberships": [...],
     *     "users": [...]
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
     * Actualiza una cooperativa existente.
     *
     * @urlParam id integer ID de la cooperativa. Example: 1
     * @bodyParam name string Nombre de la cooperativa. Example: Som Energia
     * @bodyParam slug string Slug único de la cooperativa. Example: som-energia
     * @bodyParam legal_name string Nombre legal de la cooperativa. Example: Som Energia SCCL
     * @bodyParam cooperative_type string Tipo de cooperativa (energy, housing, agriculture, etc). Example: energy
     * @bodyParam scope string Alcance (local, regional, national). Example: national
     * @bodyParam nif string NIF/CIF de la cooperativa. Example: F12345678
     * @bodyParam founded_at date Fecha de fundación. Example: 2010-01-01
     * @bodyParam phone string Teléfono de contacto. Example: +34 972 123 456
     * @bodyParam email string Email de contacto. Example: info@somenergia.coop
     * @bodyParam website string Sitio web. Example: https://www.somenergia.coop
     * @bodyParam logo_url string URL del logo. Example: https://example.com/logo.png
     * @bodyParam municipality_id integer ID del municipio. Example: 1
     * @bodyParam address string Dirección física. Example: Carrer de la Pau, 123
     * @bodyParam latitude number Latitud geográfica. Example: 41.3851
     * @bodyParam longitude number Longitud geográfica. Example: 2.1734
     * @bodyParam description string Descripción de la cooperativa. Example: Cooperativa de energía renovable
     * @bodyParam number_of_members integer Número de miembros. Example: 1000
     * @bodyParam main_activity string Actividad principal. Example: Producción y comercialización de energía renovable
     * @bodyParam is_open_to_new_members boolean Abierta a nuevos miembros. Example: true
     * @bodyParam has_energy_market_access boolean Acceso al mercado energético. Example: true
     * @bodyParam legal_form string Forma legal. Example: SCCL
     * @bodyParam statutes_url string URL de los estatutos. Example: https://example.com/estatutos.pdf
     * @bodyParam accepts_new_installations boolean Acepta nuevas instalaciones. Example: true
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Som Energia",
     *     "slug": "som-energia",
     *     "cooperative_type": "energy",
     *     "scope": "national"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Cooperativa no encontrada"
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\Cooperative
     */
    public function update(Request $request, $id): JsonResponse
    {
        $cooperative = Cooperative::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:cooperatives,slug,' . $id,
            'legal_name' => 'nullable|string|max:255',
            'cooperative_type' => 'sometimes|string|in:energy,housing,agriculture,etc',
            'scope' => 'sometimes|string|in:local,regional,national',
            'nif' => 'nullable|string|max:20',
            'founded_at' => 'nullable|date',
            'phone' => 'sometimes|string|max:20',
            'email' => 'sometimes|email|max:255',
            'website' => 'sometimes|url|max:255',
            'logo_url' => 'nullable|url|max:255',
            'municipality_id' => 'sometimes|integer|exists:municipalities,id',
            'address' => 'sometimes|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'description' => 'nullable|string|max:2000',
            'number_of_members' => 'nullable|integer|min:1',
            'main_activity' => 'sometimes|string|max:255',
            'is_open_to_new_members' => 'boolean',
            'has_energy_market_access' => 'boolean',
            'legal_form' => 'nullable|string|max:100',
            'statutes_url' => 'nullable|url|max:255',
            'accepts_new_installations' => 'boolean',
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
     * Elimina una cooperativa.
     *
     * @urlParam id integer ID de la cooperativa. Example: 1
     *
     * @response 204 {
     *   "message": "Cooperativa eliminada exitosamente"
     * }
     *
     * @response 404 {
     *   "message": "Cooperativa no encontrada"
     * }
     */
    public function destroy($id): JsonResponse
    {
        $cooperative = Cooperative::findOrFail($id);
        $cooperative->delete();

        return response()->json([
            'message' => 'Cooperativa eliminada exitosamente'
        ], 204);
    }

    /**
     * Get cooperative members
     *
     * Obtiene la lista de miembros de una cooperativa.
     *
     * @urlParam idOrSlug integer|string ID o slug de la cooperativa. Example: 1
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 15
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Juan Pérez",
     *       "email": "juan@example.com",
     *       "membership_date": "2020-01-01"
     *     }
     *   ],
     *   "meta": {...}
     * }
     */
    public function members(Request $request, $idOrSlug): JsonResponse
    {
        $cooperative = Cooperative::where('id', $idOrSlug)
            ->orWhere('slug', $idOrSlug)
            ->firstOrFail();

        $perPage = min($request->get('per_page', 15), 100);
        $members = $cooperative->users()->paginate($perPage);

        return response()->json([
            'data' => $members->items(),
            'meta' => [
                'current_page' => $members->currentPage(),
                'last_page' => $members->lastPage(),
                'per_page' => $members->perPage(),
                'total' => $members->total(),
            ]
        ]);
    }

    /**
     * Join cooperative
     *
     * Permite a un usuario unirse a una cooperativa.
     *
     * @urlParam idOrSlug integer|string ID o slug de la cooperativa. Example: 1
     *
     * @response 200 {
     *   "message": "Te has unido exitosamente a la cooperativa",
     *   "cooperative": {...}
     * }
     *
     * @response 400 {
     *   "message": "Ya eres miembro de esta cooperativa"
     * }
     *
     * @response 404 {
     *   "message": "Cooperativa no encontrada"
     * }
     *
     * @authenticated
     */
    public function join($idOrSlug): JsonResponse
    {
        $cooperative = Cooperative::where('id', $idOrSlug)
            ->orWhere('slug', $idOrSlug)
            ->firstOrFail();

        $userId = Auth::guard('sanctum')->user()->id;

        if ($cooperative->users()->where('user_id', $userId)->exists()) {
            return response()->json([
                'message' => 'Ya eres miembro de esta cooperativa'
            ], 400);
        }

        $cooperative->users()->attach($userId, [
            'joined_at' => now(),
            'status' => 'active'
        ]);

        return response()->json([
            'message' => 'Te has unido exitosamente a la cooperativa',
            'cooperative' => new CooperativeResource($cooperative)
        ]);
    }

    /**
     * Leave cooperative
     *
     * Permite a un usuario salir de una cooperativa.
     *
     * @urlParam idOrSlug integer|string ID o slug de la cooperativa. Example: 1
     *
     * @response 200 {
     *   "message": "Has salido exitosamente de la cooperativa"
     * }
     *
     * @response 400 {
     *   "message": "No eres miembro de esta cooperativa"
     * }
     *
     * @response 404 {
     *   "message": "Cooperativa no encontrada"
     * }
     *
     * @authenticated
     */
    public function leave($idOrSlug): JsonResponse
    {
        $cooperative = Cooperative::where('id', $idOrSlug)
            ->orWhere('slug', $idOrSlug)
            ->firstOrFail();

        $userId = Auth::guard('sanctum')->user()->id;

        if (!$cooperative->users()->where('user_id', $userId)->exists()) {
            return response()->json([
                'message' => 'No eres miembro de esta cooperativa'
            ], 400);
        }

        $cooperative->users()->detach($userId);

        return response()->json([
            'message' => 'Has salido exitosamente de la cooperativa'
        ]);
    }
}
