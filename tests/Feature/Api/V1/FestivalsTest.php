<?php

declare(strict_types=1);

use App\Models\Festival;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Festivals API', function () {
    it('returns a list of festivals', function () {
        Festival::factory()->count(3)->create();
        $response = $this->getJson('/api/v1/festivals');
        $response->assertOk()->assertJsonStructure(['data' => [['id', 'name', 'slug', 'description']]]);
    });

    it('returns a single festival by id', function () {
        $festival = Festival::factory()->create();
        $response = $this->getJson('/api/v1/festivals/' . $festival->id);
        $response->assertOk()->assertJsonPath('data.id', $festival->id);
    });

    it('returns 404 for non-existent festival', function () {
        $response = $this->getJson('/api/v1/festivals/999999');
        $response->assertNotFound();
    });

    it('creates a festival with valid data', function () {
        $data = [
            'name' => 'Test Festival',
            'slug' => 'test-festival',
        ];
        $response = $this->postJson('/api/v1/festivals', $data);
        $response->assertCreated()->assertJsonPath('data.name', 'Test Festival');
        $this->assertDatabaseHas('festivals', ['slug' => 'test-festival']);
    });

    it('returns 422 for invalid festival data', function () {
        $response = $this->postJson('/api/v1/festivals', [
            'name' => '', // required
            'slug' => '', // required
        ]);
        $response->assertStatus(422)->assertJsonValidationErrors(['name', 'slug']);
    });
});
