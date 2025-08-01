<?php

namespace App\Http\Controllers\Api;

use App\Models\Region;
use App\Models\Province;
use App\Models\AutonomousCommunity;
use App\Models\Country;
use App\Http\Controllers\Controller;
use App\Http\Resources\RegionResource;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    public function index()
    {
        return RegionResource::collection(Region::with(['province', 'autonomousCommunity', 'country', 'timezone'])->get());
    }

    public function show($idOrSlug)
    {
        $region = Region::with(['province', 'autonomousCommunity', 'country', 'timezone'])
                        ->where('slug', $idOrSlug)
                        ->orWhere('id', $idOrSlug)
                        ->firstOrFail();

        return new RegionResource($region);
    }

    public function byProvince($slug)
    {
        $province = Province::where('slug', $slug)->firstOrFail();
        $regions = $province->regions()->with(['province', 'autonomousCommunity', 'country', 'timezone'])->get();

        return RegionResource::collection($regions);
    }

    public function byAutonomousCommunity($slug)
    {
        $community = AutonomousCommunity::where('slug', $slug)->firstOrFail();

        $regions = Region::with(['province', 'autonomousCommunity', 'country', 'timezone'])
                         ->where('autonomous_community_id', $community->id)
                         ->get();

        return RegionResource::collection($regions);
    }

    public function byCountry($slug)
    {
        $country = Country::where('slug', $slug)->firstOrFail();

        $regions = $country->regions()->with(['province', 'autonomousCommunity', 'country', 'timezone'])->get();

        return RegionResource::collection($regions);
    }
}
