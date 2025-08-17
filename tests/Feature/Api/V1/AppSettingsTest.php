<?php

declare(strict_types=1);

use App\Models\AppSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('App Settings API', function () {
    it('returns global app settings when authenticated', function () {
        $user = \App\Models\User::factory()->create();
        $organization = \App\Models\Organization::create(['name' => 'Test Org', 'slug' => 'test-org']);
        AppSetting::create(['organization_id' => $organization->id, 'name' => 'Test App']);
        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/app-settings');
        $response->assertOk()->assertJsonStructure(['data' => ['id', 'name']]);
    });

    it('returns a specific app setting when authenticated', function () {
        $user = \App\Models\User::factory()->create();
        $organization = \App\Models\Organization::create(['name' => 'Test Org', 'slug' => 'test-org']);
        $appSetting = AppSetting::create(['organization_id' => $organization->id, 'name' => 'Test App']);
        $response = $this->actingAs($user, 'sanctum')->getJson("/api/v1/app-settings/{$appSetting->id}");
        $response->assertOk()->assertJsonStructure(['data' => ['id', 'name']]);
    });

    it('requires authentication for app settings', function () {
        $response = $this->getJson('/api/v1/app-settings');
        $response->assertUnauthorized();
    });

    it('returns 404 for non-existent app setting when authenticated', function () {
        $user = \App\Models\User::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/app-settings/99999');
        $response->assertNotFound();
    });
});
