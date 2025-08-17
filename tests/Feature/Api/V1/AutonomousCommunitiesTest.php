<?php

declare(strict_types=1);

use App\Models\AutonomousCommunity;
use App\Models\Province;
use App\Models\Municipality;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Autonomous Communities API', function () {
    it('returns paginated autonomous communities', function () {
        AutonomousCommunity::factory()->count(3)->create();
        $response = $this->getJson('/api/v1/autonomous-communities');
        $response->assertOk()->assertJsonStructure(['data' => [['id', 'name', 'slug']]]);
    });

    it('returns a specific autonomous community by slug', function () {
        $community = AutonomousCommunity::factory()->create();
        $response = $this->getJson("/api/v1/autonomous-communities/{$community->slug}");
        $response->assertOk()->assertJsonStructure(['data' => ['id', 'name', 'slug']]);
    });

    it('returns autonomous communities with provinces', function () {
        $community = AutonomousCommunity::factory()->create();
        Province::factory()->count(2)->create(['autonomous_community_id' => $community->id]);
        $response = $this->getJson('/api/v1/autonomous-communities-with-provinces');
        $response->assertOk()->assertJsonStructure(['data' => [['id', 'name', 'provinces']]]);
    });

    it('returns autonomous communities with provinces and municipalities', function () {
        $community = AutonomousCommunity::factory()->create();
        $province = Province::factory()->create(['autonomous_community_id' => $community->id]);
        Municipality::factory()->create(['province_id' => $province->id, 'autonomous_community_id' => $community->id]);
        $response = $this->getJson('/api/v1/autonomous-communities-with-provinces-and-municipalities');
        $response->assertOk()->assertJsonStructure(['data' => [['id', 'name', 'provinces']]]);
    });

    it('returns 404 for non-existent autonomous community', function () {
        $response = $this->getJson('/api/v1/autonomous-communities/non-existent');
        $response->assertNotFound();
    });
});
