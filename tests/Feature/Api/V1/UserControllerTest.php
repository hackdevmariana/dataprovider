<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

describe('UserController', function () {
    
    describe('GET /api/v1/users', function () {
        test('returns paginated list of users', function () {
            User::factory()->count(5)->create();
            
            $response = $this->getJson('/api/v1/users');
            
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
        });
        
        test('filters users by search parameter', function () {
            User::factory()->create(['name' => 'Juan Pérez']);
            User::factory()->create(['name' => 'María García']);
            
            $response = $this->getJson('/api/v1/users?search=juan');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.name', 'Juan Pérez');
        });
        
        test('filters users by status', function () {
            $activeUser = User::factory()->create(['status' => 'active']);
            $inactiveUser = User::factory()->create(['status' => 'inactive']);
            
            $response = $this->getJson('/api/v1/users?status=active');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.id', $activeUser->id);
        });
        
        test('respects per_page parameter', function () {
            User::factory()->count(15)->create();
            
            $response = $this->getJson('/api/v1/users?per_page=10');
            
            $response->assertStatus(200)
                ->assertJsonPath('meta.per_page', 10)
                ->assertJsonCount(10, 'data');
        });
    });
    
    describe('POST /api/v1/users', function () {
        test('creates new user with valid data', function () {
            $userData = [
                'name' => 'Nuevo Usuario',
                'email' => 'nuevo@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123'
            ];
            
            $response = $this->postJson('/api/v1/users', $userData);
            
            $response->assertStatus(201)
                ->assertJsonPath('data.name', 'Nuevo Usuario')
                ->assertJsonPath('data.email', 'nuevo@example.com');
                
            $this->assertDatabaseHas('users', [
                'name' => 'Nuevo Usuario',
                'email' => 'nuevo@example.com'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $response = $this->postJson('/api/v1/users', []);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'email', 'password']);
        });
        
        test('returns 422 with duplicate email', function () {
            User::factory()->create(['email' => 'test@example.com']);
            
            $userData = [
                'name' => 'Usuario Duplicado',
                'email' => 'test@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123'
            ];
            
            $response = $this->postJson('/api/v1/users', $userData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
        });
        
        test('returns 422 with password confirmation mismatch', function () {
            $userData = [
                'name' => 'Usuario Test',
                'email' => 'test@example.com',
                'password' => 'password123',
                'password_confirmation' => 'different123'
            ];
            
            $response = $this->postJson('/api/v1/users', $userData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['password']);
        });
    });
    
    describe('GET /api/v1/users/{id}', function () {
        test('returns user details', function () {
            $user = User::factory()->create();
            
            $response = $this->getJson("/api/v1/users/{$user->id}");
            
            $response->assertStatus(200)
                ->assertJsonPath('data.id', $user->id)
                ->assertJsonPath('data.name', $user->name)
                ->assertJsonPath('data.email', $user->email);
        });
        
        test('returns 404 for non-existent user', function () {
            $response = $this->getJson('/api/v1/users/999');
            
            $response->assertStatus(404);
        });
    });
    
    describe('PUT /api/v1/users/{id}', function () {
        test('updates user with valid data', function () {
            $user = User::factory()->create();
            $updateData = [
                'name' => 'Nombre Actualizado',
                'email' => 'actualizado@example.com'
            ];
            
            $response = $this->putJson("/api/v1/users/{$user->id}", $updateData);
            
            $response->assertStatus(200)
                ->assertJsonPath('data.name', 'Nombre Actualizado')
                ->assertJsonPath('data.email', 'actualizado@example.com');
                
            $this->assertDatabaseHas('users', [
                'id' => $user->id,
                'name' => 'Nombre Actualizado',
                'email' => 'actualizado@example.com'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $user = User::factory()->create();
            
            $response = $this->putJson("/api/v1/users/{$user->id}", [
                'email' => 'invalid-email'
            ]);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
        });
        
        test('returns 404 for non-existent user', function () {
            $response = $this->putJson('/api/v1/users/999', [
                'name' => 'Test'
            ]);
            
            $response->assertStatus(404);
        });
    });
    
    describe('DELETE /api/v1/users/{id}', function () {
        test('deletes user successfully', function () {
            $user = User::factory()->create();
            
            $response = $this->deleteJson("/api/v1/users/{$user->id}");
            
            $response->assertStatus(204);
            $this->assertDatabaseMissing('users', ['id' => $user->id]);
        });
        
        test('returns 404 for non-existent user', function () {
            $response = $this->deleteJson('/api/v1/users/999');
            
            $response->assertStatus(404);
        });
    });
});
