<?php

declare(strict_types=1);

use App\Models\Venue;
use App\Models\Municipality;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Venues API', function () {
    it('returns a list of venues', function () {
        Venue::factory()->count(3)->create();
        $response = $this->getJson('/api/v1/venues');
        $response->assertOk()->assertJsonStructure(['data' => [['id', 'name', 'slug', 'address']]]);
    });

    it('returns a single venue by id', function () {
        $venue = Venue::factory()->create();
        $response = $this->getJson('/api/v1/venues/' . $venue->id);
        $response->assertOk()->assertJsonPath('data.id', $venue->id);
    });

    it('returns a single venue by slug', function () {
        $venue = Venue::factory()->create(['slug' => 'test-venue']);
        $response = $this->getJson('/api/v1/venues/test-venue');
        $response->assertOk()->assertJsonPath('data.slug', 'test-venue');
    });

    it('returns 404 for non-existent venue', function () {
        $response = $this->getJson('/api/v1/venues/999999');
        $response->assertNotFound();
    });

    it('creates a venue with valid data', function () {
        $municipality = Municipality::factory()->create();
        $data = [
            'name' => 'Test Venue',
            'slug' => 'test-venue',
            'municipality_id' => $municipality->id,
            'address' => 'Calle Falsa 123',
        ];
        $response = $this->postJson('/api/v1/venues', $data);
        $response->assertCreated()->assertJsonPath('data.name', 'Test Venue');
        $this->assertDatabaseHas('venues', ['slug' => 'test-venue']);
    });

    it('returns 422 for invalid venue data', function () {
        $response = $this->postJson('/api/v1/venues', [
            'name' => '', // required
            'slug' => '', // required
            'municipality_id' => null, // required
        ]);
        $response->assertStatus(422)->assertJsonValidationErrors(['name', 'slug', 'municipality_id']);
    });
});
