<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Timezone;

/**
 * @OA\Tag(
 *     name="Timezones",
 *     description="Gestión de zonas horarias"
 * )
 */
class TimezoneController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/timezones",
     *     summary="Listado de zonas horarias",
     *     tags={"Timezones"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de zonas horarias",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Timezone")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $timezones = Timezone::with(['countries'])->get();
        return response()->json($timezones);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/timezones/{idOrName}",
     *     summary="Mostrar zona horaria por ID o nombre",
     *     tags={"Timezones"},
     *     @OA\Parameter(
     *         name="idOrName",
     *         in="path",
     *         required=true,
     *         description="ID o nombre de la zona horaria",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Zona horaria encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/Timezone")
     *     ),
     *     @OA\Response(response=404, description="No encontrado")
     * )
     */
    public function show($idOrName)
    {
        $timezone = Timezone::with(['countries'])
            ->where('id', $idOrName)
            ->orWhere('name', $idOrName)
            ->first();

        if (!$timezone) {
            return response()->json(['message' => 'Zona horaria no encontrada'], 404);
        }

        return response()->json($timezone);
    }
}
