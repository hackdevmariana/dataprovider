<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('POST /api/v1/awards returns 201 with valid payload', function () {
    Sanctum::actingAs(User::factory()->create());

    $payload = [
        'name' => 'Premio Ejemplo',
        'slug' => 'premio-ejemplo',
        'description' => 'Descripción',
        'awarded_by' => 'Organización',
        'first_year_awarded' => 1990,
        'category' => 'Cultura',
    ];

    $response = $this->postJson('/api/v1/awards', $payload);

    $response->assertStatus(201)
        ->assertJsonPath('data.slug', 'premio-ejemplo');
});

test('POST /api/v1/awards returns 422 with invalid payload', function () {
    Sanctum::actingAs(User::factory()->create());

    $response = $this->postJson('/api/v1/awards', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'slug']);
});


