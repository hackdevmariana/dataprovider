<?php

declare(strict_types=1);

use App\Models\Municipality;
use App\Models\Province;
use App\Models\Country;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Municipalities API', function () {
    it('returns paginated municipalities', function () {
        Municipality::factory()->count(3)->create();
        $response = $this->getJson('/api/v1/municipalities');
        $response->assertOk()->assertJsonStructure(['data' => [['id', 'name', 'slug']]]);
    });

    it('returns a specific municipality by slug', function () {
        $municipality = Municipality::factory()->create();
        $response = $this->getJson("/api/v1/municipalities/{$municipality->slug}");
        $response->assertOk()->assertJsonStructure(['data' => ['id', 'name', 'slug']]);
    });

    it('returns a specific municipality by id', function () {
        $municipality = Municipality::factory()->create();
        $response = $this->getJson("/api/v1/municipalities/{$municipality->id}");
        $response->assertOk()->assertJsonStructure(['data' => ['id', 'name', 'slug']]);
    });

    it('returns municipalities by province', function () {
        $province = Province::factory()->create();
        Municipality::factory()->count(2)->create(['province_id' => $province->id]);
        $response = $this->getJson("/api/v1/municipalities/province/{$province->slug}");
        $response->assertOk()->assertJsonStructure(['data' => [['id', 'name', 'slug']]]);
    });

    it('returns municipalities by country', function () {
        $country = Country::factory()->create();
        Municipality::factory()->count(2)->create(['country_id' => $country->id]);
        $response = $this->getJson("/api/v1/municipalities/country/{$country->slug}");
        $response->assertOk()->assertJsonStructure(['data' => [['id', 'name', 'slug']]]);
    });

    it('returns 404 for non-existent municipality', function () {
        $response = $this->getJson('/api/v1/municipalities/non-existent');
        $response->assertNotFound();
    });
});
