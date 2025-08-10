<?php

use App\Models\User;
use App\Models\Municipality;
use App\Models\Province;
use App\Models\Country;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('POST /api/v1/points-of-interest returns 201 with valid payload', function () {
    Sanctum::actingAs(User::factory()->create());

    $country = Country::query()->create(['name' => 'Spain', 'slug' => 'spain', 'iso_code' => 'ES']);
    $province = Province::query()->create([
        'name' => 'Madrid',
        'slug' => 'madrid',
        'country_id' => $country->id,
    ]);
    $municipality = Municipality::factory()->create(['province_id' => $province->id, 'country_id' => $country->id]);

    $payload = [
        'name' => 'Centro Cultural',
        'slug' => 'centro-cultural',
        'latitude' => 10.5,
        'longitude' => 20.6,
        'municipality_id' => $municipality->id,
    ];

    $response = $this->postJson('/api/v1/points-of-interest', $payload);

    $response->assertStatus(201)
        ->assertJsonPath('slug', 'centro-cultural');
});

test('POST /api/v1/points-of-interest returns 422 with invalid payload', function () {
    Sanctum::actingAs(User::factory()->create());

    $response = $this->postJson('/api/v1/points-of-interest', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'slug', 'latitude', 'longitude', 'municipality_id']);
});


