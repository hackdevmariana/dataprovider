<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\EnergyCertificateResource;
use App\Models\EnergyCertificate;
use App\Models\EnergyInstallation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * @group Energy Certificates
 *
 * APIs para la gestión de certificados de energía renovable.
 * Permite a los usuarios generar, gestionar y verificar
 * certificados de origen de energía renovable (GAR, REC, etc.).
 */
class EnergyCertificateController extends Controller
{
    /**
     * Display a listing of energy certificates
     *
     * Obtiene una lista de certificados de energía con opciones de filtrado.
     *
     * @queryParam user_id int ID del usuario propietario. Example: 1
     * @queryParam installation_id int ID de la instalación energética. Example: 2
     * @queryParam certificate_type string Tipo de certificado (GAR, REC, I-REC, TIGR). Example: GAR
     * @queryParam energy_type string Tipo de energía (solar, wind, hydro, biomass). Example: solar
     * @queryParam status string Estado del certificado (pending, active, expired, cancelled, verified). Example: active
     * @queryParam date_from date Fecha de inicio (YYYY-MM-DD). Example: 2024-01-01
     * @queryParam date_to date Fecha de fin (YYYY-MM-DD). Example: 2024-12-31
     * @queryParam min_kwh int Cantidad mínima en kWh. Example: 100
     * @queryParam max_kwh int Cantidad máxima en kWh. Example: 1000
     * @queryParam is_verified boolean Solo certificados verificados. Example: true
     * @queryParam sort string Ordenamiento (recent, oldest, kwh_desc, kwh_asc, expiry_date). Example: recent
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 15
     *
     * @apiResourceCollection App\Http\Resources\V1\EnergyCertificateResource
     * @apiResourceModel App\Models\EnergyCertificate
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'sometimes|integer|exists:users,id',
            'installation_id' => 'sometimes|integer|exists:energy_installations,id',
            'certificate_type' => 'sometimes|string|in:GAR,REC,I-REC,TIGR',
            'energy_type' => 'sometimes|string|in:solar,wind,hydro,biomass',
            'status' => 'sometimes|string|in:pending,active,expired,cancelled,verified',
            'date_from' => 'sometimes|date',
            'date_to' => 'sometimes|date',
            'min_kwh' => 'sometimes|numeric|min:0',
            'max_kwh' => 'sometimes|numeric|min:0',
            'is_verified' => 'sometimes|boolean',
            'sort' => 'sometimes|string|in:recent,oldest,kwh_desc,kwh_asc,expiry_date',
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100'
        ]);

        $query = EnergyCertificate::with(['user', 'installation']);

        // Filtros
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('installation_id')) {
            $query->where('installation_id', $request->installation_id);
        }

        if ($request->filled('certificate_type')) {
            $query->where('certificate_type', $request->certificate_type);
        }

        if ($request->filled('energy_type')) {
            $query->where('energy_type', $request->energy_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('generation_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('generation_date', '<=', $request->date_to);
        }

        if ($request->filled('min_kwh')) {
            $query->where('kwh_amount', '>=', $request->min_kwh);
        }

        if ($request->filled('max_kwh')) {
            $query->where('kwh_amount', '<=', $request->max_kwh);
        }

        if ($request->filled('is_verified')) {
            $query->where('is_verified', $request->boolean('is_verified'));
        }

        // Ordenamiento
        $sort = $request->get('sort', 'recent');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('generation_date', 'asc');
                break;
            case 'kwh_desc':
                $query->orderBy('kwh_amount', 'desc');
                break;
            case 'kwh_asc':
                $query->orderBy('kwh_amount', 'asc');
                break;
            case 'expiry_date':
                $query->orderBy('expiry_date', 'asc');
                break;
            default: // recent
                $query->orderBy('generation_date', 'desc');
        }

        $perPage = min($request->get('per_page', 15), 100);
        $certificates = $query->paginate($perPage);

        return EnergyCertificateResource::collection($certificates)->response();
    }

    /**
     * Store a newly created energy certificate
     *
     * Crea un nuevo certificado de energía renovable.
     *
     * @bodyParam installation_id int required ID de la instalación energética. Example: 2
     * @bodyParam certificate_type string required Tipo de certificado. Example: GAR
     * @bodyParam kwh_amount number required Cantidad de energía en kWh. Example: 500
     * @bodyParam energy_type string Tipo de energía. Example: solar
     * @bodyParam generation_date date Fecha de generación (YYYY-MM-DD). Example: 2024-01-15
     * @bodyParam expiry_date date Fecha de expiración (YYYY-MM-DD). Example: 2025-01-15
     * @bodyParam certificate_number string Número de certificado. Example: GAR-2024-001
     * @bodyParam description text Descripción del certificado. Example: Certificado de energía solar generada
     * @bodyParam metadata json Metadatos adicionales. Example: {"grid": "peninsular", "region": "madrid"}
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "user_id": 1,
     *     "installation_id": 2,
     *     "certificate_type": "GAR",
     *     "kwh_amount": 500,
     *     "status": "pending",
     *     "generation_date": "2024-01-15T00:00:00.000000Z",
     *     "created_at": "2024-01-15T10:00:00.000000Z"
     *   },
     *   "message": "Certificado de energía creado exitosamente"
     * }
     *
     * @response 422 {
     *   "message": "La instalación no pertenece al usuario",
     *   "errors": {
     *     "installation_id": ["La instalación especificada no pertenece al usuario"]
     *   }
     * }
     *
     * @authenticated
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'installation_id' => 'required|integer|exists:energy_installations,id',
            'certificate_type' => 'required|string|in:GAR,REC,I-REC,TIGR',
            'kwh_amount' => 'required|numeric|min:0.001',
            'energy_type' => 'sometimes|string|in:solar,wind,hydro,biomass',
            'generation_date' => 'sometimes|date',
            'expiry_date' => 'sometimes|date|after:generation_date',
            'certificate_number' => 'sometimes|string|max:100|unique:energy_certificates,certificate_number',
            'description' => 'sometimes|string|max:1000',
            'metadata' => 'sometimes|json'
        ]);

        $userId = Auth::guard('sanctum')->user()->id;

        // Verificar que la instalación pertenece al usuario
        $installation = EnergyInstallation::where('id', $request->installation_id)
            ->where('owner_id', $userId)
            ->first();

        if (!$installation) {
            throw ValidationException::withMessages([
                'installation_id' => ['La instalación especificada no pertenece al usuario']
            ]);
        }

        // Generar número de certificado si no se proporciona
        $certificateNumber = $request->certificate_number;
        if (!$certificateNumber) {
            $certificateNumber = $this->generateCertificateNumber($request->certificate_type);
        }

        // Calcular fecha de expiración si no se proporciona
        $expiryDate = $request->expiry_date;
        if (!$expiryDate) {
            $expiryDate = now()->addYear();
        }

        $certificate = EnergyCertificate::create([
            'user_id' => $userId,
            'installation_id' => $request->installation_id,
            'certificate_type' => $request->certificate_type,
            'kwh_amount' => $request->kwh_amount,
            'energy_type' => $request->energy_type ?? $installation->type,
            'generation_date' => $request->generation_date ?? now(),
            'expiry_date' => $expiryDate,
            'certificate_number' => $certificateNumber,
            'description' => $request->description,
            'status' => 'pending',
            'is_verified' => false,
            'metadata' => $request->metadata ?? []
        ]);

        return (new EnergyCertificateResource($certificate))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified energy certificate
     *
     * Obtiene los detalles de un certificado de energía específico.
     *
     * @urlParam energyCertificate int required ID del certificado. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "user_id": 1,
     *     "installation_id": 2,
     *     "certificate_type": "GAR",
     *     "kwh_amount": 500,
     *     "energy_type": "solar",
     *     "status": "active",
     *     "generation_date": "2024-01-15T00:00:00.000000Z",
     *     "expiry_date": "2025-01-15T00:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Certificado de energía no encontrado"
     * }
     */
    public function show(EnergyCertificate $energyCertificate): JsonResponse
    {
        $energyCertificate->load(['user', 'installation']);
        return (new EnergyCertificateResource($energyCertificate))->response();
    }

    /**
     * Update the specified energy certificate
     *
     * Actualiza un certificado de energía existente. Solo el propietario puede modificarlo
     * y solo si está pendiente.
     *
     * @urlParam energyCertificate int required ID del certificado. Example: 1
     * @bodyParam kwh_amount number Cantidad de energía en kWh. Example: 450
     * @bodyParam description text Descripción del certificado. Example: Certificado actualizado
     * @bodyParam metadata json Metadatos adicionales. Example: {"grid": "peninsular", "region": "madrid", "updated": true}
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "kwh_amount": 450,
     *     "updated_at": "2024-01-15T11:00:00.000000Z"
     *   },
     *   "message": "Certificado de energía actualizado exitosamente"
     * }
     *
     * @response 403 {
     *   "message": "No tienes permiso para modificar este certificado"
     * }
     *
     * @response 422 {
     *   "message": "No se puede modificar un certificado activo"
     * }
     *
     * @authenticated
     */
    public function update(Request $request, EnergyCertificate $energyCertificate): JsonResponse
    {
        // Verificar permisos
        if ($energyCertificate->user_id !== Auth::guard('sanctum')->user()->id) {
            return response()->json([
                'message' => 'No tienes permiso para modificar este certificado'
            ], 403);
        }

        if ($energyCertificate->status !== 'pending') {
            return response()->json([
                'message' => 'No se puede modificar un certificado activo'
            ], 422);
        }

        $request->validate([
            'kwh_amount' => 'sometimes|numeric|min:0.001',
            'description' => 'sometimes|string|max:1000',
            'metadata' => 'sometimes|json'
        ]);

        $energyCertificate->update($request->only([
            'kwh_amount', 'description', 'metadata'
        ]));

        return (new EnergyCertificateResource($energyCertificate))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Remove the specified energy certificate
     *
     * Elimina un certificado de energía. Solo el propietario puede eliminarlo
     * y solo si está pendiente.
     *
     * @urlParam energyCertificate int required ID del certificado. Example: 1
     *
     * @response 200 {
     *   "message": "Certificado de energía eliminado exitosamente"
     * }
     *
     * @response 403 {
     *   "message": "No tienes permiso para eliminar este certificado"
     * }
     *
     * @response 422 {
     *   "message": "No se puede eliminar un certificado activo"
     * }
     *
     * @authenticated
     */
    public function destroy(EnergyCertificate $energyCertificate): JsonResponse
    {
        // Verificar permisos
        if ($energyCertificate->user_id !== Auth::guard('sanctum')->user()->id) {
            return response()->json([
                'message' => 'No tienes permiso para eliminar este certificado'
            ], 403);
        }

        if ($energyCertificate->status !== 'pending') {
            return response()->json([
                'message' => 'No se puede eliminar un certificado activo'
            ], 422);
        }

        $energyCertificate->delete();

        return response()->json([
            'message' => 'Certificado de energía eliminado exitosamente'
        ]);
    }

    /**
     * Verify energy certificate
     *
     * Marca un certificado como verificado. Solo administradores pueden verificarlo.
     *
     * @urlParam energyCertificate int required ID del certificado. Example: 1
     * @bodyParam verification_method string Método de verificación. Example: Automated system
     * @bodyParam verification_notes text Notas de verificación. Example: Verificado por sistema automático
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "is_verified": true,
     *     "verification_method": "Automated system",
     *     "verified_at": "2024-01-15T12:00:00.000000Z"
     *   },
     *   "message": "Certificado verificado exitosamente"
     * }
     *
     * @authenticated
     */
    public function verify(Request $request, EnergyCertificate $energyCertificate): JsonResponse
    {
        // Verificar permisos de administrador
        if (!Auth::guard('sanctum')->user()->hasRole('admin')) {
            return response()->json([
                'message' => 'No tienes permisos para verificar certificados'
            ], 403);
        }

        $request->validate([
            'verification_method' => 'sometimes|string|max:255',
            'verification_notes' => 'sometimes|string|max:1000'
        ]);

        $energyCertificate->update([
            'is_verified' => true,
            'verification_method' => $request->verification_method,
            'verification_notes' => $request->verification_notes,
            'verified_at' => now(),
            'verified_by' => Auth::guard('sanctum')->user()->id,
            'status' => 'active'
        ]);

        return (new EnergyCertificateResource($energyCertificate))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Get user's certificate summary
     *
     * Obtiene un resumen de los certificados de energía del usuario.
     *
     * @queryParam period string Período de tiempo (month, quarter, year, all). Example: year
     * @queryParam certificate_type string Filtrar por tipo de certificado. Example: GAR
     *
     * @response 200 {
     *   "data": {
     *     "total_certificates": 15,
     *     "total_kwh": 7500,
     *     "by_type": {
     *       "GAR": 10,
     *       "REC": 5
     *     },
     *     "by_energy_type": {
     *       "solar": 5000,
     *       "wind": 2500
     *     },
     *     "by_status": {
     *       "active": 12,
     *       "pending": 3
     *     }
     *   }
     * }
     *
     * @authenticated
     */
    public function summary(Request $request): JsonResponse
    {
        $request->validate([
            'period' => 'sometimes|string|in:month,quarter,year,all',
            'certificate_type' => 'sometimes|string|in:GAR,REC,I-REC,TIGR'
        ]);

        $userId = Auth::guard('sanctum')->user()->id;
        $query = EnergyCertificate::where('user_id', $userId);

        // Filtrar por período
        $period = $request->get('period', 'year');
        switch ($period) {
            case 'month':
                $query->whereDate('generation_date', '>=', now()->startOfMonth());
                break;
            case 'quarter':
                $query->whereDate('generation_date', '>=', now()->startOfQuarter());
                break;
            case 'year':
                $query->whereDate('generation_date', '>=', now()->startOfYear());
                break;
            // 'all' no aplica filtro de fecha
        }

        if ($request->filled('certificate_type')) {
            $query->where('certificate_type', $request->certificate_type);
        }

        $certificates = $query->get();

        $totalCertificates = $certificates->count();
        $totalKwh = $certificates->sum('kwh_amount');

        $byType = $certificates->groupBy('certificate_type')
            ->map(function ($group) {
                return $group->count();
            });

        $byEnergyType = $certificates->groupBy('energy_type')
            ->map(function ($group) {
                return $group->sum('kwh_amount');
            });

        $byStatus = $certificates->groupBy('status')
            ->map(function ($group) {
                return $group->count();
            });

        return response()->json([
            'data' => [
                'total_certificates' => $totalCertificates,
                'total_kwh' => round($totalKwh, 2),
                'by_type' => $byType,
                'by_energy_type' => $byEnergyType->map(function ($value) {
                    return round($value, 2);
                }),
                'by_status' => $byStatus,
                'period' => $period
            ]
        ]);
    }

    /**
     * Generate unique certificate number
     *
     * Genera un número único de certificado basado en el tipo.
     */
    private function generateCertificateNumber(string $certificateType): string
    {
        $year = now()->year;
        $count = EnergyCertificate::where('certificate_type', $certificateType)
            ->whereYear('created_at', $year)
            ->count();

        return sprintf('%s-%d-%03d', $certificateType, $year, $count + 1);
    }
}
