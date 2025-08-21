<?php

use App\Models\User;
use App\Models\Appearance;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

describe('AppearanceController', function () {
    
    describe('GET /api/v1/appearances', function () {
        test('returns paginated list of appearances', function () {
            Appearance::factory()->count(3)->create();
            
            $response = $this->getJson('/api/v1/appearances');
            
            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'theme',
                            'description',
                            'is_active',
                            'primary_color'
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
        
        test('filters appearances by theme', function () {
            Appearance::factory()->create(['theme' => 'light']);
            Appearance::factory()->create(['theme' => 'dark']);
            
            $response = $this->getJson('/api/v1/appearances?theme=light');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.theme', 'light');
        });
        
        test('filters appearances by is_active', function () {
            Appearance::factory()->create(['is_active' => true]);
            Appearance::factory()->create(['is_active' => false]);
            
            $response = $this->getJson('/api/v1/appearances?is_active=true');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.is_active', true);
        });
        
        test('searches appearances by name or description', function () {
            Appearance::factory()->create(['name' => 'Tema Moderno']);
            Appearance::factory()->create(['name' => 'Tema Clásico']);
            
            $response = $this->getJson('/api/v1/appearances?search=moderno');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.name', 'Tema Moderno');
        });
        
        test('respects per_page parameter', function () {
            Appearance::factory()->count(15)->create();
            
            $response = $this->getJson('/api/v1/appearances?per_page=10');
            
            $response->assertStatus(200)
                ->assertJsonPath('meta.per_page', 10)
                ->assertJsonCount(10, 'data');
        });
    });
    
    describe('POST /api/v1/appearances', function () {
        test('creates new appearance with valid data', function () {
            $appearanceData = [
                'name' => 'Tema Moderno',
                'theme' => 'light',
                'description' => 'Tema claro con diseño moderno',
                'primary_color' => '#2196F3',
                'secondary_color' => '#FFC107',
                'accent_color' => '#4CAF50',
                'background_color' => '#FFFFFF',
                'text_color' => '#212121',
                'is_active' => true
            ];
            
            $response = $this->postJson('/api/v1/appearances', $appearanceData);
            
            $response->assertStatus(201)
                ->assertJsonPath('data.name', 'Tema Moderno')
                ->assertJsonPath('data.theme', 'light')
                ->assertJsonPath('data.primary_color', '#2196F3');
                
            $this->assertDatabaseHas('appearances', [
                'name' => 'Tema Moderno',
                'theme' => 'light',
                'is_active' => true
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $response = $this->postJson('/api/v1/appearances', []);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'theme']);
        });
        
        test('returns 422 with invalid theme', function () {
            $appearanceData = [
                'name' => 'Test Theme',
                'theme' => 'invalid_theme'
            ];
            
            $response = $this->postJson('/api/v1/appearances', $appearanceData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['theme']);
        });
        
        test('returns 422 with invalid color format', function () {
            $appearanceData = [
                'name' => 'Test Theme',
                'theme' => 'light',
                'primary_color' => 'invalid-color'
            ];
            
            $response = $this->postJson('/api/v1/appearances', $appearanceData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['primary_color']);
        });
        
        test('returns 422 with duplicate name', function () {
            Appearance::factory()->create(['name' => 'Test Theme']);
            
            $appearanceData = [
                'name' => 'Test Theme',
                'theme' => 'light'
            ];
            
            $response = $this->postJson('/api/v1/appearances', $appearanceData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name']);
        });
    });
    
    describe('GET /api/v1/appearances/{id}', function () {
        test('returns appearance details', function () {
            $appearance = Appearance::factory()->create();
            
            $response = $this->getJson("/api/v1/appearances/{$appearance->id}");
            
            $response->assertStatus(200)
                ->assertJsonPath('data.id', $appearance->id)
                ->assertJsonPath('data.name', $appearance->name)
                ->assertJsonPath('data.theme', $appearance->theme);
        });
        
        test('returns 404 for non-existent appearance', function () {
            $response = $this->getJson('/api/v1/appearances/999');
            
            $response->assertStatus(404);
        });
    });
    
    describe('PUT /api/v1/appearances/{id}', function () {
        test('updates appearance with valid data', function () {
            $appearance = Appearance::factory()->create();
            $updateData = [
                'name' => 'Tema Moderno Actualizado',
                'primary_color' => '#1976D2'
            ];
            
            $response = $this->putJson("/api/v1/appearances/{$appearance->id}", $updateData);
            
            $response->assertStatus(200)
                ->assertJsonPath('data.name', 'Tema Moderno Actualizado')
                ->assertJsonPath('data.primary_color', '#1976D2');
                
            $this->assertDatabaseHas('appearances', [
                'id' => $appearance->id,
                'name' => 'Tema Moderno Actualizado',
                'primary_color' => '#1976D2'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $appearance = Appearance::factory()->create();
            
            $response = $this->putJson("/api/v1/appearances/{$appearance->id}", [
                'primary_color' => 'invalid-color'
            ]);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['primary_color']);
        });
        
        test('returns 404 for non-existent appearance', function () {
            $response = $this->putJson('/api/v1/appearances/999', [
                'name' => 'Updated Name'
            ]);
            
            $response->assertStatus(404);
        });
    });
    
    describe('DELETE /api/v1/appearances/{id}', function () {
        test('deletes appearance successfully', function () {
            $appearance = Appearance::factory()->create();
            
            $response = $this->deleteJson("/api/v1/appearances/{$appearance->id}");
            
            $response->assertStatus(204);
            $this->assertDatabaseMissing('appearances', ['id' => $appearance->id]);
        });
        
        test('returns 404 for non-existent appearance', function () {
            $response = $this->deleteJson('/api/v1/appearances/999');
            
            $response->assertStatus(404);
        });
    });
});
