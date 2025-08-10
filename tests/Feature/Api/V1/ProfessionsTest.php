<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('POST /api/v1/professions returns 201 with valid payload', function () {
    Sanctum::actingAs(User::factory()->create());

    $payload = [
        'name' => 'Ingeniero de Software',
        'slug' => 'ingeniero-de-software',
        'is_public_facing' => true,
    ];

    $response = $this->postJson('/api/v1/professions', $payload);

    $response->assertStatus(201)
        ->assertJsonPath('data.slug', 'ingeniero-de-software');
});

test('POST /api/v1/professions returns 422 with invalid payload', function () {
    Sanctum::actingAs(User::factory()->create());

    $response = $this->postJson('/api/v1/professions', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'slug', 'is_public_facing']);
});


