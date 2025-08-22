<?php

use App\Models\User;
use App\Models\Setting;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

describe('SettingController', function () {
    
    describe('GET /api/v1/settings', function () {
        test('returns paginated list of settings', function () {
            Setting::factory()->count(3)->create();
            
            $response = $this->getJson('/api/v1/settings');
            
            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'key',
                            'value',
                            'type',
                            'description',
                            'is_public',
                            'group'
                        ]
                    ],
                    'meta' => [
                        'current_page',
                        'last_page',
                        'per_page',
                        'total'
                    ]
                ]);
        });
        
        test('filters settings by group', function () {
            Setting::factory()->create(['group' => 'general']);
            Setting::factory()->create(['group' => 'security']);
            
            $response = $this->getJson('/api/v1/settings?group=general');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.group', 'general');
        });
        
        test('filters settings by type', function () {
            Setting::factory()->create(['type' => 'string']);
            Setting::factory()->create(['type' => 'boolean']);
            
            $response = $this->getJson('/api/v1/settings?type=string');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.type', 'string');
        });
        
        test('filters settings by is_public', function () {
            Setting::factory()->create(['is_public' => true]);
            Setting::factory()->create(['is_public' => false]);
            
            $response = $this->getJson('/api/v1/settings?is_public=true');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.is_public', true);
        });
        
        test('searches settings by key or description', function () {
            Setting::factory()->create(['key' => 'app_name', 'description' => 'Nombre de la aplicación']);
            Setting::factory()->create(['key' => 'app_version', 'description' => 'Versión de la aplicación']);
            
            $response = $this->getJson('/api/v1/settings?search=app_name');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.key', 'app_name');
        });
        
        test('respects per_page parameter', function () {
            Setting::factory()->count(15)->create();
            
            $response = $this->getJson('/api/v1/settings?per_page=10');
            
            $response->assertStatus(200)
                ->assertJsonPath('meta.per_page', 10)
                ->assertJsonCount(10, 'data');
        });
    });
    
    describe('POST /api/v1/settings', function () {
        test('creates new setting with valid data', function () {
            $settingData = [
                'key' => 'app_name',
                'value' => 'DataProvider',
                'type' => 'string',
                'description' => 'Nombre de la aplicación',
                'is_public' => true,
                'group' => 'general'
            ];
            
            $response = $this->postJson('/api/v1/settings', $settingData);
            
            $response->assertStatus(201)
                ->assertJsonPath('data.key', 'app_name')
                ->assertJsonPath('data.value', 'DataProvider')
                ->assertJsonPath('data.type', 'string');
                
            $this->assertDatabaseHas('settings', [
                'key' => 'app_name',
                'value' => 'DataProvider',
                'type' => 'string'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $response = $this->postJson('/api/v1/settings', []);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['key', 'value', 'type']);
        });
        
        test('returns 422 with invalid type', function () {
            $settingData = [
                'key' => 'test_setting',
                'value' => 'test_value',
                'type' => 'invalid_type'
            ];
            
            $response = $this->postJson('/api/v1/settings', $settingData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['type']);
        });
        
        test('returns 422 with duplicate key', function () {
            Setting::factory()->create(['key' => 'test_setting']);
            
            $settingData = [
                'key' => 'test_setting',
                'value' => 'another_value',
                'type' => 'string'
            ];
            
            $response = $this->postJson('/api/v1/settings', $settingData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['key']);
        });
        
        test('returns 422 with invalid value for boolean type', function () {
            $settingData = [
                'key' => 'test_setting',
                'value' => 'not_boolean',
                'type' => 'boolean'
            ];
            
            $response = $this->postJson('/api/v1/settings', $settingData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['value']);
        });
        
        test('returns 422 with invalid value for integer type', function () {
            $settingData = [
                'key' => 'test_setting',
                'value' => 'not_integer',
                'type' => 'integer'
            ];
            
            $response = $this->postJson('/api/v1/settings', $settingData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['value']);
        });
    });
    
    describe('GET /api/v1/settings/{id}', function () {
        test('returns setting details', function () {
            $setting = Setting::factory()->create();
            
            $response = $this->getJson("/api/v1/settings/{$setting->id}");
            
            $response->assertStatus(200)
                ->assertJsonPath('data.id', $setting->id)
                ->assertJsonPath('data.key', $setting->key)
                ->assertJsonPath('data.value', $setting->value);
        });
        
        test('returns 404 for non-existent setting', function () {
            $response = $this->getJson('/api/v1/settings/999');
            
            $response->assertStatus(404);
        });
    });
    
    describe('PUT /api/v1/settings/{id}', function () {
        test('updates setting with valid data', function () {
            $setting = Setting::factory()->create();
            $updateData = [
                'value' => 'DataProvider Pro',
                'description' => 'Nombre de la aplicación (actualizado)'
            ];
            
            $response = $this->putJson("/api/v1/settings/{$setting->id}", $updateData);
            
            $response->assertStatus(200)
                ->assertJsonPath('data.value', 'DataProvider Pro')
                ->assertJsonPath('data.description', 'Nombre de la aplicación (actualizado)');
                
            $this->assertDatabaseHas('settings', [
                'id' => $setting->id,
                'value' => 'DataProvider Pro',
                'description' => 'Nombre de la aplicación (actualizado)'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $setting = Setting::factory()->create(['type' => 'boolean']);
            
            $response = $this->putJson("/api/v1/settings/{$setting->id}", [
                'value' => 'not_boolean'
            ]);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['value']);
        });
        
        test('returns 404 for non-existent setting', function () {
            $response = $this->putJson('/api/v1/settings/999', [
                'value' => 'Updated Value'
            ]);
            
            $response->assertStatus(404);
        });
    });
    
    describe('DELETE /api/v1/settings/{id}', function () {
        test('deletes setting successfully', function () {
            $setting = Setting::factory()->create();
            
            $response = $this->deleteJson("/api/v1/settings/{$setting->id}");
            
            $response->assertStatus(204);
            $this->assertDatabaseMissing('settings', ['id' => $setting->id]);
        });
        
        test('returns 404 for non-existent setting', function () {
            $response = $this->deleteJson('/api/v1/settings/999');
            
            $response->assertStatus(404);
        });
    });
    
    describe('GET /api/v1/settings/key/{key}', function () {
        test('returns setting by key', function () {
            $setting = Setting::factory()->create(['key' => 'app_name']);
            
            $response = $this->getJson('/api/v1/settings/key/app_name');
            
            $response->assertStatus(200)
                ->assertJsonPath('data.key', 'app_name')
                ->assertJsonPath('data.value', $setting->value);
        });
        
        test('returns 404 for non-existent key', function () {
            $response = $this->getJson('/api/v1/settings/key/non_existent_key');
            
            $response->assertStatus(404);
        });
    });
});
