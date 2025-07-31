<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PointOfInterest;
use Illuminate\Http\Request;

class PointOfInterestController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/points-of-interest",
     *     summary="Listar todos los puntos de interés",
     *     tags={"Points of Interest"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de puntos de interés",
     *         @OA\JsonContent(type="array", @OA\Items())
     *     )
     * )
     */
    public function index()
    {
        $points = PointOfInterest::with(['municipality', 'tags'])->paginate(50);
        return response()->json($points);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/points-of-interest/{idOrSlug}",
     *     summary="Obtener detalle de un punto de interés",
     *     tags={"Points of Interest"},
     *     @OA\Parameter(
     *         name="idOrSlug",
     *         in="path",
     *         description="ID o slug del punto de interés",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Punto encontrado", @OA\JsonContent()),
     *     @OA\Response(response=404, description="No encontrado")
     * )
     */
    public function show($idOrSlug)
    {
        $poi = PointOfInterest::with(['municipality', 'tags'])
            ->where('id', $idOrSlug)
            ->orWhere('slug', $idOrSlug)
            ->first();

        if (!$poi) {
            return response()->json(['message' => 'Punto de interés no encontrado'], 404);
        }

        return response()->json($poi);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/points-of-interest",
     *     summary="Crear un nuevo punto de interés",
     *     tags={"Points of Interest"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/PointOfInterest")
     *     ),
     *     @OA\Response(response=201, description="Creado con éxito"),
     *     @OA\Response(response=400, description="Datos inválidos")
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'slug' => 'required|string|unique:point_of_interests,slug',
            'address' => 'nullable|string',
            'type' => 'nullable|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'municipality_id' => 'required|exists:municipalities,id',
            'source' => 'nullable|string',
            'description' => 'nullable|string',
            'is_cultural_center' => 'boolean',
            'is_energy_installation' => 'boolean',
            'is_cooperative_office' => 'boolean',
            'opening_hours' => 'nullable|array',
        ]);

        $poi = PointOfInterest::create($data);

        return response()->json($poi, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/points-of-interest/{id}",
     *     summary="Actualizar un punto de interés",
     *     tags={"Points of Interest"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/PointOfInterest")
     *     ),
     *     @OA\Response(response=200, description="Actualizado con éxito"),
     *     @OA\Response(response=404, description="No encontrado")
     * )
     */
    public function update(Request $request, $id)
    {
        $poi = PointOfInterest::find($id);
        if (!$poi) {
            return response()->json(['message' => 'Punto de interés no encontrado'], 404);
        }

        $data = $request->validate([
            'name' => 'string',
            'slug' => "string|unique:point_of_interests,slug,$id",
            'address' => 'nullable|string',
            'type' => 'nullable|string',
            'latitude' => 'numeric',
            'longitude' => 'numeric',
            'municipality_id' => 'exists:municipalities,id',
            'source' => 'nullable|string',
            'description' => 'nullable|string',
            'is_cultural_center' => 'boolean',
            'is_energy_installation' => 'boolean',
            'is_cooperative_office' => 'boolean',
            'opening_hours' => 'nullable|array',
        ]);

        $poi->update($data);

        return response()->json($poi);
    }
    /**
     * @OA\Get(
     *     path="/api/v1/points-of-interest/municipality/{slug}",
     *     summary="Listar puntos de interés por municipio",
     *     tags={"Points of Interest"},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         required=true,
     *         description="Slug del municipio",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Lista de puntos de interés", @OA\JsonContent(type="array", @OA\Items())),
     *     @OA\Response(response=404, description="Municipio no encontrado")
     * )
     */
    public function byMunicipality($slug)
    {
        $municipality = \App\Models\Municipality::where('slug', $slug)->first();

        if (!$municipality) {
            return response()->json(['message' => 'Municipio no encontrado'], 404);
        }

        $points = $municipality->pointsOfInterest()->with('tags')->get();

        return response()->json($points);
    }
    /**
     * @OA\Get(
     *     path="/api/v1/points-of-interest/type/{type}",
     *     summary="Listar puntos de interés por tipo",
     *     tags={"Points of Interest"},
     *     @OA\Parameter(
     *         name="type",
     *         in="path",
     *         required=true,
     *         description="Tipo de punto de interés (ej: centro_energia)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Puntos encontrados", @OA\JsonContent(type="array", @OA\Items()))
     * )
     */
    public function byType($type)
    {
        $points = PointOfInterest::where('type', $type)->with(['municipality', 'tags'])->get();
        return response()->json($points);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/points-of-interest/tag/{tagSlug}",
     *     summary="Listar puntos de interés por etiqueta (tag)",
     *     tags={"Points of Interest"},
     *     @OA\Parameter(
     *         name="tagSlug",
     *         in="path",
     *         required=true,
     *         description="Slug de la etiqueta (tag)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Puntos encontrados", @OA\JsonContent(type="array", @OA\Items())),
     *     @OA\Response(response=404, description="Etiqueta no encontrada")
     * )
     */
    public function byTag($tagSlug)
    {
        $tag = \App\Models\Tag::where('slug', $tagSlug)->first();

        if (!$tag) {
            return response()->json(['message' => 'Etiqueta no encontrada'], 404);
        }

        $points = $tag->pointsOfInterest()->with(['municipality', 'tags'])->get();

        return response()->json($points);
    }


    /**
     * @OA\Delete(
     *     path="/api/v1/points-of-interest/{id}",
     *     summary="Eliminar un punto de interés",
     *     tags={"Points of Interest"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Eliminado con éxito"),
     *     @OA\Response(response=404, description="No encontrado")
     * )
     */



    public function destroy($id)
    {
        $poi = PointOfInterest::find($id);
        if (!$poi) {
            return response()->json(['message' => 'Punto de interés no encontrado'], 404);
        }

        $poi->delete();
        return response()->json(null, 204);
    }
}
