<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\FamilyMember;
use App\Http\Resources\V1\FamilyMemberResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @group Family Members
 *
 * APIs para la gestión de relaciones familiares entre personas.
 * Permite consultar información de miembros familiares y sus relaciones.
 */
class FamilyMemberController extends Controller
{
    /**
     * Display a listing of family members
     *

     * Obtiene una lista de todas las relaciones familiares.
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "person_id": 1,
     *       "relative_id": 2,
     *       "relationship_type": "parent",
     *       "person": {...},
     *       "relative": {...}
     *     }
     *   ]
     * }
     *
     * @apiResourceCollection App\Http\Resources\V1\FamilyMemberResource
     * @apiResourceModel App\Models\FamilyMember
     */
    public function index(): JsonResponse
    {
        $family = FamilyMember::with(['person', 'relative'])->get();
        
        return response()->json([
            'data' => FamilyMemberResource::collection($family)
        ]);
    }

    /**
     * Display the specified family member
     *

     * Obtiene los detalles de una relación familiar específica por ID.
     *
     * @urlParam id integer ID de la relación familiar. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *       "person_id": 1,
     *       "relative_id": 2,
     *       "relationship_type": "parent",
     *       "person": {...},
     *       "relative": {...}
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Relación familiar no encontrada"
     * }
     *
     * @apiResourceModel App\Models\FamilyMember
     */
    public function show($id): JsonResponse
    {
        $family = FamilyMember::with(['person', 'relative'])->findOrFail($id);
        
        return response()->json([
            'data' => new FamilyMemberResource($family)
        ]);
    }
}
