<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\EnergyCompany;
use App\Http\Resources\V1\EnergyCompanyResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @group Energy Companies
 *
 * APIs para la gestión de empresas energéticas (comercializadoras, distribuidoras, cooperativas).
 * Permite consultar, filtrar y buscar empresas del sector energético.
 */
class EnergyCompanyController extends Controller
{
    /**
     * Display a listing of energy companies
     *
     * Obtiene una lista de empresas energéticas con opciones de filtrado.
     *
     * @queryParam company_type string Tipo de empresa (comercializadora, distribuidora, cooperativa, productora). Example: comercializadora
     * @queryParam coverage_scope string Alcance de cobertura (local, provincial, autonómico, nacional, internacional). Example: nacional
     * @queryParam municipality_id int ID del municipio para filtrar. Example: 1
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 20
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Iberdrola",
     *       "slug": "iberdrola",
     *       "company_type": "comercializadora",
     *       "coverage_scope": "nacional",
     *       "municipality": {
     *         "id": 1,
     *         "name": "Madrid"
     *       }
     *     }
     *   ],
     *   "meta": {
     *     "current_page": 1,
     *     "last_page": 5,
     *     "per_page": 20,
     *     "total": 100
     *   }
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\EnergyCompanyResource
     * @apiResourceModel App\Models\EnergyCompany
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'company_type' => 'sometimes|string|in:comercializadora,distribuidora,cooperativa,productora',
            'coverage_scope' => 'sometimes|string|in:local,provincial,autonomico,nacional,internacional',
            'municipality_id' => 'sometimes|integer|exists:municipalities,id',
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100'
        ]);

        $query = EnergyCompany::with(['municipality', 'image']);

        if ($request->has('company_type')) {
            $query->where('company_type', $request->company_type);
        }

        if ($request->has('coverage_scope')) {
            $query->where('coverage_scope', $request->coverage_scope);
        }

        if ($request->has('municipality_id')) {
            $query->where('municipality_id', $request->municipality_id);
        }

        $perPage = min($request->get('per_page', 20), 100);
        $companies = $query->orderBy('name')->paginate($perPage);

        return response()->json([
            'data' => EnergyCompanyResource::collection($companies),
            'meta' => [
                'current_page' => $companies->currentPage(),
                'last_page' => $companies->lastPage(),
                'per_page' => $companies->perPage(),
                'total' => $companies->total(),
            ]
        ]);
    }

    /**
     * Display the specified energy company
     *
     * Obtiene los detalles de una empresa energética específica por ID o slug.
     *
     * @urlParam idOrSlug mixed ID o slug de la empresa. Example: iberdrola
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Iberdrola",
     *     "slug": "iberdrola",
     *     "company_type": "comercializadora",
     *     "coverage_scope": "nacional",
     *     "description": "Empresa líder en energías renovables",
     *     "municipality": {
     *       "id": 1,
     *       "name": "Madrid"
     *     }
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Empresa energética no encontrada"
     * }
     *
     * @apiResourceModel App\Models\EnergyCompany
     */
    public function show($idOrSlug): JsonResponse
    {
        $company = EnergyCompany::with(['municipality', 'image'])
            ->where('id', $idOrSlug)
            ->orWhere('slug', $idOrSlug)
            ->firstOrFail();

        return response()->json([
            'data' => new EnergyCompanyResource($company)
        ]);
    }

    /**
     * Filter companies by geographical location
     *
     * Filtra empresas energéticas por ubicación geográfica usando coordenadas y radio.
     *
     * @queryParam latitude number Latitud central para la búsqueda. Example: 40.4168
     * @queryParam longitude number Longitud central para la búsqueda. Example: -3.7038
     * @queryParam radius_km number Radio de búsqueda en kilómetros. Example: 50.0
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 20
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Iberdrola",
     *       "distance_km": 25.3
     *     }
     *   ],
     *   "meta": {
     *     "current_page": 1,
     *     "last_page": 1,
     *     "per_page": 20,
     *     "total": 1
     *   }
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\EnergyCompanyResource
     * @apiResourceModel App\Models\EnergyCompany
     */
    public function filterByLocation(Request $request): JsonResponse
    {
        $request->validate([
            'latitude' => 'required_with:longitude,radius_km|numeric|between:-90,90',
            'longitude' => 'required_with:latitude,radius_km|numeric|between:-180,180',
            'radius_km' => 'required_with:latitude,longitude|numeric|min:0.1|max:1000',
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100'
        ]);

        $query = EnergyCompany::with(['municipality', 'image']);

        if ($request->has('latitude') && $request->has('longitude') && $request->has('radius_km')) {
            $lat = (float)$request->latitude;
            $lng = (float)$request->longitude;
            $radiusKm = (float)$request->radius_km;

            $query->whereNotNull('latitude')
                  ->whereNotNull('longitude')
                  ->whereRaw(
                      '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) <= ?',
                      [$lat, $lng, $lat, $radiusKm]
                  );
        }

        $perPage = min($request->get('per_page', 20), 100);
        $companies = $query->orderBy('name')->paginate($perPage);

        return response()->json([
            'data' => EnergyCompanyResource::collection($companies),
            'meta' => [
                'current_page' => $companies->currentPage(),
                'last_page' => $companies->lastPage(),
                'per_page' => $companies->perPage(),
                'total' => $companies->total(),
            ]
        ]);
    }

    /**
     * Search energy companies
     *
     * Busca empresas energéticas por nombre y otros criterios.
     *
     * @queryParam q string Término de búsqueda para el nombre de la empresa. Example: iberdrola
     * @queryParam company_type string Filtrar por tipo de empresa. Example: comercializadora
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 20
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Iberdrola",
     *       "company_type": "comercializadora"
     *     }
     *   ],
     *   "meta": {
     *     "current_page": 1,
     *     "last_page": 1,
     *     "per_page": 20,
     *     "total": 1
     *   }
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\EnergyCompanyResource
     * @apiResourceModel App\Models\EnergyCompany
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'sometimes|string|max:255',
            'company_type' => 'sometimes|string|in:comercializadora,distribuidora,cooperativa,productora',
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100'
        ]);

        $query = EnergyCompany::with(['municipality', 'image']);

        if ($request->has('q') && !empty($request->q)) {
            $searchTerm = $request->q;
            $query->where('name', 'LIKE', '%' . $searchTerm . '%');
        }

        if ($request->has('company_type')) {
            $query->where('company_type', $request->company_type);
        }

        $perPage = min($request->get('per_page', 20), 100);
        $companies = $query->orderBy('name')->paginate($perPage);

        return response()->json([
            'data' => EnergyCompanyResource::collection($companies),
            'meta' => [
                'current_page' => $companies->currentPage(),
                'last_page' => $companies->lastPage(),
                'per_page' => $companies->perPage(),
                'total' => $companies->total(),
            ]
        ]);
    }

    /**
     * Get commercializing companies
     *
     * Obtiene una lista de empresas comercializadoras de energía.
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Iberdrola",
     *       "company_type": "comercializadora"
     *     }
     *   ]
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\EnergyCompanyResource
     * @apiResourceModel App\Models\EnergyCompany
     */
    public function commercializers(): JsonResponse
    {
        $companies = EnergyCompany::with(['municipality', 'image'])
            ->where('company_type', 'comercializadora')
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => EnergyCompanyResource::collection($companies)
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
     *       "id": 2,
     *       "name": "Som Energia",
     *       "company_type": "cooperativa"
     *     }
     *   ]
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\EnergyCompanyResource
     * @apiResourceModel App\Models\EnergyCompany
     */
    public function cooperatives(): JsonResponse
    {
        $companies = EnergyCompany::with(['municipality', 'image'])
            ->where('company_type', 'cooperativa')
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => EnergyCompanyResource::collection($companies)
        ]);
    }
}