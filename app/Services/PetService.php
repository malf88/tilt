<?php

namespace App\Services;

use App\Contracts\PetServiceInterface;
use App\Models\Pet;
use DateTime;

class PetService implements PetServiceInterface
{
    /**
     * Create a new pet with default values.
     *
     * @param string $name The name of the pet
     * @param string $petType The type of the pet
     * @return Pet The created pet
     */
    public function createPet(string $name, string $petType = 'dog'): Pet
    {
        $pet = Pet::create([
            'name' => $name,
            'pet_type' => $petType,
            'health' => 100,
            'hunger' => 0,
            'training_level' => 0,
            'last_updated_at' => new DateTime(),
        ]);

        return $pet;
    }

    /**
     * Feed the pet to reduce hunger and increase health.
     *
     * @param Pet $pet The pet to feed
     * @return Pet The updated pet
     */
    public function feedPet(Pet $pet): Pet
    {
        // Reduce hunger by 20 (minimum 0)
        $pet->hunger = max(0, $pet->hunger - 20);
        
        // Increase health by 10 (maximum 100)
        $pet->health = min(100, $pet->health + 10);
        
        // Update last_updated_at timestamp
        $pet->last_updated_at = new DateTime();
        
        // Save the pet
        $pet->save();
        
        return $pet;
    }

    /**
     * Train the pet to increase training level.
     *
     * @param Pet $pet The pet to train
     * @return Pet The updated pet
     */
    /**
     * Train the pet to increase training level.
     *
     * @param Pet $pet The pet to train
     * @return Pet The updated pet
     * @throws \Exception If pet health is below 30
     */
    public function trainPet(Pet $pet): Pet
    {
        // Validate health >= 30 (throw exception if fails)
        if ($pet->health < 30) {
            throw new \Exception('Pet muito fraco para treinar. Alimente-o primeiro.');
        }

        // Store training level before training
        $trainingLevelBefore = $pet->training_level;

        // Increase training_level by 10 (maximum 100)
        $pet->training_level = min(100, $pet->training_level + 10);

        // Increase hunger by 15 (maximum 100)
        $pet->hunger = min(100, $pet->hunger + 15);

        // Reduce health by 5 (minimum 20)
        $pet->health = max(20, $pet->health - 5);

        // Update last_updated_at timestamp
        $pet->last_updated_at = new DateTime();

        // Save the pet
        $pet->save();

        // Create TrainingLog
        \App\Models\TrainingLog::create([
            'pet_id' => $pet->id,
            'training_level_before' => $trainingLevelBefore,
            'training_level_after' => $pet->training_level,
            'trained_at' => new DateTime(),
        ]);

        return $pet;
    }

    /**
     * Apply time-based degradation to the pet's attributes.
     *
     * @param Pet $pet The pet to apply degradation to
     * @return Pet The updated pet
     */
    public function applyTimeDegradation(Pet $pet): Pet
    {
        $timeService = app(TimeService::class);
        
        // Calculate elapsed 30-minute intervals
        $intervals = $timeService->calculateElapsedIntervals($pet->last_updated_at, 30);
        
        // Apply 8-hour cap
        $cappedIntervals = $timeService->applyDegradationCap($intervals, 8, 30);
        
        // Store initial hunger to determine health degradation
        $initialHunger = $pet->hunger;
        
        // Increase hunger by 5 points per 30-minute interval
        $pet->hunger = min(100, $pet->hunger + ($cappedIntervals * 5));
        
        // If hunger > 50: reduce health by 2 points per 30-minute interval
        if ($initialHunger > 50) {
            $pet->health = max(0, $pet->health - ($cappedIntervals * 2));
        }
        
        // If hunger > 80: reduce health by 5 points per hour (additional degradation)
        if ($initialHunger > 80) {
            $hourlyIntervals = $timeService->calculateElapsedIntervals($pet->last_updated_at, 60);
            $cappedHourlyIntervals = $timeService->applyDegradationCap($hourlyIntervals, 8, 60);
            $pet->health = max(0, $pet->health - ($cappedHourlyIntervals * 5));
        }
        
        // Update last_updated_at timestamp
        $pet->last_updated_at = new DateTime();
        
        // Save the pet
        $pet->save();
        
        return $pet;
    }

    /**
     * Calculate the pet's battle strength.
     *
     * @param Pet $pet The pet to calculate strength for
     * @return float The battle strength (0-100)
     */
    /**
     * Calculate the pet's battle strength.
     * Formula: (health × 0.4) + (training_level × 0.6)
     * With 20% penalty if hunger > 70
     *
     * @param Pet $pet The pet to calculate strength for
     * @return float The battle strength (0-100)
     */
    public function calculateBattleStrength(Pet $pet): float
    {
        // Apply formula: (health × 0.4) + (training_level × 0.6)
        $baseStrength = ($pet->health * 0.4) + ($pet->training_level * 0.6);

        // Apply 20% penalty if hunger > 70
        if ($pet->hunger > 70) {
            $baseStrength *= 0.8; // 20% penalty
        }

        // Ensure result is between 0 and 100
        return max(0, min(100, $baseStrength));
    }

    /**
     * Validate and clamp the pet's state to ensure all attributes are within valid ranges.
     *
     * @param Pet $pet The pet to validate
     * @return Pet The validated pet
     */
    public function validatePetState(Pet $pet): Pet
    {
        // Clamp health between 0 and 100
        $pet->health = max(0, min(100, $pet->health));
        
        // Clamp hunger between 0 and 100
        $pet->hunger = max(0, min(100, $pet->hunger));
        
        // Clamp training_level between 0 and 100
        $pet->training_level = max(0, min(100, $pet->training_level));
        
        // Special rule: if hunger == 100, set health = 50
        if ($pet->hunger == 100) {
            $pet->health = 50;
        }
        
        // Save the pet
        $pet->save();
        
        return $pet;
    }
}
