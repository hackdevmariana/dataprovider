<?php

declare(strict_types=1);

use App\Models\Artist;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Artists API', function () {
    it('returns a list of artists', function () {
        Artist::factory()->count(3)->create();
        $response = $this->getJson('/api/v1/artists');
        $response->assertOk()->assertJsonStructure(['data' => [['id', 'name', 'slug', 'description']]]);
    });

    it('returns a single artist by id', function () {
        $artist = Artist::factory()->create();
        $response = $this->getJson('/api/v1/artists/' . $artist->id);
        $response->assertOk()->assertJsonPath('data.id', $artist->id);
    });

    it('returns a single artist by slug', function () {
        $artist = Artist::factory()->create(['slug' => 'test-artist']);
        $response = $this->getJson('/api/v1/artists/test-artist');
        $response->assertOk()->assertJsonPath('data.slug', 'test-artist');
    });

    it('returns 404 for non-existent artist', function () {
        $response = $this->getJson('/api/v1/artists/999999');
        $response->assertNotFound();
    });

    it('creates an artist with valid data', function () {
        $data = [
            'name' => 'Test Artist',
            'slug' => 'test-artist',
        ];
        $response = $this->postJson('/api/v1/artists', $data);
        $response->assertCreated()->assertJsonPath('data.name', 'Test Artist');
        $this->assertDatabaseHas('artists', ['slug' => 'test-artist']);
    });

    it('returns 422 for invalid artist data', function () {
        $response = $this->postJson('/api/v1/artists', [
            'name' => '', // required
            'slug' => '', // required
        ]);
        $response->assertStatus(422)->assertJsonValidationErrors(['name', 'slug']);
    });
});
