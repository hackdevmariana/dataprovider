<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CooperativeUserMember;
use App\Http\Resources\V1\CooperativeUserMemberResource;
use App\Http\Requests\StoreCooperativeUserMemberRequest;
use App\Http\Requests\UpdateCooperativeUserMemberRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
/**
 * @group Cooperative User Members
 *
 * APIs para la gestión de miembros de cooperativas del sistema.
 * Permite crear, consultar y gestionar la relación entre usuarios y cooperativas.
 */
class CooperativeUserMemberController extends Controller
{
    /**
     * Display a listing of cooperative user members
     *
     * Obtiene una lista paginada de todos los miembros de cooperativas.
     *
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 20
     * @queryParam cooperative_id integer Filtrar por cooperativa. Example: 1
     * @queryParam user_id integer Filtrar por usuario. Example: 1
     * @queryParam role string Filtrar por rol (member, admin, manager). Example: member
     * @queryParam status string Filtrar por estado (active, inactive, pending). Example: active
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "cooperative_id": 1,
     *       "user_id": 1,
     *       "role": "member",
     *       "status": "active",
     *       "joined_at": "2024-01-01T00:00:00.000000Z",
     *       "cooperative": {...},
     *       "user": {...}
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\CooperativeUserMemberResource
     * @apiResourceModel App\Models\CooperativeUserMember
     * @authenticated
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'cooperative_id' => 'sometimes|integer|exists:cooperatives,id',
            'user_id' => 'sometimes|integer|exists:users,id',
            'role' => 'sometimes|string|in:member,admin,manager',
            'status' => 'sometimes|string|in:active,inactive,pending'
        ]);

        $query = CooperativeUserMember::with(['cooperative', 'user']);

        if ($request->has('cooperative_id')) {
            $query->where('cooperative_id', $request->cooperative_id);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $members = $query->orderBy('joined_at', 'desc')
                        ->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => CooperativeUserMemberResource::collection($members),
            'meta' => [
                'current_page' => $members->currentPage(),
                'last_page' => $members->lastPage(),
                'per_page' => $members->perPage(),
                'total' => $members->total(),
            ]
        ]);
    }

    /**
     * Store a newly created cooperative user member
     *
     * Crea un nuevo miembro de cooperativa en el sistema.
     *
     * @bodyParam cooperative_id integer required ID de la cooperativa. Example: 1
     * @bodyParam user_id integer required ID del usuario. Example: 1
     * @bodyParam role string required Rol del usuario (member, admin, manager). Example: member
     * @bodyParam status string Estado del miembro (active, inactive, pending). Example: active
     * @bodyParam joined_at string Fecha de ingreso (ISO 8601). Example: 2024-01-01T00:00:00Z
     * @bodyParam metadata object Metadatos adicionales (JSON). Example: {"contribution": "monthly", "amount": 100}
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *       "cooperative_id": 1,
     *       "user_id": 1,
     *       "role": "member",
     *       "status": "active",
     *       "joined_at": "2024-01-01T00:00:00.000000Z",
     *       "created_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\CooperativeUserMember
     * @authenticated
     */
    public function store(StoreCooperativeUserMemberRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        if (!isset($data['joined_at'])) {
            $data['joined_at'] = now();
        }
        
        $member = CooperativeUserMember::create($data);

        return response()->json([
            'data' => new CooperativeUserMemberResource($member->load(['cooperative', 'user']))
        ], 201);
    }

    /**
     * Display the specified cooperative user member
     *
     * Obtiene los detalles de un miembro de cooperativa específico.
     *
     * @urlParam cooperativeUserMember integer ID del miembro. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *       "cooperative_id": 1,
     *       "user_id": 1,
     *       "role": "member",
     *       "status": "active",
     *       "joined_at": "2024-01-01T00:00:00.000000Z",
     *       "metadata": {
     *         "contribution": "monthly",
     *         "amount": 100
     *       },
     *       "cooperative": {...},
     *       "user": {...}
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Miembro de cooperativa no encontrado"
     * }
     *
     * @apiResourceModel App\Models\CooperativeUserMember
     * @authenticated
     */
    public function show(CooperativeUserMember $cooperativeUserMember): JsonResponse
    {
        return response()->json([
            'data' => new CooperativeUserMemberResource($cooperativeUserMember->load(['cooperative', 'user']))
        ]);
    }

    /**
     * Update the specified cooperative user member
     *
     * Actualiza un miembro de cooperativa existente.
     *
     * @urlParam cooperativeUserMember integer ID del miembro. Example: 1
     * @bodyParam role string Rol del usuario (member, admin, manager). Example: admin
     * @bodyParam status string Estado del miembro (active, inactive, pending). Example: active
     * @bodyParam joined_at string Fecha de ingreso (ISO 8601). Example: 2024-01-01T00:00:00Z
     * @bodyParam metadata object Metadatos adicionales (JSON). Example: {"contribution": "monthly", "amount": 150, "seniority": "2 years"}
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *       "role": "admin",
     *       "status": "active",
     *       "metadata": {
     *         "contribution": "monthly",
     *         "amount": 150,
     *         "seniority": "2 years"
     *       },
     *       "updated_at": "2024-01-02T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Miembro de cooperativa no encontrado"
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\CooperativeUserMember
     * @authenticated
     */
    public function update(UpdateCooperativeUserMemberRequest $request, CooperativeUserMember $cooperativeUserMember): JsonResponse
    {
        $data = $request->validated();
        
        $cooperativeUserMember->update($data);

        return response()->json([
            'data' => new CooperativeUserMemberResource($cooperativeUserMember->load(['cooperative', 'user']))
        ]);
    }

    /**
     * Remove the specified cooperative user member
     *
     * Elimina un miembro de cooperativa del sistema.
     *
     * @urlParam cooperativeUserMember integer ID del miembro. Example: 1
     *
     * @response 204 {
     *   "message": "Miembro de cooperativa eliminado exitosamente"
     * }
     *
     * @response 404 {
     *   "message": "Miembro de cooperativa no encontrado"
     * }
     *
     * @authenticated
     */
    public function destroy(CooperativeUserMember $cooperativeUserMember): JsonResponse
    {
        $cooperativeUserMember->delete();

        return response()->json([
            'message' => 'Miembro de cooperativa eliminado exitosamente'
        ], 204);
    }
}
