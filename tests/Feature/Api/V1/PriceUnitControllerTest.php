<?php

use App\Models\User;
use App\Models\PriceUnit;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

describe('PriceUnitController', function () {
    
    describe('GET /api/v1/price-units', function () {
        test('returns paginated list of price units', function () {
            PriceUnit::factory()->count(3)->create();
            
            $response = $this->getJson('/api/v1/price-units');
            
            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'symbol',
                            'description',
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
        
        test('filters price units by is_active', function () {
            PriceUnit::factory()->create(['is_active' => true]);
            PriceUnit::factory()->create(['is_active' => false]);
            
            $response = $this->getJson('/api/v1/price-units?is_active=true');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.is_active', true);
        });
        
        test('searches price units by name or description', function () {
            PriceUnit::factory()->create(['name' => 'Euro por hora']);
            PriceUnit::factory()->create(['name' => 'Dólar por día']);
            
            $response = $this->getJson('/api/v1/price-units?search=euro');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.name', 'Euro por hora');
        });
        
        test('respects per_page parameter', function () {
            PriceUnit::factory()->count(15)->create();
            
            $response = $this->getJson('/api/v1/price-units?per_page=10');
            
            $response->assertStatus(200)
                ->assertJsonPath('meta.per_page', 10)
                ->assertJsonCount(10, 'data');
        });
    });
    
    describe('POST /api/v1/price-units', function () {
        test('creates new price unit with valid data', function () {
            $priceUnitData = [
                'name' => 'Euro por hora',
                'symbol' => '€/h',
                'description' => 'Precio en euros por hora de servicio',
                'is_active' => true
            ];
            
            $response = $this->postJson('/api/v1/price-units', $priceUnitData);
            
            $response->assertStatus(201)
                ->assertJsonPath('data.name', 'Euro por hora')
                ->assertJsonPath('data.symbol', '€/h')
                ->assertJsonPath('data.is_active', true);
                
            $this->assertDatabaseHas('price_units', [
                'name' => 'Euro por hora',
                'symbol' => '€/h',
                'is_active' => true
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $response = $this->postJson('/api/v1/price-units', []);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'symbol']);
        });
        
        test('returns 422 with duplicate symbol', function () {
            PriceUnit::factory()->create(['symbol' => '€/h']);
            
            $priceUnitData = [
                'name' => 'Another Euro per Hour',
                'symbol' => '€/h'
            ];
            
            $response = $this->postJson('/api/v1/price-units', $priceUnitData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['symbol']);
        });
        
        test('returns 422 with symbol too long', function () {
            $priceUnitData = [
                'name' => 'Test Unit',
                'symbol' => 'very-long-symbol-that-exceeds-limit'
            ];
            
            $response = $this->postJson('/api/v1/price-units', $priceUnitData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['symbol']);
        });
    });
    
    describe('GET /api/v1/price-units/{id}', function () {
        test('returns price unit details', function () {
            $priceUnit = PriceUnit::factory()->create();
            
            $response = $this->getJson("/api/v1/price-units/{$priceUnit->id}");
            
            $response->assertStatus(200)
                ->assertJsonPath('data.id', $priceUnit->id)
                ->assertJsonPath('data.name', $priceUnit->name)
                ->assertJsonPath('data.symbol', $priceUnit->symbol);
        });
        
        test('returns 404 for non-existent price unit', function () {
            $response = $this->getJson('/api/v1/price-units/999');
            
            $response->assertStatus(404);
        });
    });
    
    describe('PUT /api/v1/price-units/{id}', function () {
        test('updates price unit with valid data', function () {
            $priceUnit = PriceUnit::factory()->create();
            $updateData = [
                'name' => 'Euro por hora actualizado',
                'description' => 'Descripción actualizada'
            ];
            
            $response = $this->putJson("/api/v1/price-units/{$priceUnit->id}", $updateData);
            
            $response->assertStatus(200)
                ->assertJsonPath('data.name', 'Euro por hora actualizado')
                ->assertJsonPath('data.description', 'Descripción actualizada');
                
            $this->assertDatabaseHas('price_units', [
                'id' => $priceUnit->id,
                'name' => 'Euro por hora actualizado',
                'description' => 'Descripción actualizada'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $priceUnit = PriceUnit::factory()->create();
            
            $response = $this->putJson("/api/v1/price-units/{$priceUnit->id}", [
                'symbol' => 'very-long-symbol-that-exceeds-limit'
            ]);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['symbol']);
        });
        
        test('returns 404 for non-existent price unit', function () {
            $response = $this->putJson('/api/v1/price-units/999', [
                'name' => 'Updated Name'
            ]);
            
            $response->assertStatus(404);
        });
    });
    
    describe('DELETE /api/v1/price-units/{id}', function () {
        test('deletes price unit successfully', function () {
            $priceUnit = PriceUnit::factory()->create();
            
            $response = $this->deleteJson("/api/v1/price-units/{$priceUnit->id}");
            
            $response->assertStatus(204);
            $this->assertDatabaseMissing('price_units', ['id' => $priceUnit->id]);
        });
        
        test('returns 404 for non-existent price unit', function () {
            $response = $this->deleteJson('/api/v1/price-units/999');
            
            $response->assertStatus(404);
        });
    });
});
