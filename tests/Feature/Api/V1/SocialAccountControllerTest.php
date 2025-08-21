<?php

use App\Models\User;
use App\Models\SocialAccount;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

describe('SocialAccountController', function () {
    
    describe('GET /api/v1/social-accounts', function () {
        test('returns paginated list of social accounts', function () {
            SocialAccount::factory()->count(3)->create(['user_id' => $this->user->id]);
            
            $response = $this->getJson('/api/v1/social-accounts');
            
            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'user_id',
                            'provider',
                            'provider_user_id',
                            'username',
                            'is_active'
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
        
        test('filters social accounts by provider', function () {
            SocialAccount::factory()->create([
                'user_id' => $this->user->id,
                'provider' => 'facebook'
            ]);
            SocialAccount::factory()->create([
                'user_id' => $this->user->id,
                'provider' => 'twitter'
            ]);
            
            $response = $this->getJson('/api/v1/social-accounts?provider=facebook');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.provider', 'facebook');
        });
        
        test('filters social accounts by is_active', function () {
            SocialAccount::factory()->create([
                'user_id' => $this->user->id,
                'is_active' => true
            ]);
            SocialAccount::factory()->create([
                'user_id' => $this->user->id,
                'is_active' => false
            ]);
            
            $response = $this->getJson('/api/v1/social-accounts?is_active=true');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.is_active', true);
        });
        
        test('searches social accounts by username', function () {
            SocialAccount::factory()->create([
                'user_id' => $this->user->id,
                'username' => 'johndoe'
            ]);
            SocialAccount::factory()->create([
                'user_id' => $this->user->id,
                'username' => 'janedoe'
            ]);
            
            $response = $this->getJson('/api/v1/social-accounts?search=john');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.username', 'johndoe');
        });
        
        test('only returns social accounts for authenticated user', function () {
            $otherUser = User::factory()->create();
            SocialAccount::factory()->create(['user_id' => $otherUser->id]);
            SocialAccount::factory()->create(['user_id' => $this->user->id]);
            
            $response = $this->getJson('/api/v1/social-accounts');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.user_id', $this->user->id);
        });
        
        test('respects per_page parameter', function () {
            SocialAccount::factory()->count(15)->create(['user_id' => $this->user->id]);
            
            $response = $this->getJson('/api/v1/social-accounts?per_page=10');
            
            $response->assertStatus(200)
                ->assertJsonPath('meta.per_page', 10)
                ->assertJsonCount(10, 'data');
        });
    });
    
    describe('POST /api/v1/social-accounts', function () {
        test('creates new social account with valid data', function () {
            $socialAccountData = [
                'provider' => 'facebook',
                'provider_user_id' => '12345',
                'username' => 'johndoe',
                'is_active' => true
            ];
            
            $response = $this->postJson('/api/v1/social-accounts', $socialAccountData);
            
            $response->assertStatus(201)
                ->assertJsonPath('data.provider', 'facebook')
                ->assertJsonPath('data.username', 'johndoe')
                ->assertJsonPath('data.user_id', $this->user->id);
                
            $this->assertDatabaseHas('social_accounts', [
                'provider' => 'facebook',
                'username' => 'johndoe',
                'user_id' => $this->user->id
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $response = $this->postJson('/api/v1/social-accounts', []);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['provider', 'provider_user_id']);
        });
        
        test('returns 422 with invalid provider', function () {
            $socialAccountData = [
                'provider' => 'invalid_provider',
                'provider_user_id' => '12345'
            ];
            
            $response = $this->postJson('/api/v1/social-accounts', $socialAccountData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['provider']);
        });
        
        test('returns 422 with duplicate provider for same user', function () {
            SocialAccount::factory()->create([
                'user_id' => $this->user->id,
                'provider' => 'facebook'
            ]);
            
            $socialAccountData = [
                'provider' => 'facebook',
                'provider_user_id' => '67890'
            ];
            
            $response = $this->postJson('/api/v1/social-accounts', $socialAccountData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['provider']);
        });
    });
    
    describe('GET /api/v1/social-accounts/{id}', function () {
        test('returns social account details', function () {
            $socialAccount = SocialAccount::factory()->create(['user_id' => $this->user->id]);
            
            $response = $this->getJson("/api/v1/social-accounts/{$socialAccount->id}");
            
            $response->assertStatus(200)
                ->assertJsonPath('data.id', $socialAccount->id)
                ->assertJsonPath('data.provider', $socialAccount->provider)
                ->assertJsonPath('data.username', $socialAccount->username);
        });
        
        test('returns 404 for non-existent social account', function () {
            $response = $this->getJson('/api/v1/social-accounts/999');
            
            $response->assertStatus(404);
        });
        
        test('returns 404 for social account belonging to other user', function () {
            $otherUser = User::factory()->create();
            $socialAccount = SocialAccount::factory()->create(['user_id' => $otherUser->id]);
            
            $response = $this->getJson("/api/v1/social-accounts/{$socialAccount->id}");
            
            $response->assertStatus(404);
        });
    });
    
    describe('PUT /api/v1/social-accounts/{id}', function () {
        test('updates social account with valid data', function () {
            $socialAccount = SocialAccount::factory()->create(['user_id' => $this->user->id]);
            $updateData = [
                'username' => 'johndoe_updated',
                'is_active' => false
            ];
            
            $response = $this->putJson("/api/v1/social-accounts/{$socialAccount->id}", $updateData);
            
            $response->assertStatus(200)
                ->assertJsonPath('data.username', 'johndoe_updated')
                ->assertJsonPath('data.is_active', false);
                
            $this->assertDatabaseHas('social_accounts', [
                'id' => $socialAccount->id,
                'username' => 'johndoe_updated',
                'is_active' => false
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $socialAccount = SocialAccount::factory()->create(['user_id' => $this->user->id]);
            
            $response = $this->putJson("/api/v1/social-accounts/{$socialAccount->id}", [
                'provider' => 'invalid_provider'
            ]);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['provider']);
        });
        
        test('returns 404 for social account belonging to other user', function () {
            $otherUser = User::factory()->create();
            $socialAccount = SocialAccount::factory()->create(['user_id' => $otherUser->id]);
            
            $response = $this->putJson("/api/v1/social-accounts/{$socialAccount->id}", [
                'username' => 'updated_username'
            ]);
            
            $response->assertStatus(404);
        });
    });
    
    describe('DELETE /api/v1/social-accounts/{id}', function () {
        test('deletes social account successfully', function () {
            $socialAccount = SocialAccount::factory()->create(['user_id' => $this->user->id]);
            
            $response = $this->deleteJson("/api/v1/social-accounts/{$socialAccount->id}");
            
            $response->assertStatus(204);
            $this->assertDatabaseMissing('social_accounts', ['id' => $socialAccount->id]);
        });
        
        test('returns 404 for social account belonging to other user', function () {
            $otherUser = User::factory()->create();
            $socialAccount = SocialAccount::factory()->create(['user_id' => $otherUser->id]);
            
            $response = $this->deleteJson("/api/v1/social-accounts/{$socialAccount->id}");
            
            $response->assertStatus(404);
        });
    });
});
