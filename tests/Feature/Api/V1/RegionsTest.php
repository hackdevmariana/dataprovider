<?php

declare(strict_types=1);

use App\Models\Region;
use App\Models\Province;
use App\Models\AutonomousCommunity;
use App\Models\Country;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Regions API', function () {
    it('returns paginated regions', function () {
        Region::factory()->count(3)->create();
        $response = $this->getJson('/api/v1/regions');
        $response->assertOk()->assertJsonStructure(['data' => [['id', 'name', 'slug']]]);
    });

    it('returns a specific region by slug', function () {
        $region = Region::factory()->create();
        $response = $this->getJson("/api/v1/regions/{$region->slug}");
        $response->assertOk()->assertJsonStructure(['data' => ['id', 'name', 'slug']]);
    });

    it('returns a specific region by id', function () {
        $region = Region::factory()->create();
        $response = $this->getJson("/api/v1/regions/{$region->id}");
        $response->assertOk()->assertJsonStructure(['data' => ['id', 'name', 'slug']]);
    });

    it('returns regions by province', function () {
        $province = Province::factory()->create();
        Region::factory()->count(2)->create(['province_id' => $province->id]);
        $response = $this->getJson("/api/v1/provinces/{$province->slug}/regions");
        $response->assertOk()->assertJsonStructure(['data' => [['id', 'name', 'slug']]]);
    });

    it('returns regions by autonomous community', function () {
        $community = AutonomousCommunity::factory()->create();
        Region::factory()->count(2)->create(['autonomous_community_id' => $community->id]);
        $response = $this->getJson("/api/v1/autonomous-communities/{$community->slug}/regions");
        $response->assertOk()->assertJsonStructure(['data' => [['id', 'name', 'slug']]]);
    });

    it('returns regions by country', function () {
        $country = Country::factory()->create();
        Region::factory()->count(2)->create(['country_id' => $country->id]);
        $response = $this->getJson("/api/v1/countries/{$country->slug}/regions");
        $response->assertOk()->assertJsonStructure(['data' => [['id', 'name', 'slug']]]);
    });

    it('returns 404 for non-existent region', function () {
        $response = $this->getJson('/api/v1/regions/non-existent');
        $response->assertNotFound();
    });
});
