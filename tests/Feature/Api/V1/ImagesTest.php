<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('POST /api/v1/images returns 201 with valid payload', function () {
    Sanctum::actingAs(User::factory()->create());

    $payload = [
        'url' => 'https://example.com/image.jpg',
        'slug' => 'image-slug',
    ];

    $response = postJson('/api/v1/images', $payload);

    $response->assertStatus(201)
        ->assertJsonPath('data.slug', 'image-slug');
});

test('POST /api/v1/images returns 422 with invalid payload', function () {
    Sanctum::actingAs(User::factory()->create());

    $response = postJson('/api/v1/images', ['url' => 'not-a-url']);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['url']);
});


