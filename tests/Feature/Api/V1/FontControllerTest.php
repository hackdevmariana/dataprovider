<?php

use App\Models\User;
use App\Models\Font;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

describe('FontController', function () {
    
    describe('GET /api/v1/fonts', function () {
        test('returns paginated list of fonts', function () {
            Font::factory()->count(3)->create();
            
            $response = $this->getJson('/api/v1/fonts');
            
            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'family',
                            'style',
                            'weight',
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
        
        test('filters fonts by family', function () {
            Font::factory()->create(['family' => 'Arial']);
            Font::factory()->create(['family' => 'Times New Roman']);
            
            $response = $this->getJson('/api/v1/fonts?family=arial');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.family', 'Arial');
        });
        
        test('filters fonts by style', function () {
            Font::factory()->create(['style' => 'normal']);
            Font::factory()->create(['style' => 'italic']);
            
            $response = $this->getJson('/api/v1/fonts?style=normal');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.style', 'normal');
        });
        
        test('filters fonts by weight', function () {
            Font::factory()->create(['weight' => 'normal']);
            Font::factory()->create(['weight' => 'bold']);
            
            $response = $this->getJson('/api/v1/fonts?weight=bold');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.weight', 'bold');
        });
        
        test('filters fonts by is_active', function () {
            Font::factory()->create(['is_active' => true]);
            Font::factory()->create(['is_active' => false]);
            
            $response = $this->getJson('/api/v1/fonts?is_active=true');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.is_active', true);
        });
        
        test('searches fonts by name', function () {
            Font::factory()->create(['name' => 'Arial Regular']);
            Font::factory()->create(['name' => 'Times New Roman Regular']);
            
            $response = $this->getJson('/api/v1/fonts?search=arial');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.name', 'Arial Regular');
        });
        
        test('respects per_page parameter', function () {
            Font::factory()->count(15)->create();
            
            $response = $this->getJson('/api/v1/fonts?per_page=10');
            
            $response->assertStatus(200)
                ->assertJsonPath('meta.per_page', 10)
                ->assertJsonCount(10, 'data');
        });
    });
    
    describe('POST /api/v1/fonts', function () {
        test('creates new font with valid data', function () {
            $fontData = [
                'name' => 'Arial Regular',
                'family' => 'Arial',
                'style' => 'normal',
                'weight' => 'normal',
                'is_active' => true
            ];
            
            $response = $this->postJson('/api/v1/fonts', $fontData);
            
            $response->assertStatus(201)
                ->assertJsonPath('data.name', 'Arial Regular')
                ->assertJsonPath('data.family', 'Arial')
                ->assertJsonPath('data.style', 'normal');
                
            $this->assertDatabaseHas('fonts', [
                'name' => 'Arial Regular',
                'family' => 'Arial',
                'style' => 'normal'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $response = $this->postJson('/api/v1/fonts', []);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'family']);
        });
        
        test('returns 422 with invalid style', function () {
            $fontData = [
                'name' => 'Test Font',
                'family' => 'Test',
                'style' => 'invalid_style'
            ];
            
            $response = $this->postJson('/api/v1/fonts', $fontData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['style']);
        });
        
        test('returns 422 with invalid weight', function () {
            $fontData = [
                'name' => 'Test Font',
                'family' => 'Test',
                'style' => 'normal',
                'weight' => 'invalid_weight'
            ];
            
            $response = $this->postJson('/api/v1/fonts', $fontData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['weight']);
        });
        
        test('returns 422 with duplicate name', function () {
            Font::factory()->create(['name' => 'Arial Regular']);
            
            $fontData = [
                'name' => 'Arial Regular',
                'family' => 'Arial'
            ];
            
            $response = $this->postJson('/api/v1/fonts', $fontData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name']);
        });
    });
    
    describe('GET /api/v1/fonts/{id}', function () {
        test('returns font details', function () {
            $font = Font::factory()->create();
            
            $response = $this->getJson("/api/v1/fonts/{$font->id}");
            
            $response->assertStatus(200)
                ->assertJsonPath('data.id', $font->id)
                ->assertJsonPath('data.name', $font->name)
                ->assertJsonPath('data.family', $font->family);
        });
        
        test('returns 404 for non-existent font', function () {
            $response = $this->getJson('/api/v1/fonts/999');
            
            $response->assertStatus(404);
        });
    });
    
    describe('PUT /api/v1/fonts/{id}', function () {
        test('updates font with valid data', function () {
            $font = Font::factory()->create();
            $updateData = [
                'name' => 'Arial Bold',
                'weight' => 'bold'
            ];
            
            $response = $this->putJson("/api/v1/fonts/{$font->id}", $updateData);
            
            $response->assertStatus(200)
                ->assertJsonPath('data.name', 'Arial Bold')
                ->assertJsonPath('data.weight', 'bold');
                
            $this->assertDatabaseHas('fonts', [
                'id' => $font->id,
                'name' => 'Arial Bold',
                'weight' => 'bold'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $font = Font::factory()->create();
            
            $response = $this->putJson("/api/v1/fonts/{$font->id}", [
                'style' => 'invalid_style'
            ]);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['style']);
        });
        
        test('returns 404 for non-existent font', function () {
            $response = $this->putJson('/api/v1/fonts/999', [
                'name' => 'Updated Name'
            ]);
            
            $response->assertStatus(404);
        });
    });
    
    describe('DELETE /api/v1/fonts/{id}', function () {
        test('deletes font successfully', function () {
            $font = Font::factory()->create();
            
            $response = $this->deleteJson("/api/v1/fonts/{$font->id}");
            
            $response->assertStatus(204);
            $this->assertDatabaseMissing('fonts', ['id' => $font->id]);
        });
        
        test('returns 404 for non-existent font', function () {
            $response = $this->deleteJson('/api/v1/fonts/999');
            
            $response->assertStatus(404);
        });
    });
});
