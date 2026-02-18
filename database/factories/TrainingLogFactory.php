<?php

namespace Database\Factories;

use App\Models\Pet;
use App\Models\TrainingLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TrainingLog>
 */
class TrainingLogFactory extends Factory
{
    protected $model = TrainingLog::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $before = fake()->numberBetween(0, 90);
        $after = min(100, $before + 10);

        return [
            'pet_id' => Pet::factory(),
            'training_level_before' => $before,
            'training_level_after' => $after,
            'trained_at' => now(),
        ];
    }

    /**
     * Indicate that this was an early training session (low levels).
     */
    public function earlyTraining(): static
    {
        return $this->state(function (array $attributes) {
            $before = fake()->numberBetween(0, 20);
            return [
                'training_level_before' => $before,
                'training_level_after' => min(100, $before + 10),
            ];
        });
    }

    /**
     * Indicate that this was a mid-level training session.
     */
    public function midTraining(): static
    {
        return $this->state(function (array $attributes) {
            $before = fake()->numberBetween(30, 60);
            return [
                'training_level_before' => $before,
                'training_level_after' => min(100, $before + 10),
            ];
        });
    }

    /**
     * Indicate that this was a late training session (high levels).
     */
    public function lateTraining(): static
    {
        return $this->state(function (array $attributes) {
            $before = fake()->numberBetween(70, 90);
            return [
                'training_level_before' => $before,
                'training_level_after' => min(100, $before + 10),
            ];
        });
    }

    /**
     * Indicate that this training reached max level.
     */
    public function maxLevel(): static
    {
        return $this->state(fn (array $attributes) => [
            'training_level_before' => 90,
            'training_level_after' => 100,
        ]);
    }
}
