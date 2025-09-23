<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\EmissionFactorResource;
use App\Models\EmissionFactor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * @group Emission Factors
 *
 * APIs para la gestión de factores de emisión de CO2.
 * Permite a los usuarios y administradores consultar y gestionar
 * los factores de emisión utilizados para calcular la huella de carbono.
 */
class EmissionFactorController extends Controller
{
    /**
     * Display a listing of emission factors
     *
     * Obtiene una lista de factores de emisión con opciones de filtrado.
     *
     * @queryParam category string Categoría del factor (energy, transport, waste, food, lifestyle). Example: energy
     * @queryParam source string Fuente de datos del factor. Example: IPCC
     * @queryParam year int Año de validez del factor. Example: 2024
     * @queryParam is_active boolean Solo factores activos. Example: true
     * @queryParam is_verified boolean Solo factores verificados. Example: true
     * @queryParam search string Buscar en nombre y descripción. Example: electricidad
     * @queryParam sort string Ordenamiento (recent, oldest, category, source, year). Example: recent
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 15
     *
     * @apiResourceCollection App\Http\Resources\V1\EmissionFactorResource
     * @apiResourceModel App\Models\EmissionFactor
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'category' => 'sometimes|string|in:energy,transport,waste,food,lifestyle',
            'source' => 'sometimes|string|max:100',
            'year' => 'sometimes|integer|min:1990|max:2030',
            'is_active' => 'sometimes|boolean',
            'is_verified' => 'sometimes|boolean',
            'search' => 'sometimes|string|max:255',
            'sort' => 'sometimes|string|in:recent,oldest,category,source,year',
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100'
        ]);

        $query = EmissionFactor::query();

        // Filtros
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('source')) {
            $query->where('source', 'like', '%' . $request->source . '%');
        }

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->filled('is_verified')) {
            $query->where('is_verified', $request->boolean('is_verified'));
        }

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('description', 'like', "%{$term}%")
                  ->orWhere('unit', 'like', "%{$term}%");
            });
        }

        // Ordenamiento
        $sort = $request->get('sort', 'recent');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'category':
                $query->orderBy('category')->orderBy('name');
                break;
            case 'source':
                $query->orderBy('source')->orderBy('name');
                break;
            case 'year':
                $query->orderBy('year', 'desc')->orderBy('name');
                break;
            default: // recent
                $query->orderBy('created_at', 'desc');
        }

        $perPage = min($request->get('per_page', 15), 100);
        $factors = $query->paginate($perPage);

        return EmissionFactorResource::collection($factors)->response();
    }

    /**
     * Store a newly created emission factor
     *
     * Crea un nuevo factor de emisión. Solo administradores pueden crear factores.
     *
     * @bodyParam name string required Nombre del factor de emisión. Example: Electricidad España
     * @bodyParam category string required Categoría del factor. Example: energy
     * @bodyParam factor_value number required Valor del factor (kg CO2/unit). Example: 0.5
     * @bodyParam unit string required Unidad de medida. Example: kWh
     * @bodyParam source string required Fuente de datos. Example: Red Eléctrica de España
     * @bodyParam year int required Año de validez. Example: 2024
     * @bodyParam description text Descripción del factor. Example: Factor de emisión para electricidad en España
     * @bodyParam methodology text Metodología de cálculo. Example: Basado en mix energético nacional
     * @bodyParam uncertainty_percentage number Porcentaje de incertidumbre. Example: 5.2
     * @bodyParam is_active boolean Si el factor está activo. Example: true
     * @bodyParam tags array Etiquetas para categorizar. Example: ["electricidad", "españa", "2024"]
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "name": "Electricidad España",
     *     "category": "energy",
     *     "factor_value": 0.5,
     *     "unit": "kWh",
     *     "source": "Red Eléctrica de España",
     *     "year": 2024,
     *     "is_active": true,
     *     "created_at": "2024-01-15T10:00:00.000000Z"
     *   },
     *   "message": "Factor de emisión creado exitosamente"
     * }
     *
     * @response 403 {
     *   "message": "No tienes permisos para crear factores de emisión"
     * }
     *
     * @authenticated
     */
    public function store(Request $request): JsonResponse
    {
        // Verificar permisos de administrador
        if (!Auth::guard('sanctum')->user()->hasRole('admin')) {
            return response()->json([
                'message' => 'No tienes permisos para crear factores de emisión'
            ], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:emission_factors,name',
            'category' => 'required|string|in:energy,transport,waste,food,lifestyle',
            'factor_value' => 'required|numeric|min:0.001|max:10000',
            'unit' => 'required|string|max:50',
            'source' => 'required|string|max:100',
            'year' => 'required|integer|min:1990|max:2030',
            'description' => 'sometimes|string|max:1000',
            'methodology' => 'sometimes|string|max:1000',
            'uncertainty_percentage' => 'sometimes|numeric|min:0|max:100',
            'is_active' => 'sometimes|boolean',
            'tags' => 'sometimes|array',
            'tags.*' => 'string|max:50'
        ]);

        $factor = EmissionFactor::create([
            'name' => $request->name,
            'category' => $request->category,
            'factor_value' => $request->factor_value,
            'unit' => $request->unit,
            'source' => $request->source,
            'year' => $request->year,
            'description' => $request->description,
            'methodology' => $request->methodology,
            'uncertainty_percentage' => $request->uncertainty_percentage,
            'is_active' => $request->boolean('is_active', true),
            'is_verified' => false,
            'tags' => $request->tags ?? [],
            'created_by' => Auth::guard('sanctum')->user()->id
        ]);

        return (new EmissionFactorResource($factor))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified emission factor
     *
     * Obtiene los detalles de un factor de emisión específico.
     *
     * @urlParam emissionFactor int required ID del factor de emisión. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Electricidad España",
     *     "category": "energy",
     *     "factor_value": 0.5,
     *     "unit": "kWh",
     *     "source": "Red Eléctrica de España",
     *     "year": 2024,
     *     "description": "Factor de emisión para electricidad en España",
     *     "is_active": true,
     *     "created_at": "2024-01-15T10:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Factor de emisión no encontrado"
     * }
     */
    public function show(EmissionFactor $emissionFactor): JsonResponse
    {
        return (new EmissionFactorResource($emissionFactor))->response();
    }

    /**
     * Update the specified emission factor
     *
     * Actualiza un factor de emisión existente. Solo administradores pueden modificarlo.
     *
     * @urlParam emissionFactor int required ID del factor de emisión. Example: 1
     * @bodyParam name string Nombre del factor de emisión. Example: Electricidad España Actualizada
     * @bodyParam factor_value number Valor del factor (kg CO2/unit). Example: 0.48
     * @bodyParam description text Descripción del factor. Example: Factor actualizado para 2024
     * @bodyParam is_active boolean Si el factor está activo. Example: true
     * @bodyParam tags array Etiquetas para categorizar. Example: ["electricidad", "españa", "2024", "actualizado"]
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Electricidad España Actualizada",
     *     "factor_value": 0.48,
     *     "updated_at": "2024-01-15T11:00:00.000000Z"
     *   },
     *   "message": "Factor de emisión actualizado exitosamente"
     * }
     *
     * @response 403 {
     *   "message": "No tienes permisos para modificar factores de emisión"
     * }
     *
     * @authenticated
     */
    public function update(Request $request, EmissionFactor $emissionFactor): JsonResponse
    {
        // Verificar permisos de administrador
        if (!Auth::guard('sanctum')->user()->hasRole('admin')) {
            return response()->json([
                'message' => 'No tienes permisos para modificar factores de emisión'
            ], 403);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255|unique:emission_factors,name,' . $emissionFactor->id,
            'factor_value' => 'sometimes|numeric|min:0.001|max:10000',
            'description' => 'sometimes|string|max:1000',
            'is_active' => 'sometimes|boolean',
            'tags' => 'sometimes|array',
            'tags.*' => 'string|max:50'
        ]);

        $emissionFactor->update($request->only([
            'name', 'factor_value', 'description', 'is_active', 'tags'
        ]));

        return (new EmissionFactorResource($emissionFactor))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Remove the specified emission factor
     *
     * Elimina un factor de emisión. Solo administradores pueden eliminarlo.
     *
     * @urlParam emissionFactor int required ID del factor de emisión. Example: 1
     *
     * @response 200 {
     *   "message": "Factor de emisión eliminado exitosamente"
     * }
     *
     * @response 403 {
     *   "message": "No tienes permisos para eliminar factores de emisión"
     * }
     *
     * @response 422 {
     *   "message": "No se puede eliminar un factor de emisión en uso"
     * }
     *
     * @authenticated
     */
    public function destroy(EmissionFactor $emissionFactor): JsonResponse
    {
        // Verificar permisos de administrador
        if (!Auth::guard('sanctum')->user()->hasRole('admin')) {
            return response()->json([
                'message' => 'No tienes permisos para eliminar factores de emisión'
            ], 403);
        }

        // Verificar que no esté en uso
        if ($emissionFactor->carbonCalculations()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar un factor de emisión en uso'
            ], 422);
        }

        $emissionFactor->delete();

        return response()->json([
            'message' => 'Factor de emisión eliminado exitosamente'
        ]);
    }

    /**
     * Get emission factors by category
     *
     * Obtiene todos los factores de emisión de una categoría específica.
     *
     * @urlParam category string required Categoría del factor. Example: energy
     * @queryParam year int Año de validez. Example: 2024
     * @queryParam is_active boolean Solo factores activos. Example: true
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Electricidad España",
     *       "factor_value": 0.5,
     *       "unit": "kWh"
     *     }
     *   ]
     * }
     */
    public function byCategory(string $category): JsonResponse
    {
        $request = request();
        $request->validate([
            'year' => 'sometimes|integer|min:1990|max:2030',
            'is_active' => 'sometimes|boolean'
        ]);

        $query = EmissionFactor::where('category', $category);

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $factors = $query->where('is_verified', true)
            ->orderBy('name')
            ->get();

        return EmissionFactorResource::collection($factors)->response();
    }

    /**
     * Verify emission factor
     *
     * Marca un factor de emisión como verificado. Solo administradores pueden verificarlo.
     *
     * @urlParam emissionFactor int required ID del factor de emisión. Example: 1
     * @bodyParam verification_method string Método de verificación. Example: Expert review
     * @bodyParam verification_notes text Notas de verificación. Example: Revisado por expertos del IPCC
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "is_verified": true,
     *     "verification_method": "Expert review",
     *     "verified_at": "2024-01-15T12:00:00.000000Z"
     *   },
     *   "message": "Factor de emisión verificado exitosamente"
     * }
     *
     * @authenticated
     */
    public function verify(Request $request, EmissionFactor $emissionFactor): JsonResponse
    {
        // Verificar permisos de administrador
        if (!Auth::guard('sanctum')->user()->hasRole('admin')) {
            return response()->json([
                'message' => 'No tienes permisos para verificar factores de emisión'
            ], 403);
        }

        $request->validate([
            'verification_method' => 'sometimes|string|max:255',
            'verification_notes' => 'sometimes|string|max:1000'
        ]);

        $emissionFactor->update([
            'is_verified' => true,
            'verification_method' => $request->verification_method,
            'verification_notes' => $request->verification_notes,
            'verified_at' => now(),
            'verified_by' => Auth::guard('sanctum')->user()->id
        ]);

        return (new EmissionFactorResource($emissionFactor))
            ->response()
            ->setStatusCode(200);
    }
}
