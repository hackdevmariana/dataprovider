<?php

namespace Tests\Feature\Feature\Api\V1;

use App\Models\ElectricityPrice;
use App\Models\PriceUnit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class ElectricityPricesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear la unidad de precio necesaria
        PriceUnit::create([
            'name' => 'Euro por megavatio hora',
            'short_name' => '€/MWh',
            'unit_code' => 'EUR_MWH',
            'conversion_factor' => 1.0,
        ]);
    }

    public function test_can_list_electricity_prices()
    {
        // Crear algunos precios de prueba
        ElectricityPrice::factory(5)->create([
            'price_unit_id' => PriceUnit::first()->id,
        ]);

        $response = $this->getJson('/api/v1/electricity-prices');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'date',
                            'hour',
                            'datetime',
                            'type',
                            'price_eur_mwh',
                            'price_eur_kwh',
                            'forecast_for_tomorrow',
                            'source',
                            'price_unit',
                            'created_at',
                            'updated_at',
                        ]
                    ],
                    'links',
                    'meta'
                ]);
    }

    public function test_can_get_electricity_price_by_id()
    {
        $price = ElectricityPrice::factory()->create([
            'price_unit_id' => PriceUnit::first()->id,
        ]);

        $response = $this->getJson("/api/v1/electricity-prices/{$price->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'date',
                        'hour',
                        'datetime',
                        'type',
                        'price_eur_mwh',
                        'price_eur_kwh',
                        'forecast_for_tomorrow',
                        'source',
                        'price_unit',
                        'created_at',
                        'updated_at',
                    ]
                ])
                ->assertJson([
                    'data' => [
                        'id' => $price->id,
                        'type' => $price->type,
                    ]
                ]);
    }

    public function test_can_get_todays_electricity_prices()
    {
        // Crear precios para hoy
        ElectricityPrice::factory(3)->create([
            'date' => Carbon::today()->format('Y-m-d'),
            'price_unit_id' => PriceUnit::first()->id,
        ]);

        // Crear precios para ayer (no deberían aparecer)
        ElectricityPrice::factory(2)->create([
            'date' => Carbon::yesterday()->format('Y-m-d'),
            'price_unit_id' => PriceUnit::first()->id,
        ]);

        $response = $this->getJson('/api/v1/electricity-prices/today');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'date',
                            'hour',
                            'type',
                            'price_eur_mwh',
                        ]
                    ]
                ]);

        // Verificar que solo se devuelven precios de hoy
        $data = $response->json('data');
        foreach ($data as $price) {
            $this->assertEquals(Carbon::today()->format('Y-m-d'), $price['date']);
        }
    }

    public function test_can_get_current_hour_electricity_price()
    {
        $currentHour = Carbon::now()->hour;
        
        // Crear precio para la hora actual
        ElectricityPrice::factory()->create([
            'date' => Carbon::today()->format('Y-m-d'),
            'hour' => $currentHour,
            'type' => 'pvpc',
            'price_unit_id' => PriceUnit::first()->id,
        ]);

        $response = $this->getJson('/api/v1/electricity-prices/current-hour');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'date',
                        'hour',
                        'type',
                        'price_eur_mwh',
                    ]
                ])
                ->assertJson([
                    'data' => [
                        'date' => Carbon::today()->format('Y-m-d'),
                        'hour' => $currentHour,
                    ]
                ]);
    }

    public function test_can_get_cheapest_hours()
    {
        // Crear precios con diferentes valores para hoy
        $prices = [
            ['hour' => 0, 'price_eur_mwh' => 50.0],
            ['hour' => 1, 'price_eur_mwh' => 30.0], // Más barato
            ['hour' => 2, 'price_eur_mwh' => 80.0],
            ['hour' => 3, 'price_eur_mwh' => 25.0], // El más barato
            ['hour' => 4, 'price_eur_mwh' => 45.0],
        ];

        foreach ($prices as $priceData) {
            ElectricityPrice::factory()->create([
                'date' => Carbon::today()->format('Y-m-d'),
                'hour' => $priceData['hour'],
                'price_eur_mwh' => $priceData['price_eur_mwh'],
                'type' => 'pvpc',
                'price_unit_id' => PriceUnit::first()->id,
            ]);
        }

        $response = $this->getJson('/api/v1/electricity-prices/cheapest-hours?hours=3');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'date',
                            'hour',
                            'price_eur_mwh',
                        ]
                    ]
                ]);

        $data = $response->json('data');
        
        // Verificar que se devuelven exactamente 3 horas
        $this->assertCount(3, $data);
        
        // Verificar que están ordenadas por precio (más barato primero)
        $this->assertEquals(25.0, $data[0]['price_eur_mwh']); // Hora 3
        $this->assertEquals(30.0, $data[1]['price_eur_mwh']); // Hora 1
        $this->assertEquals(45.0, $data[2]['price_eur_mwh']); // Hora 4
    }

    public function test_can_get_daily_summary()
    {
        // Crear precios para hoy con diferentes valores
        $prices = [
            ['hour' => 0, 'price_eur_mwh' => 50.0],
            ['hour' => 1, 'price_eur_mwh' => 30.0],
            ['hour' => 2, 'price_eur_mwh' => 80.0],
        ];

        foreach ($prices as $priceData) {
            ElectricityPrice::factory()->create([
                'date' => Carbon::today()->format('Y-m-d'),
                'hour' => $priceData['hour'],
                'price_eur_mwh' => $priceData['price_eur_mwh'],
                'type' => 'pvpc',
                'price_unit_id' => PriceUnit::first()->id,
            ]);
        }

        $response = $this->getJson('/api/v1/electricity-prices/daily-summary');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'date',
                    'type',
                    'summary' => [
                        'min_price_eur_mwh',
                        'max_price_eur_mwh',
                        'avg_price_eur_mwh',
                        'min_price_eur_kwh',
                        'max_price_eur_kwh',
                        'avg_price_eur_kwh',
                    ]
                ]);

        $summary = $response->json('summary');
        
        // Verificar cálculos
        $this->assertEquals(30.0, $summary['min_price_eur_mwh']);
        $this->assertEquals(80.0, $summary['max_price_eur_mwh']);
        $this->assertEquals(53.33, $summary['avg_price_eur_mwh']); // (50+30+80)/3
    }

    public function test_returns_404_for_nonexistent_electricity_price()
    {
        $response = $this->getJson('/api/v1/electricity-prices/99999');

        $response->assertStatus(404);
    }

    public function test_current_hour_returns_404_when_no_price_available()
    {
        // No crear ningún precio para la hora actual
        $response = $this->getJson('/api/v1/electricity-prices/current-hour');

        $response->assertStatus(404);
    }

    public function test_cheapest_hours_validates_hours_parameter()
    {
        // Test con parámetro hours muy alto
        $response = $this->getJson('/api/v1/electricity-prices/cheapest-hours?hours=100');

        $response->assertStatus(422);

        // Test con parámetro hours negativo
        $response = $this->getJson('/api/v1/electricity-prices/cheapest-hours?hours=-1');

        $response->assertStatus(422);
    }

    public function test_electricity_prices_include_correct_price_conversions()
    {
        $price = ElectricityPrice::factory()->create([
            'price_eur_mwh' => 100.0, // 100 €/MWh = 0.1 €/kWh
            'price_unit_id' => PriceUnit::first()->id,
        ]);

        $response = $this->getJson("/api/v1/electricity-prices/{$price->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'price_eur_mwh' => 100.0,
                        'price_eur_kwh' => 0.1,
                    ]
                ]);
    }
}