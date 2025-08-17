<?php

declare(strict_types=1);

use App\Models\Link;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Links API', function () {
    it('returns paginated links', function () {
        Link::factory()->count(3)->create();
        $response = $this->getJson('/api/v1/links');
        $response->assertOk()->assertJsonStructure(['data' => [['id', 'url', 'text']]]);
    });

    it('returns a specific link', function () {
        $link = Link::factory()->create();
        $response = $this->getJson("/api/v1/links/{$link->id}");
        $response->assertOk()->assertJsonStructure(['data' => ['id', 'url', 'text']]);
    });

    it('can create a new link', function () {
        $user = \App\Models\User::factory()->create();
        $linkData = [
            'url' => 'https://example.com',
            'text' => 'Example Link',
            'description' => 'Example description',
        ];
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/links', $linkData);
        $response->assertStatus(201)->assertJsonStructure(['data' => ['id', 'url', 'text']]);
    });

    it('validates required fields when creating a link', function () {
        $user = \App\Models\User::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/links', []);
        $response->assertStatus(422)->assertJsonValidationErrors(['url', 'text']);
    });

    it('returns 404 for non-existent link', function () {
        $response = $this->getJson('/api/v1/links/99999');
        $response->assertNotFound();
    });
});
