<?php

namespace Database\Factories;

use App\Models\Pet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pet>
 */
class PetFactory extends Factory
{
    protected $model = Pet::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->firstName() . fake()->numberBetween(1, 9999),
            'health' => 100,
            'hunger' => 0,
            'training_level' => 0,
            'last_updated_at' => now(),
        ];
    }

    /**
     * Indicate that the pet has low health.
     */
    public function lowHealth(): static
    {
        return $this->state(fn (array $attributes) => [
            'health' => fake()->numberBetween(0, 29),
        ]);
    }

    /**
     * Indicate that the pet has high hunger.
     */
    public function highHunger(): static
    {
        return $this->state(fn (array $attributes) => [
            'hunger' => fake()->numberBetween(71, 100),
        ]);
    }

    /**
     * Indicate that the pet is trained.
     */
    public function trained(): static
    {
        return $this->state(fn (array $attributes) => [
            'training_level' => fake()->numberBetween(50, 100),
        ]);
    }
}
