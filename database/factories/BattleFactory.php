<?php

namespace Database\Factories;

use App\Models\Battle;
use App\Models\Pet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Battle>
 */
class BattleFactory extends Factory
{
    protected $model = Battle::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pet_id' => Pet::factory(),
            'opponent_name' => fake()->firstName() . ' the ' . fake()->randomElement(['Fierce', 'Mighty', 'Swift', 'Brave', 'Wild']),
            'opponent_strength' => fake()->randomFloat(2, 20, 95),
            'pet_strength' => fake()->randomFloat(2, 0, 100),
            'result' => fake()->randomElement(['win', 'loss', 'draw']),
            'difficulty' => fake()->randomElement(['easy', 'medium', 'hard']),
            'fought_at' => now(),
        ];
    }

    /**
     * Indicate that the battle was won.
     */
    public function won(): static
    {
        return $this->state(fn (array $attributes) => [
            'result' => 'win',
            'pet_strength' => fake()->randomFloat(2, 50, 100),
            'opponent_strength' => fake()->randomFloat(2, 20, 49),
        ]);
    }

    /**
     * Indicate that the battle was lost.
     */
    public function lost(): static
    {
        return $this->state(fn (array $attributes) => [
            'result' => 'loss',
            'pet_strength' => fake()->randomFloat(2, 20, 49),
            'opponent_strength' => fake()->randomFloat(2, 50, 100),
        ]);
    }

    /**
     * Indicate that the battle was a draw.
     */
    public function draw(): static
    {
        return $this->state(function (array $attributes) {
            $strength = fake()->randomFloat(2, 30, 70);
            return [
                'result' => 'draw',
                'pet_strength' => $strength,
                'opponent_strength' => $strength,
            ];
        });
    }

    /**
     * Indicate that the battle was easy difficulty.
     */
    public function easy(): static
    {
        return $this->state(fn (array $attributes) => [
            'difficulty' => 'easy',
            'opponent_strength' => fake()->randomFloat(2, 20, 40),
        ]);
    }

    /**
     * Indicate that the battle was medium difficulty.
     */
    public function medium(): static
    {
        return $this->state(fn (array $attributes) => [
            'difficulty' => 'medium',
            'opponent_strength' => fake()->randomFloat(2, 40, 70),
        ]);
    }

    /**
     * Indicate that the battle was hard difficulty.
     */
    public function hard(): static
    {
        return $this->state(fn (array $attributes) => [
            'difficulty' => 'hard',
            'opponent_strength' => fake()->randomFloat(2, 70, 95),
        ]);
    }
}
