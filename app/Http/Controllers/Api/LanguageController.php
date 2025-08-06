<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Http\Resources\LanguageResource;

/**
 * @OA\Tag(
 *     name="Languages",
 *     description="Gestión de idiomas"
 * )
 */
class LanguageController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/languages",
     *     summary="Listado de idiomas",
     *     tags={"Languages"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de idiomas",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Language")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $languages = Language::with('countries')->get();
        return LanguageResource::collection($languages);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/languages/{idOrSlug}",
     *     summary="Mostrar idioma por ID o slug",
     *     tags={"Languages"},
     *     @OA\Parameter(
     *         name="idOrSlug",
     *         in="path",
     *         required=true,
     *         description="ID o slug del idioma",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Idioma encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/Language")
     *     ),
     *     @OA\Response(response=404, description="No encontrado")
     * )
     */
    public function show($idOrSlug)
    {
        $language = Language::with('countries')
            ->where('id', $idOrSlug)
            ->orWhere('slug', $idOrSlug)
            ->firstOrFail();

        return new LanguageResource($language);
    }
}
