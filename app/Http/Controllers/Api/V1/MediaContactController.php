<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\MediaContact;
use App\Http\Resources\V1\MediaContactResource;
use App\Http\Requests\RecordInteractionRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * Controlador para gestión de contactos de medios.
 */
/**
 * @OA\Tag(
 *     name="Contactos de Medios",
 *     description="APIs para la gestión de Contactos de Medios"
 * )
 */
class MediaContactController extends Controller
{
    /**
     * Listar contactos de medios.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->get('per_page', 15), 100);
        
        $query = MediaContact::with(['mediaOutlet'])->active();

        if ($request->filled('media_outlet_id')) {
            $query->where('media_outlet_id', $request->media_outlet_id);
        }

        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        if ($request->boolean('sustainability_focused')) {
            $query->sustainabilityFocused();
        }

        if ($request->boolean('high_priority')) {
            $query->highPriority();
        }

        $contacts = $query->paginate($perPage);

        return response()->json([
            'data' => $contacts->items(),
            'meta' => [
                'current_page' => $contacts->currentPage(),
                'total' => $contacts->total(),
            ]
        ]);
    }

    /**
     * Mostrar un contacto específico.
     */
    public function show(MediaContact $contact): JsonResponse
    {
        $contact->load(['mediaOutlet']);
        return response()->json($contact);
    }

    /**
     * Contactos de prensa.
     */
    public function pressContacts(Request $request): JsonResponse
    {
        $limit = min((int) $request->get('limit', 20), 50);
        
        $contacts = MediaContact::with(['mediaOutlet'])
                               ->pressContacts()
                               ->active()
                               ->limit($limit)
                               ->get();

        return response()->json(['data' => $contacts]);
    }

    /**
     * Contactos especializados en sostenibilidad.
     */
    public function sustainabilityFocused(Request $request): JsonResponse
    {
        $limit = min((int) $request->get('limit', 15), 50);
        
        $contacts = MediaContact::with(['mediaOutlet'])
                               ->sustainabilityFocused()
                               ->active()
                               ->orderBy('priority_level', 'desc')
                               ->limit($limit)
                               ->get();

        return response()->json(['data' => $contacts]);
    }

    /**
     * Registrar nueva interacción.
     */
    public function recordInteraction(RecordInteractionRequest $request, MediaContact $contact): JsonResponse
    {
        $data = $request->getProcessedData();
        
        $contact->recordInteraction(
            $request->type,
            $request->description,
            $request->boolean('successful')
        );

        return response()->json([
            'contact_id' => $contact->id,
            'interaction_type' => $request->getInteractionTypeName(),
            'interaction_recorded' => true,
            'total_contacts' => $contact->contacts_count,
            'successful_contacts' => $contact->successful_contacts,
            'response_rate' => $contact->response_rate,
            'message' => 'Interacción registrada exitosamente',
        ]);
    }

    /**
     * Estadísticas de contactos.
     */
    public function statistics(): JsonResponse
    {
        $totalContacts = MediaContact::count();
        $activeContacts = MediaContact::active()->count();
        $sustainabilityContacts = MediaContact::sustainabilityFocused()->count();

        return response()->json([
            'total_contacts' => $totalContacts,
            'active_contacts' => $activeContacts,
            'sustainability_contacts' => $sustainabilityContacts,
        ]);
    }
}
