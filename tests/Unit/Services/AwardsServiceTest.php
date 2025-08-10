<?php

use App\Models\Award;
use App\Services\AwardsService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('lists awards', function () {
    Award::factory()->count(2)->create();
    $service = new AwardsService();
    $awards = $service->listAwards();
    expect($awards)->toHaveCount(2);
});

it('creates an award', function () {
    $service = new AwardsService();
    $award = $service->createAward([
        'name' => 'Test',
        'slug' => 'test',
    ]);
    expect($award->exists)->toBeTrue();
});


