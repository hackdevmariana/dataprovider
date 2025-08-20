<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\EnergyInstallationResource;
use App\Models\EnergyInstallation;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Energy Installations",
 *     description="API endpoints para gestión de instalaciones energéticas"
 * )
 * 
 * @group Energy Installations
 *
 * APIs para la gestión de instalaciones energéticas renovables.
 * Permite a los usuarios registrar, gestionar y consultar
 * instalaciones de energía solar, eólica, hidráulica y biomasa.
 */
class EnergyInstallationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/energy-installations",
     *     summary="List energy installations",
     *     tags={"Energy Installations"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="List of energy installations")
     * )
     * 
     * Display a listing of energy installations
     *
     * Obtiene una lista de instalaciones energéticas con opciones de paginación.
     *
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 15
     *
     * @apiResourceCollection App\Http\Resources\V1\EnergyInstallationResource
     * @apiResourceModel App\Models\EnergyInstallation
     */
    public function index(Request $request)
    {
        $installations = EnergyInstallation::with(['owner'])
            ->paginate($request->get('per_page', 15));

        return EnergyInstallationResource::collection($installations);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/energy-installations",
     *     summary="Create energy installation",
     *     tags={"Energy Installations"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "type", "capacity_kw", "location"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="type", type="string", enum={"solar", "wind", "hydro", "biomass", "other"}),
     *             @OA\Property(property="capacity_kw", type="number"),
     *             @OA\Property(property="location", type="string"),
     *             @OA\Property(property="commissioned_at", type="string", format="date")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Installation created successfully"),
     *     @OA\Response(response=422, description="Validation errors")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:solar,wind,hydro,biomass,other',
            'capacity_kw' => 'required|numeric|min:0.1|max:99999',
            'location' => 'required|string|max:255',
            'commissioned_at' => 'nullable|date',
        ]);

        // Asignar al usuario autenticado si existe
        if (auth()->check()) {
            $validated['owner_id'] = auth()->id();
        }

        $installation = EnergyInstallation::create($validated);
        $installation->load('owner');

        return new EnergyInstallationResource($installation);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/energy-installations/{id}",
     *     summary="Show energy installation",
     *     tags={"Energy Installations"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Installation details"),
     *     @OA\Response(response=404, description="Installation not found")
     * )
     */
    public function show(EnergyInstallation $energyInstallation)
    {
        $energyInstallation->load(['owner', 'energyTransactions']);
        return new EnergyInstallationResource($energyInstallation);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/energy-installations/{id}",
     *     summary="Update energy installation",
     *     tags={"Energy Installations"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="type", type="string", enum={"solar", "wind", "hydro", "biomass", "other"}),
     *             @OA\Property(property="capacity_kw", type="number"),
     *             @OA\Property(property="location", type="string"),
     *             @OA\Property(property="commissioned_at", type="string", format="date")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Installation updated successfully"),
     *     @OA\Response(response=404, description="Installation not found"),
     *     @OA\Response(response=422, description="Validation errors")
     * )
     */
    public function update(Request $request, EnergyInstallation $energyInstallation)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'type' => 'sometimes|in:solar,wind,hydro,biomass,other',
            'capacity_kw' => 'sometimes|numeric|min:0.1|max:99999',
            'location' => 'sometimes|string|max:255',
            'commissioned_at' => 'nullable|date',
        ]);

        $energyInstallation->update($validated);
        $energyInstallation->load('owner');

        return new EnergyInstallationResource($energyInstallation);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/energy-installations/{id}",
     *     summary="Delete energy installation",
     *     tags={"Energy Installations"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Installation deleted successfully"),
     *     @OA\Response(response=404, description="Installation not found")
     * )
     */
    public function destroy(EnergyInstallation $energyInstallation)
    {
        $energyInstallation->delete();
        return response()->noContent();
    }

    /**
     * @OA\Get(
     *     path="/api/v1/energy-installations/filter/by-type/{type}",
     *     summary="Filter installations by type",
     *     tags={"Energy Installations"},
     *     @OA\Parameter(
     *         name="type",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string", enum={"solar", "wind", "hydro", "biomass", "other"})
     *     ),
     *     @OA\Response(response=200, description="Filtered installations")
     * )
     */
    public function filterByType($type)
    {
        $installations = EnergyInstallation::with(['owner'])
            ->ofType($type)
            ->paginate(15);

        return EnergyInstallationResource::collection($installations);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/energy-installations/filter/by-capacity",
     *     summary="Filter installations by capacity range",
     *     tags={"Energy Installations"},
     *     @OA\Parameter(
     *         name="min_kw",
     *         in="query",
     *         description="Minimum capacity in kW",
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="max_kw",
     *         in="query",
     *         description="Maximum capacity in kW",
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Response(response=200, description="Filtered installations")
     * )
     */
    public function filterByCapacity(Request $request)
    {
        $query = EnergyInstallation::with(['owner']);

        if ($request->has('min_kw')) {
            $query->minCapacity($request->get('min_kw'));
        }

        if ($request->has('max_kw')) {
            $query->maxCapacity($request->get('max_kw'));
        }

        $installations = $query->paginate(15);

        return EnergyInstallationResource::collection($installations);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/energy-installations/commissioned",
     *     summary="Get commissioned installations",
     *     tags={"Energy Installations"},
     *     @OA\Response(response=200, description="Commissioned installations")
     * )
     */
    public function commissioned()
    {
        $installations = EnergyInstallation::with(['owner'])
            ->commissioned()
            ->paginate(15);

        return EnergyInstallationResource::collection($installations);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/energy-installations/in-development",
     *     summary="Get installations in development",
     *     tags={"Energy Installations"},
     *     @OA\Response(response=200, description="Installations in development")
     * )
     */
    public function inDevelopment()
    {
        $installations = EnergyInstallation::with(['owner'])
            ->inDevelopment()
            ->paginate(15);

        return EnergyInstallationResource::collection($installations);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/energy-installations/search",
     *     summary="Search installations",
     *     tags={"Energy Installations"},
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         required=true,
     *         description="Search query",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Search results")
     * )
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (empty($query)) {
            return response()->json(['error' => 'Query parameter q is required'], 400);
        }

        $installations = EnergyInstallation::with(['owner'])
            ->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('location', 'LIKE', "%{$query}%")
                  ->orWhere('type', 'LIKE', "%{$query}%");
            })
            ->paginate(15);

        return EnergyInstallationResource::collection($installations);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/energy-installations/statistics",
     *     summary="Get installation statistics",
     *     tags={"Energy Installations"},
     *     @OA\Response(response=200, description="Installation statistics")
     * )
     */
    public function statistics()
    {
        $stats = [
            'total_installations' => EnergyInstallation::count(),
            'commissioned_installations' => EnergyInstallation::commissioned()->count(),
            'in_development_installations' => EnergyInstallation::inDevelopment()->count(),
            'total_capacity_kw' => EnergyInstallation::sum('capacity_kw'),
            'commissioned_capacity_kw' => EnergyInstallation::commissioned()->sum('capacity_kw'),
            'by_type' => EnergyInstallation::selectRaw('type, COUNT(*) as count, SUM(capacity_kw) as total_capacity_kw')
                ->groupBy('type')
                ->get(),
            'average_capacity_kw' => round(EnergyInstallation::avg('capacity_kw'), 2),
            'estimated_monthly_production_kwh' => EnergyInstallation::commissioned()
                ->get()
                ->sum('estimated_monthly_production'),
        ];

        return response()->json(['data' => $stats]);
    }
}