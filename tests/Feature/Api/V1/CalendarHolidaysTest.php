<?php

declare(strict_types=1);

use App\Models\CalendarHoliday;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('CalendarHolidays API', function () {
    it('returns a list of calendar holidays', function () {
        CalendarHoliday::factory()->count(3)->create();
        $response = $this->getJson('/api/v1/calendar-holidays');
        $response->assertOk()->assertJsonStructure(['data' => [['id', 'name', 'date', 'slug', 'municipality_id']]]);
    });

    it('returns a single calendar holiday by id', function () {
        $holiday = CalendarHoliday::factory()->create();
        $response = $this->getJson('/api/v1/calendar-holidays/' . $holiday->id);
        $response->assertOk()->assertJsonPath('data.id', $holiday->id);
    });

    it('returns a single calendar holiday by slug', function () {
        $holiday = CalendarHoliday::factory()->create(['slug' => 'test-holiday']);
        $response = $this->getJson('/api/v1/calendar-holidays/test-holiday');
        $response->assertOk()->assertJsonPath('data.slug', 'test-holiday');
    });

    it('returns 404 for non-existent holiday', function () {
        $response = $this->getJson('/api/v1/calendar-holidays/999999');
        $response->assertNotFound();
    });

    it('returns holidays by date', function () {
        CalendarHoliday::factory()->create(['date' => '2024-12-25']);
        CalendarHoliday::factory()->create(['date' => '2024-12-25']);
        CalendarHoliday::factory()->create(['date' => '2024-01-01']);
        $response = $this->getJson('/api/v1/calendar-holidays/date/2024-12-25');
        $response->assertOk()->assertJsonCount(2, 'data');
    });
});
