<?php

use App\Models\User;
use App\Models\Currency;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

describe('CurrencyController', function () {
    
    describe('GET /api/v1/currencies', function () {
        test('returns paginated list of currencies', function () {
            Currency::factory()->count(3)->create();
            
            $response = $this->getJson('/api/v1/currencies');
            
            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'code',
                            'symbol',
                            'exchange_rate',
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
        
        test('filters currencies by is_active', function () {
            Currency::factory()->create(['is_active' => true]);
            Currency::factory()->create(['is_active' => false]);
            
            $response = $this->getJson('/api/v1/currencies?is_active=true');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.is_active', true);
        });
        
        test('searches currencies by name or code', function () {
            Currency::factory()->create(['name' => 'US Dollar', 'code' => 'USD']);
            Currency::factory()->create(['name' => 'Euro', 'code' => 'EUR']);
            
            $response = $this->getJson('/api/v1/currencies?search=dollar');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.name', 'US Dollar');
        });
        
        test('respects per_page parameter', function () {
            Currency::factory()->count(15)->create();
            
            $response = $this->getJson('/api/v1/currencies?per_page=10');
            
            $response->assertStatus(200)
                ->assertJsonPath('meta.per_page', 10)
                ->assertJsonCount(10, 'data');
        });
    });
    
    describe('POST /api/v1/currencies', function () {
        test('creates new currency with valid data', function () {
            $currencyData = [
                'name' => 'US Dollar',
                'code' => 'USD',
                'symbol' => '$',
                'exchange_rate' => 1.0,
                'is_active' => true
            ];
            
            $response = $this->postJson('/api/v1/currencies', $currencyData);
            
            $response->assertStatus(201)
                ->assertJsonPath('data.name', 'US Dollar')
                ->assertJsonPath('data.code', 'USD')
                ->assertJsonPath('data.symbol', '$');
                
            $this->assertDatabaseHas('currencies', [
                'name' => 'US Dollar',
                'code' => 'USD',
                'symbol' => '$'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $response = $this->postJson('/api/v1/currencies', []);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'code', 'symbol']);
        });
        
        test('returns 422 with invalid exchange rate', function () {
            $currencyData = [
                'name' => 'Test Currency',
                'code' => 'TST',
                'symbol' => 'T',
                'exchange_rate' => -1.0
            ];
            
            $response = $this->postJson('/api/v1/currencies', $currencyData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['exchange_rate']);
        });
        
        test('returns 422 with duplicate code', function () {
            Currency::factory()->create(['code' => 'USD']);
            
            $currencyData = [
                'name' => 'Another Dollar',
                'code' => 'USD',
                'symbol' => '$'
            ];
            
            $response = $this->postJson('/api/v1/currencies', $currencyData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['code']);
        });
        
        test('returns 422 with code longer than 3 characters', function () {
            $currencyData = [
                'name' => 'Test Currency',
                'code' => 'TEST',
                'symbol' => 'T'
            ];
            
            $response = $this->postJson('/api/v1/currencies', $currencyData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['code']);
        });
    });
    
    describe('GET /api/v1/currencies/{id}', function () {
        test('returns currency details', function () {
            $currency = Currency::factory()->create();
            
            $response = $this->getJson("/api/v1/currencies/{$currency->id}");
            
            $response->assertStatus(200)
                ->assertJsonPath('data.id', $currency->id)
                ->assertJsonPath('data.name', $currency->name)
                ->assertJsonPath('data.code', $currency->code);
        });
        
        test('returns 404 for non-existent currency', function () {
            $response = $this->getJson('/api/v1/currencies/999');
            
            $response->assertStatus(404);
        });
    });
    
    describe('PUT /api/v1/currencies/{id}', function () {
        test('updates currency with valid data', function () {
            $currency = Currency::factory()->create();
            $updateData = [
                'name' => 'Updated US Dollar',
                'exchange_rate' => 1.1
            ];
            
            $response = $this->putJson("/api/v1/currencies/{$currency->id}", $updateData);
            
            $response->assertStatus(200)
                ->assertJsonPath('data.name', 'Updated US Dollar')
                ->assertJsonPath('data.exchange_rate', 1.1);
                
            $this->assertDatabaseHas('currencies', [
                'id' => $currency->id,
                'name' => 'Updated US Dollar',
                'exchange_rate' => 1.1
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $currency = Currency::factory()->create();
            
            $response = $this->putJson("/api/v1/currencies/{$currency->id}", [
                'exchange_rate' => -1.0
            ]);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['exchange_rate']);
        });
        
        test('returns 404 for non-existent currency', function () {
            $response = $this->putJson('/api/v1/currencies/999', [
                'name' => 'Updated Name'
            ]);
            
            $response->assertStatus(404);
        });
    });
    
    describe('DELETE /api/v1/currencies/{id}', function () {
        test('deletes currency successfully', function () {
            $currency = Currency::factory()->create();
            
            $response = $this->deleteJson("/api/v1/currencies/{$currency->id}");
            
            $response->assertStatus(204);
            $this->assertDatabaseMissing('currencies', ['id' => $currency->id]);
        });
        
        test('returns 404 for non-existent currency', function () {
            $response = $this->deleteJson('/api/v1/currencies/999');
            
            $response->assertStatus(404);
        });
    });
});
