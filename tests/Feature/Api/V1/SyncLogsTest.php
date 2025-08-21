<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\SyncLog;
use App\Models\User;
use App\Models\DataSource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;

class SyncLogsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear usuario autenticado
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
        
        // Crear fuente de datos
        $this->dataSource = DataSource::factory()->create();
    }

    /** @test */
    public function it_can_list_sync_logs_with_pagination()
    {
        SyncLog::factory()->count(25)->create(['data_source_id' => $this->dataSource->id]);
        
        $response = $this->getJson('/api/v1/sync-logs?page=1&per_page=10');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'data_source_id',
                            'status',
                            'started_at',
                            'completed_at',
                            'records_processed',
                            'records_synced'
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
    public function it_can_filter_sync_logs_by_data_source()
    {
        $dataSource2 = DataSource::factory()->create();
        SyncLog::factory()->count(3)->create(['data_source_id' => $this->dataSource->id]);
        SyncLog::factory()->count(2)->create(['data_source_id' => $dataSource2->id]);
        
        $response = $this->getJson("/api/v1/sync-logs?data_source_id={$this->dataSource->id}");

        $response->assertStatus(200);
        $this->assertCount(3, $response->json('data'));
    }

    /** @test */
    public function it_can_filter_sync_logs_by_status()
    {
        SyncLog::factory()->count(3)->create([
            'data_source_id' => $this->dataSource->id,
            'status' => 'completed'
        ]);
        SyncLog::factory()->count(2)->create([
            'data_source_id' => $this->dataSource->id,
            'status' => 'failed'
        ]);
        
        $response = $this->getJson('/api/v1/sync-logs?status=completed');

        $response->assertStatus(200);
        $this->assertCount(3, $response->json('data'));
    }

    /** @test */
    public function it_can_filter_sync_logs_by_date_range()
    {
        SyncLog::factory()->create([
            'data_source_id' => $this->dataSource->id,
            'started_at' => '2024-01-01 10:00:00'
        ]);
        SyncLog::factory()->create([
            'data_source_id' => $this->dataSource->id,
            'started_at' => '2024-01-15 10:00:00'
        ]);
        SyncLog::factory()->create([
            'data_source_id' => $this->dataSource->id,
            'started_at' => '2024-02-01 10:00:00'
        ]);
        
        $response = $this->getJson('/api/v1/sync-logs?date_from=2024-01-01&date_to=2024-01-31');

        $response->assertStatus(200);
        $this->assertCount(2, $response->json('data'));
    }

    /** @test */
    public function it_can_create_a_new_sync_log()
    {
        $syncLogData = [
            'data_source_id' => $this->dataSource->id,
            'status' => 'in_progress',
            'description' => 'Sincronización de datos del clima',
            'metadata' => [
                'api_version' => 'v2.1',
                'endpoint' => '/weather/current'
            ]
        ];

        $response = $this->postJson('/api/v1/sync-logs', $syncLogData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'data_source_id',
                        'status',
                        'description',
                        'metadata',
                        'started_at',
                        'created_at'
                    ]
                ]);

        $this->assertDatabaseHas('sync_logs', [
            'data_source_id' => $this->dataSource->id,
            'status' => 'in_progress'
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_sync_log()
    {
        $response = $this->postJson('/api/v1/sync-logs', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['data_source_id', 'status']);
    }

    /** @test */
    public function it_validates_data_source_exists_when_creating_sync_log()
    {
        $response = $this->postJson('/api/v1/sync-logs', [
            'data_source_id' => 999,
            'status' => 'in_progress'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['data_source_id']);
    }

    /** @test */
    public function it_validates_status_enum_when_creating_sync_log()
    {
        $response = $this->postJson('/api/v1/sync-logs', [
            'data_source_id' => $this->dataSource->id,
            'status' => 'invalid_status'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['status']);
    }

    /** @test */
    public function it_automatically_sets_started_at_when_creating_sync_log()
    {
        $syncLogData = [
            'data_source_id' => $this->dataSource->id,
            'status' => 'in_progress'
        ];

        $response = $this->postJson('/api/v1/sync-logs', $syncLogData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('sync_logs', [
            'data_source_id' => $this->dataSource->id,
            'started_at' => now()->toDateTimeString()
        ]);
    }

    /** @test */
    public function it_can_show_sync_log_details()
    {
        $syncLog = SyncLog::factory()->create(['data_source_id' => $this->dataSource->id]);

        $response = $this->getJson("/api/v1/sync-logs/{$syncLog->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'id' => $syncLog->id,
                        'data_source_id' => $syncLog->data_source_id,
                        'status' => $syncLog->status
                    ]
                ]);
    }

    /** @test */
    public function it_returns_404_for_nonexistent_sync_log()
    {
        $response = $this->getJson('/api/v1/sync-logs/999');

        $response->assertStatus(404);
    }

    /** @test */
    public function it_can_update_sync_log()
    {
        $syncLog = SyncLog::factory()->create(['data_source_id' => $this->dataSource->id]);

        $updateData = [
            'status' => 'completed',
            'completed_at' => now()->toDateTimeString(),
            'records_processed' => 1000,
            'records_synced' => 950
        ];

        $response = $this->putJson("/api/v1/sync-logs/{$syncLog->id}", $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'status' => 'completed',
                        'records_processed' => 1000,
                        'records_synced' => 950
                    ]
                ]);

        $this->assertDatabaseHas('sync_logs', [
            'id' => $syncLog->id,
            'status' => 'completed'
        ]);
    }

    /** @test */
    public function it_can_delete_sync_log()
    {
        $syncLog = SyncLog::factory()->create(['data_source_id' => $this->dataSource->id]);

        $response = $this->deleteJson("/api/v1/sync-logs/{$syncLog->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('sync_logs', ['id' => $syncLog->id]);
    }

    /** @test */
    public function it_returns_404_when_deleting_nonexistent_sync_log()
    {
        $response = $this->deleteJson('/api/v1/sync-logs/999');

        $response->assertStatus(404);
    }

    /** @test */
    public function it_enforces_per_page_limit()
    {
        SyncLog::factory()->count(150)->create(['data_source_id' => $this->dataSource->id]);
        
        $response = $this->getJson('/api/v1/sync-logs?per_page=150');

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['per_page']);
    }

    /** @test */
    public function it_returns_sync_logs_ordered_by_started_at_desc()
    {
        $oldLog = SyncLog::factory()->create([
            'data_source_id' => $this->dataSource->id,
            'started_at' => now()->subDays(2)
        ]);
        $newLog = SyncLog::factory()->create([
            'data_source_id' => $this->dataSource->id,
            'started_at' => now()
        ]);
        
        $response = $this->getJson('/api/v1/sync-logs');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertEquals($newLog->id, $data[0]['id']);
        $this->assertEquals($oldLog->id, $data[1]['id']);
    }

    /** @test */
    public function it_can_filter_sync_logs_by_multiple_criteria()
    {
        SyncLog::factory()->create([
            'data_source_id' => $this->dataSource->id,
            'status' => 'completed',
            'started_at' => '2024-01-15 10:00:00'
        ]);
        SyncLog::factory()->create([
            'data_source_id' => $this->dataSource->id,
            'status' => 'failed',
            'started_at' => '2024-01-15 10:00:00'
        ]);
        
        $response = $this->getJson("/api/v1/sync-logs?data_source_id={$this->dataSource->id}&status=completed&date_from=2024-01-01&date_to=2024-01-31");

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('completed', $response->json('data.0.status'));
    }

    /** @test */
    public function it_requires_authentication_for_all_endpoints()
    {
        $syncLog = SyncLog::factory()->create(['data_source_id' => $this->dataSource->id]);
        
        // Desautenticar
        auth()->logout();

        // Test index
        $this->getJson('/api/v1/sync-logs')->assertStatus(401);
        
        // Test store
        $this->postJson('/api/v1/sync-logs', [])->assertStatus(401);
        
        // Test show
        $this->getJson("/api/v1/sync-logs/{$syncLog->id}")->assertStatus(401);
        
        // Test update
        $this->putJson("/api/v1/sync-logs/{$syncLog->id}", [])->assertStatus(401);
        
        // Test delete
        $this->deleteJson("/api/v1/sync-logs/{$syncLog->id}")->assertStatus(401);
    }

    /** @test */
    public function it_can_handle_sync_log_with_complex_metadata()
    {
        $syncLogData = [
            'data_source_id' => $this->dataSource->id,
            'status' => 'in_progress',
            'description' => 'Sincronización compleja',
            'metadata' => [
                'api_version' => 'v3.0',
                'endpoint' => '/data/weather/historical',
                'parameters' => [
                    'start_date' => '2024-01-01',
                    'end_date' => '2024-01-31',
                    'location' => 'Madrid'
                ],
                'rate_limit' => '1000/hour',
                'timeout' => 30
            ]
        ];

        $response = $this->postJson('/api/v1/sync-logs', $syncLogData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('sync_logs', [
            'data_source_id' => $this->dataSource->id,
            'status' => 'in_progress'
        ]);
    }

    /** @test */
    public function it_can_calculate_sync_duration()
    {
        $syncLog = SyncLog::factory()->create([
            'data_source_id' => $this->dataSource->id,
            'started_at' => now()->subMinutes(5),
            'completed_at' => now()
        ]);

        $response = $this->getJson("/api/v1/sync-logs/{$syncLog->id}");

        $response->assertStatus(200);
        $this->assertArrayHasKey('duration_minutes', $response->json('data'));
    }

    /** @test */
    public function it_can_handle_sync_log_with_error_details()
    {
        $syncLogData = [
            'data_source_id' => $this->dataSource->id,
            'status' => 'failed',
            'error_message' => 'API rate limit exceeded',
            'error_code' => 'RATE_LIMIT_ERROR',
            'retry_count' => 3
        ];

        $response = $this->postJson('/api/v1/sync-logs', $syncLogData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('sync_logs', [
            'status' => 'failed',
            'error_message' => 'API rate limit exceeded'
        ]);
    }
}
