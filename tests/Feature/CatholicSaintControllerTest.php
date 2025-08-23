<?php

namespace Tests\Feature;

use App\Models\CatholicSaint;
use App\Models\Municipality;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Carbon\Carbon;

class CatholicSaintControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $adminUser;
    protected $municipality;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear permisos necesarios
        Permission::create(['name' => 'view catholic-saints']);
        Permission::create(['name' => 'create catholic-saints']);
        Permission::create(['name' => 'edit catholic-saints']);
        Permission::create(['name' => 'delete catholic-saints']);

        // Crear roles
        $adminRole = Role::create(['name' => 'admin']);
        $userRole = Role::create(['name' => 'user']);

        // Asignar permisos a roles
        $adminRole->givePermissionTo([
            'view catholic-saints',
            'create catholic-saints',
            'edit catholic-saints',
            'delete catholic-saints'
        ]);
        
        $userRole->givePermissionTo(['view catholic-saints']);

        // Crear usuarios
        $this->user = User::factory()->create();
        $this->user->assignRole($userRole);

        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole($adminRole);

        // Crear municipio para pruebas
        $this->municipality = Municipality::factory()->create([
            'name' => 'Madrid',
            'slug' => 'madrid'
        ]);

        // Crear algunos santos para pruebas
        $this->createSampleSaints();
    }

    protected function createSampleSaints(): void
    {
        // Santo mártir
        CatholicSaint::create([
            'name' => 'San Esteban',
            'canonical_name' => 'Sanctus Stephanus',
            'slug' => 'san-esteban',
            'description' => 'Primer mártir de la Iglesia',
            'biography' => 'San Esteban fue el primer mártir cristiano...',
            'birth_date' => '0001-01-01',
            'death_date' => '0033-12-26',
            'canonization_date' => '0033-12-26',
            'feast_date' => '2024-12-26',
            'feast_date_optional' => null,
            'category' => 'martyr',
            'feast_type' => 'feast',
            'liturgical_color' => 'red',
            'patron_of' => 'Diáconos, albañiles',
            'is_patron' => true,
            'patronages' => ['diaconos' => 'Patrono de diáconos'],
            'specialties' => 'Valentía, fe inquebrantable',
            'birth_place_id' => null,
            'death_place_id' => null,
            'municipality_id' => null,
            'region' => 'Jerusalén',
            'country' => 'Israel',
            'liturgical_rank' => 'Santo Mayor',
            'prayers' => 'Oración a San Esteban',
            'hymns' => 'Himno a San Esteban',
            'attributes' => ['piedras' => 'Símbolo de su martirio'],
            'is_active' => true,
            'is_universal' => true,
            'is_local' => false,
            'popularity_score' => 8,
            'notes' => 'Santo muy venerado en la Iglesia'
        ]);

        // Santo confesor
        CatholicSaint::create([
            'name' => 'San Francisco de Asís',
            'canonical_name' => 'Sanctus Franciscus Assisiensis',
            'slug' => 'san-francisco-de-asis',
            'description' => 'Fundador de la Orden Franciscana',
            'biography' => 'San Francisco nació en Asís...',
            'birth_date' => '1181-09-26',
            'death_date' => '1226-10-03',
            'canonization_date' => '1228-07-16',
            'feast_date' => '2024-10-04',
            'feast_date_optional' => null,
            'category' => 'founder',
            'feast_type' => 'memorial',
            'liturgical_color' => 'white',
            'patron_of' => 'Ecología, animales, paz',
            'is_patron' => true,
            'patronages' => ['ecologia' => 'Patrono de la ecología'],
            'specialties' => 'Pobreza, humildad, amor a la creación',
            'birth_place_id' => null,
            'death_place_id' => null,
            'municipality_id' => $this->municipality->id,
            'region' => 'Umbría',
            'country' => 'Italia',
            'liturgical_rank' => 'Santo Mayor',
            'prayers' => 'Señor, hazme instrumento de tu paz...',
            'hymns' => 'Cántico de las Criaturas',
            'attributes' => ['estigmas' => 'Marcas de la pasión'],
            'is_active' => true,
            'is_universal' => true,
            'is_local' => false,
            'popularity_score' => 10,
            'notes' => 'Uno de los santos más populares'
        ]);

        // Santo virgen
        CatholicSaint::create([
            'name' => 'Santa María',
            'canonical_name' => 'Sancta Maria',
            'slug' => 'santa-maria',
            'description' => 'Madre de Jesucristo',
            'biography' => 'Santa María es la madre de Jesús...',
            'birth_date' => '0001-01-01',
            'death_date' => '0048-08-15',
            'canonization_date' => '0001-01-01',
            'feast_date' => '2024-08-15',
            'feast_date_optional' => null,
            'category' => 'virgin',
            'feast_type' => 'solemnity',
            'liturgical_color' => 'white',
            'patron_of' => 'Iglesia Universal',
            'is_patron' => true,
            'patronages' => ['iglesia' => 'Patrona de la Iglesia'],
            'specialties' => 'Pureza, humildad, fe',
            'birth_place_id' => null,
            'death_place_id' => null,
            'municipality_id' => null,
            'region' => 'Galilea',
            'country' => 'Israel',
            'liturgical_rank' => 'Santo Mayor',
            'prayers' => 'Ave María',
            'hymns' => 'Salve Regina',
            'attributes' => ['corona' => 'Corona de estrellas'],
            'is_active' => true,
            'is_universal' => true,
            'is_local' => false,
            'popularity_score' => 10,
            'notes' => 'La más alta de todas las criaturas'
        ]);
    }

    // ========================================
    // TESTS DE RUTAS PÚBLICAS (SIN AUTENTICACIÓN)
    // ========================================

    public function test_index_returns_paginated_saints(): void
    {
        $response = $this->getJson('/api/v1/catholic-saints');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'category',
                            'feast_date',
                            'feast_type',
                            'is_active',
                            'is_patron',
                            'popularity_score'
                        ]
                    ],
                    'links',
                    'meta'
                ])
                ->assertJson([
                    'success' => true,
                    'message' => 'Santos católicos obtenidos exitosamente'
                ]);

        $this->assertCount(3, $response->json('data'));
    }

    public function test_index_with_search_filter(): void
    {
        $response = $this->getJson('/api/v1/catholic-saints?search=Francisco');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.name', 'San Francisco de Asís');
    }

    public function test_index_with_category_filter(): void
    {
        $response = $this->getJson('/api/v1/catholic-saints?category=martyr');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.category', 'martyr');
    }

    public function test_index_with_feast_type_filter(): void
    {
        $response = $this->getJson('/api/v1/catholic-saints?feast_type=solemnity');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.feast_type', 'solemnity');
    }

    public function test_index_with_patron_filter(): void
    {
        $response = $this->getJson('/api/v1/catholic-saints?is_patron=true');

        $response->assertStatus(200)
                ->assertJsonCount(3, 'data'); // Todos son patronos en nuestros datos de prueba
    }

    public function test_index_with_sorting(): void
    {
        $response = $this->getJson('/api/v1/catholic-saints?sort_by=popularity_score&sort_direction=desc');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $this->assertGreaterThanOrEqual($data[1]['popularity_score'], $data[0]['popularity_score']);
    }

    public function test_index_with_pagination(): void
    {
        $response = $this->getJson('/api/v1/catholic-saints?per_page=2');

        $response->assertStatus(200)
                ->assertJsonCount(2, 'data')
                ->assertJsonPath('meta.per_page', 2)
                ->assertJsonPath('meta.total', 3);
    }

    public function test_show_returns_saint_details(): void
    {
        $saint = CatholicSaint::first();

        $response = $this->getJson("/api/v1/catholic-saints/{$saint->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'id',
                        'name',
                        'canonical_name',
                        'slug',
                        'description',
                        'biography',
                        'category',
                        'feast_date',
                        'feast_type',
                        'liturgical_color',
                        'patron_of',
                        'is_patron',
                        'patronages',
                        'specialties',
                        'municipality',
                        'birth_place',
                        'death_place',
                        'region',
                        'country',
                        'liturgical_rank',
                        'prayers',
                        'hymns',
                        'attributes',
                        'is_active',
                        'is_universal',
                        'is_local',
                        'popularity_score',
                        'notes',
                        'created_at',
                        'updated_at'
                    ]
                ])
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'id' => $saint->id,
                        'name' => $saint->name
                    ]
                ]);
    }

    public function test_show_returns_404_for_nonexistent_saint(): void
    {
        $response = $this->getJson('/api/v1/catholic-saints/99999');

        $response->assertStatus(404);
    }

    public function test_today_returns_todays_saint(): void
    {
        // Crear un santo para hoy
        $today = Carbon::today();
        $todaySaint = CatholicSaint::create([
            'name' => 'Santo de Hoy',
            'slug' => 'santo-de-hoy',
            'feast_date' => $today->format('Y-m-d'),
            'category' => 'other',
            'feast_type' => 'memorial',
            'is_active' => true
        ]);

        $response = $this->getJson('/api/v1/catholic-saints/today');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data',
                    'date'
                ])
                ->assertJson([
                    'success' => true,
                    'date' => $today->format('Y-m-d')
                ]);

        if ($response->json('data')) {
            $response->assertJsonPath('data.name', 'Santo de Hoy');
        }
    }

    public function test_today_returns_null_when_no_saint_today(): void
    {
        // Asegurar que no hay santos para hoy
        CatholicSaint::where('feast_date', Carbon::today()->format('Y-m-d'))->delete();

        $response = $this->getJson('/api/v1/catholic-saints/today');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'data' => null
                ]);
    }

    public function test_by_date_returns_saints_for_specific_date(): void
    {
        $response = $this->getJson('/api/v1/catholic-saints/by-date?date=2024-12-26');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data',
                    'date',
                    'count'
                ])
                ->assertJson([
                    'success' => true,
                    'date' => '2024-12-26'
                ]);

        $this->assertGreaterThan(0, $response->json('count'));
    }

    public function test_by_date_validates_date_format(): void
    {
        $response = $this->getJson('/api/v1/catholic-saints/by-date?date=invalid-date');

        $response->assertStatus(400)
                ->assertJson([
                    'success' => false,
                    'message' => 'Formato de fecha incorrecto. Use Y-m-d'
                ]);
    }

    public function test_by_category_returns_saints_by_category(): void
    {
        $response = $this->getJson('/api/v1/catholic-saints/by-category?category=martyr');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data',
                    'category',
                    'links',
                    'meta'
                ])
                ->assertJson([
                    'success' => true,
                    'category' => 'martyr'
                ]);

        $this->assertGreaterThan(0, count($response->json('data')));
    }

    public function test_by_category_validates_category(): void
    {
        $response = $this->getJson('/api/v1/catholic-saints/by-category?category=invalid-category');

        $response->assertStatus(400)
                ->assertJson([
                    'success' => false,
                    'message' => 'Parámetros de validación incorrectos'
                ]);
    }

    public function test_search_returns_filtered_results(): void
    {
        $response = $this->getJson('/api/v1/catholic-saints/search?q=Francisco&category=founder');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data',
                    'filters_applied',
                    'links',
                    'meta'
                ])
                ->assertJson([
                    'success' => true,
                    'filters_applied' => [
                        'q' => 'Francisco',
                        'category' => 'founder'
                    ]
                ]);

        $this->assertGreaterThan(0, count($response->json('data')));
    }

    public function test_search_with_multiple_filters(): void
    {
        $response = $this->getJson('/api/v1/catholic-saints/search?is_patron=true&min_popularity=8');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true
                ]);

        $data = $response->json('data');
        foreach ($data as $saint) {
            $this->assertTrue($saint['is_patron']);
            $this->assertGreaterThanOrEqual(8, $saint['popularity_score']);
        }
    }

    public function test_stats_returns_saint_statistics(): void
    {
        $response = $this->getJson('/api/v1/catholic-saints/stats');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'total_saints',
                        'active_saints',
                        'universal_saints',
                        'local_saints',
                        'patron_saints',
                        'category_stats',
                        'feast_type_stats',
                        'top_saints',
                        'upcoming_feasts',
                        'generated_at'
                    ]
                ])
                ->assertJson([
                    'success' => true
                ]);

        $data = $response->json('data');
        $this->assertEquals(3, $data['total_saints']);
        $this->assertEquals(3, $data['active_saints']);
        $this->assertEquals(3, $data['universal_saints']);
        $this->assertEquals(3, $data['patron_saints']);
    }

    // ========================================
    // TESTS DE RUTAS AUTENTICADAS
    // ========================================

    public function test_store_requires_authentication(): void
    {
        $saintData = [
            'name' => 'Nuevo Santo',
            'slug' => 'nuevo-santo',
            'feast_date' => '2024-01-01',
            'category' => 'other',
            'feast_type' => 'memorial'
        ];

        $response = $this->postJson('/api/v1/catholic-saints', $saintData);

        $response->assertStatus(401);
    }

    public function test_store_requires_permission(): void
    {
        Sanctum::actingAs($this->user);

        $saintData = [
            'name' => 'Nuevo Santo',
            'slug' => 'nuevo-santo',
            'feast_date' => '2024-01-01',
            'category' => 'other',
            'feast_type' => 'memorial'
        ];

        $response = $this->postJson('/api/v1/catholic-saints', $saintData);

        $response->assertStatus(403);
    }

    public function test_store_creates_new_saint(): void
    {
        Sanctum::actingAs($this->adminUser);

        $saintData = [
            'name' => 'Nuevo Santo',
            'canonical_name' => 'Sanctus Novus',
            'slug' => 'nuevo-santo',
            'description' => 'Descripción del nuevo santo',
            'biography' => 'Biografía completa del santo',
            'birth_date' => '1200-01-01',
            'death_date' => '1250-01-01',
            'canonization_date' => '1300-01-01',
            'feast_date' => '2024-01-01',
            'category' => 'confessor',
            'feast_type' => 'memorial',
            'liturgical_color' => 'white',
            'patron_of' => 'Nuevas causas',
            'is_patron' => true,
            'patronages' => ['nuevas_causas' => 'Patrono de nuevas causas'],
            'specialties' => 'Novedad, innovación',
            'region' => 'Nueva Región',
            'country' => 'Nuevo País',
            'liturgical_rank' => 'Santo Menor',
            'prayers' => 'Oración al nuevo santo',
            'hymns' => 'Himno al nuevo santo',
            'attributes' => ['novedad' => 'Símbolo de lo nuevo'],
            'is_active' => true,
            'is_universal' => true,
            'is_local' => false,
            'popularity_score' => 5,
            'notes' => 'Notas sobre el nuevo santo'
        ];

        $response = $this->postJson('/api/v1/catholic-saints', $saintData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data'
                ])
                ->assertJson([
                    'success' => true,
                    'message' => 'Santo católico creado exitosamente'
                ]);

        $this->assertDatabaseHas('catholic_saints', [
            'name' => 'Nuevo Santo',
            'slug' => 'nuevo-santo'
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        Sanctum::actingAs($this->adminUser);

        $response = $this->postJson('/api/v1/catholic-saints', []);

        $response->assertStatus(400)
                ->assertJson([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos'
                ])
                ->assertJsonStructure([
                    'success',
                    'message',
                    'errors'
                ]);
    }

    public function test_store_validates_unique_slug(): void
    {
        Sanctum::actingAs($this->adminUser);

        $saintData = [
            'name' => 'Santo Duplicado',
            'slug' => 'san-esteban', // Slug ya existe
            'feast_date' => '2024-01-01',
            'category' => 'other',
            'feast_type' => 'memorial'
        ];

        $response = $this->postJson('/api/v1/catholic-saints', $saintData);

        $response->assertStatus(400)
                ->assertJson([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos'
                ]);
    }

    public function test_store_validates_date_constraints(): void
    {
        Sanctum::actingAs($this->adminUser);

        $saintData = [
            'name' => 'Santo Fechas Incorrectas',
            'slug' => 'santo-fechas-incorrectas',
            'birth_date' => '1250-01-01',
            'death_date' => '1200-01-01', // Muerte antes que nacimiento
            'feast_date' => '2024-01-01',
            'category' => 'other',
            'feast_type' => 'memorial'
        ];

        $response = $this->postJson('/api/v1/catholic-saints', $saintData);

        $response->assertStatus(400)
                ->assertJson([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos'
                ]);
    }

    public function test_update_requires_authentication(): void
    {
        $saint = CatholicSaint::first();

        $response = $this->putJson("/api/v1/catholic-saints/{$saint->id}", [
            'name' => 'Nombre Actualizado'
        ]);

        $response->assertStatus(401);
    }

    public function test_update_requires_permission(): void
    {
        Sanctum::actingAs($this->user);

        $saint = CatholicSaint::first();

        $response = $this->putJson("/api/v1/catholic-saints/{$saint->id}", [
            'name' => 'Nombre Actualizado'
        ]);

        $response->assertStatus(403);
    }

    public function test_update_modifies_saint(): void
    {
        Sanctum::actingAs($this->adminUser);

        $saint = CatholicSaint::first();
        $newName = 'Nombre Actualizado';

        $response = $this->putJson("/api/v1/catholic-saints/{$saint->id}", [
            'name' => $newName,
            'description' => 'Descripción actualizada'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Santo católico actualizado exitosamente'
                ]);

        $this->assertDatabaseHas('catholic_saints', [
            'id' => $saint->id,
            'name' => $newName
        ]);
    }

    public function test_update_validates_unique_slug(): void
    {
        Sanctum::actingAs($this->adminUser);

        $saint1 = CatholicSaint::where('slug', 'san-esteban')->first();
        $saint2 = CatholicSaint::where('slug', 'san-francisco-de-asis')->first();

        $response = $this->putJson("/api/v1/catholic-saints/{$saint1->id}", [
            'slug' => 'san-francisco-de-asis' // Slug ya existe en otro santo
        ]);

        $response->assertStatus(400)
                ->assertJson([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos'
                ]);
    }

    public function test_destroy_requires_authentication(): void
    {
        $saint = CatholicSaint::first();

        $response = $this->deleteJson("/api/v1/catholic-saints/{$saint->id}");

        $response->assertStatus(401);
    }

    public function test_destroy_requires_permission(): void
    {
        Sanctum::actingAs($this->user);

        $saint = CatholicSaint::first();

        $response = $this->deleteJson("/api/v1/catholic-saints/{$saint->id}");

        $response->assertStatus(403);
    }

    public function test_destroy_deletes_saint(): void
    {
        Sanctum::actingAs($this->adminUser);

        $saint = CatholicSaint::first();
        $saintId = $saint->id;

        $response = $this->deleteJson("/api/v1/catholic-saints/{$saintId}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Santo católico eliminado exitosamente'
                ]);

        $this->assertDatabaseMissing('catholic_saints', [
            'id' => $saintId
        ]);
    }

    // ========================================
    // TESTS DE VALIDACIÓN Y ERRORES
    // ========================================

    public function test_invalid_category_returns_validation_error(): void
    {
        $response = $this->getJson('/api/v1/catholic-saints?category=invalid-category');

        $response->assertStatus(400)
                ->assertJson([
                    'success' => false,
                    'message' => 'Parámetros de validación incorrectos'
                ]);
    }

    public function test_invalid_feast_type_returns_validation_error(): void
    {
        $response = $this->getJson('/api/v1/catholic-saints?feast_type=invalid-type');

        $response->assertStatus(400)
                ->assertJson([
                    'success' => false,
                    'message' => 'Parámetros de validación incorrectos'
                ]);
    }

    public function test_invalid_sort_field_returns_validation_error(): void
{
        $response = $this->getJson('/api/v1/catholic-saints?sort_by=invalid_field');

        $response->assertStatus(400)
                ->assertJson([
                    'success' => false,
                    'message' => 'Parámetros de validación incorrectos'
                ]);
    }

    public function test_invalid_sort_direction_returns_validation_error(): void
    {
        $response = $this->getJson('/api/v1/catholic-saints?sort_direction=invalid');

        $response->assertStatus(400)
                ->assertJson([
                    'success' => false,
                    'message' => 'Parámetros de validación incorrectos'
                ]);
    }

    public function test_per_page_limit_enforced(): void
    {
        $response = $this->getJson('/api/v1/catholic-saints?per_page=150'); // Más del límite de 100

        $response->assertStatus(400)
                ->assertJson([
                    'success' => false,
                    'message' => 'Parámetros de validación incorrectos'
                ]);
    }

    // ========================================
    // TESTS DE RELACIONES Y DATOS RELACIONADOS
    // ========================================

    public function test_saint_with_municipality_returns_related_data(): void
    {
        $saint = CatholicSaint::where('municipality_id', $this->municipality->id)->first();

        $response = $this->getJson("/api/v1/catholic-saints/{$saint->id}");

        $response->assertStatus(200)
                ->assertJsonPath('data.municipality.id', $this->municipality->id)
                ->assertJsonPath('data.municipality.name', $this->municipality->name);
    }

    public function test_index_includes_related_data(): void
    {
        $response = $this->getJson('/api/v1/catholic-saints');

        $response->assertStatus(200);

        $data = $response->json('data');
        foreach ($data as $saint) {
            if (isset($saint['municipality'])) {
                $this->assertArrayHasKey('id', $saint['municipality']);
                $this->assertArrayHasKey('name', $saint['municipality']);
            }
        }
    }

    // ========================================
    // TESTS DE CACHÉ Y RENDIMIENTO
    // ========================================

    public function test_stats_are_cached(): void
    {
        $response1 = $this->getJson('/api/v1/catholic-saints/stats');
        $response1->assertStatus(200);

        $response2 = $this->getJson('/api/v1/catholic-saints/stats');
        $response2->assertStatus(200);

        // Las respuestas deberían ser idénticas si el caché funciona
        $this->assertEquals($response1->json('data.generated_at'), $response2->json('data.generated_at'));
    }

    // ========================================
    // TESTS DE CASOS BORDE
    // ========================================

    public function test_empty_search_returns_all_saints(): void
    {
        $response = $this->getJson('/api/v1/catholic-saints/search');

        $response->assertStatus(200)
                ->assertJsonCount(3, 'data');
    }

    public function test_search_with_nonexistent_term_returns_empty(): void
    {
        $response = $this->getJson('/api/v1/catholic-saints/search?q=nonexistentterm');

        $response->assertStatus(200)
                ->assertJsonCount(0, 'data');
    }

    public function test_by_category_with_nonexistent_category_returns_empty(): void
    {
        $response = $this->getJson('/api/v1/catholic-saints/by-category?category=apostle');

        $response->assertStatus(200)
                ->assertJsonCount(0, 'data');
    }

    public function test_by_date_with_nonexistent_date_returns_empty(): void
    {
        $response = $this->getJson('/api/v1/catholic-saints/by-date?date=2024-02-30'); // Fecha inválida

        $response->assertStatus(400);
    }
}
