<?php

namespace Database\Factories;

use App\Models\SyncLog;
use App\Models\DataSource;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SyncLog>
 */
class SyncLogFactory extends Factory
{
    protected $model = SyncLog::class;

    public function definition(): array
    {
        $statuses = ['success', 'failed'];
        $status = $this->faker->randomElement($statuses);
        
        $startedAt = $this->faker->dateTimeBetween('-30 days', 'now');
        $finishedAt = $status === 'success' 
            ? $this->faker->dateTimeBetween($startedAt, '+2 hours')
            : ($this->faker->boolean(70) ? $this->faker->dateTimeBetween($startedAt, '+1 hour') : null);

        return [
            'data_source_id' => DataSource::inRandomOrder()->first()?->id ?? 1,
            'status' => $status,
            'started_at' => $startedAt,
            'finished_at' => $finishedAt,
            'processed_items_count' => $this->faker->numberBetween(0, 1000),
        ];
    }

    public function success(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'success',
            'finished_at' => $this->faker->dateTimeBetween($attributes['started_at'], '+2 hours'),
            'processed_items_count' => $this->faker->numberBetween(10, 1000),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'finished_at' => $this->faker->boolean(70) 
                ? $this->faker->dateTimeBetween($attributes['started_at'], '+30 minutes')
                : null,
            'processed_items_count' => $this->faker->numberBetween(0, 50),
        ]);
    }

    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'started_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
            'finished_at' => $attributes['status'] === 'success' 
                ? $this->faker->dateTimeBetween($attributes['started_at'], '+2 hours')
                : ($this->faker->boolean(70) ? $this->faker->dateTimeBetween($attributes['started_at'], '+1 hour') : null),
        ]);
    }

    public function old(): static
    {
        return $this->state(fn (array $attributes) => [
            'started_at' => $this->faker->dateTimeBetween('-1 year', '-30 days'),
            'finished_at' => $attributes['status'] === 'success' 
                ? $this->faker->dateTimeBetween($attributes['started_at'], '+2 hours')
                : ($this->faker->boolean(70) ? $this->faker->dateTimeBetween($attributes['started_at'], '+1 hour') : null),
        ]);
    }

    public function withManyItems(): static
    {
        return $this->state(fn (array $attributes) => [
            'processed_items_count' => $this->faker->numberBetween(500, 2000),
        ]);
    }

    public function withFewItems(): static
    {
        return $this->state(fn (array $attributes) => [
            'processed_items_count' => $this->faker->numberBetween(0, 10),
        ]);
    }

    public function longRunning(): static
    {
        return $this->state(fn (array $attributes) => [
            'started_at' => $this->faker->dateTimeBetween('-2 hours', '-1 hour'),
            'finished_at' => $this->faker->dateTimeBetween('-30 minutes', 'now'),
        ]);
    }

    public function quickSync(): static
    {
        return $this->state(fn (array $attributes) => [
            'started_at' => $this->faker->dateTimeBetween('-10 minutes', '-5 minutes'),
            'finished_at' => $this->faker->dateTimeBetween('-5 minutes', 'now'),
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'success', // Asumimos que está en progreso pero será exitoso
            'finished_at' => null,
            'started_at' => $this->faker->dateTimeBetween('-1 hour', 'now'),
        ]);
    }

    public function forDataSource(DataSource $dataSource): static
    {
        return $this->state(fn (array $attributes) => [
            'data_source_id' => $dataSource->id,
        ]);
    }

    public function withCustomDuration(int $minutes): static
    {
        return $this->state(function (array $attributes) use ($minutes) {
            $startedAt = $this->faker->dateTimeBetween('-1 day', 'now');
            $finishedAt = $attributes['status'] === 'success' 
                ? (clone $startedAt)->modify("+{$minutes} minutes")
                : null;
            
            return [
                'started_at' => $startedAt,
                'finished_at' => $finishedAt,
            ];
        });
    }

    public function withCustomItemsCount(int $count): static
    {
        return $this->state(fn (array $attributes) => [
            'processed_items_count' => $count,
        ]);
    }

    public function withCustomStatus(string $status): static
    {
        return $this->state(function (array $attributes) use ($status) {
            $startedAt = $attributes['started_at'] ?? $this->faker->dateTimeBetween('-1 day', 'now');
            $finishedAt = $status === 'success' 
                ? $this->faker->dateTimeBetween($startedAt, '+2 hours')
                : ($this->faker->boolean(70) ? $this->faker->dateTimeBetween($startedAt, '+1 hour') : null);
            
            return [
                'status' => $status,
                'started_at' => $startedAt,
                'finished_at' => $finishedAt,
            ];
        });
    }

    public function withCustomStartTime(\DateTime $startTime): static
    {
        return $this->state(function (array $attributes) use ($startTime) {
            $finishedAt = $attributes['status'] === 'success' 
                ? $this->faker->dateTimeBetween($startTime, '+2 hours')
                : ($this->faker->boolean(70) ? $this->faker->dateTimeBetween($startTime, '+1 hour') : null);
            
            return [
                'started_at' => $startTime,
                'finished_at' => $finishedAt,
            ];
        });
    }

    public function withCustomEndTime(\DateTime $endTime): static
    {
        return $this->state(function (array $attributes) use ($endTime) {
            $startedAt = $this->faker->dateTimeBetween('-2 hours', $endTime);
            
            return [
                'started_at' => $startedAt,
                'finished_at' => $endTime,
            ];
        });
    }

    public function withCustomTimeRange(\DateTime $startTime, \DateTime $endTime): static
    {
        return $this->state(fn (array $attributes) => [
            'started_at' => $startTime,
            'finished_at' => $endTime,
        ]);
    }

    public function withCustomProcessedItems(int $min, int $max): static
    {
        return $this->state(fn (array $attributes) => [
            'processed_items_count' => $this->faker->numberBetween($min, $max),
        ]);
    }

    public function withCustomProcessedItemsRange(int $min, int $max): static
    {
        return $this->state(fn (array $attributes) => [
            'processed_items_count' => $this->faker->numberBetween($min, $max),
        ]);
    }

    public function withCustomProcessedItemsExact(int $count): static
    {
        return $this->state(fn (array $attributes) => [
            'processed_items_count' => $count,
        ]);
    }

    public function withCustomProcessedItemsZero(): static
    {
        return $this->state(fn (array $attributes) => [
            'processed_items_count' => 0,
        ]);
    }

    public function withCustomProcessedItemsHigh(): static
    {
        return $this->state(fn (array $attributes) => [
            'processed_items_count' => $this->faker->numberBetween(1000, 5000),
        ]);
    }

    public function withCustomProcessedItemsLow(): static
    {
        return $this->state(fn (array $attributes) => [
            'processed_items_count' => $this->faker->numberBetween(0, 10),
        ]);
    }

    public function withCustomProcessedItemsMedium(): static
    {
        return $this->state(fn (array $attributes) => [
            'processed_items_count' => $this->faker->numberBetween(50, 500),
        ]);
    }

    public function withCustomProcessedItemsVeryHigh(): static
    {
        return $this->state(fn (array $attributes) => [
            'processed_items_count' => $this->faker->numberBetween(5000, 10000),
        ]);
    }

    public function withCustomProcessedItemsVeryLow(): static
    {
        return $this->state(fn (array $attributes) => [
            'processed_items_count' => $this->faker->numberBetween(0, 5),
        ]);
    }

    public function withCustomProcessedItemsVeryMedium(): static
    {
        return $this->state(fn (array $attributes) => [
            'processed_items_count' => $this->faker->numberBetween(100, 1000),
        ]);
    }
}
