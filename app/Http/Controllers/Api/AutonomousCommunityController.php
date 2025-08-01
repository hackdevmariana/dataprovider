<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AutonomousCommunity;
use Illuminate\Http\Request;
use App\Http\Resources\AutonomousCommunityResource;

class AutonomousCommunityController extends Controller
{
    // Listar todas las comunidades autónomas
    public function index()
    {
        return AutonomousCommunityResource::collection(AutonomousCommunity::all());
    }

    public function show($slug)
    {
        $community = AutonomousCommunity::where('slug', $slug)->firstOrFail();
        return new AutonomousCommunityResource($community);
    }

    public function withProvinces()
    {
        $communities = AutonomousCommunity::with('provinces')->get();
        return AutonomousCommunityResource::collection($communities);
    }

    public function withProvincesAndMunicipalities()
    {
        $communities = AutonomousCommunity::with('provinces.municipalities')->get();
        return AutonomousCommunityResource::collection($communities);
    }
}
