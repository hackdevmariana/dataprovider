<?php

use App\Models\Award;
use App\Models\AwardWinner;
use App\Models\Person;
use App\Services\AwardWinnersService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates an award winner', function () {
    $service = new AwardWinnersService();
    $person = Person::query()->create(['name' => 'John Doe', 'slug' => 'john-doe']);
    $award = Award::factory()->create();

    $winner = $service->createWinner([
        'person_id' => $person->id,
        'award_id' => $award->id,
        'year' => 2020,
    ]);

    expect($winner->exists)->toBeTrue();
});


