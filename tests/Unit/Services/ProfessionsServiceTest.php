<?php

use App\Services\ProfessionsService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates a profession', function () {
    $service = new ProfessionsService();
    $profession = $service->create([
        'name' => 'Dev',
        'slug' => 'dev',
        'is_public_facing' => true,
    ]);

    expect($profession->exists)->toBeTrue();
});


