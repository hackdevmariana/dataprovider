<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Person;
use Illuminate\Http\Request;
use App\Http\Resources\V1\PersonResource;

class PersonController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/persons",
     *     summary="Obtener listado de personas",
     *     tags={"Persons"},
     *     @OA\Response(
     *         response=200,
     *         description="Listado de personas",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Person"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        $persons = Person::with(['nationality', 'language', 'image', 'aliases'])->get();
        return PersonResource::collection($persons);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/persons/{idOrSlug}",
     *     summary="Mostrar detalles de una persona",
     *     tags={"Persons"},
     *     @OA\Parameter(
     *         name="idOrSlug",
     *         in="path",
     *         required=true,
     *         description="ID o slug de la persona",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Persona encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/Person")
     *     ),
     *     @OA\Response(response=404, description="Persona no encontrada")
     * )
     */
    public function show($idOrSlug)
    {
        $person = Person::with([
            'nationality',
            'language',
            'image',
            'aliases'
        ])->where('slug', $idOrSlug)
            ->orWhere('id', $idOrSlug)
            ->firstOrFail();

        return new PersonResource($person);
    }
}


