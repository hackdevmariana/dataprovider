<?php

namespace Tests\Feature\Feature\Api\V1;

use App\Models\EnergyCompany;
use App\Models\Municipality;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnergyCompaniesTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_energy_companies()
    {
        EnergyCompany::factory(5)->create();

        $response = $this->getJson('/api/v1/energy-companies');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'slug',
                            'website',
                            'contact',
                            'highlighted_offer',
                            'cnmc_id',
                            'logo_url',
                            'company_type',
                            'address',
                            'coverage_scope',
                            'municipality',
                            'image',
                            'created_at',
                            'updated_at',
                        ]
                    ],
                    'links',
                    'meta'
                ]);
    }

    public function test_can_get_energy_company_by_id()
    {
        $company = EnergyCompany::factory()->create();

        $response = $this->getJson("/api/v1/energy-companies/{$company->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'name',
                        'slug',
                        'website',
                        'contact',
                        'company_type',
                        'coverage_scope',
                    ]
                ])
                ->assertJson([
                    'data' => [
                        'id' => $company->id,
                        'name' => $company->name,
                        'slug' => $company->slug,
                    ]
                ]);
    }

    public function test_can_get_energy_company_by_slug()
    {
        $company = EnergyCompany::factory()->create([
            'slug' => 'test-company',
        ]);

        $response = $this->getJson('/api/v1/energy-companies/test-company');

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'id' => $company->id,
                        'slug' => 'test-company',
                    ]
                ]);
    }

    public function test_can_filter_companies_by_location()
    {
        $madrid = Municipality::factory()->create([
            'name' => 'Madrid',
            'latitude' => 40.4168,
            'longitude' => -3.7038,
        ]);

        // Crear empresa en Madrid
        $madridCompany = EnergyCompany::factory()->create([
            'municipality_id' => $madrid->id,
            'latitude' => 40.4168,
            'longitude' => -3.7038,
        ]);

        // Crear empresa lejos de Madrid
        $farCompany = EnergyCompany::factory()->create([
            'latitude' => 41.3851,
            'longitude' => 2.1734, // Barcelona
        ]);

        // Buscar empresas cerca de Madrid (radio 50km)
        $response = $this->getJson('/api/v1/energy-companies/filter/by-location?latitude=40.4168&longitude=-3.7038&radius_km=50');

        $response->assertStatus(200);

        $data = $response->json('data');
        
        // Verificar que la empresa de Madrid aparece
        $madridFound = collect($data)->first(function ($company) use ($madridCompany) {
            return $company['id'] === $madridCompany->id;
        });
        
        $this->assertNotNull($madridFound);
    }

    public function test_location_filter_requires_all_parameters()
    {
        // Test sin latitud
        $response = $this->getJson('/api/v1/energy-companies/filter/by-location?longitude=-3.7038&radius_km=50');
        $response->assertStatus(400);

        // Test sin longitud
        $response = $this->getJson('/api/v1/energy-companies/filter/by-location?latitude=40.4168&radius_km=50');
        $response->assertStatus(400);

        // Test sin radio
        $response = $this->getJson('/api/v1/energy-companies/filter/by-location?latitude=40.4168&longitude=-3.7038');
        $response->assertStatus(400);
    }

    public function test_can_search_energy_companies()
    {
        EnergyCompany::factory()->create([
            'name' => 'Iberdrola Clientes',
            'slug' => 'iberdrola-clientes',
        ]);

        EnergyCompany::factory()->create([
            'name' => 'Endesa Energía',
            'slug' => 'endesa-energia',
        ]);

        EnergyCompany::factory()->create([
            'name' => 'Naturgy Iberia',
            'slug' => 'naturgy-iberia',
        ]);

        // Buscar por nombre
        $response = $this->getJson('/api/v1/energy-companies/search?q=iberdrola');

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('Iberdrola Clientes', $data[0]['name']);
    }

    public function test_search_requires_query_parameter()
    {
        $response = $this->getJson('/api/v1/energy-companies/search');

        $response->assertStatus(400)
                ->assertJson([
                    'error' => 'Query parameter q is required'
                ]);
    }

    public function test_can_get_commercializers()
    {
        // Crear empresas de diferentes tipos
        EnergyCompany::factory()->create([
            'company_type' => 'comercializadora',
            'name' => 'Comercializadora Test',
        ]);

        EnergyCompany::factory()->create([
            'company_type' => 'distribuidora',
            'name' => 'Distribuidora Test',
        ]);

        EnergyCompany::factory()->create([
            'company_type' => 'cooperativa',
            'name' => 'Cooperativa Test',
        ]);

        $response = $this->getJson('/api/v1/energy-companies/commercializers');

        $response->assertStatus(200);

        $data = $response->json('data');
        
        // Verificar que solo aparecen comercializadoras
        foreach ($data as $company) {
            $this->assertEquals('comercializadora', $company['company_type']);
        }
    }

    public function test_can_get_cooperatives()
    {
        // Crear empresas de diferentes tipos
        EnergyCompany::factory()->create([
            'company_type' => 'comercializadora',
            'name' => 'Comercializadora Test',
        ]);

        EnergyCompany::factory()->create([
            'company_type' => 'cooperativa',
            'name' => 'Cooperativa Test',
        ]);

        EnergyCompany::factory()->create([
            'company_type' => 'cooperativa',
            'name' => 'Som Energia',
        ]);

        $response = $this->getJson('/api/v1/energy-companies/cooperatives');

        $response->assertStatus(200);

        $data = $response->json('data');
        
        // Verificar que solo aparecen cooperativas
        foreach ($data as $company) {
            $this->assertEquals('cooperativa', $company['company_type']);
        }
        
        // Verificar que aparecen las 2 cooperativas
        $this->assertCount(2, $data);
    }

    public function test_returns_404_for_nonexistent_energy_company()
    {
        $response = $this->getJson('/api/v1/energy-companies/99999');

        $response->assertStatus(404);
    }

    public function test_returns_404_for_nonexistent_slug()
    {
        $response = $this->getJson('/api/v1/energy-companies/nonexistent-slug');

        $response->assertStatus(404);
    }

    public function test_energy_company_contact_structure()
    {
        $company = EnergyCompany::factory()->create([
            'phone_customer' => '900 123 456',
            'phone_commercial' => '900 654 321',
            'email_customer' => 'clientes@test.com',
            'email_commercial' => 'comercial@test.com',
        ]);

        $response = $this->getJson("/api/v1/energy-companies/{$company->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'contact' => [
                            'phone_customer',
                            'phone_commercial',
                            'email_customer',
                            'email_commercial',
                        ]
                    ]
                ])
                ->assertJson([
                    'data' => [
                        'contact' => [
                            'phone_customer' => '900 123 456',
                            'phone_commercial' => '900 654 321',
                            'email_customer' => 'clientes@test.com',
                            'email_commercial' => 'comercial@test.com',
                        ]
                    ]
                ]);
    }

    public function test_energy_company_includes_municipality_relationship()
    {
        $municipality = Municipality::factory()->create([
            'name' => 'Madrid',
            'slug' => 'madrid',
        ]);

        $company = EnergyCompany::factory()->create([
            'municipality_id' => $municipality->id,
        ]);

        $response = $this->getJson("/api/v1/energy-companies/{$company->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'municipality' => [
                            'id',
                            'name',
                            'slug',
                        ]
                    ]
                ])
                ->assertJson([
                    'data' => [
                        'municipality' => [
                            'id' => $municipality->id,
                            'name' => 'Madrid',
                            'slug' => 'madrid',
                        ]
                    ]
                ]);
    }
}