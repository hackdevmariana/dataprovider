<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\EnergyTransactionResource;
use App\Models\EnergyTransaction;
use App\Models\EnergyInstallation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * @group Energy Transactions
 *
 * APIs para la gestión de transacciones energéticas.
 * Permite a los usuarios registrar y consultar transacciones
 * de compra, venta e intercambio de energía renovable.
 */
/**
 * @OA\Tag(
 *     name="Transacciones Energéticas",
 *     description="APIs para la gestión de Transacciones Energéticas"
 * )
 */
class EnergyTransactionController extends Controller
{
    /**
     * Display a listing of energy transactions
     *
     * Obtiene una lista de transacciones energéticas con opciones de filtrado.
     *
     * @queryParam user_id int ID del usuario propietario. Example: 1
     * @queryParam installation_id int ID de la instalación energética. Example: 2
     * @queryParam transaction_type string Tipo de transacción (buy, sell, exchange, donation). Example: buy
     * @queryParam energy_type string Tipo de energía (solar, wind, hydro, biomass). Example: solar
     * @queryParam status string Estado de la transacción (pending, completed, cancelled, failed). Example: completed
     * @queryParam date_from date Fecha de inicio (YYYY-MM-DD). Example: 2024-01-01
     * @queryParam date_to date Fecha de fin (YYYY-MM-DD). Example: 2024-12-31
     * @queryParam min_kwh int Cantidad mínima en kWh. Example: 100
     * @queryParam max_kwh int Cantidad máxima en kWh. Example: 1000
     * @queryParam sort string Ordenamiento (recent, oldest, kwh_desc, kwh_asc, price_desc, price_asc). Example: recent
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 15
     *
     * @apiResourceCollection App\Http\Resources\V1\EnergyTransactionResource
     * @apiResourceModel App\Models\EnergyTransaction
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'sometimes|integer|exists:users,id',
            'installation_id' => 'sometimes|integer|exists:energy_installations,id',
            'transaction_type' => 'sometimes|string|in:buy,sell,exchange,donation',
            'energy_type' => 'sometimes|string|in:solar,wind,hydro,biomass',
            'status' => 'sometimes|string|in:pending,completed,cancelled,failed',
            'date_from' => 'sometimes|date',
            'date_to' => 'sometimes|date',
            'min_kwh' => 'sometimes|numeric|min:0',
            'max_kwh' => 'sometimes|numeric|min:0',
            'sort' => 'sometimes|string|in:recent,oldest,kwh_desc,kwh_asc,price_desc,price_asc',
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100'
        ]);

        $query = EnergyTransaction::with(['user', 'installation', 'counterparty']);

        // Filtros
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('installation_id')) {
            $query->where('installation_id', $request->installation_id);
        }

        if ($request->filled('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }

        if ($request->filled('energy_type')) {
            $query->where('energy_type', $request->energy_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', $request->date_to);
        }

        if ($request->filled('min_kwh')) {
            $query->where('kwh_amount', '>=', $request->min_kwh);
        }

        if ($request->filled('max_kwh')) {
            $query->where('kwh_amount', '<=', $request->max_kwh);
        }

        // Ordenamiento
        $sort = $request->get('sort', 'recent');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('transaction_date', 'asc');
                break;
            case 'kwh_desc':
                $query->orderBy('kwh_amount', 'desc');
                break;
            case 'kwh_asc':
                $query->orderBy('kwh_amount', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price_per_kwh', 'desc');
                break;
            case 'price_asc':
                $query->orderBy('price_per_kwh', 'asc');
                break;
            default: // recent
                $query->orderBy('transaction_date', 'desc');
        }

        $perPage = min($request->get('per_page', 15), 100);
        $transactions = $query->paginate($perPage);

        return EnergyTransactionResource::collection($transactions)->response();
    }

    /**
     * Store a newly created energy transaction
     *
     * Crea una nueva transacción energética.
     *
     * @bodyParam installation_id int required ID de la instalación energética. Example: 2
     * @bodyParam transaction_type string required Tipo de transacción. Example: sell
     * @bodyParam kwh_amount number required Cantidad de energía en kWh. Example: 500
     * @bodyParam price_per_kwh number Precio por kWh en euros. Example: 0.15
     * @bodyParam counterparty_id int ID de la contraparte (comprador/vendedor). Example: 3
     * @bodyParam energy_type string Tipo de energía. Example: solar
     * @bodyParam transaction_date date Fecha de la transacción (YYYY-MM-DD). Example: 2024-01-15
     * @bodyParam description text Descripción de la transacción. Example: Venta de excedente solar
     * @bodyParam is_verified boolean Si la transacción está verificada. Example: false
     * @bodyParam metadata json Metadatos adicionales. Example: {"certificate": "GAR-001", "grid": "peninsular"}
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "user_id": 1,
     *     "installation_id": 2,
     *     "transaction_type": "sell",
     *     "kwh_amount": 500,
     *     "price_per_kwh": 0.15,
     *     "status": "pending",
     *     "created_at": "2024-01-15T10:00:00.000000Z"
     *   },
     *   "message": "Transacción energética creada exitosamente"
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
            'transaction_type' => 'required|string|in:buy,sell,exchange,donation',
            'kwh_amount' => 'required|numeric|min:0.001',
            'price_per_kwh' => 'sometimes|numeric|min:0|max:10',
            'counterparty_id' => 'sometimes|integer|exists:users,id',
            'energy_type' => 'sometimes|string|in:solar,wind,hydro,biomass',
            'transaction_date' => 'sometimes|date',
            'description' => 'sometimes|string|max:1000',
            'is_verified' => 'sometimes|boolean',
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

        $transaction = EnergyTransaction::create([
            'user_id' => $userId,
            'installation_id' => $request->installation_id,
            'transaction_type' => $request->transaction_type,
            'kwh_amount' => $request->kwh_amount,
            'price_per_kwh' => $request->price_per_kwh ?? 0,
            'counterparty_id' => $request->counterparty_id,
            'energy_type' => $request->energy_type ?? $installation->type,
            'transaction_date' => $request->transaction_date ?? now(),
            'description' => $request->description,
            'status' => 'pending',
            'is_verified' => $request->boolean('is_verified', false),
            'metadata' => $request->metadata ?? [],
            'total_amount_eur' => ($request->price_per_kwh ?? 0) * $request->kwh_amount
        ]);

        return (new EnergyTransactionResource($transaction))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified energy transaction
     *
     * Obtiene los detalles de una transacción energética específica.
     *
     * @urlParam energyTransaction int required ID de la transacción. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "user_id": 1,
     *     "installation_id": 2,
     *     "transaction_type": "sell",
     *     "kwh_amount": 500,
     *     "price_per_kwh": 0.15,
     *     "status": "completed",
     *     "transaction_date": "2024-01-15T10:00:00.000000Z"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Transacción energética no encontrada"
     * }
     */
    public function show(EnergyTransaction $energyTransaction): JsonResponse
    {
        $energyTransaction->load(['user', 'installation', 'counterparty']);
        return (new EnergyTransactionResource($energyTransaction))->response();
    }

    /**
     * Update the specified energy transaction
     *
     * Actualiza una transacción energética existente. Solo el propietario puede modificarla
     * y solo si está pendiente.
     *
     * @urlParam energyTransaction int required ID de la transacción. Example: 1
     * @bodyParam kwh_amount number Cantidad de energía en kWh. Example: 450
     * @bodyParam price_per_kwh number Precio por kWh en euros. Example: 0.18
     * @bodyParam description text Descripción de la transacción. Example: Transacción actualizada
     * @bodyParam metadata json Metadatos adicionales. Example: {"certificate": "GAR-002"}
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "kwh_amount": 450,
     *     "price_per_kwh": 0.18,
     *     "total_amount_eur": 81.00,
     *     "updated_at": "2024-01-15T11:00:00.000000Z"
     *   },
     *   "message": "Transacción energética actualizada exitosamente"
     * }
     *
     * @response 403 {
     *   "message": "No tienes permiso para modificar esta transacción"
     * }
     *
     * @response 422 {
     *   "message": "No se puede modificar una transacción completada"
     * }
     *
     * @authenticated
     */
    public function update(Request $request, EnergyTransaction $energyTransaction): JsonResponse
    {
        // Verificar permisos
        if ($energyTransaction->user_id !== Auth::guard('sanctum')->user()->id) {
            return response()->json([
                'message' => 'No tienes permiso para modificar esta transacción'
            ], 403);
        }

        if ($energyTransaction->status !== 'pending') {
            return response()->json([
                'message' => 'No se puede modificar una transacción completada'
            ], 422);
        }

        $request->validate([
            'kwh_amount' => 'sometimes|numeric|min:0.001',
            'price_per_kwh' => 'sometimes|numeric|min:0|max:10',
            'description' => 'sometimes|string|max:1000',
            'metadata' => 'sometimes|json'
        ]);

        // Recalcular monto total si se actualiza cantidad o precio
        $totalAmount = $energyTransaction->total_amount_eur;
        if ($request->filled('kwh_amount') || $request->filled('price_per_kwh')) {
            $kwhAmount = $request->get('kwh_amount', $energyTransaction->kwh_amount);
            $pricePerKwh = $request->get('price_per_kwh', $energyTransaction->price_per_kwh);
            $totalAmount = $kwhAmount * $pricePerKwh;
        }

        $energyTransaction->update(array_merge($request->only([
            'kwh_amount', 'price_per_kwh', 'description', 'metadata'
        ]), ['total_amount_eur' => $totalAmount]));

        return (new EnergyTransactionResource($energyTransaction))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Remove the specified energy transaction
     *
     * Elimina una transacción energética. Solo el propietario puede eliminarla
     * y solo si está pendiente.
     *
     * @urlParam energyTransaction int required ID de la transacción. Example: 1
     *
     * @response 200 {
     *   "message": "Transacción energética eliminada exitosamente"
     * }
     *
     * @response 403 {
     *   "message": "No tienes permiso para eliminar esta transacción"
     * }
     *
     * @response 422 {
     *   "message": "No se puede eliminar una transacción completada"
     * }
     *
     * @authenticated
     */
    public function destroy(EnergyTransaction $energyTransaction): JsonResponse
    {
        // Verificar permisos
        if ($energyTransaction->user_id !== Auth::guard('sanctum')->user()->id) {
            return response()->json([
                'message' => 'No tienes permiso para eliminar esta transacción'
            ], 403);
        }

        if ($energyTransaction->status !== 'pending') {
            return response()->json([
                'message' => 'No se puede eliminar una transacción completada'
            ], 422);
        }

        $energyTransaction->delete();

        return response()->json([
            'message' => 'Transacción energética eliminada exitosamente'
        ]);
    }

    /**
     * Complete energy transaction
     *
     * Marca una transacción como completada. Solo el propietario puede completarla.
     *
     * @urlParam energyTransaction int required ID de la transacción. Example: 1
     * @bodyParam completion_notes text Notas de finalización. Example: Transacción completada exitosamente
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "status": "completed",
     *     "completed_at": "2024-01-15T12:00:00.000000Z"
     *   },
     *   "message": "Transacción completada exitosamente"
     * }
     *
     * @authenticated
     */
    public function complete(Request $request, EnergyTransaction $energyTransaction): JsonResponse
    {
        // Verificar permisos
        if ($energyTransaction->user_id !== Auth::guard('sanctum')->user()->id) {
            return response()->json([
                'message' => 'No tienes permiso para completar esta transacción'
            ], 403);
        }

        if ($energyTransaction->status !== 'pending') {
            return response()->json([
                'message' => 'La transacción ya no está pendiente'
            ], 422);
        }

        $request->validate([
            'completion_notes' => 'sometimes|string|max:1000'
        ]);

        $energyTransaction->update([
            'status' => 'completed',
            'completed_at' => now(),
            'completion_notes' => $request->completion_notes
        ]);

        return (new EnergyTransactionResource($energyTransaction))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Cancel energy transaction
     *
     * Cancela una transacción pendiente.
     *
     * @urlParam energyTransaction int required ID de la transacción. Example: 1
     * @bodyParam cancellation_reason text Razón de la cancelación. Example: Cambio de planes
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "status": "cancelled",
     *     "cancelled_at": "2024-01-15T12:00:00.000000Z"
     *   },
     *   "message": "Transacción cancelada exitosamente"
     * }
     *
     * @authenticated
     */
    public function cancel(Request $request, EnergyTransaction $energyTransaction): JsonResponse
    {
        // Verificar permisos (solo el autor puede cancelar)
        if ($energyTransaction->user_id !== Auth::guard('sanctum')->user()->id) {
            return response()->json([
                'message' => 'No tienes permiso para cancelar esta transacción'
            ], 403);
        }

        if ($energyTransaction->status !== 'pending') {
            return response()->json([
                'message' => 'Solo se pueden cancelar transacciones pendientes'
            ], 422);
        }

        $request->validate([
            'cancellation_reason' => 'sometimes|string|max:1000'
        ]);

        $energyTransaction->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $request->cancellation_reason
        ]);

        return (new EnergyTransactionResource($energyTransaction))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Get user's energy transaction summary
     *
     * Obtiene un resumen de las transacciones energéticas del usuario.
     *
     * @queryParam period string Período de tiempo (month, quarter, year, all). Example: year
     * @queryParam transaction_type string Filtrar por tipo de transacción. Example: sell
     *
     * @response 200 {
     *   "data": {
     *     "total_transactions": 25,
     *     "total_kwh": 12500,
     *     "total_amount_eur": 1875.00,
     *     "by_type": {
     *       "sell": 20,
     *       "buy": 5
     *     },
     *     "by_energy_type": {
     *       "solar": 10000,
     *       "wind": 2500
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
            'transaction_type' => 'sometimes|string|in:buy,sell,exchange,donation'
        ]);

        $userId = Auth::guard('sanctum')->user()->id;
        $query = EnergyTransaction::where('user_id', $userId);

        // Filtrar por período
        $period = $request->get('period', 'year');
        switch ($period) {
            case 'month':
                $query->whereDate('transaction_date', '>=', now()->startOfMonth());
                break;
            case 'quarter':
                $query->whereDate('transaction_date', '>=', now()->startOfQuarter());
                break;
            case 'year':
                $query->whereDate('transaction_date', '>=', now()->startOfYear());
                break;
            // 'all' no aplica filtro de fecha
        }

        if ($request->filled('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }

        $transactions = $query->get();

        $totalTransactions = $transactions->count();
        $totalKwh = $transactions->sum('kwh_amount');
        $totalAmount = $transactions->sum('total_amount_eur');

        $byType = $transactions->groupBy('transaction_type')
            ->map(function ($group) {
                return $group->count();
            });

        $byEnergyType = $transactions->groupBy('energy_type')
            ->map(function ($group) {
                return $group->sum('kwh_amount');
            });

        return response()->json([
            'data' => [
                'total_transactions' => $totalTransactions,
                'total_kwh' => round($totalKwh, 2),
                'total_amount_eur' => round($totalAmount, 2),
                'by_type' => $byType,
                'by_energy_type' => $byEnergyType->map(function ($value) {
                    return round($value, 2);
                }),
                'period' => $period,
                'average_price_per_kwh' => $totalKwh > 0 ? round($totalAmount / $totalKwh, 4) : 0
            ]
        ]);
    }
}
