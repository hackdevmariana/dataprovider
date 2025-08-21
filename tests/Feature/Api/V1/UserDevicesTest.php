<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\UserDevice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;

class UserDevicesTest extends TestCase
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
    public function it_can_list_user_devices_with_pagination()
    {
        UserDevice::factory()->count(25)->create(['user_id' => $this->user->id]);
        
        $response = $this->getJson('/api/v1/user-devices?page=1&per_page=10');

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

        $this->assertCount(10, $response->json('data'));
    }

    /** @test */
    public function it_can_filter_devices_by_device_type()
    {
        UserDevice::factory()->count(3)->create(['user_id' => $this->user->id, 'device_type' => 'mobile']);
        UserDevice::factory()->count(2)->create(['user_id' => $this->user->id, 'device_type' => 'desktop']);
        
        $response = $this->getJson('/api/v1/user-devices?device_type=mobile');

        $response->assertStatus(200);
        $this->assertCount(3, $response->json('data'));
    }

    /** @test */
    public function it_can_filter_devices_by_active_status()
    {
        UserDevice::factory()->count(3)->create(['user_id' => $this->user->id, 'is_active' => true]);
        UserDevice::factory()->count(2)->create(['user_id' => $this->user->id, 'is_active' => false]);
        
        $response = $this->getJson('/api/v1/user-devices?is_active=true');

        $response->assertStatus(200);
        $this->assertCount(3, $response->json('data'));
    }

    /** @test */
    public function it_can_create_a_new_device()
    {
        $deviceData = [
            'device_name' => 'iPhone 13',
            'device_type' => 'mobile',
            'device_token' => 'abc123def456',
            'platform' => 'ios',
            'is_active' => true
        ];

        $response = $this->postJson('/api/v1/user-devices', $deviceData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'device_name',
                        'device_type',
                        'device_token',
                        'platform',
                        'is_active',
                        'user_id',
                        'created_at'
                    ]
                ]);

        $this->assertDatabaseHas('user_devices', [
            'device_name' => 'iPhone 13',
            'user_id' => $this->user->id
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_device()
    {
        $response = $this->postJson('/api/v1/user-devices', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['device_name', 'device_type', 'device_token', 'platform']);
    }

    /** @test */
    public function it_validates_device_type_enum_when_creating_device()
    {
        $response = $this->postJson('/api/v1/user-devices', [
            'device_name' => 'Test Device',
            'device_type' => 'invalid_type',
            'device_token' => 'token123',
            'platform' => 'ios'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['device_type']);
    }

    /** @test */
    public function it_validates_platform_enum_when_creating_device()
    {
        $response = $this->postJson('/api/v1/user-devices', [
            'device_name' => 'Test Device',
            'device_type' => 'mobile',
            'device_token' => 'token123',
            'platform' => 'invalid_platform'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['platform']);
    }

    /** @test */
    public function it_automatically_sets_user_id_when_creating_device()
    {
        $deviceData = [
            'device_name' => 'Test Device',
            'device_type' => 'mobile',
            'device_token' => 'token123',
            'platform' => 'ios'
        ];

        $response = $this->postJson('/api/v1/user-devices', $deviceData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('user_devices', [
            'device_name' => 'Test Device',
            'user_id' => $this->user->id
        ]);
    }

    /** @test */
    public function it_can_show_device_details()
    {
        $device = UserDevice::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson("/api/v1/user-devices/{$device->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'id' => $device->id,
                        'device_name' => $device->device_name
                    ]
                ]);
    }

    /** @test */
    public function it_returns_404_for_nonexistent_device()
    {
        $response = $this->getJson('/api/v1/user-devices/999');

        $response->assertStatus(404);
    }

    /** @test */
    public function it_cannot_show_device_from_other_user()
    {
        $otherUser = User::factory()->create();
        $device = UserDevice::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->getJson("/api/v1/user-devices/{$device->id}");

        $response->assertStatus(404);
    }

    /** @test */
    public function it_can_update_device()
    {
        $device = UserDevice::factory()->create(['user_id' => $this->user->id]);

        $updateData = [
            'device_name' => 'iPhone 13 Pro',
            'is_active' => false
        ];

        $response = $this->putJson("/api/v1/user-devices/{$device->id}", $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'device_name' => 'iPhone 13 Pro',
                        'is_active' => false
                    ]
                ]);

        $this->assertDatabaseHas('user_devices', [
            'id' => $device->id,
            'device_name' => 'iPhone 13 Pro'
        ]);
    }

    /** @test */
    public function it_cannot_update_device_from_other_user()
    {
        $otherUser = User::factory()->create();
        $device = UserDevice::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->putJson("/api/v1/user-devices/{$device->id}", [
            'device_name' => 'Updated Name'
        ]);

        $response->assertStatus(404);
    }

    /** @test */
    public function it_can_delete_device()
    {
        $device = UserDevice::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/api/v1/user-devices/{$device->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('user_devices', ['id' => $device->id]);
    }

    /** @test */
    public function it_cannot_delete_device_from_other_user()
    {
        $otherUser = User::factory()->create();
        $device = UserDevice::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->deleteJson("/api/v1/user-devices/{$device->id}");

        $response->assertStatus(404);
    }

    /** @test */
    public function it_returns_404_when_deleting_nonexistent_device()
    {
        $response = $this->deleteJson('/api/v1/user-devices/999');

        $response->assertStatus(404);
    }

    /** @test */
    public function it_enforces_per_page_limit()
    {
        UserDevice::factory()->count(150)->create(['user_id' => $this->user->id]);
        
        $response = $this->getJson('/api/v1/user-devices?per_page=150');

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['per_page']);
    }

    /** @test */
    public function it_returns_devices_ordered_by_last_used_at_desc()
    {
        $oldDevice = UserDevice::factory()->create([
            'user_id' => $this->user->id,
            'last_used_at' => now()->subDays(2)
        ]);
        $newDevice = UserDevice::factory()->create([
            'user_id' => $this->user->id,
            'last_used_at' => now()
        ]);
        
        $response = $this->getJson('/api/v1/user-devices');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertEquals($newDevice->id, $data[0]['id']);
        $this->assertEquals($oldDevice->id, $data[1]['id']);
    }

    /** @test */
    public function it_can_filter_devices_by_multiple_criteria()
    {
        UserDevice::factory()->create([
            'device_name' => 'Test Mobile',
            'device_type' => 'mobile',
            'is_active' => true,
            'user_id' => $this->user->id
        ]);
        UserDevice::factory()->create([
            'device_name' => 'Test Desktop',
            'device_type' => 'desktop',
            'is_active' => true,
            'user_id' => $this->user->id
        ]);
        
        $response = $this->getJson('/api/v1/user-devices?device_type=mobile&is_active=true');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('Test Mobile', $response->json('data.0.device_name'));
    }

    /** @test */
    public function it_requires_authentication_for_all_endpoints()
    {
        $device = UserDevice::factory()->create(['user_id' => $this->user->id]);
        
        // Desautenticar
        auth()->logout();

        // Test index
        $this->getJson('/api/v1/user-devices')->assertStatus(401);
        
        // Test store
        $this->postJson('/api/v1/user-devices', [])->assertStatus(401);
        
        // Test show
        $this->getJson("/api/v1/user-devices/{$device->id}")->assertStatus(401);
        
        // Test update
        $this->putJson("/api/v1/user-devices/{$device->id}", [])->assertStatus(401);
        
        // Test delete
        $this->deleteJson("/api/v1/user-devices/{$device->id}")->assertStatus(401);
    }

    /** @test */
    public function it_can_handle_device_with_metadata()
    {
        $deviceData = [
            'device_name' => 'Device con Metadatos',
            'device_type' => 'mobile',
            'device_token' => 'token123',
            'platform' => 'android',
            'metadata' => [
                'app_version' => '1.2.3',
                'os_version' => 'Android 12',
                'screen_resolution' => '1080x1920'
            ]
        ];

        $response = $this->postJson('/api/v1/user-devices', $deviceData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('user_devices', [
            'device_name' => 'Device con Metadatos'
        ]);
    }

    /** @test */
    public function it_updates_last_used_at_when_device_is_accessed()
    {
        $device = UserDevice::factory()->create([
            'user_id' => $this->user->id,
            'last_used_at' => now()->subDays(1)
        ]);

        $oldLastUsed = $device->last_used_at;

        $this->getJson("/api/v1/user-devices/{$device->id}");

        $device->refresh();
        $this->assertNotEquals($oldLastUsed, $device->last_used_at);
    }
}
