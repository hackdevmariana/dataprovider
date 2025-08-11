<?php

declare(strict_types=1);

use App\Models\Anniversary;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Anniversaries API', function () {
    it('returns a list of anniversaries', function () {
        Anniversary::factory()->count(3)->create();
        $response = $this->getJson('/api/v1/anniversaries');
        $response->assertOk()->assertJsonStructure(['data' => [['id', 'day', 'month', 'year', 'title', 'slug', 'description']]]);
    });

    it('returns a single anniversary by id', function () {
        $anniversary = Anniversary::factory()->create();
        $response = $this->getJson('/api/v1/anniversaries/' . $anniversary->id);
        $response->assertOk()->assertJsonPath('data.id', $anniversary->id);
    });

    it('returns a single anniversary by slug', function () {
        $anniversary = Anniversary::factory()->create(['slug' => 'test-slug']);
        $response = $this->getJson('/api/v1/anniversaries/test-slug');
        $response->assertOk()->assertJsonPath('data.slug', 'test-slug');
    });

    it('returns 404 for non-existent anniversary', function () {
        $response = $this->getJson('/api/v1/anniversaries/999999');
        $response->assertNotFound();
    });

    it('returns anniversaries by day and month', function () {
        Anniversary::factory()->create(['month' => 4, 'day' => 23]);
        Anniversary::factory()->create(['month' => 4, 'day' => 23]);
        Anniversary::factory()->create(['month' => 12, 'day' => 25]);
        $response = $this->getJson('/api/v1/anniversaries/day/4/23');
        $response->assertOk()->assertJsonCount(2, 'data');
    });
});
