<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;

class UsersTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear roles básicos
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'user']);
    }

    /** @test */
    public function it_can_list_users_with_pagination()
    {
        // Crear usuarios de prueba
        User::factory()->count(25)->create();
        
        Sanctum::actingAs(User::factory()->create(['role_id' => 1]));

        $response = $this->getJson('/api/v1/users?page=1&per_page=10');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'email',
                            'email_verified_at',
                            'created_at'
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
    public function it_can_filter_users_by_search()
    {
        User::factory()->create(['name' => 'Juan Pérez', 'email' => 'juan@example.com']);
        User::factory()->create(['name' => 'María García', 'email' => 'maria@example.com']);
        
        Sanctum::actingAs(User::factory()->create(['role_id' => 1]));

        $response = $this->getJson('/api/v1/users?search=juan');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('Juan Pérez', $response->json('data.0.name'));
    }

    /** @test */
    public function it_can_filter_users_by_status()
    {
        User::factory()->create(['status' => 'active']);
        User::factory()->create(['status' => 'inactive']);
        
        Sanctum::actingAs(User::factory()->create(['role_id' => 1]));

        $response = $this->getJson('/api/v1/users?status=active');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('active', $response->json('data.0.status'));
    }

    /** @test */
    public function it_can_create_a_new_user()
    {
        Sanctum::actingAs(User::factory()->create(['role_id' => 1]));

        $userData = [
            'name' => 'Nuevo Usuario',
            'email' => 'nuevo@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role_id' => 2
        ];

        $response = $this->postJson('/api/v1/users', $userData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'name',
                        'email',
                        'created_at'
                    ]
                ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Nuevo Usuario',
            'email' => 'nuevo@example.com'
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_user()
    {
        Sanctum::actingAs(User::factory()->create(['role_id' => 1]));

        $response = $this->postJson('/api/v1/users', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    /** @test */
    public function it_validates_email_uniqueness_when_creating_user()
    {
        User::factory()->create(['email' => 'test@example.com']);
        Sanctum::actingAs(User::factory()->create(['role_id' => 1]));

        $response = $this->postJson('/api/v1/users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function it_can_show_user_details()
    {
        $user = User::factory()->create();
        Sanctum::actingAs(User::factory()->create(['role_id' => 1]));

        $response = $this->getJson("/api/v1/users/{$user->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email
                    ]
                ]);
    }

    /** @test */
    public function it_returns_404_for_nonexistent_user()
    {
        Sanctum::actingAs(User::factory()->create(['role_id' => 1]));

        $response = $this->getJson('/api/v1/users/999');

        $response->assertStatus(404);
    }

    /** @test */
    public function it_can_update_user()
    {
        $user = User::factory()->create();
        Sanctum::actingAs(User::factory()->create(['role_id' => 1]));

        $updateData = [
            'name' => 'Usuario Actualizado',
            'email' => 'actualizado@example.com'
        ];

        $response = $this->putJson("/api/v1/users/{$user->id}", $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'name' => 'Usuario Actualizado',
                        'email' => 'actualizado@example.com'
                    ]
                ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Usuario Actualizado',
            'email' => 'actualizado@example.com'
        ]);
    }

    /** @test */
    public function it_validates_email_uniqueness_when_updating_user()
    {
        $user1 = User::factory()->create(['email' => 'user1@example.com']);
        $user2 = User::factory()->create(['email' => 'user2@example.com']);
        Sanctum::actingAs(User::factory()->create(['role_id' => 1]));

        $response = $this->putJson("/api/v1/users/{$user1->id}", [
            'email' => 'user2@example.com'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function it_can_delete_user()
    {
        $user = User::factory()->create();
        Sanctum::actingAs(User::factory()->create(['role_id' => 1]));

        $response = $this->deleteJson("/api/v1/users/{$user->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    /** @test */
    public function it_returns_404_when_deleting_nonexistent_user()
    {
        Sanctum::actingAs(User::factory()->create(['role_id' => 1]));

        $response = $this->deleteJson('/api/v1/users/999');

        $response->assertStatus(404);
    }

    /** @test */
    public function it_enforces_per_page_limit()
    {
        User::factory()->count(150)->create();
        Sanctum::actingAs(User::factory()->create(['role_id' => 1]));

        $response = $this->getJson('/api/v1/users?per_page=150');

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['per_page']);
    }

    /** @test */
    public function it_requires_authentication_for_all_endpoints()
    {
        $user = User::factory()->create();

        // Test index
        $this->getJson('/api/v1/users')->assertStatus(401);
        
        // Test store
        $this->postJson('/api/v1/users', [])->assertStatus(401);
        
        // Test show
        $this->getJson("/api/v1/users/{$user->id}")->assertStatus(401);
        
        // Test update
        $this->putJson("/api/v1/users/{$user->id}", [])->assertStatus(401);
        
        // Test delete
        $this->deleteJson("/api/v1/users/{$user->id}")->assertStatus(401);
    }
}
