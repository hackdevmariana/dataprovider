<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ReputationTransactionResource;
use App\Models\ReputationTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group Reputation Transactions
 *
 * APIs para la gestión de transacciones del sistema de reputación.
 * Registra todos los cambios de reputación (ganancias, pérdidas, transferencias)
 * para mantener un historial completo y transparente.
 */
class ReputationTransactionController extends Controller
{
    /**
     * Display a listing of reputation transactions
     *
     * Obtiene una lista de transacciones de reputación con opciones de filtrado.
     *
     * @queryParam user_id int ID del usuario para filtrar transacciones. Example: 1
     * @queryParam transaction_type string Tipo de transacción (earned, lost, transferred, bonus, penalty, adjustment). Example: earned
     * @queryParam source_type string Tipo de fuente (post, comment, vote, badge, endorsement, review, admin). Example: post
     * @queryParam source_id int ID de la fuente de la transacción. Example: 1
     * @queryParam min_amount int Cantidad mínima de reputación. Example: 10
     * @queryParam max_amount int Cantidad máxima de reputación. Example: 100
     * @queryParam date_from date Fecha de inicio (YYYY-MM-DD). Example: 2024-01-01
     * @queryParam date_to date Fecha de fin (YYYY-MM-DD). Example: 2024-12-31
     * @queryParam sort string Ordenamiento (recent, oldest, amount_desc, amount_asc). Example: recent
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 15
     *
     * @apiResourceCollection App\Http\Resources\V1\ReputationTransactionResource
     * @apiResourceModel App\Models\ReputationTransaction
     */
    public function index(Request $request)
    {
        $query = ReputationTransaction::with(['user', 'source'])
            ->when($request->user_id, fn($q, $userId) => $q->where('user_id', $userId))
            ->when($request->transaction_type, fn($q, $type) => $q->where('transaction_type', $type))
            ->when($request->source_type, fn($q, $type) => $q->where('source_type', $type))
            ->when($request->source_id, fn($q, $id) => $q->where('source_id', $id))
            ->when($request->min_amount, fn($q, $amount) => $q->where('amount', '>=', $amount))
            ->when($request->max_amount, fn($q, $amount) => $q->where('amount', '<=', $amount))
            ->when($request->date_from, fn($q, $date) => $q->whereDate('created_at', '>=', $date))
            ->when($request->date_to, fn($q, $date) => $q->whereDate('created_at', '<=', $date));

        // Aplicar ordenamiento
        switch ($request->get('sort', 'recent')) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'amount_desc':
                $query->orderBy('amount', 'desc');
                break;
            case 'amount_asc':
                $query->orderBy('amount', 'asc');
                break;
            default: // recent
                $query->orderBy('created_at', 'desc');
        }

        $perPage = min($request->get('per_page', 15), 100);
        $transactions = $query->paginate($perPage);

        return ReputationTransactionResource::collection($transactions);
    }

    /**
     * Store a new reputation transaction
     *
     * Crea una nueva transacción de reputación.
     *
     * @bodyParam user_id int required ID del usuario. Example: 1
     * @bodyParam transaction_type string required Tipo de transacción (earned, lost, transferred, bonus, penalty, adjustment). Example: earned
     * @bodyParam amount int required Cantidad de reputación (positivo para ganancias, negativo para pérdidas). Example: 10
     * @bodyParam source_type string Tipo de fuente (post, comment, vote, badge, endorsement, review, admin). Example: post
     * @bodyParam source_id int ID de la fuente. Example: 1
     * @bodyParam description string Descripción de la transacción. Example: Post destacado sobre energía solar
     * @bodyParam metadata array Metadatos adicionales. Example: {"post_title": "Guía Solar", "category": "energy"}
     * @bodyParam expires_at datetime Fecha de expiración (para transacciones temporales). Example: 2024-12-31 23:59:59
     *
     * @apiResource App\Http\Resources\V1\ReputationTransactionResource
     * @apiResourceModel App\Models\ReputationTransaction
     *
     * @response 201 {"data": {...}, "message": "Transacción creada exitosamente"}
     * @response 422 {"message": "Datos inválidos", "errors": {...}}
     * @authenticated
     */
    public function store(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'transaction_type' => 'required|in:earned,lost,transferred,bonus,penalty,adjustment',
            'amount' => 'required|integer',
            'source_type' => 'nullable|string|max:100',
            'source_id' => 'nullable|integer',
            'description' => 'nullable|string|max:1000',
            'metadata' => 'nullable|array',
            'expires_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        // Solo administradores pueden crear transacciones manuales
        $user = Auth::guard('sanctum')->user();
        if (!$user->hasRole('admin') && $request->transaction_type === 'adjustment') {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $transaction = ReputationTransaction::create(array_merge($validator->validated(), [
            'created_by' => $user->id,
            'is_processed' => false,
        ]));

        $transaction->load(['user', 'source']);
        return new ReputationTransactionResource($transaction);
    }

    /**
     * Display the specified reputation transaction
     *
     * Muestra una transacción específica.
     *
     * @urlParam reputationTransaction int required ID de la transacción. Example: 1
     *
     * @apiResource App\Http\Resources\V1\ReputationTransactionResource
     * @apiResourceModel App\Models\ReputationTransaction
     *
     * @response 404 {"message": "Transacción no encontrada"}
     */
    public function show(ReputationTransaction $reputationTransaction)
    {
        $reputationTransaction->load(['user', 'source']);
        return new ReputationTransactionResource($reputationTransaction);
    }

    /**
     * Update the specified reputation transaction
     *
     * Actualiza una transacción existente (solo si no ha sido procesada).
     *
     * @urlParam reputationTransaction int required ID de la transacción. Example: 1
     * @bodyParam description string Nueva descripción. Example: Descripción actualizada
     * @bodyParam metadata array Metadatos actualizados. Example: {"updated": true}
     * @bodyParam expires_at datetime Nueva fecha de expiración. Example: 2024-12-31 23:59:59
     *
     * @apiResource App\Http\Resources\V1\ReputationTransactionResource
     * @apiResourceModel App\Models\ReputationTransaction
     *
     * @response 403 {"message": "No autorizado"}
     * @response 422 {"message": "No se puede modificar una transacción procesada"}
     * @authenticated
     */
    public function update(Request $request, ReputationTransaction $reputationTransaction)
    {
        // Solo administradores pueden modificar transacciones
        $user = Auth::guard('sanctum')->user();
        if (!$user->hasRole('admin')) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        if ($reputationTransaction->is_processed) {
            return response()->json([
                'message' => 'No se puede modificar una transacción procesada'
            ], 422);
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'description' => 'nullable|string|max:1000',
            'metadata' => 'nullable|array',
            'expires_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $reputationTransaction->update($validator->validated());
        $reputationTransaction->load(['user', 'source']);

        return new ReputationTransactionResource($reputationTransaction);
    }

    /**
     * Remove the specified reputation transaction
     *
     * Elimina una transacción (solo si no ha sido procesada).
     *
     * @urlParam reputationTransaction int required ID de la transacción. Example: 1
     *
     * @response 200 {"message": "Transacción eliminada exitosamente"}
     * @response 403 {"message": "No autorizado"}
     * @response 422 {"message": "No se puede eliminar una transacción procesada"}
     * @authenticated
     */
    public function destroy(ReputationTransaction $reputationTransaction)
    {
        // Solo administradores pueden eliminar transacciones
        $user = Auth::guard('sanctum')->user();
        if (!$user->hasRole('admin')) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        if ($reputationTransaction->is_processed) {
            return response()->json([
                'message' => 'No se puede eliminar una transacción procesada'
            ], 422);
        }

        $reputationTransaction->delete();

        return response()->json(['message' => 'Transacción eliminada exitosamente']);
    }

    /**
     * Get user's reputation history
     *
     * Obtiene el historial completo de reputación de un usuario.
     *
     * @urlParam user int required ID del usuario. Example: 1
     * @queryParam transaction_type string Filtrar por tipo. Example: earned
     * @queryParam date_from date Fecha de inicio. Example: 2024-01-01
     * @queryParam date_to date Fecha de fin. Example: 2024-12-31
     * @queryParam per_page int Cantidad por página. Example: 15
     *
     * @apiResourceCollection App\Http\Resources\V1\ReputationTransactionResource
     * @apiResourceModel App\Models\ReputationTransaction
     */
    public function userHistory(Request $request, User $user)
    {
        $query = $user->reputationTransactions()
            ->with(['user', 'source'])
            ->when($request->transaction_type, fn($q, $type) => $q->where('transaction_type', $type))
            ->when($request->date_from, fn($q, $date) => $q->whereDate('created_at', '>=', $date))
            ->when($request->date_to, fn($q, $date) => $q->whereDate('created_at', '<=', $date))
            ->orderBy('created_at', 'desc');

        $perPage = min($request->get('per_page', 15), 100);
        $transactions = $query->paginate($perPage);

        return ReputationTransactionResource::collection($transactions);
    }

    /**
     * Process pending transactions
     *
     * Procesa todas las transacciones pendientes de reputación.
     * Solo para administradores del sistema.
     *
     * @response 200 {"message": "Transacciones procesadas", "processed_count": 5}
     * @response 403 {"message": "No autorizado"}
     * @authenticated
     */
    public function processPending()
    {
        // Solo administradores pueden procesar transacciones
        $user = Auth::guard('sanctum')->user();
        if (!$user->hasRole('admin')) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $pendingTransactions = ReputationTransaction::where('is_processed', false)->get();
        $processedCount = 0;

        foreach ($pendingTransactions as $transaction) {
            // Procesar la transacción
            $user = User::find($transaction->user_id);
            if ($user) {
                $user->reputation()->increment('score', $transaction->amount);
                $transaction->update(['is_processed' => true, 'processed_at' => now()]);
                $processedCount++;
            }
        }

        return response()->json([
            'message' => 'Transacciones procesadas',
            'processed_count' => $processedCount
        ]);
    }
}
