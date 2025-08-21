<?php

use App\Models\User;
use App\Models\UserDevice;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

describe('UserDeviceController', function () {
    
    describe('GET /api/v1/user-devices', function () {
        test('returns paginated list of user devices', function () {
            UserDevice::factory()->count(3)->create(['user_id' => $this->user->id]);
            
            $response = $this->getJson('/api/v1/user-devices');
            
            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'device_name',
                            'device_type',
                            'device_token',
                            'platform',
                            'is_active',
                            'last_used_at'
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
        
        test('filters devices by device type', function () {
            UserDevice::factory()->create([
                'user_id' => $this->user->id,
                'device_type' => 'mobile'
            ]);
            UserDevice::factory()->create([
                'user_id' => $this->user->id,
                'device_type' => 'desktop'
            ]);
            
            $response = $this->getJson('/api/v1/user-devices?device_type=mobile');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.device_type', 'mobile');
        });
        
        test('filters devices by active status', function () {
            UserDevice::factory()->create([
                'user_id' => $this->user->id,
                'is_active' => true
            ]);
            UserDevice::factory()->create([
                'user_id' => $this->user->id,
                'is_active' => false
            ]);
            
            $response = $this->getJson('/api/v1/user-devices?is_active=true');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.is_active', true);
        });
        
        test('only returns devices for authenticated user', function () {
            $otherUser = User::factory()->create();
            UserDevice::factory()->create(['user_id' => $otherUser->id]);
            UserDevice::factory()->create(['user_id' => $this->user->id]);
            
            $response = $this->getJson('/api/v1/user-devices');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.user_id', $this->user->id);
        });
    });
    
    describe('POST /api/v1/user-devices', function () {
        test('creates new user device with valid data', function () {
            $deviceData = [
                'device_name' => 'iPhone 13',
                'device_type' => 'mobile',
                'device_token' => 'abc123token',
                'platform' => 'ios',
                'is_active' => true
            ];
            
            $response = $this->postJson('/api/v1/user-devices', $deviceData);
            
            $response->assertStatus(201)
                ->assertJsonPath('data.device_name', 'iPhone 13')
                ->assertJsonPath('data.device_type', 'mobile')
                ->assertJsonPath('data.user_id', $this->user->id);
                
            $this->assertDatabaseHas('user_devices', [
                'device_name' => 'iPhone 13',
                'user_id' => $this->user->id
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $response = $this->postJson('/api/v1/user-devices', []);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['device_name', 'device_type', 'device_token']);
        });
        
        test('returns 422 with invalid device type', function () {
            $deviceData = [
                'device_name' => 'Test Device',
                'device_type' => 'invalid_type',
                'device_token' => 'token123'
            ];
            
            $response = $this->postJson('/api/v1/user-devices', $deviceData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['device_type']);
        });
    });
    
    describe('GET /api/v1/user-devices/{id}', function () {
        test('returns device details', function () {
            $device = UserDevice::factory()->create(['user_id' => $this->user->id]);
            
            $response = $this->getJson("/api/v1/user-devices/{$device->id}");
            
            $response->assertStatus(200)
                ->assertJsonPath('data.id', $device->id)
                ->assertJsonPath('data.device_name', $device->device_name);
        });
        
        test('returns 404 for non-existent device', function () {
            $response = $this->getJson('/api/v1/user-devices/999');
            
            $response->assertStatus(404);
        });
        
        test('returns 404 for device belonging to other user', function () {
            $otherUser = User::factory()->create();
            $device = UserDevice::factory()->create(['user_id' => $otherUser->id]);
            
            $response = $this->getJson("/api/v1/user-devices/{$device->id}");
            
            $response->assertStatus(404);
        });
    });
    
    describe('PUT /api/v1/user-devices/{id}', function () {
        test('updates device with valid data', function () {
            $device = UserDevice::factory()->create(['user_id' => $this->user->id]);
            $updateData = [
                'device_name' => 'iPhone 13 Pro',
                'is_active' => false
            ];
            
            $response = $this->putJson("/api/v1/user-devices/{$device->id}", $updateData);
            
            $response->assertStatus(200)
                ->assertJsonPath('data.device_name', 'iPhone 13 Pro')
                ->assertJsonPath('data.is_active', false);
                
            $this->assertDatabaseHas('user_devices', [
                'id' => $device->id,
                'device_name' => 'iPhone 13 Pro',
                'is_active' => false
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $device = UserDevice::factory()->create(['user_id' => $this->user->id]);
            
            $response = $this->putJson("/api/v1/user-devices/{$device->id}", [
                'device_type' => 'invalid_type'
            ]);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['device_type']);
        });
        
        test('returns 404 for device belonging to other user', function () {
            $otherUser = User::factory()->create();
            $device = UserDevice::factory()->create(['user_id' => $otherUser->id]);
            
            $response = $this->putJson("/api/v1/user-devices/{$device->id}", [
                'device_name' => 'Updated Name'
            ]);
            
            $response->assertStatus(404);
        });
    });
    
    describe('DELETE /api/v1/user-devices/{id}', function () {
        test('deletes device successfully', function () {
            $device = UserDevice::factory()->create(['user_id' => $this->user->id]);
            
            $response = $this->deleteJson("/api/v1/user-devices/{$device->id}");
            
            $response->assertStatus(204);
            $this->assertDatabaseMissing('user_devices', ['id' => $device->id]);
        });
        
        test('returns 404 for device belonging to other user', function () {
            $otherUser = User::factory()->create();
            $device = UserDevice::factory()->create(['user_id' => $otherUser->id]);
            
            $response = $this->deleteJson("/api/v1/user-devices/{$device->id}");
            
            $response->assertStatus(404);
        });
    });
});
