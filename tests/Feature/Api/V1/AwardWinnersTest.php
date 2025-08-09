<?php

use App\Models\User;
use App\Models\Person;
use App\Models\Award;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('POST /api/v1/award-winners returns 201 with valid payload', function () {
    Sanctum::actingAs(User::factory()->create());

    $person = Person::factory()->create();
    $award = Award::factory()->create();

    $payload = [
        'person_id' => $person->id,
        'award_id' => $award->id,
        'year' => 2020,
    ];

    $response = postJson('/api/v1/award-winners', $payload);

    $response->assertStatus(201)
        ->assertJsonPath('data.year', 2020);
});

test('POST /api/v1/award-winners returns 422 with invalid payload', function () {
    Sanctum::actingAs(User::factory()->create());

    $response = postJson('/api/v1/award-winners', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['person_id', 'award_id', 'year']);
});


