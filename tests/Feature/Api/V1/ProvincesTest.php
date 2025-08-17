<?php

declare(strict_types=1);

use App\Models\Province;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Provinces API', function () {
    it('returns paginated provinces', function () {
        Province::factory()->count(3)->create();
        $response = $this->getJson('/api/v1/provinces');
        $response->assertOk()->assertJsonStructure(['data' => [['id', 'name', 'slug']]]);
    });

    it('returns a specific province', function () {
        $province = Province::factory()->create();
        $response = $this->getJson("/api/v1/provinces/{$province->id}");
        $response->assertOk()->assertJsonStructure(['data' => ['id', 'name', 'slug']]);
    });

    it('returns 404 for non-existent province', function () {
        $response = $this->getJson('/api/v1/provinces/99999');
        $response->assertNotFound();
    });
});
