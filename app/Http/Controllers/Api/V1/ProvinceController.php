<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Province;
use Illuminate\Http\Request;

class ProvinceController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/provinces",
     *     summary="Obtener listado de provincias",
     *     tags={"Provinces"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista paginada de provincias",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="slug", type="string"),
     *                 @OA\Property(property="ine_code", type="string"),
     *                 @OA\Property(property="latitude", type="number", format="float"),
     *                 @OA\Property(property="longitude", type="number", format="float"),
     *                 @OA\Property(property="area_km2", type="number", format="float"),
     *                 @OA\Property(property="altitude_m", type="integer"),
     *                 @OA\Property(property="autonomous_community", type="object",
     *                     @OA\Property(property="name", type="string")
     *                 ),
     *                 @OA\Property(property="country", type="object",
     *                     @OA\Property(property="name", type="string")
     *                 )
     *             )),
     *             @OA\Property(property="links", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $provinces = Province::with(['autonomousCommunity', 'country'])->paginate(20);
        return response()->json($provinces);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/provinces/{idOrSlug}",
     *     summary="Mostrar detalles de una provincia",
     *     tags={"Provinces"},
     *     @OA\Parameter(
     *         name="idOrSlug",
     *         in="path",
     *         required=true,
     *         description="ID o slug de la provincia",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Provincia encontrada",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="slug", type="string"),
     *             @OA\Property(property="ine_code", type="string"),
     *             @OA\Property(property="latitude", type="number", format="float"),
     *             @OA\Property(property="longitude", type="number", format="float"),
     *             @OA\Property(property="area_km2", type="number", format="float"),
     *             @OA\Property(property="altitude_m", type="integer"),
     *             @OA\Property(property="autonomous_community", type="object",
     *                 @OA\Property(property="name", type="string")
     *             ),
     *             @OA\Property(property="country", type="object",
     *                 @OA\Property(property="name", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Provincia no encontrada")
     * )
     */
    public function show($idOrSlug)
    {
        $province = Province::with(['autonomousCommunity', 'country'])
            ->where('id', $idOrSlug)
            ->orWhere('slug', $idOrSlug)
            ->first();

        if (!$province) {
            return response()->json(['message' => 'Provincia no encontrada'], 404);
        }

        return response()->json($province);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/provinces/with-municipalities-count",
     *     summary="Provincias con conteo de municipios",
     *     tags={"Provinces"},
     *     @OA\Parameter(
     *         name="min_municipalities",
     *         in="query",
     *         description="Número mínimo de municipios",
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Parameter(
     *         name="max_municipalities",
     *         in="query",
     *         description="Número máximo de municipios",
     *         @OA\Schema(type="integer", example=500)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Provincias con conteo de municipios",
     *         @OA\JsonContent(type="array", @OA\Items())
     *     )
     * )
     */
    public function withMunicipalitiesCount(Request $request)
    {
        $query = Province::with(['autonomousCommunity', 'country'])
            ->withCount('municipalities');

        // Filtro por número mínimo de municipios
        if ($request->has('min_municipalities')) {
            $query->having('municipalities_count', '>=', (int)$request->min_municipalities);
        }

        // Filtro por número máximo de municipios
        if ($request->has('max_municipalities')) {
            $query->having('municipalities_count', '<=', (int)$request->max_municipalities);
        }

        $provinces = $query->orderBy('municipalities_count', 'desc')->paginate(20);
        
        return response()->json($provinces);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/provinces/filter/by-area",
     *     summary="Filtrar provincias por superficie",
     *     tags={"Provinces"},
     *     @OA\Parameter(
     *         name="min_area",
     *         in="query",
     *         description="Superficie mínima en km²",
     *         @OA\Schema(type="number", format="float", example=1000.0)
     *     ),
     *     @OA\Parameter(
     *         name="max_area",
     *         in="query",
     *         description="Superficie máxima en km²",
     *         @OA\Schema(type="number", format="float", example=50000.0)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Provincias filtradas por superficie",
     *         @OA\JsonContent(type="array", @OA\Items())
     *     )
     * )
     */
    public function filterByArea(Request $request)
    {
        $query = Province::with(['autonomousCommunity', 'country']);

        // Filtro por superficie mínima
        if ($request->has('min_area')) {
            $query->where('area_km2', '>=', (float)$request->min_area);
        }

        // Filtro por superficie máxima
        if ($request->has('max_area')) {
            $query->where('area_km2', '<=', (float)$request->max_area);
        }

        $provinces = $query->orderBy('area_km2', 'desc')->paginate(20);
        
        return response()->json($provinces);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/provinces/by-autonomous-community/{slug}",
     *     summary="Provincias por comunidad autónoma",
     *     tags={"Provinces"},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         description="Slug de la comunidad autónoma",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="include_municipalities",
     *         in="query",
     *         description="Incluir municipios en la respuesta",
     *         @OA\Schema(type="boolean", example=false)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Provincias de la comunidad autónoma",
     *         @OA\JsonContent(type="array", @OA\Items())
     *     )
     * )
     */
    public function byAutonomousCommunity($slug, Request $request)
    {
        $with = ['autonomousCommunity', 'country'];
        
        // Opcionalmente incluir municipios
        if ($request->boolean('include_municipalities', false)) {
            $with[] = 'municipalities';
        }

        $provinces = Province::with($with)
            ->whereHas('autonomousCommunity', fn($q) => $q->where('slug', $slug))
            ->orderBy('name')
            ->get();

        return response()->json($provinces);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/provinces/search",
     *     summary="Búsqueda avanzada de provincias",
     *     tags={"Provinces"},
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Búsqueda parcial por nombre",
     *         @OA\Schema(type="string", example="Madrid")
     *     ),
     *     @OA\Parameter(
     *         name="autonomous_community_slug",
     *         in="query",
     *         description="Slug de la comunidad autónoma",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="min_area",
     *         in="query",
     *         description="Superficie mínima en km²",
     *         @OA\Schema(type="number", format="float")
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Campo para ordenar: name, area_km2, altitude_m",
     *         @OA\Schema(type="string", enum={"name", "area_km2", "altitude_m"}, example="name")
     *     ),
     *     @OA\Parameter(
     *         name="sort_direction",
     *         in="query",
     *         description="Dirección de ordenamiento",
     *         @OA\Schema(type="string", enum={"asc", "desc"}, example="asc")
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
        $query = Province::with(['autonomousCommunity', 'country']);

        // Búsqueda por nombre (parcial)
        if ($request->has('name') && !empty($request->name)) {
            $query->where('name', 'LIKE', '%' . $request->name . '%');
        }

        // Filtro por comunidad autónoma
        if ($request->has('autonomous_community_slug')) {
            $query->whereHas('autonomousCommunity', fn($q) => $q->where('slug', $request->autonomous_community_slug));
        }

        // Filtro por superficie mínima
        if ($request->has('min_area')) {
            $query->where('area_km2', '>=', (float)$request->min_area);
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'name');
        $sortDirection = $request->get('sort_direction', 'asc');
        
        // Validar campos de ordenamiento
        $allowedSortFields = ['name', 'area_km2', 'altitude_m', 'ine_code'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'name';
        }
        
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }

        $query->orderBy($sortBy, $sortDirection);

        $provinces = $query->paginate(20);
        
        return response()->json($provinces);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/provinces/largest/{limit}",
     *     summary="Obtener las provincias más grandes por superficie",
     *     tags={"Provinces"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="path",
     *         description="Número de provincias a retornar",
     *         required=true,
     *         @OA\Schema(type="integer", minimum=1, maximum=100, example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Provincias más grandes por superficie",
     *         @OA\JsonContent(type="array", @OA\Items())
     *     )
     * )
     */
    public function largest($limit = 10)
    {
        $limit = min(max((int)$limit, 1), 100); // Entre 1 y 100
        
        $provinces = Province::with(['autonomousCommunity', 'country'])
            ->whereNotNull('area_km2')
            ->where('area_km2', '>', 0)
            ->orderBy('area_km2', 'desc')
            ->limit($limit)
            ->get();

        return response()->json($provinces);
    }
}


