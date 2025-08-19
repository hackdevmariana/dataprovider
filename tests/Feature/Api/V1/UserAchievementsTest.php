<?php

use App\Models\User;
use App\Models\Achievement;
use App\Models\UserAchievement;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

test('can get user achievements', function () {
    $achievement = Achievement::factory()->create();
    $userAchievement = UserAchievement::factory()->create([
        'user_id' => $this->user->id,
        'achievement_id' => $achievement->id,
        'is_completed' => true,
        'points_earned' => 50
    ]);

    $response = $this->getJson('/api/v1/user-achievements');

    $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'user_id',
                        'achievement_id',
                        'progress',
                        'level',
                        'is_completed',
                        'completed_at',
                        'points_earned'
                    ]
                ]
            ]);
});

test('can create user achievement', function () {
    $achievement = Achievement::factory()->create();

    $response = $this->postJson('/api/v1/user-achievements', [
        'achievement_id' => $achievement->id,
        'progress' => 50,
        'level' => 1,
        'is_completed' => false,
        'points_earned' => 25
    ]);

    $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'user_id' => $this->user->id,
                    'achievement_id' => $achievement->id,
                    'progress' => 50,
                    'points_earned' => 25
                ]
            ]);
});

test('can update user achievement progress', function () {
    $achievement = Achievement::factory()->create();
    $userAchievement = UserAchievement::factory()->create([
        'user_id' => $this->user->id,
        'achievement_id' => $achievement->id,
        'progress' => 50
    ]);

    $response = $this->putJson("/api/v1/user-achievements/{$userAchievement->id}", [
        'progress' => 100,
        'is_completed' => true,
        'points_earned' => 100
    ]);

    $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'progress' => 100,
                    'is_completed' => true,
                    'points_earned' => 100
                ]
            ]);
});

test('can get single user achievement', function () {
    $achievement = Achievement::factory()->create();
    $userAchievement = UserAchievement::factory()->create([
        'user_id' => $this->user->id,
        'achievement_id' => $achievement->id
    ]);

    $response = $this->getJson("/api/v1/user-achievements/{$userAchievement->id}");

    $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $userAchievement->id,
                    'user_id' => $this->user->id,
                    'achievement_id' => $achievement->id
                ]
            ]);
});
