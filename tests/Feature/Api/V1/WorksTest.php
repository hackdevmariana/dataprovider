<?php

declare(strict_types=1);

use App\Models\Work;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Works API', function () {
    it('returns paginated works', function () {
        Work::factory()->count(3)->create();
        $response = $this->getJson('/api/v1/works');
        $response->assertOk()->assertJsonStructure(['data' => [['id', 'title', 'slug']]]);
    });

    it('returns a specific work by slug', function () {
        $work = Work::factory()->create();
        $response = $this->getJson("/api/v1/works/{$work->slug}");
        $response->assertOk()->assertJsonStructure(['data' => ['id', 'title', 'slug']]);
    });

    it('returns a specific work by id', function () {
        $work = Work::factory()->create();
        $response = $this->getJson("/api/v1/works/{$work->id}");
        $response->assertOk()->assertJsonStructure(['data' => ['id', 'title', 'slug']]);
    });

    it('can create a new work', function () {
        $user = \App\Models\User::factory()->create();
        $workData = [
            'title' => 'Test Work',
            'slug' => 'test-work',
            'description' => 'A test work description',
            'year' => 2023,
        ];
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/works', $workData);
        $response->assertStatus(201)->assertJsonStructure(['data' => ['id', 'title', 'slug']]);
    });

    it('validates required fields when creating a work', function () {
        $user = \App\Models\User::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/works', []);
        $response->assertStatus(422)->assertJsonValidationErrors(['title', 'slug']);
    });

    it('returns 404 for non-existent work', function () {
        $response = $this->getJson('/api/v1/works/non-existent');
        $response->assertNotFound();
    });
});
