<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\NotificationSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;

class NotificationSettingsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear usuario autenticado
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    /** @test */
    public function it_can_list_notification_settings_with_pagination()
    {
        NotificationSetting::factory()->count(25)->create(['user_id' => $this->user->id]);
        
        $response = $this->getJson('/api/v1/notification-settings?page=1&per_page=10');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'notification_type',
                            'is_enabled',
                            'channels',
                            'frequency'
                        ]
                    ],
                    'meta' => [
                        'current_page',
                        'last_page',
                        'per_page',
                        'total'
                    ]
                ]);

        $this->assertCount(10, $response->json('data'));
    }

    /** @test */
    public function it_can_filter_settings_by_notification_type()
    {
        NotificationSetting::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'notification_type' => 'email'
        ]);
        NotificationSetting::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'notification_type' => 'push'
        ]);
        
        $response = $this->getJson('/api/v1/notification-settings?notification_type=email');

        $response->assertStatus(200);
        $this->assertCount(3, $response->json('data'));
    }

    /** @test */
    public function it_can_filter_settings_by_enabled_status()
    {
        NotificationSetting::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'is_enabled' => true
        ]);
        NotificationSetting::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'is_enabled' => false
        ]);
        
        $response = $this->getJson('/api/v1/notification-settings?is_enabled=true');

        $response->assertStatus(200);
        $this->assertCount(3, $response->json('data'));
    }

    /** @test */
    public function it_can_create_a_new_notification_setting()
    {
        $settingData = [
            'notification_type' => 'email',
            'is_enabled' => true,
            'channels' => ['email', 'sms'],
            'frequency' => 'immediate',
            'preferences' => [
                'quiet_hours' => ['start' => '22:00', 'end' => '08:00'],
                'timezone' => 'UTC'
            ]
        ];

        $response = $this->postJson('/api/v1/notification-settings', $settingData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'notification_type',
                        'is_enabled',
                        'channels',
                        'frequency',
                        'preferences',
                        'user_id',
                        'created_at'
                    ]
                ]);

        $this->assertDatabaseHas('notification_settings', [
            'notification_type' => 'email',
            'user_id' => $this->user->id
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_setting()
    {
        $response = $this->postJson('/api/v1/notification-settings', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['notification_type', 'is_enabled']);
    }

    /** @test */
    public function it_validates_notification_type_enum_when_creating_setting()
    {
        $response = $this->postJson('/api/v1/notification-settings', [
            'notification_type' => 'invalid_type',
            'is_enabled' => true
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['notification_type']);
    }

    /** @test */
    public function it_validates_frequency_enum_when_creating_setting()
    {
        $response = $this->postJson('/api/v1/notification-settings', [
            'notification_type' => 'email',
            'is_enabled' => true,
            'frequency' => 'invalid_frequency'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['frequency']);
    }

    /** @test */
    public function it_automatically_sets_user_id_when_creating_setting()
    {
        $settingData = [
            'notification_type' => 'email',
            'is_enabled' => true
        ];

        $response = $this->postJson('/api/v1/notification-settings', $settingData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('notification_settings', [
            'notification_type' => 'email',
            'user_id' => $this->user->id
        ]);
    }

    /** @test */
    public function it_can_show_setting_details()
    {
        $setting = NotificationSetting::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson("/api/v1/notification-settings/{$setting->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'id' => $setting->id,
                        'notification_type' => $setting->notification_type
                    ]
                ]);
    }

    /** @test */
    public function it_returns_404_for_nonexistent_setting()
    {
        $response = $this->getJson('/api/v1/notification-settings/999');

        $response->assertStatus(404);
    }

    /** @test */
    public function it_cannot_show_setting_from_other_user()
    {
        $otherUser = User::factory()->create();
        $setting = NotificationSetting::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->getJson("/api/v1/notification-settings/{$setting->id}");

        $response->assertStatus(404);
    }

    /** @test */
    public function it_can_update_setting()
    {
        $setting = NotificationSetting::factory()->create(['user_id' => $this->user->id]);

        $updateData = [
            'is_enabled' => false,
            'channels' => ['email'],
            'frequency' => 'daily'
        ];

        $response = $this->putJson("/api/v1/notification-settings/{$setting->id}", $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'is_enabled' => false,
                        'channels' => ['email'],
                        'frequency' => 'daily'
                    ]
                ]);

        $this->assertDatabaseHas('notification_settings', [
            'id' => $setting->id,
            'is_enabled' => false
        ]);
    }

    /** @test */
    public function it_cannot_update_setting_from_other_user()
    {
        $otherUser = User::factory()->create();
        $setting = NotificationSetting::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->putJson("/api/v1/notification-settings/{$setting->id}", [
            'is_enabled' => false
        ]);

        $response->assertStatus(404);
    }

    /** @test */
    public function it_can_delete_setting()
    {
        $setting = NotificationSetting::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/api/v1/notification-settings/{$setting->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('notification_settings', ['id' => $setting->id]);
    }

    /** @test */
    public function it_cannot_delete_setting_from_other_user()
    {
        $otherUser = User::factory()->create();
        $setting = NotificationSetting::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->deleteJson("/api/v1/notification-settings/{$setting->id}");

        $response->assertStatus(404);
    }

    /** @test */
    public function it_returns_404_when_deleting_nonexistent_setting()
    {
        $response = $this->deleteJson('/api/v1/notification-settings/999');

        $response->assertStatus(404);
    }

    /** @test */
    public function it_enforces_per_page_limit()
    {
        NotificationSetting::factory()->count(150)->create(['user_id' => $this->user->id]);
        
        $response = $this->getJson('/api/v1/notification-settings?per_page=150');

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['per_page']);
    }

    /** @test */
    public function it_returns_settings_ordered_by_notification_type()
    {
        NotificationSetting::factory()->create([
            'user_id' => $this->user->id,
            'notification_type' => 'zebra'
        ]);
        NotificationSetting::factory()->create([
            'user_id' => $this->user->id,
            'notification_type' => 'alpha'
        ]);
        NotificationSetting::factory()->create([
            'user_id' => $this->user->id,
            'notification_type' => 'beta'
        ]);
        
        $response = $this->getJson('/api/v1/notification-settings');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertEquals('alpha', $data[0]['notification_type']);
        $this->assertEquals('beta', $data[1]['notification_type']);
        $this->assertEquals('zebra', $data[2]['notification_type']);
    }

    /** @test */
    public function it_can_filter_settings_by_multiple_criteria()
    {
        NotificationSetting::factory()->create([
            'notification_type' => 'email',
            'is_enabled' => true,
            'user_id' => $this->user->id
        ]);
        NotificationSetting::factory()->create([
            'notification_type' => 'email',
            'is_enabled' => false,
            'user_id' => $this->user->id
        ]);
        
        $response = $this->getJson('/api/v1/notification-settings?notification_type=email&is_enabled=true');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertTrue($response->json('data.0.is_enabled'));
    }

    /** @test */
    public function it_requires_authentication_for_all_endpoints()
    {
        $setting = NotificationSetting::factory()->create(['user_id' => $this->user->id]);
        
        // Desautenticar
        auth()->logout();

        // Test index
        $this->getJson('/api/v1/notification-settings')->assertStatus(401);
        
        // Test store
        $this->postJson('/api/v1/notification-settings', [])->assertStatus(401);
        
        // Test show
        $this->getJson("/api/v1/notification-settings/{$setting->id}")->assertStatus(401);
        
        // Test update
        $this->putJson("/api/v1/notification-settings/{$setting->id}", [])->assertStatus(401);
        
        // Test delete
        $this->deleteJson("/api/v1/notification-settings/{$setting->id}")->assertStatus(401);
    }

    /** @test */
    public function it_can_handle_setting_with_complex_preferences()
    {
        $settingData = [
            'notification_type' => 'marketing',
            'is_enabled' => true,
            'channels' => ['email', 'push', 'sms'],
            'frequency' => 'weekly',
            'preferences' => [
                'quiet_hours' => ['start' => '22:00', 'end' => '08:00'],
                'timezone' => 'Europe/Madrid',
                'categories' => ['news', 'offers', 'updates'],
                'max_per_day' => 5
            ]
        ];

        $response = $this->postJson('/api/v1/notification-settings', $settingData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('notification_settings', [
            'notification_type' => 'marketing',
            'user_id' => $this->user->id
        ]);
    }

    /** @test */
    public function it_validates_channels_array_when_creating_setting()
    {
        $response = $this->postJson('/api/v1/notification-settings', [
            'notification_type' => 'email',
            'is_enabled' => true,
            'channels' => 'invalid_channels'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['channels']);
    }

    /** @test */
    public function it_can_bulk_update_notification_settings()
    {
        $settings = NotificationSetting::factory()->count(3)->create(['user_id' => $this->user->id]);

        $bulkUpdateData = [
            'settings' => [
                ['id' => $settings[0]->id, 'is_enabled' => false],
                ['id' => $settings[1]->id, 'frequency' => 'daily'],
                ['id' => $settings[2]->id, 'channels' => ['email']]
            ]
        ];

        $response = $this->putJson('/api/v1/notification-settings/bulk', $bulkUpdateData);

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('notification_settings', [
            'id' => $settings[0]->id,
            'is_enabled' => false
        ]);
    }
}
