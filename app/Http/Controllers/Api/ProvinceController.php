<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Province;
use Illuminate\Http\Request;

class ProvinceController extends Controller
{
    // Listado paginado de provincias con comunidad autónoma y país
    public function index()
    {
        $provinces = Province::with(['autonomousCommunity', 'country'])->paginate(20);
        return response()->json($provinces);
    }

    // Mostrar detalle de una provincia por ID o slug
    public function show($idOrSlug)
    {
        $province = Province::with(['autonomousCommunity', 'country'])->where('id', $idOrSlug)->orWhere('slug', $idOrSlug)->first();

        if (!$province) {
            return response()->json(['message' => 'Provincia no encontrada'], 404);
        }

        return response()->json($province);
    }
}
