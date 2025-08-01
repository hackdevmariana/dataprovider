<?php
namespace App\Http\Controllers\Api;

use App\Models\Region;
use App\Models\Province;
use App\Models\AutonomousCommunity;
use App\Models\Country;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    public function index()
    {
        return response()->json(Region::all());
    }

    public function show($idOrSlug)
    {
        $region = Region::where('slug', $idOrSlug)->orWhere('id', $idOrSlug)->firstOrFail();
        return response()->json($region);
    }

    public function byProvince($slug)
    {
        $province = Province::where('slug', $slug)->firstOrFail();
        return response()->json($province->regions);
    }

    public function byAutonomousCommunity($slug)
    {
        $community = AutonomousCommunity::where('slug', $slug)->firstOrFail();
        return response()->json($community->provinces->flatMap->regions);
    }

    public function byCountry($slug)
    {
        $country = Country::where('slug', $slug)->firstOrFail();
        return response()->json($country->regions);
    }
}
