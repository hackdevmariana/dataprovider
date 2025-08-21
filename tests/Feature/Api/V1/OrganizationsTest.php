<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\Organization;
use App\Models\User;
use App\Models\CompanyType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;

class OrganizationsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear usuario autenticado
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
        
        // Crear tipos de empresa
        CompanyType::create(['name' => 'Startup', 'slug' => 'startup']);
        CompanyType::create(['name' => 'Corporation', 'slug' => 'corporation']);
    }

    /** @test */
    public function it_can_list_organizations_with_pagination()
    {
        Organization::factory()->count(25)->create();
        
        $response = $this->getJson('/api/v1/organizations?page=1&per_page=10');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'slug',
                            'type',
                            'email',
                            'phone',
                            'website',
                            'status'
                        ]
                    ],
                    'meta' => [
                        'current_page',
                        'last_page',
                        'per_page',
                        'total'
                    ]
                ]);

        $this->assertCount(10, $response->json('data'));
    }

    /** @test */
    public function it_can_filter_organizations_by_type()
    {
        Organization::factory()->count(3)->create(['type' => 'startup']);
        Organization::factory()->count(2)->create(['type' => 'corporation']);
        
        $response = $this->getJson('/api/v1/organizations?type=startup');

        $response->assertStatus(200);
        $this->assertCount(3, $response->json('data'));
    }

    /** @test */
    public function it_can_filter_organizations_by_status()
    {
        Organization::factory()->count(3)->create(['status' => 'active']);
        Organization::factory()->count(2)->create(['status' => 'inactive']);
        
        $response = $this->getJson('/api/v1/organizations?status=active');

        $response->assertStatus(200);
        $this->assertCount(3, $response->json('data'));
    }

    /** @test */
    public function it_can_search_organizations_by_name_or_description()
    {
        Organization::factory()->create(['name' => 'Tech Corp', 'description' => 'Empresa tecnológica']);
        Organization::factory()->create(['name' => 'Food Inc', 'description' => 'Empresa de alimentos']);
        
        $response = $this->getJson('/api/v1/organizations?search=tech');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('Tech Corp', $response->json('data.0.name'));
    }

    /** @test */
    public function it_can_create_a_new_organization()
    {
        $organizationData = [
            'name' => 'Nueva Organización',
            'slug' => 'nueva-organizacion',
            'type' => 'startup',
            'email' => 'contacto@nuevaorg.com',
            'phone' => '+1234567890',
            'website' => 'https://nuevaorg.com',
            'status' => 'active'
        ];

        $response = $this->postJson('/api/v1/organizations', $organizationData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'name',
                        'slug',
                        'type',
                        'email',
                        'phone',
                        'website',
                        'status',
                        'created_at'
                    ]
                ]);

        $this->assertDatabaseHas('organizations', [
            'name' => 'Nueva Organización',
            'slug' => 'nueva-organizacion'
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_organization()
    {
        $response = $this->postJson('/api/v1/organizations', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'slug', 'type']);
    }

    /** @test */
    public function it_validates_slug_uniqueness_when_creating_organization()
    {
        Organization::factory()->create(['slug' => 'test-org']);
        
        $response = $this->postJson('/api/v1/organizations', [
            'name' => 'Test Organization',
            'slug' => 'test-org',
            'type' => 'startup'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['slug']);
    }

    /** @test */
    public function it_validates_email_format_when_creating_organization()
    {
        $response = $this->postJson('/api/v1/organizations', [
            'name' => 'Test Organization',
            'slug' => 'test-org',
            'type' => 'startup',
            'email' => 'invalid-email'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function it_can_show_organization_details()
    {
        $organization = Organization::factory()->create();

        $response = $this->getJson("/api/v1/organizations/{$organization->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'id' => $organization->id,
                        'name' => $organization->name,
                        'slug' => $organization->slug
                    ]
                ]);
    }

    /** @test */
    public function it_returns_404_for_nonexistent_organization()
    {
        $response = $this->getJson('/api/v1/organizations/999');

        $response->assertStatus(404);
    }

    /** @test */
    public function it_can_update_organization()
    {
        $organization = Organization::factory()->create();

        $updateData = [
            'name' => 'Organización Actualizada',
            'email' => 'nuevo@org.com',
            'status' => 'inactive'
        ];

        $response = $this->putJson("/api/v1/organizations/{$organization->id}", $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'name' => 'Organización Actualizada',
                        'email' => 'nuevo@org.com',
                        'status' => 'inactive'
                    ]
                ]);

        $this->assertDatabaseHas('organizations', [
            'id' => $organization->id,
            'name' => 'Organización Actualizada'
        ]);
    }

    /** @test */
    public function it_validates_slug_uniqueness_when_updating_organization()
    {
        $org1 = Organization::factory()->create(['slug' => 'org-1']);
        $org2 = Organization::factory()->create(['slug' => 'org-2']);

        $response = $this->putJson("/api/v1/organizations/{$org1->id}", [
            'slug' => 'org-2'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['slug']);
    }

    /** @test */
    public function it_can_delete_organization()
    {
        $organization = Organization::factory()->create();

        $response = $this->deleteJson("/api/v1/organizations/{$organization->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('organizations', ['id' => $organization->id]);
    }

    /** @test */
    public function it_returns_404_when_deleting_nonexistent_organization()
    {
        $response = $this->deleteJson('/api/v1/organizations/999');

        $response->assertStatus(404);
    }

    /** @test */
    public function it_enforces_per_page_limit()
    {
        Organization::factory()->count(150)->create();
        
        $response = $this->getJson('/api/v1/organizations?per_page=150');

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['per_page']);
    }

    /** @test */
    public function it_returns_organizations_ordered_by_name()
    {
        Organization::factory()->create(['name' => 'Zebra Corp']);
        Organization::factory()->create(['name' => 'Alpha Inc']);
        Organization::factory()->create(['name' => 'Beta LLC']);
        
        $response = $this->getJson('/api/v1/organizations');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertEquals('Alpha Inc', $data[0]['name']);
        $this->assertEquals('Beta LLC', $data[1]['name']);
        $this->assertEquals('Zebra Corp', $data[2]['name']);
    }

    /** @test */
    public function it_can_filter_organizations_by_multiple_criteria()
    {
        Organization::factory()->create([
            'name' => 'Tech Startup',
            'type' => 'startup',
            'status' => 'active'
        ]);
        Organization::factory()->create([
            'name' => 'Tech Corp',
            'type' => 'corporation',
            'status' => 'active'
        ]);
        
        $response = $this->getJson('/api/v1/organizations?type=startup&status=active&search=tech');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('Tech Startup', $response->json('data.0.name'));
    }

    /** @test */
    public function it_requires_authentication_for_all_endpoints()
    {
        $organization = Organization::factory()->create();
        
        // Desautenticar
        auth()->logout();

        // Test index
        $this->getJson('/api/v1/organizations')->assertStatus(401);
        
        // Test store
        $this->postJson('/api/v1/organizations', [])->assertStatus(401);
        
        // Test show
        $this->getJson("/api/v1/organizations/{$organization->id}")->assertStatus(401);
        
        // Test update
        $this->putJson("/api/v1/organizations/{$organization->id}", [])->assertStatus(401);
        
        // Test delete
        $this->deleteJson("/api/v1/organizations/{$organization->id}")->assertStatus(401);
    }
}
