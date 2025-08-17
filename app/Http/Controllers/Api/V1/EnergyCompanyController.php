<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\EnergyCompany;
use App\Http\Resources\V1\EnergyCompanyResource;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Energy Companies",
 *     description="API for energy companies (commercializers, distributors, cooperatives)"
 * )
 */
class EnergyCompanyController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/energy-companies",
     *     summary="Get energy companies",
     *     tags={"Energy Companies"},
     *     @OA\Parameter(
     *         name="company_type",
     *         in="query",
     *         description="Filter by company type",
     *         @OA\Schema(type="string", example="comercializadora")
     *     ),
     *     @OA\Parameter(
     *         name="coverage_scope",
     *         in="query",
     *         description="Filter by coverage scope",
     *         @OA\Schema(type="string", example="nacional")
     *     ),
     *     @OA\Parameter(
     *         name="municipality_id",
     *         in="query",
     *         description="Filter by municipality",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of energy companies"
     *     )
     * )
     */
    public function index(Request $request)
    {
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

        $companies = $query->orderBy('name')->paginate(20);

        return EnergyCompanyResource::collection($companies);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/energy-companies/{idOrSlug}",
     *     summary="Get specific energy company",
     *     tags={"Energy Companies"},
     *     @OA\Response(response=200, description="Energy company details")
     * )
     */
    public function show($idOrSlug)
    {
        $company = EnergyCompany::with(['municipality', 'image'])
            ->where('id', $idOrSlug)
            ->orWhere('slug', $idOrSlug)
            ->firstOrFail();

        return new EnergyCompanyResource($company);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/energy-companies/filter/by-location",
     *     summary="Filter companies by geographical location",
     *     tags={"Energy Companies"},
     *     @OA\Parameter(
     *         name="latitude",
     *         in="query",
     *         description="Central latitude for search",
     *         @OA\Schema(type="number", format="float")
     *     ),
     *     @OA\Parameter(
     *         name="longitude",
     *         in="query",
     *         description="Central longitude for search",
     *         @OA\Schema(type="number", format="float")
     *     ),
     *     @OA\Parameter(
     *         name="radius_km",
     *         in="query",
     *         description="Search radius in kilometers",
     *         @OA\Schema(type="number", format="float", example=50.0)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Companies filtered by location"
     *     )
     * )
     */
    public function filterByLocation(Request $request)
    {
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

        $companies = $query->orderBy('name')->paginate(20);

        return EnergyCompanyResource::collection($companies);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/energy-companies/search",
     *     summary="Search energy companies",
     *     tags={"Energy Companies"},
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         description="Search query for company name",
     *         @OA\Schema(type="string", example="iberdrola")
     *     ),
     *     @OA\Parameter(
     *         name="company_type",
     *         in="query",
     *         description="Filter by company type",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Search results"
     *     )
     * )
     */
    public function search(Request $request)
    {
        $query = EnergyCompany::with(['municipality', 'image']);

        if ($request->has('q') && !empty($request->q)) {
            $searchTerm = $request->q;
            $query->where('name', 'LIKE', '%' . $searchTerm . '%');
        }

        if ($request->has('company_type')) {
            $query->where('company_type', $request->company_type);
        }

        $companies = $query->orderBy('name')->paginate(20);

        return EnergyCompanyResource::collection($companies);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/energy-companies/commercializers",
     *     summary="Get commercializing companies",
     *     tags={"Energy Companies"},
     *     @OA\Response(response=200, description="List of commercializing companies")
     * )
     */
    public function commercializers()
    {
        $companies = EnergyCompany::with(['municipality', 'image'])
            ->where('company_type', 'comercializadora')
            ->orderBy('name')
            ->get();

        return EnergyCompanyResource::collection($companies);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/energy-companies/cooperatives",
     *     summary="Get energy cooperatives",
     *     tags={"Energy Companies"},
     *     @OA\Response(response=200, description="List of energy cooperatives")
     * )
     */
    public function cooperatives()
    {
        $companies = EnergyCompany::with(['municipality', 'image'])
            ->where('company_type', 'cooperativa')
            ->orderBy('name')
            ->get();

        return EnergyCompanyResource::collection($companies);
    }
}