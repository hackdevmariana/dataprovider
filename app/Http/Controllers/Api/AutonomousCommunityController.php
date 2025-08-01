<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AutonomousCommunity;
use Illuminate\Http\Request;

class AutonomousCommunityController extends Controller
{
    // Listar todas las comunidades autónomas
    public function index()
    {
        return response()->json(AutonomousCommunity::all());
    }

    // Mostrar detalles de una comunidad autónoma por slug
    public function show($slug)
    {
        $community = AutonomousCommunity::where('slug', $slug)->firstOrFail();
        return response()->json($community);
    }

    // Listar comunidades autónomas con sus provincias
    public function withProvinces()
    {
        $communities = AutonomousCommunity::with('provinces')->get();
        return response()->json($communities);
    }

    // Listar comunidades autónomas con provincias y municipios
    public function withProvincesAndMunicipalities()
    {
        $communities = AutonomousCommunity::with([
            'provinces.municipalities' => function ($query) {
                $query->select('id', 'name', 'slug', 'province_id', 'autonomous_community_id');
            }
        ])->get();

        return response()->json($communities);
    }
}
