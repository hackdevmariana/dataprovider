<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\RoofMarketplaceResource;
use App\Models\RoofMarketplace;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Roof Marketplace",
 *     description="Marketplace de techos y espacios para instalaciones solares"
 * )
 */
class RoofMarketplaceController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/roof-marketplace",
     *     summary="Listar espacios disponibles",
     *     tags={"Roof Marketplace"},
     *     @OA\Parameter(
     *         name="space_type",
     *         in="query",
     *         description="Tipo de espacio",
     *         @OA\Schema(type="string", enum={"residential_roof", "commercial_roof", "industrial_roof", "agricultural_land", "parking_lot", "warehouse_roof", "community_space", "unused_land", "building_facade", "other"})
     *     ),
     *     @OA\Parameter(
     *         name="offering_type",
     *         in="query",
     *         description="Tipo de oferta",
     *         @OA\Schema(type="string", enum={"rent", "sale", "partnership", "free_use", "energy_share", "mixed"})
     *     ),
     *     @OA\Parameter(
     *         name="min_area",
     *         in="query",
     *         description="Área mínima en m²",
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="max_rent",
     *         in="query",
     *         description="Renta máxima mensual",
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="verified_only",
     *         in="query",
     *         description="Solo espacios verificados",
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Response(response=200, description="Lista de espacios disponibles")
     * )
     */
    public function index(Request $request)
    {
        $filters = [
            'space_type' => $request->space_type,
            'offering_type' => $request->offering_type,
            'min_area' => $request->min_area,
            'max_area' => $request->max_area,
            'municipality_id' => $request->municipality_id,
            'max_rent' => $request->max_rent,
            'verified_only' => $request->boolean('verified_only'),
        ];

        $spaces = RoofMarketplace::getAvailable($filters, 20);
        
        return RoofMarketplaceResource::collection($spaces);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/roof-marketplace",
     *     summary="Publicar nuevo espacio",
     *     tags={"Roof Marketplace"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "description", "space_type", "address", "total_area_m2", "usable_area_m2", "offering_type"},
     *             @OA\Property(property="title", type="string", example="Techo Industrial 500m² - Zona Sur"),
     *             @OA\Property(property="description", type="string", example="Amplio techo industrial con excelente orientación sur"),
     *             @OA\Property(property="space_type", type="string", enum={"residential_roof", "commercial_roof", "industrial_roof"}),
     *             @OA\Property(property="address", type="string", example="Calle Industrial 123, Madrid"),
     *             @OA\Property(property="total_area_m2", type="number", example=500),
     *             @OA\Property(property="usable_area_m2", type="number", example=450),
     *             @OA\Property(property="offering_type", type="string", enum={"rent", "sale", "partnership", "energy_share"}),
     *             @OA\Property(property="monthly_rent_eur", type="number", example=200),
     *             @OA\Property(property="latitude", type="number", example=40.4168),
     *             @OA\Property(property="longitude", type="number", example=-3.7038)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Espacio publicado"),
     *     @OA\Response(response=422, description="Error de validación")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'space_type' => 'required|in:residential_roof,commercial_roof,industrial_roof,agricultural_land,parking_lot,warehouse_roof,community_space,unused_land,building_facade,other',
            'address' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'postal_code' => 'nullable|string|max:10',
            'municipality_id' => 'nullable|exists:municipalities,id',
            'total_area_m2' => 'required|numeric|min:10',
            'usable_area_m2' => 'required|numeric|min:5',
            'max_installable_power_kw' => 'nullable|numeric|min:1',
            'roof_orientation' => 'nullable|in:north,northeast,east,southeast,south,southwest,west,northwest,flat,multiple,optimal',
            'roof_inclination_degrees' => 'nullable|integer|between:0,90',
            'roof_material' => 'nullable|in:tile,metal,concrete,asphalt,slate,wood,membrane,other',
            'roof_condition' => 'nullable|in:excellent,good,fair,needs_repair,poor',
            'roof_age_years' => 'nullable|integer|min:0|max:100',
            'offering_type' => 'required|in:rent,sale,partnership,free_use,energy_share,mixed',
            'monthly_rent_eur' => 'nullable|numeric|min:0',
            'sale_price_eur' => 'nullable|numeric|min:0',
            'energy_share_percentage' => 'nullable|numeric|min:0|max:100',
            'contract_duration_years' => 'nullable|integer|min:1|max:99',
            'available_from' => 'nullable|date|after_or_equal:today',
            'available_until' => 'nullable|date|after:available_from',
        ]);

        $space = RoofMarketplace::create([
            'owner_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'space_type' => $request->space_type,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'postal_code' => $request->postal_code,
            'municipality_id' => $request->municipality_id,
            'total_area_m2' => $request->total_area_m2,
            'usable_area_m2' => $request->usable_area_m2,
            'max_installable_power_kw' => $request->max_installable_power_kw,
            'roof_orientation' => $request->roof_orientation,
            'roof_inclination_degrees' => $request->roof_inclination_degrees,
            'roof_material' => $request->roof_material,
            'roof_condition' => $request->roof_condition,
            'roof_age_years' => $request->roof_age_years,
            'offering_type' => $request->offering_type,
            'monthly_rent_eur' => $request->monthly_rent_eur,
            'sale_price_eur' => $request->sale_price_eur,
            'energy_share_percentage' => $request->energy_share_percentage,
            'contract_duration_years' => $request->contract_duration_years,
            'available_from' => $request->available_from,
            'available_until' => $request->available_until,
            'availability_status' => 'available',
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'Espacio publicado exitosamente',
            'data' => new RoofMarketplaceResource($space),
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/roof-marketplace/{roofSpace}",
     *     summary="Obtener detalles de un espacio",
     *     tags={"Roof Marketplace"},
     *     @OA\Parameter(
     *         name="roofSpace",
     *         in="path",
     *         required=true,
     *         description="Slug del espacio",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Detalles del espacio"),
     *     @OA\Response(response=404, description="Espacio no encontrado")
     * )
     */
    public function show(string $slug)
    {
        $space = RoofMarketplace::where('slug', $slug)
                               ->where('is_active', true)
                               ->with(['owner', 'municipality'])
                               ->firstOrFail();

        $space->incrementViews();

        return new RoofMarketplaceResource($space);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/roof-marketplace/featured",
     *     summary="Obtener espacios destacados",
     *     tags={"Roof Marketplace"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Número de espacios a retornar",
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(response=200, description="Espacios destacados")
     * )
     */
    public function featured(Request $request)
    {
        $limit = $request->integer('limit', 10);
        $spaces = RoofMarketplace::getFeatured($limit);
        
        return RoofMarketplaceResource::collection($spaces);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/roof-marketplace/nearby",
     *     summary="Buscar espacios cercanos",
     *     tags={"Roof Marketplace"},
     *     @OA\Parameter(
     *         name="latitude",
     *         in="query",
     *         required=true,
     *         description="Latitud",
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="longitude",
     *         in="query",
     *         required=true,
     *         description="Longitud",
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="radius",
     *         in="query",
     *         description="Radio en kilómetros",
     *         @OA\Schema(type="number", default=50)
     *     ),
     *     @OA\Response(response=200, description="Espacios cercanos")
     * )
     */
    public function nearby(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:1|max:500',
        ]);

        $spaces = RoofMarketplace::searchNearby(
            $request->latitude,
            $request->longitude,
            $request->radius ?? 50
        );

        return RoofMarketplaceResource::collection($spaces);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/roof-marketplace/{roofSpace}/inquire",
     *     summary="Realizar consulta sobre un espacio",
     *     tags={"Roof Marketplace"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="roofSpace",
     *         in="path",
     *         required=true,
     *         description="Slug del espacio",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"message"},
     *             @OA\Property(property="message", type="string", example="Me interesa su techo para instalación solar"),
     *             @OA\Property(property="contact_phone", type="string", example="+34600123456"),
     *             @OA\Property(property="proposed_capacity_kw", type="number", example=100),
     *             @OA\Property(property="proposed_terms", type="string", example="Alquiler mensual con participación en beneficios")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Consulta enviada"),
     *     @OA\Response(response=404, description="Espacio no encontrado")
     * )
     */
    public function inquire(Request $request, string $slug): JsonResponse
    {
        $space = RoofMarketplace::where('slug', $slug)
                               ->where('is_active', true)
                               ->firstOrFail();

        $request->validate([
            'message' => 'required|string|max:1000',
            'contact_phone' => 'nullable|string|max:20',
            'proposed_capacity_kw' => 'nullable|numeric|min:1',
            'proposed_terms' => 'nullable|string|max:500',
        ]);

        // Aquí iría la lógica para crear la consulta/mensaje
        // Por simplicidad, solo incrementamos el contador
        $space->incrementInquiries();

        // Si hay respuesta automática configurada
        $response = [
            'message' => 'Consulta enviada exitosamente al propietario',
            'space_title' => $space->title,
            'owner_response_time' => '24-48 horas',
        ];

        if ($space->auto_respond_inquiries && $space->auto_response_message) {
            $response['auto_response'] = $space->auto_response_message;
        }

        return response()->json($response, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/roof-marketplace/{roofSpace}/energy-potential",
     *     summary="Calcular potencial energético del espacio",
     *     tags={"Roof Marketplace"},
     *     @OA\Parameter(
     *         name="roofSpace",
     *         in="path",
     *         required=true,
     *         description="Slug del espacio",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Potencial energético calculado")
     * )
     */
    public function energyPotential(string $slug): JsonResponse
    {
        $space = RoofMarketplace::where('slug', $slug)
                               ->where('is_active', true)
                               ->firstOrFail();

        $potential = $space->getEnergyPotential();
        $attractiveness = $space->getAttractivenessScore();

        return response()->json([
            'space_info' => [
                'title' => $space->title,
                'usable_area_m2' => $space->usable_area_m2,
                'roof_orientation' => $space->roof_orientation,
                'roof_condition' => $space->roof_condition,
            ],
            'energy_potential' => $potential,
            'attractiveness_score' => $attractiveness,
            'investment_estimate' => [
                'estimated_cost_eur' => $potential['estimated_power_kw'] * 1200, // €1200/kW aprox
                'payback_period_years' => 8,
                'estimated_roi_percentage' => 12,
            ],
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/roof-marketplace/stats",
     *     summary="Obtener estadísticas del marketplace",
     *     tags={"Roof Marketplace"},
     *     @OA\Response(response=200, description="Estadísticas del marketplace")
     * )
     */
    public function stats(): JsonResponse
    {
        $stats = RoofMarketplace::getMarketplaceStats();

        return response()->json([
            'marketplace_stats' => $stats,
            'market_insights' => [
                'most_popular_space_type' => array_keys($stats['by_space_type'], max($stats['by_space_type']))[0] ?? null,
                'most_popular_offering_type' => array_keys($stats['by_offering_type'], max($stats['by_offering_type']))[0] ?? null,
                'average_space_size_m2' => $stats['total_area_m2'] > 0 ? round($stats['total_area_m2'] / $stats['total_spaces'], 2) : 0,
                'total_potential_power_mw' => round($stats['estimated_total_power_kw'] / 1000, 2),
            ],
        ]);
    }
}
