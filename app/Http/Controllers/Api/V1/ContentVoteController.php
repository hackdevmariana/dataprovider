<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ContentVoteResource;
use App\Models\ContentVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * @group Content Votes
 *
 * APIs para el sistema de votaciones de contenido (upvote/downvote).
 * Similar al sistema de Reddit o Stack Overflow.
 */
class ContentVoteController extends Controller
{
    /**
     * Display a listing of votes
     *
     * Obtiene una lista de votos con opciones de filtrado.
     *
     * @queryParam user_id int ID del usuario que votó. Example: 1
     * @queryParam votable_type string Tipo de contenido votado. Example: App\Models\TopicPost
     * @queryParam votable_id int ID del contenido votado. Example: 1
     * @queryParam vote_type string Tipo de voto (upvote, downvote). Example: upvote
     * @queryParam page int Número de página. Example: 1
     * @queryParam per_page int Cantidad por página (máx 100). Example: 15
     *
     * @apiResourceCollection App\Http\Resources\V1\ContentVoteResource
     * @apiResourceModel App\Models\ContentVote
     */
    public function index(Request $request)
    {
        $query = ContentVote::with(['user'])
            ->when($request->user_id, fn($q, $userId) => $q->where('user_id', $userId))
            ->when($request->votable_type, fn($q, $type) => $q->where('votable_type', $type))
            ->when($request->votable_id, fn($q, $id) => $q->where('votable_id', $id))
            ->when($request->vote_type, fn($q, $type) => $q->where('vote_type', $type))
            ->orderBy('voted_at', 'desc');

        $perPage = min($request->get('per_page', 15), 100);
        $votes = $query->paginate($perPage);

        return ContentVoteResource::collection($votes);
    }

    /**
     * Store a new vote
     *
     * Crea un nuevo voto o actualiza uno existente.
     *
     * @bodyParam votable_type string required Tipo de contenido a votar. Example: App\Models\TopicPost
     * @bodyParam votable_id int required ID del contenido a votar. Example: 1
     * @bodyParam vote_type string required Tipo de voto (upvote, downvote). Example: upvote
     * @bodyParam reason string Razón del voto (opcional). Example: Muy útil
     *
     * @apiResource App\Http\Resources\V1\ContentVoteResource
     * @apiResourceModel App\Models\ContentVote
     *
     * @response 201 {"data": {...}, "message": "Voto registrado exitosamente"}
     * @response 200 {"data": {...}, "message": "Voto actualizado exitosamente"}
     * @response 422 {"message": "Datos inválidos", "errors": {...}}
     * @authenticated
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'votable_type' => 'required|string|max:255',
            'votable_id' => 'required|integer',
            'vote_type' => 'required|in:upvote,downvote',
            'reason' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::guard('sanctum')->user();
        
        // Verificar si ya existe un voto
        $existingVote = ContentVote::where('user_id', $user->id)
            ->where('votable_type', $request->votable_type)
            ->where('votable_id', $request->votable_id)
            ->first();

        if ($existingVote) {
            // Actualizar voto existente
            $existingVote->update([
                'vote_type' => $request->vote_type,
                'reason' => $request->reason,
                'voted_at' => now(),
            ]);

            $existingVote->load(['user']);

            return response()->json([
                'data' => new ContentVoteResource($existingVote),
                'message' => 'Voto actualizado exitosamente'
            ]);
        }

        // Crear nuevo voto
        $vote = ContentVote::create(array_merge($validator->validated(), [
            'user_id' => $user->id,
            'voted_at' => now(),
        ]));

        $vote->load(['user']);

        return response()->json([
            'data' => new ContentVoteResource($vote),
            'message' => 'Voto registrado exitosamente'
        ], 201);
    }

    /**
     * Display the specified vote
     *
     * Muestra un voto específico.
     *
     * @urlParam contentVote int required ID del voto. Example: 1
     *
     * @apiResource App\Http\Resources\V1\ContentVoteResource
     * @apiResourceModel App\Models\ContentVote
     *
     * @response 404 {"message": "Voto no encontrado"}
     */
    public function show(ContentVote $contentVote)
    {
        $contentVote->load(['user']);
        return new ContentVoteResource($contentVote);
    }

    /**
     * Update the specified vote
     *
     * Actualiza un voto existente.
     *
     * @urlParam contentVote int required ID del voto. Example: 1
     * @bodyParam vote_type string Tipo de voto (upvote, downvote). Example: downvote
     * @bodyParam reason string Razón del voto. Example: No me parece útil
     *
     * @apiResource App\Http\Resources\V1\ContentVoteResource
     * @apiResourceModel App\Models\ContentVote
     *
     * @response 403 {"message": "No autorizado"}
     * @response 422 {"message": "Datos inválidos", "errors": {...}}
     * @authenticated
     */
    public function update(Request $request, ContentVote $contentVote)
    {
        $user = Auth::guard('sanctum')->user();

        if ($contentVote->user_id !== $user->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $validator = Validator::make($request->all(), [
            'vote_type' => 'in:upvote,downvote',
            'reason' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $contentVote->update(array_merge($validator->validated(), [
            'voted_at' => now(),
        ]));

        $contentVote->load(['user']);

        return new ContentVoteResource($contentVote);
    }

    /**
     * Remove the specified vote
     *
     * Elimina un voto.
     *
     * @urlParam contentVote int required ID del voto. Example: 1
     *
     * @response 200 {"message": "Voto eliminado exitosamente"}
     * @response 403 {"message": "No autorizado"}
     * @authenticated
     */
    public function destroy(ContentVote $contentVote)
    {
        $user = Auth::guard('sanctum')->user();

        if ($contentVote->user_id !== $user->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $contentVote->delete();

        return response()->json(['message' => 'Voto eliminado exitosamente']);
    }

    /**
     * Vote on content
     *
     * Método simplificado para votar contenido.
     *
     * @bodyParam votable_type string required Tipo de contenido. Example: App\Models\TopicPost
     * @bodyParam votable_id int required ID del contenido. Example: 1
     * @bodyParam vote_type string required Tipo de voto (upvote, downvote). Example: upvote
     *
     * @response 200 {"message": "Voto registrado", "vote_type": "upvote", "total_votes": 5}
     * @response 422 {"message": "Datos inválidos", "errors": {...}}
     * @authenticated
     */
    public function vote(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'votable_type' => 'required|string|max:255',
            'votable_id' => 'required|integer',
            'vote_type' => 'required|in:upvote,downvote',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::guard('sanctum')->user();
        
        // Verificar si ya existe un voto
        $existingVote = ContentVote::where('user_id', $user->id)
            ->where('votable_type', $request->votable_type)
            ->where('votable_id', $request->votable_id)
            ->first();

        if ($existingVote) {
            if ($existingVote->vote_type === $request->vote_type) {
                // Si es el mismo tipo de voto, eliminarlo (toggle)
                $existingVote->delete();
                $message = 'Voto eliminado';
                $voteType = null;
            } else {
                // Cambiar tipo de voto
                $existingVote->update([
                    'vote_type' => $request->vote_type,
                    'voted_at' => now(),
                ]);
                $message = 'Voto actualizado';
                $voteType = $request->vote_type;
            }
        } else {
            // Crear nuevo voto
            ContentVote::create([
                'user_id' => $user->id,
                'votable_type' => $request->votable_type,
                'votable_id' => $request->votable_id,
                'vote_type' => $request->vote_type,
                'voted_at' => now(),
            ]);
            $message = 'Voto registrado';
            $voteType = $request->vote_type;
        }

        // Obtener estadísticas de votos
        $totalVotes = ContentVote::where('votable_type', $request->votable_type)
            ->where('votable_id', $request->votable_id)
            ->selectRaw('
                SUM(CASE WHEN vote_type = "upvote" THEN 1 ELSE 0 END) as upvotes,
                SUM(CASE WHEN vote_type = "downvote" THEN 1 ELSE 0 END) as downvotes
            ')
            ->first();

        return response()->json([
            'message' => $message,
            'vote_type' => $voteType,
            'upvotes' => $totalVotes->upvotes ?? 0,
            'downvotes' => $totalVotes->downvotes ?? 0,
            'score' => ($totalVotes->upvotes ?? 0) - ($totalVotes->downvotes ?? 0)
        ]);
    }

    /**
     * Remove vote from content
     *
     * Elimina el voto del usuario en un contenido específico.
     *
     * @bodyParam votable_type string required Tipo de contenido. Example: App\Models\TopicPost
     * @bodyParam votable_id int required ID del contenido. Example: 1
     *
     * @response 200 {"message": "Voto eliminado", "total_votes": 4}
     * @response 404 {"message": "No has votado este contenido"}
     * @authenticated
     */
    public function unvote(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'votable_type' => 'required|string|max:255',
            'votable_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::guard('sanctum')->user();
        
        $vote = ContentVote::where('user_id', $user->id)
            ->where('votable_type', $request->votable_type)
            ->where('votable_id', $request->votable_id)
            ->first();

        if (!$vote) {
            return response()->json(['message' => 'No has votado este contenido'], 404);
        }

        $vote->delete();

        // Obtener estadísticas actualizadas
        $totalVotes = ContentVote::where('votable_type', $request->votable_type)
            ->where('votable_id', $request->votable_id)
            ->selectRaw('
                SUM(CASE WHEN vote_type = "upvote" THEN 1 ELSE 0 END) as upvotes,
                SUM(CASE WHEN vote_type = "downvote" THEN 1 ELSE 0 END) as downvotes
            ')
            ->first();

        return response()->json([
            'message' => 'Voto eliminado',
            'upvotes' => $totalVotes->upvotes ?? 0,
            'downvotes' => $totalVotes->downvotes ?? 0,
            'score' => ($totalVotes->upvotes ?? 0) - ($totalVotes->downvotes ?? 0)
        ]);
    }
}