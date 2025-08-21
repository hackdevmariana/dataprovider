<?php

use App\Models\User;
use App\Models\NotificationSetting;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

describe('NotificationSettingController', function () {
    
    describe('GET /api/v1/notification-settings', function () {
        test('returns paginated list of notification settings', function () {
            NotificationSetting::factory()->count(3)->create(['user_id' => $this->user->id]);
            
            $response = $this->getJson('/api/v1/notification-settings');
            
            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'user_id',
                            'notification_type',
                            'is_enabled',
                            'frequency',
                            'channels'
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
        
        test('filters by notification_type', function () {
            NotificationSetting::factory()->create([
                'user_id' => $this->user->id,
                'notification_type' => 'email'
            ]);
            NotificationSetting::factory()->create([
                'user_id' => $this->user->id,
                'notification_type' => 'push'
            ]);
            
            $response = $this->getJson('/api/v1/notification-settings?notification_type=email');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.notification_type', 'email');
        });
        
        test('filters by is_enabled', function () {
            NotificationSetting::factory()->create([
                'user_id' => $this->user->id,
                'is_enabled' => true
            ]);
            NotificationSetting::factory()->create([
                'user_id' => $this->user->id,
                'is_enabled' => false
            ]);
            
            $response = $this->getJson('/api/v1/notification-settings?is_enabled=true');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.is_enabled', true);
        });
        
        test('filters by frequency', function () {
            NotificationSetting::factory()->create([
                'user_id' => $this->user->id,
                'frequency' => 'daily'
            ]);
            NotificationSetting::factory()->create([
                'user_id' => $this->user->id,
                'frequency' => 'weekly'
            ]);
            
            $response = $this->getJson('/api/v1/notification-settings?frequency=daily');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.frequency', 'daily');
        });
        
        test('only returns settings for authenticated user', function () {
            $otherUser = User::factory()->create();
            NotificationSetting::factory()->create(['user_id' => $otherUser->id]);
            NotificationSetting::factory()->create(['user_id' => $this->user->id]);
            
            $response = $this->getJson('/api/v1/notification-settings');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.user_id', $this->user->id);
        });
        
        test('respects per_page parameter', function () {
            NotificationSetting::factory()->count(15)->create(['user_id' => $this->user->id]);
            
            $response = $this->getJson('/api/v1/notification-settings?per_page=10');
            
            $response->assertStatus(200)
                ->assertJsonPath('meta.per_page', 10)
                ->assertJsonCount(10, 'data');
        });
    });
    
    describe('POST /api/v1/notification-settings', function () {
        test('creates new notification setting with valid data', function () {
            $settingData = [
                'notification_type' => 'email',
                'is_enabled' => true,
                'frequency' => 'daily',
                'channels' => ['email', 'sms']
            ];
            
            $response = $this->postJson('/api/v1/notification-settings', $settingData);
            
            $response->assertStatus(201)
                ->assertJsonPath('data.notification_type', 'email')
                ->assertJsonPath('data.is_enabled', true)
                ->assertJsonPath('data.user_id', $this->user->id);
                
            $this->assertDatabaseHas('notification_settings', [
                'notification_type' => 'email',
                'user_id' => $this->user->id,
                'is_enabled' => true
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $response = $this->postJson('/api/v1/notification-settings', []);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['notification_type']);
        });
        
        test('returns 422 with invalid notification_type', function () {
            $settingData = [
                'notification_type' => 'invalid_type',
                'is_enabled' => true
            ];
            
            $response = $this->postJson('/api/v1/notification-settings', $settingData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['notification_type']);
        });
        
        test('returns 422 with invalid frequency', function () {
            $settingData = [
                'notification_type' => 'email',
                'frequency' => 'invalid_frequency'
            ];
            
            $response = $this->postJson('/api/v1/notification-settings', $settingData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['frequency']);
        });
        
        test('returns 422 with invalid channels format', function () {
            $settingData = [
                'notification_type' => 'email',
                'channels' => 'invalid-channels'
            ];
            
            $response = $this->postJson('/api/v1/notification-settings', $settingData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['channels']);
        });
        
        test('returns 422 with duplicate notification type for same user', function () {
            NotificationSetting::factory()->create([
                'user_id' => $this->user->id,
                'notification_type' => 'email'
            ]);
            
            $settingData = [
                'notification_type' => 'email',
                'is_enabled' => false
            ];
            
            $response = $this->postJson('/api/v1/notification-settings', $settingData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['notification_type']);
        });
    });
    
    describe('GET /api/v1/notification-settings/{id}', function () {
        test('returns notification setting details', function () {
            $setting = NotificationSetting::factory()->create(['user_id' => $this->user->id]);
            
            $response = $this->getJson("/api/v1/notification-settings/{$setting->id}");
            
            $response->assertStatus(200)
                ->assertJsonPath('data.id', $setting->id)
                ->assertJsonPath('data.user_id', $this->user->id);
        });
        
        test('returns 404 for non-existent setting', function () {
            $response = $this->getJson('/api/v1/notification-settings/999');
            
            $response->assertStatus(404);
        });
        
        test('returns 404 for setting belonging to other user', function () {
            $otherUser = User::factory()->create();
            $setting = NotificationSetting::factory()->create(['user_id' => $otherUser->id]);
            
            $response = $this->getJson("/api/v1/notification-settings/{$setting->id}");
            
            $response->assertStatus(404);
        });
    });
    
    describe('PUT /api/v1/notification-settings/{id}', function () {
        test('updates notification setting with valid data', function () {
            $setting = NotificationSetting::factory()->create(['user_id' => $this->user->id]);
            $updateData = [
                'is_enabled' => false,
                'frequency' => 'weekly',
                'channels' => ['email']
            ];
            
            $response = $this->putJson("/api/v1/notification-settings/{$setting->id}", $updateData);
            
            $response->assertStatus(200)
                ->assertJsonPath('data.is_enabled', false)
                ->assertJsonPath('data.frequency', 'weekly');
                
            $this->assertDatabaseHas('notification_settings', [
                'id' => $setting->id,
                'is_enabled' => false,
                'frequency' => 'weekly'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $setting = NotificationSetting::factory()->create(['user_id' => $this->user->id]);
            
            $response = $this->putJson("/api/v1/notification-settings/{$setting->id}", [
                'frequency' => 'invalid_frequency'
            ]);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['frequency']);
        });
        
        test('returns 404 for setting belonging to other user', function () {
            $otherUser = User::factory()->create();
            $setting = NotificationSetting::factory()->create(['user_id' => $otherUser->id]);
            
            $response = $this->putJson("/api/v1/notification-settings/{$setting->id}", [
                'is_enabled' => false
            ]);
            
            $response->assertStatus(404);
        });
    });
    
    describe('DELETE /api/v1/notification-settings/{id}', function () {
        test('deletes notification setting successfully', function () {
            $setting = NotificationSetting::factory()->create(['user_id' => $this->user->id]);
            
            $response = $this->deleteJson("/api/v1/notification-settings/{$setting->id}");
            
            $response->assertStatus(204);
            $this->assertDatabaseMissing('notification_settings', ['id' => $setting->id]);
        });
        
        test('returns 404 for setting belonging to other user', function () {
            $otherUser = User::factory()->create();
            $setting = NotificationSetting::factory()->create(['user_id' => $otherUser->id]);
            
            $response = $this->deleteJson("/api/v1/notification-settings/{$setting->id}");
            
            $response->assertStatus(404);
        });
    });
});
