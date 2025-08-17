<?php

declare(strict_types=1);

use App\Models\Country;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Countries API', function () {
    it('returns paginated countries', function () {
        Country::factory()->count(3)->create();
        $response = $this->getJson('/api/v1/countries');
        $response->assertOk()->assertJsonStructure(['data' => [['id', 'name', 'slug']]]);
    });

    it('returns a specific country by slug', function () {
        $country = Country::factory()->create();
        $response = $this->getJson("/api/v1/countries/{$country->slug}");
        $response->assertOk()->assertJsonStructure(['data' => ['id', 'name', 'slug', 'iso_code']]);
    });

    it('returns a specific country by id', function () {
        $country = Country::factory()->create();
        $response = $this->getJson("/api/v1/countries/{$country->id}");
        $response->assertOk()->assertJsonStructure(['data' => ['id', 'name', 'slug', 'iso_code']]);
    });

    it('returns 404 for non-existent country', function () {
        $response = $this->getJson('/api/v1/countries/non-existent');
        $response->assertNotFound();
    });
});
