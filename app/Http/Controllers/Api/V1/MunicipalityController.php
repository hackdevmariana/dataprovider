<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Municipality;
use Illuminate\Http\Request;

class MunicipalityController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/municipalities",
     *     summary="Listar todos los municipios",
     *     tags={"Municipalities"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de municipios",
     *         @OA\JsonContent(type="array", @OA\Items())
     *     )
     * )
     */
    public function index()
    {
        $municipalities = Municipality::with(['province', 'autonomousCommunity', 'country'])->paginate(50);
        return response()->json($municipalities);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/municipalities/province/{slug}",
     *     summary="Listar municipios por provincia",
     *     tags={"Municipalities"},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         description="Slug de la provincia",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Municipios encontrados",
     *         @OA\JsonContent(type="array", @OA\Items())
     *     )
     * )
     */
    public function byProvince($slug)
    {
        $municipalities = Municipality::whereHas('province', fn($q) => $q->where('slug', $slug))
            ->with(['province', 'autonomousCommunity', 'country'])
            ->get();

        return response()->json($municipalities);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/municipalities/country/{slug}",
     *     summary="Listar municipios por país",
     *     tags={"Municipalities"},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         description="Slug del país",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Municipios encontrados",
     *         @OA\JsonContent(type="array", @OA\Items())
     *     )
     * )
     */
    public function byCountry($slug)
    {
        $municipalities = Municipality::whereHas('country', fn($q) => $q->where('slug', $slug))
            ->with(['province', 'autonomousCommunity', 'country'])
            ->paginate(50);

        return response()->json($municipalities);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/municipalities/{idOrSlug}",
     *     summary="Mostrar detalle de un municipio",
     *     tags={"Municipalities"},
     *     @OA\Parameter(
     *         name="idOrSlug",
     *         in="path",
     *         description="ID o slug del municipio",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Municipio encontrado",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(response=404, description="No encontrado")
     * )
     */
    public function show($idOrSlug)
    {
        $municipality = Municipality::with(['province', 'autonomousCommunity', 'country'])
            ->where('id', $idOrSlug)
            ->orWhere('slug', $idOrSlug)
            ->first();

        if (!$municipality) {
            return response()->json(['message' => 'Municipio no encontrado'], 404);
        }

        return response()->json($municipality);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/municipalities/filter/by-population",
     *     summary="Filtrar municipios por rango de población",
     *     tags={"Municipalities"},
     *     @OA\Parameter(
     *         name="min_population",
     *         in="query",
     *         description="Población mínima",
     *         @OA\Schema(type="integer", example=1000)
     *     ),
     *     @OA\Parameter(
     *         name="max_population",
     *         in="query",
     *         description="Población máxima",
     *         @OA\Schema(type="integer", example=100000)
     *     ),
     *     @OA\Parameter(
     *         name="province_id",
     *         in="query",
     *         description="ID de provincia para filtrar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Municipios filtrados por población",
     *         @OA\JsonContent(type="array", @OA\Items())
     *     )
     * )
     */
    public function filterByPopulation(Request $request)
    {
        $query = Municipality::with(['province', 'autonomousCommunity', 'country']);

        // Filtro por población mínima
        if ($request->has('min_population')) {
            $query->where('population', '>=', (int)$request->min_population);
        }

        // Filtro por población máxima
        if ($request->has('max_population')) {
            $query->where('population', '<=', (int)$request->max_population);
        }

        // Filtro por provincia
        if ($request->has('province_id')) {
            $query->where('province_id', $request->province_id);
        }

        $municipalities = $query->orderBy('population', 'desc')->paginate(50);
        
        return response()->json($municipalities);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/municipalities/filter/by-area",
     *     summary="Filtrar municipios por superficie",
     *     tags={"Municipalities"},
     *     @OA\Parameter(
     *         name="min_area",
     *         in="query",
     *         description="Superficie mínima en km²",
     *         @OA\Schema(type="number", format="float", example=10.5)
     *     ),
     *     @OA\Parameter(
     *         name="max_area",
     *         in="query",
     *         description="Superficie máxima en km²",
     *         @OA\Schema(type="number", format="float", example=500.0)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Municipios filtrados por superficie",
     *         @OA\JsonContent(type="array", @OA\Items())
     *     )
     * )
     */
    public function filterByArea(Request $request)
    {
        $query = Municipality::with(['province', 'autonomousCommunity', 'country']);

        // Filtro por superficie mínima
        if ($request->has('min_area')) {
            $query->where('area_km2', '>=', (float)$request->min_area);
        }

        // Filtro por superficie máxima
        if ($request->has('max_area')) {
            $query->where('area_km2', '<=', (float)$request->max_area);
        }

        $municipalities = $query->orderBy('area_km2', 'desc')->paginate(50);
        
        return response()->json($municipalities);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/municipalities/search",
     *     summary="Búsqueda avanzada de municipios",
     *     tags={"Municipalities"},
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Búsqueda parcial por nombre",
     *         @OA\Schema(type="string", example="Madrid")
     *     ),
     *     @OA\Parameter(
     *         name="postal_code",
     *         in="query",
     *         description="Código postal",
     *         @OA\Schema(type="string", example="28001")
     *     ),
     *     @OA\Parameter(
     *         name="province_slug",
     *         in="query",
     *         description="Slug de la provincia",
     *         @OA\Schema(type="string", example="madrid")
     *     ),
     *     @OA\Parameter(
     *         name="autonomous_community_slug",
     *         in="query",
     *         description="Slug de la comunidad autónoma",
     *         @OA\Schema(type="string", example="madrid")
     *     ),
     *     @OA\Parameter(
     *         name="min_population",
     *         in="query",
     *         description="Población mínima",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Campo para ordenar: name, population, area_km2",
     *         @OA\Schema(type="string", enum={"name", "population", "area_km2"}, example="population")
     *     ),
     *     @OA\Parameter(
     *         name="sort_direction",
     *         in="query",
     *         description="Dirección de ordenamiento",
     *         @OA\Schema(type="string", enum={"asc", "desc"}, example="desc")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Resultados de búsqueda",
     *         @OA\JsonContent(type="array", @OA\Items())
     *     )
     * )
     */
    public function search(Request $request)
    {
        $query = Municipality::with(['province', 'autonomousCommunity', 'country']);

        // Búsqueda por nombre (parcial)
        if ($request->has('name') && !empty($request->name)) {
            $query->where('name', 'LIKE', '%' . $request->name . '%');
        }

        // Filtro por código postal
        if ($request->has('postal_code')) {
            $query->where('postal_code', $request->postal_code);
        }

        // Filtro por provincia
        if ($request->has('province_slug')) {
            $query->whereHas('province', fn($q) => $q->where('slug', $request->province_slug));
        }

        // Filtro por comunidad autónoma
        if ($request->has('autonomous_community_slug')) {
            $query->whereHas('autonomousCommunity', fn($q) => $q->where('slug', $request->autonomous_community_slug));
        }

        // Filtro por población mínima
        if ($request->has('min_population')) {
            $query->where('population', '>=', (int)$request->min_population);
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'name');
        $sortDirection = $request->get('sort_direction', 'asc');
        
        // Validar campos de ordenamiento
        $allowedSortFields = ['name', 'population', 'area_km2', 'postal_code'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'name';
        }
        
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }

        $query->orderBy($sortBy, $sortDirection);

        $municipalities = $query->paginate(50);
        
        return response()->json($municipalities);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/municipalities/largest/{limit}",
     *     summary="Obtener los municipios más grandes por población",
     *     tags={"Municipalities"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="path",
     *         description="Número de municipios a retornar",
     *         required=true,
     *         @OA\Schema(type="integer", minimum=1, maximum=100, example=10)
     *     ),
     *     @OA\Parameter(
     *         name="province_id",
     *         in="query",
     *         description="Filtrar por provincia específica",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Municipios más grandes por población",
     *         @OA\JsonContent(type="array", @OA\Items())
     *     )
     * )
     */
    public function largest($limit = 10, Request $request)
    {
        $limit = min(max((int)$limit, 1), 100); // Entre 1 y 100
        
        $query = Municipality::with(['province', 'autonomousCommunity', 'country'])
            ->whereNotNull('population')
            ->where('population', '>', 0);

        // Filtro opcional por provincia
        if ($request->has('province_id')) {
            $query->where('province_id', $request->province_id);
        }

        $municipalities = $query->orderBy('population', 'desc')
            ->limit($limit)
            ->get();

        return response()->json($municipalities);
    }
}


