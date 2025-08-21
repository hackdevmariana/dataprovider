<?php

use App\Models\User;
use App\Models\CooperativeUserMember;
use App\Models\Cooperative;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

describe('CooperativeUserMemberController', function () {
    
    describe('GET /api/v1/cooperative-user-members', function () {
        test('returns paginated list of cooperative user members', function () {
            CooperativeUserMember::factory()->count(3)->create(['user_id' => $this->user->id]);
            
            $response = $this->getJson('/api/v1/cooperative-user-members');
            
            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'cooperative_id',
                            'user_id',
                            'role',
                            'status',
                            'joined_at',
                            'cooperative',
                            'user'
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
        
        test('filters members by cooperative_id', function () {
            $cooperative = Cooperative::factory()->create();
            CooperativeUserMember::factory()->create([
                'user_id' => $this->user->id,
                'cooperative_id' => $cooperative->id
            ]);
            CooperativeUserMember::factory()->create([
                'user_id' => $this->user->id,
                'cooperative_id' => Cooperative::factory()->create()->id
            ]);
            
            $response = $this->getJson("/api/v1/cooperative-user-members?cooperative_id={$cooperative->id}");
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.cooperative_id', $cooperative->id);
        });
        
        test('filters members by user_id', function () {
            $otherUser = User::factory()->create();
            CooperativeUserMember::factory()->create([
                'user_id' => $otherUser->id,
                'cooperative_id' => Cooperative::factory()->create()->id
            ]);
            CooperativeUserMember::factory()->create([
                'user_id' => $this->user->id,
                'cooperative_id' => Cooperative::factory()->create()->id
            ]);
            
            $response = $this->getJson("/api/v1/cooperative-user-members?user_id={$this->user->id}");
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.user_id', $this->user->id);
        });
        
        test('filters members by role', function () {
            CooperativeUserMember::factory()->create([
                'user_id' => $this->user->id,
                'role' => 'member'
            ]);
            CooperativeUserMember::factory()->create([
                'user_id' => $this->user->id,
                'role' => 'admin'
            ]);
            
            $response = $this->getJson('/api/v1/cooperative-user-members?role=member');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.role', 'member');
        });
        
        test('filters members by status', function () {
            CooperativeUserMember::factory()->create([
                'user_id' => $this->user->id,
                'status' => 'active'
            ]);
            CooperativeUserMember::factory()->create([
                'user_id' => $this->user->id,
                'status' => 'inactive'
            ]);
            
            $response = $this->getJson('/api/v1/cooperative-user-members?status=active');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.status', 'active');
        });
        
        test('respects per_page parameter', function () {
            CooperativeUserMember::factory()->count(15)->create(['user_id' => $this->user->id]);
            
            $response = $this->getJson('/api/v1/cooperative-user-members?per_page=10');
            
            $response->assertStatus(200)
                ->assertJsonPath('meta.per_page', 10)
                ->assertJsonCount(10, 'data');
        });
    });
    
    describe('POST /api/v1/cooperative-user-members', function () {
        test('creates new cooperative user member with valid data', function () {
            $cooperative = Cooperative::factory()->create();
            $memberData = [
                'cooperative_id' => $cooperative->id,
                'role' => 'member',
                'status' => 'active'
            ];
            
            $response = $this->postJson('/api/v1/cooperative-user-members', $memberData);
            
            $response->assertStatus(201)
                ->assertJsonPath('data.cooperative_id', $cooperative->id)
                ->assertJsonPath('data.role', 'member')
                ->assertJsonPath('data.user_id', $this->user->id);
                
            $this->assertDatabaseHas('cooperative_user_members', [
                'cooperative_id' => $cooperative->id,
                'user_id' => $this->user->id,
                'role' => 'member'
            ]);
        });
        
        test('sets joined_at when creating member', function () {
            $cooperative = Cooperative::factory()->create();
            $memberData = [
                'cooperative_id' => $cooperative->id,
                'role' => 'member'
            ];
            
            $response = $this->postJson('/api/v1/cooperative-user-members', $memberData);
            
            $response->assertStatus(201)
                ->assertJsonPath('data.joined_at', function ($date) {
                    return !is_null($date);
                });
        });
        
        test('returns 422 with invalid data', function () {
            $response = $this->postJson('/api/v1/cooperative-user-members', []);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['cooperative_id', 'role']);
        });
        
        test('returns 422 with invalid role', function () {
            $cooperative = Cooperative::factory()->create();
            $memberData = [
                'cooperative_id' => $cooperative->id,
                'role' => 'invalid_role'
            ];
            
            $response = $this->postJson('/api/v1/cooperative-user-members', $memberData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['role']);
        });
        
        test('returns 422 with invalid cooperative_id', function () {
            $memberData = [
                'cooperative_id' => 999,
                'role' => 'member'
            ];
            
            $response = $this->postJson('/api/v1/cooperative-user-members', $memberData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['cooperative_id']);
        });
        
        test('returns 422 with duplicate member for same cooperative and user', function () {
            $cooperative = Cooperative::factory()->create();
            CooperativeUserMember::factory()->create([
                'cooperative_id' => $cooperative->id,
                'user_id' => $this->user->id
            ]);
            
            $memberData = [
                'cooperative_id' => $cooperative->id,
                'role' => 'admin'
            ];
            
            $response = $this->postJson('/api/v1/cooperative-user-members', $memberData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['cooperative_id']);
        });
    });
    
    describe('GET /api/v1/cooperative-user-members/{id}', function () {
        test('returns cooperative user member details', function () {
            $member = CooperativeUserMember::factory()->create(['user_id' => $this->user->id]);
            
            $response = $this->getJson("/api/v1/cooperative-user-members/{$member->id}");
            
            $response->assertStatus(200)
                ->assertJsonPath('data.id', $member->id)
                ->assertJsonPath('data.user_id', $this->user->id);
        });
        
        test('returns 404 for non-existent member', function () {
            $response = $this->getJson('/api/v1/cooperative-user-members/999');
            
            $response->assertStatus(404);
        });
    });
    
    describe('PUT /api/v1/cooperative-user-members/{id}', function () {
        test('updates cooperative user member with valid data', function () {
            $member = CooperativeUserMember::factory()->create(['user_id' => $this->user->id]);
            $updateData = [
                'role' => 'admin',
                'status' => 'inactive'
            ];
            
            $response = $this->putJson("/api/v1/cooperative-user-members/{$member->id}", $updateData);
            
            $response->assertStatus(200)
                ->assertJsonPath('data.role', 'admin')
                ->assertJsonPath('data.status', 'inactive');
                
            $this->assertDatabaseHas('cooperative_user_members', [
                'id' => $member->id,
                'role' => 'admin',
                'status' => 'inactive'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $member = CooperativeUserMember::factory()->create(['user_id' => $this->user->id]);
            
            $response = $this->putJson("/api/v1/cooperative-user-members/{$member->id}", [
                'role' => 'invalid_role'
            ]);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['role']);
        });
        
        test('returns 404 for non-existent member', function () {
            $response = $this->putJson('/api/v1/cooperative-user-members/999', [
                'role' => 'admin'
            ]);
            
            $response->assertStatus(404);
        });
    });
    
    describe('DELETE /api/v1/cooperative-user-members/{id}', function () {
        test('deletes cooperative user member successfully', function () {
            $member = CooperativeUserMember::factory()->create(['user_id' => $this->user->id]);
            
            $response = $this->deleteJson("/api/v1/cooperative-user-members/{$member->id}");
            
            $response->assertStatus(204);
            $this->assertDatabaseMissing('cooperative_user_members', ['id' => $member->id]);
        });
        
        test('returns 404 for non-existent member', function () {
            $response = $this->deleteJson('/api/v1/cooperative-user-members/999');
            
            $response->assertStatus(404);
        });
    });
});
