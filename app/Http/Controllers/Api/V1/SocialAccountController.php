<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Http\Resources\V1\SocialAccountResource;
use App\Http\Requests\StoreSocialAccountRequest;
use App\Http\Requests\UpdateSocialAccountRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
/**
 * @group Social Accounts
 *
 * APIs para la gestión de cuentas sociales del sistema.
 * Permite crear, consultar y gestionar cuentas de redes sociales.
 */
class SocialAccountController extends Controller
{
    /**
     * Display a listing of social accounts
     *
     * Obtiene una lista paginada de todas las cuentas sociales.
     *
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 20
     * @queryParam platform string Filtrar por plataforma (facebook, twitter, instagram). Example: twitter
     * @queryParam is_active boolean Filtrar por cuentas activas. Example: true
     * @queryParam search string Buscar por username o nombre. Example: juanperez
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "platform": "twitter",
     *       "username": "@juanperez",
     *       "display_name": "Juan Pérez",
     *       "is_active": true,
     *       "followers_count": 1250
     *     }
     *   ],
     *   "meta": {...}
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\SocialAccountResource
     * @apiResourceModel App\Models\SocialAccount
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'platform' => 'sometimes|string|in:facebook,twitter,instagram,linkedin,youtube',
            'is_active' => 'sometimes|boolean',
            'search' => 'sometimes|string|max:255'
        ]);

        $query = SocialAccount::query();

        if ($request->has('platform')) {
            $query->where('platform', $request->platform);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('username', 'like', '%' . $request->search . '%')
                  ->orWhere('display_name', 'like', '%' . $request->search . '%');
            });
        }

        $socialAccounts = $query->orderBy('platform')
                               ->orderBy('username')
                               ->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => SocialAccountResource::collection($socialAccounts),
            'meta' => [
                'current_page' => $socialAccounts->currentPage(),
                'last_page' => $socialAccounts->lastPage(),
                'per_page' => $socialAccounts->perPage(),
                'total' => $socialAccounts->total(),
            ]
        ]);
    }

    /**
     * Store a newly created social account
     *
     * Crea una nueva cuenta social en el sistema.
     *
     * @bodyParam platform string required Plataforma social (facebook, twitter, instagram). Example: twitter
     * @bodyParam username string required Nombre de usuario. Example: @juanperez
     * @bodyParam display_name string required Nombre para mostrar. Example: Juan Pérez
     * @bodyParam profile_url string URL del perfil. Example: https://twitter.com/juanperez
     * @bodyParam is_active boolean Si la cuenta está activa. Example: true
     * @bodyParam followers_count integer Número de seguidores. Example: 1250
     * @bodyParam verified boolean Si la cuenta está verificada. Example: false
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "platform": "twitter",
     *     "username": "@juanperez",
     *     "display_name": "Juan Pérez",
     *     "profile_url": "https://twitter.com/juanperez",
     *     "is_active": true,
     *     "followers_count": 1250,
     *     "created_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\SocialAccount
     * @authenticated
     */
    public function store(StoreSocialAccountRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        $socialAccount = SocialAccount::create($data);

        return response()->json([
            'data' => new SocialAccountResource($socialAccount)
        ], 201);
    }

    /**
     * Display the specified social account
     *
     * Obtiene los detalles de una cuenta social específica.
     *
     * @urlParam socialAccount integer ID de la cuenta social. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "platform": "twitter",
     *     "username": "@juanperez",
     *     "display_name": "Juan Pérez",
     *     "profile_url": "https://twitter.com/juanperez",
     *     "is_active": true,
     *     "followers_count": 1250,
     *     "verified": false
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Cuenta social no encontrada"
     * }
     *
     * @apiResourceModel App\Models\SocialAccount
     */
    public function show(SocialAccount $socialAccount): JsonResponse
    {
        return response()->json([
            'data' => new SocialAccountResource($socialAccount)
        ]);
    }

    /**
     * Update the specified social account
     *
     * Actualiza una cuenta social existente.
     *
     * @urlParam socialAccount integer ID de la cuenta social. Example: 1
     * @bodyParam platform string Plataforma social (facebook, twitter, instagram). Example: twitter
     * @bodyParam username string Nombre de usuario. Example: @juanperez
     * @bodyParam display_name string Nombre para mostrar. Example: Juan Carlos Pérez
     * @bodyParam profile_url string URL del perfil. Example: https://twitter.com/juancarlosperez
     * @bodyParam is_active boolean Si la cuenta está activa. Example: true
     * @bodyParam followers_count integer Número de seguidores. Example: 1500
     * @bodyParam verified boolean Si la cuenta está verificada. Example: true
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "platform": "twitter",
     *     "username": "@juanperez",
     *     "display_name": "Juan Carlos Pérez",
     *     "profile_url": "https://twitter.com/juancarlosperez",
     *     "is_active": true,
     *     "followers_count": 1500,
     *     "verified": true,
     *     "updated_at": "2024-01-02T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Cuenta social no encontrada"
     * }
     *
     * @response 422 {
     *   "message": "Los datos proporcionados no son válidos.",
     *   "errors": {...}
     * }
     *
     * @apiResourceModel App\Models\SocialAccount
     * @authenticated
     */
    public function update(UpdateSocialAccountRequest $request, SocialAccount $socialAccount): JsonResponse
    {
        $data = $request->validated();
        
        $socialAccount->update($data);

        return response()->json([
            'data' => new SocialAccountResource($socialAccount)
        ]);
    }

    /**
     * Remove the specified social account
     *
     * Elimina una cuenta social del sistema.
     *
     * @urlParam socialAccount integer ID de la cuenta social. Example: 1
     *
     * @response 204 {
     *   "message": "Cuenta social eliminada exitosamente"
     * }
     *
     * @response 404 {
     *   "message": "Cuenta social no encontrada"
     * }
     *
     * @authenticated
     */
    public function destroy(SocialAccount $socialAccount): JsonResponse
    {
        $socialAccount->delete();

        return response()->json([
            'message' => 'Cuenta social eliminada exitosamente'
        ], 204);
    }
}
