<?php

use App\Models\User;
use App\Models\Color;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

describe('ColorController', function () {
    
    describe('GET /api/v1/colors', function () {
        test('returns paginated list of colors', function () {
            Color::factory()->count(3)->create();
            
            $response = $this->getJson('/api/v1/colors');
            
            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'hex_code',
                            'rgb_values',
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
        
        test('filters colors by is_active', function () {
            Color::factory()->create(['is_active' => true]);
            Color::factory()->create(['is_active' => false]);
            
            $response = $this->getJson('/api/v1/colors?is_active=true');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.is_active', true);
        });
        
        test('searches colors by name', function () {
            Color::factory()->create(['name' => 'Azul Marino']);
            Color::factory()->create(['name' => 'Verde Bosque']);
            
            $response = $this->getJson('/api/v1/colors?search=azul');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.name', 'Azul Marino');
        });
        
        test('respects per_page parameter', function () {
            Color::factory()->count(15)->create();
            
            $response = $this->getJson('/api/v1/colors?per_page=10');
            
            $response->assertStatus(200)
                ->assertJsonPath('meta.per_page', 10)
                ->assertJsonCount(10, 'data');
        });
    });
    
    describe('POST /api/v1/colors', function () {
        test('creates new color with valid data', function () {
            $colorData = [
                'name' => 'Azul Marino',
                'hex_code' => '#1B365D',
                'rgb_values' => '27, 54, 93',
                'is_active' => true
            ];
            
            $response = $this->postJson('/api/v1/colors', $colorData);
            
            $response->assertStatus(201)
                ->assertJsonPath('data.name', 'Azul Marino')
                ->assertJsonPath('data.hex_code', '#1B365D')
                ->assertJsonPath('data.rgb_values', '27, 54, 93');
                
            $this->assertDatabaseHas('colors', [
                'name' => 'Azul Marino',
                'hex_code' => '#1B365D',
                'rgb_values' => '27, 54, 93'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $response = $this->postJson('/api/v1/colors', []);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'hex_code']);
        });
        
        test('returns 422 with invalid hex code format', function () {
            $colorData = [
                'name' => 'Test Color',
                'hex_code' => 'invalid-hex'
            ];
            
            $response = $this->postJson('/api/v1/colors', $colorData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['hex_code']);
        });
        
        test('returns 422 with invalid RGB values format', function () {
            $colorData = [
                'name' => 'Test Color',
                'hex_code' => '#FF0000',
                'rgb_values' => 'invalid-rgb'
            ];
            
            $response = $this->postJson('/api/v1/colors', $colorData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['rgb_values']);
        });
        
        test('returns 422 with duplicate hex code', function () {
            Color::factory()->create(['hex_code' => '#FF0000']);
            
            $colorData = [
                'name' => 'Another Color',
                'hex_code' => '#FF0000'
            ];
            
            $response = $this->postJson('/api/v1/colors', $colorData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['hex_code']);
        });
    });
    
    describe('GET /api/v1/colors/{id}', function () {
        test('returns color details', function () {
            $color = Color::factory()->create();
            
            $response = $this->getJson("/api/v1/colors/{$color->id}");
            
            $response->assertStatus(200)
                ->assertJsonPath('data.id', $color->id)
                ->assertJsonPath('data.name', $color->name)
                ->assertJsonPath('data.hex_code', $color->hex_code);
        });
        
        test('returns 404 for non-existent color', function () {
            $response = $this->getJson('/api/v1/colors/999');
            
            $response->assertStatus(404);
        });
    });
    
    describe('PUT /api/v1/colors/{id}', function () {
        test('updates color with valid data', function () {
            $color = Color::factory()->create();
            $updateData = [
                'name' => 'Azul Marino Oscuro',
                'hex_code' => '#0F1B2D'
            ];
            
            $response = $this->putJson("/api/v1/colors/{$color->id}", $updateData);
            
            $response->assertStatus(200)
                ->assertJsonPath('data.name', 'Azul Marino Oscuro')
                ->assertJsonPath('data.hex_code', '#0F1B2D');
                
            $this->assertDatabaseHas('colors', [
                'id' => $color->id,
                'name' => 'Azul Marino Oscuro',
                'hex_code' => '#0F1B2D'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $color = Color::factory()->create();
            
            $response = $this->putJson("/api/v1/colors/{$color->id}", [
                'hex_code' => 'invalid-hex'
            ]);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['hex_code']);
        });
        
        test('returns 404 for non-existent color', function () {
            $response = $this->putJson('/api/v1/colors/999', [
                'name' => 'Updated Name'
            ]);
            
            $response->assertStatus(404);
        });
    });
    
    describe('DELETE /api/v1/colors/{id}', function () {
        test('deletes color successfully', function () {
            $color = Color::factory()->create();
            
            $response = $this->deleteJson("/api/v1/colors/{$color->id}");
            
            $response->assertStatus(204);
            $this->assertDatabaseMissing('colors', ['id' => $color->id]);
        });
        
        test('returns 404 for non-existent color', function () {
            $response = $this->deleteJson('/api/v1/colors/999');
            
            $response->assertStatus(404);
        });
    });
});
