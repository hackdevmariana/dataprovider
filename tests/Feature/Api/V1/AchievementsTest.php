<?php

use App\Models\Achievement;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

test('can get all achievements', function () {
    Achievement::factory()->count(3)->create();

    $response = $this->getJson('/api/v1/achievements');

    $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'description',
                        'type',
                        'difficulty',
                        'points',
                        'icon',
                        'banner_color',
                        'conditions',
                        'is_secret',
                        'is_active',
                        'created_at',
                        'updated_at'
                    ]
                ],
                'meta' => ['total']
            ]);
});

test('can filter achievements by type', function () {
    Achievement::factory()->create(['type' => 'single']);
    Achievement::factory()->create(['type' => 'progressive']);

    $response = $this->getJson('/api/v1/achievements?type=single');

    $response->assertStatus(200);
    expect($response->json('data'))->toHaveCount(1);
    expect($response->json('data.0.type'))->toBe('single');
});

test('can filter achievements by difficulty', function () {
    Achievement::factory()->create(['difficulty' => 'bronze']);
    Achievement::factory()->create(['difficulty' => 'gold']);

    $response = $this->getJson('/api/v1/achievements?difficulty=gold');

    $response->assertStatus(200);
    expect($response->json('data'))->toHaveCount(1);
    expect($response->json('data.0.difficulty'))->toBe('gold');
});

test('can get single achievement', function () {
    $achievement = Achievement::factory()->create();

    $response = $this->getJson("/api/v1/achievements/{$achievement->id}");

    $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $achievement->id,
                    'name' => $achievement->name,
                    'slug' => $achievement->slug,
                ]
            ]);
});

test('returns 404 for non-existent achievement', function () {
    $response = $this->getJson('/api/v1/achievements/999999');

    $response->assertStatus(404);
});

test('only returns active achievements by default', function () {
    Achievement::factory()->create(['is_active' => true]);
    Achievement::factory()->create(['is_active' => false]);

    $response = $this->getJson('/api/v1/achievements');

    $response->assertStatus(200);
    expect($response->json('data'))->toHaveCount(1);
    expect($response->json('data.0.is_active'))->toBeTrue();
});
