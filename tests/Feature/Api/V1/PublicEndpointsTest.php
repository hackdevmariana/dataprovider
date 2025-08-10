<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('GET /api/v1/countries returns 200', function () {
    $this->getJson('/api/v1/countries')->assertStatus(200);
});

test('GET /api/v1/languages returns 200', function () {
    $this->getJson('/api/v1/languages')->assertStatus(200);
});

test('GET /api/v1/timezones returns 200', function () {
    $this->getJson('/api/v1/timezones')->assertStatus(200);
});

test('GET /api/v1/municipalities returns 200', function () {
    $this->getJson('/api/v1/municipalities')->assertStatus(200);
});

test('GET /api/v1/points-of-interest returns 200', function () {
    $this->getJson('/api/v1/points-of-interest')->assertStatus(200);
});

test('GET /api/v1/autonomous-communities returns 200', function () {
    $this->getJson('/api/v1/autonomous-communities')->assertStatus(200);
});

test('GET /api/v1/persons returns 200', function () {
    $this->getJson('/api/v1/persons')->assertStatus(200);
});

test('GET /api/v1/images returns 200', function () {
    $this->getJson('/api/v1/images')->assertStatus(200);
});

test('GET /api/v1/professions returns 200', function () {
    $this->getJson('/api/v1/professions')->assertStatus(200);
});

test('GET /api/v1/works returns 200', function () {
    $this->getJson('/api/v1/works')->assertStatus(200);
});

test('GET /api/v1/links returns 200', function () {
    $this->getJson('/api/v1/links')->assertStatus(200);
});

test('GET /api/v1/provinces returns 200', function () {
    $this->getJson('/api/v1/provinces')->assertStatus(200);
});

// Nota: regiones no tiene listado público directo en las rutas actuales
// test('GET /api/v1/regions returns 200', function () {
//     $this->getJson('/api/v1/regions')->assertStatus(200);
// });

test('GET /api/v1/family-members returns 200', function () {
    $this->getJson('/api/v1/family-members')->assertStatus(200);
});


