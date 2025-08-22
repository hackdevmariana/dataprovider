<?php

use App\Models\User;
use App\Models\Report;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

describe('ReportController', function () {
    
    describe('GET /api/v1/reports', function () {
        test('returns paginated list of reports', function () {
            Report::factory()->count(3)->create();
            
            $response = $this->getJson('/api/v1/reports');
            
            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'title',
                            'description',
                            'report_type',
                            'status',
                            'generated_at',
                            'is_public',
                            'file_path'
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
        
        test('filters reports by report_type', function () {
            Report::factory()->create(['report_type' => 'sustainability']);
            Report::factory()->create(['report_type' => 'financial']);
            
            $response = $this->getJson('/api/v1/reports?report_type=sustainability');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.report_type', 'sustainability');
        });
        
        test('filters reports by status', function () {
            Report::factory()->create(['status' => 'completed']);
            Report::factory()->create(['status' => 'pending']);
            
            $response = $this->getJson('/api/v1/reports?status=completed');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.status', 'completed');
        });
        
        test('filters reports by is_public', function () {
            Report::factory()->create(['is_public' => true]);
            Report::factory()->create(['is_public' => false]);
            
            $response = $this->getJson('/api/v1/reports?is_public=true');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.is_public', true);
        });
        
        test('filters reports by date range', function () {
            Report::factory()->create(['generated_at' => '2024-01-01 10:00:00']);
            Report::factory()->create(['generated_at' => '2024-03-01 10:00:00']);
            
            $response = $this->getJson('/api/v1/reports?date_from=2024-01-01&date_to=2024-02-29');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data');
        });
        
        test('searches reports by title or description', function () {
            Report::factory()->create(['title' => 'Reporte de Sostenibilidad 2024']);
            Report::factory()->create(['title' => 'Reporte Financiero Q1']);
            
            $response = $this->getJson('/api/v1/reports?search=sostenibilidad');
            
            $response->assertStatus(200)
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.title', 'Reporte de Sostenibilidad 2024');
        });
        
        test('respects per_page parameter', function () {
            Report::factory()->count(15)->create();
            
            $response = $this->getJson('/api/v1/reports?per_page=10');
            
            $response->assertStatus(200)
                ->assertJsonPath('meta.per_page', 10)
                ->assertJsonCount(10, 'data');
        });
    });
    
    describe('POST /api/v1/reports', function () {
        test('creates new report with valid data', function () {
            $reportData = [
                'title' => 'Reporte de Sostenibilidad 2024',
                'description' => 'Análisis completo de prácticas sostenibles',
                'report_type' => 'sustainability',
                'status' => 'pending',
                'is_public' => true,
                'parameters' => ['year' => 2024, 'scope' => 'global']
            ];
            
            $response = $this->postJson('/api/v1/reports', $reportData);
            
            $response->assertStatus(201)
                ->assertJsonPath('data.title', 'Reporte de Sostenibilidad 2024')
                ->assertJsonPath('data.report_type', 'sustainability')
                ->assertJsonPath('data.status', 'pending');
                
            $this->assertDatabaseHas('reports', [
                'title' => 'Reporte de Sostenibilidad 2024',
                'report_type' => 'sustainability',
                'status' => 'pending'
            ]);
        });
        
        test('sets generated_at when creating report', function () {
            $reportData = [
                'title' => 'Test Report',
                'report_type' => 'sustainability'
            ];
            
            $response = $this->postJson('/api/v1/reports', $reportData);
            
            $response->assertStatus(201)
                ->assertJsonPath('data.generated_at', function ($date) {
                    return !is_null($date);
                });
        });
        
        test('returns 422 with invalid data', function () {
            $response = $this->postJson('/api/v1/reports', []);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['title', 'report_type']);
        });
        
        test('returns 422 with invalid report_type', function () {
            $reportData = [
                'title' => 'Test Report',
                'report_type' => 'invalid_type'
            ];
            
            $response = $this->postJson('/api/v1/reports', $reportData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['report_type']);
        });
        
        test('returns 422 with invalid status', function () {
            $reportData = [
                'title' => 'Test Report',
                'report_type' => 'sustainability',
                'status' => 'invalid_status'
            ];
            
            $response = $this->postJson('/api/v1/reports', $reportData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['status']);
        });
        
        test('returns 422 with invalid parameters format', function () {
            $reportData = [
                'title' => 'Test Report',
                'report_type' => 'sustainability',
                'parameters' => 'invalid-json'
            ];
            
            $response = $this->postJson('/api/v1/reports', $reportData);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['parameters']);
        });
    });
    
    describe('GET /api/v1/reports/{id}', function () {
        test('returns report details', function () {
            $report = Report::factory()->create();
            
            $response = $this->getJson("/api/v1/reports/{$report->id}");
            
            $response->assertStatus(200)
                ->assertJsonPath('data.id', $report->id)
                ->assertJsonPath('data.title', $report->title)
                ->assertJsonPath('data.report_type', $report->report_type);
        });
        
        test('returns 404 for non-existent report', function () {
            $response = $this->getJson('/api/v1/reports/999');
            
            $response->assertStatus(404);
        });
    });
    
    describe('PUT /api/v1/reports/{id}', function () {
        test('updates report with valid data', function () {
            $report = Report::factory()->create();
            $updateData = [
                'title' => 'Reporte de Sostenibilidad 2024 Actualizado',
                'status' => 'completed',
                'file_path' => '/reports/sustainability_2024.pdf'
            ];
            
            $response = $this->putJson("/api/v1/reports/{$report->id}", $updateData);
            
            $response->assertStatus(200)
                ->assertJsonPath('data.title', 'Reporte de Sostenibilidad 2024 Actualizado')
                ->assertJsonPath('data.status', 'completed');
                
            $this->assertDatabaseHas('reports', [
                'id' => $report->id,
                'title' => 'Reporte de Sostenibilidad 2024 Actualizado',
                'status' => 'completed'
            ]);
        });
        
        test('returns 422 with invalid data', function () {
            $report = Report::factory()->create();
            
            $response = $this->putJson("/api/v1/reports/{$report->id}", [
                'report_type' => 'invalid_type'
            ]);
            
            $response->assertStatus(422)
                ->assertJsonValidationErrors(['report_type']);
        });
        
        test('returns 404 for non-existent report', function () {
            $response = $this->putJson('/api/v1/reports/999', [
                'title' => 'Updated Title'
            ]);
            
            $response->assertStatus(404);
        });
    });
    
    describe('DELETE /api/v1/reports/{id}', function () {
        test('deletes report successfully', function () {
            $report = Report::factory()->create();
            
            $response = $this->deleteJson("/api/v1/reports/{$report->id}");
            
            $response->assertStatus(204);
            $this->assertDatabaseMissing('reports', ['id' => $report->id]);
        });
        
        test('returns 404 for non-existent report', function () {
            $response = $this->deleteJson('/api/v1/reports/999');
            
            $response->assertStatus(404);
        });
    });
    
    describe('POST /api/v1/reports/{id}/generate', function () {
        test('generates report successfully', function () {
            $report = Report::factory()->create(['status' => 'pending']);
            
            $response = $this->postJson("/api/v1/reports/{$report->id}/generate");
            
            $response->assertStatus(200)
                ->assertJsonPath('message', 'Reporte generado exitosamente');
        });
        
        test('returns 404 for non-existent report', function () {
            $response = $this->postJson('/api/v1/reports/999/generate');
            
            $response->assertStatus(404);
        });
    });
});
