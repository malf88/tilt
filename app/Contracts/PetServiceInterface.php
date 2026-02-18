<?php

namespace App\Contracts;

use App\Models\Pet;

interface PetServiceInterface
{
    /**
     * Create a new pet with default values.
     *
     * @param string $name The name of the pet
     * @return Pet The created pet
     */
    public function createPet(string $name): Pet;

    /**
     * Feed the pet to reduce hunger and increase health.
     *
     * @param Pet $pet The pet to feed
     * @return Pet The updated pet
     */
    public function feedPet(Pet $pet): Pet;

    /**
     * Train the pet to increase training level.
     *
     * @param Pet $pet The pet to train
     * @return Pet The updated pet
     */
    public function trainPet(Pet $pet): Pet;

    /**
     * Apply time-based degradation to the pet's attributes.
     *
     * @param Pet $pet The pet to apply degradation to
     * @return Pet The updated pet
     */
    public function applyTimeDegradation(Pet $pet): Pet;

    /**
     * Calculate the pet's battle strength.
     *
     * @param Pet $pet The pet to calculate strength for
     * @return float The battle strength (0-100)
     */
    public function calculateBattleStrength(Pet $pet): float;

    /**
     * Validate and clamp the pet's state to ensure all attributes are within valid ranges.
     *
     * @param Pet $pet The pet to validate
     * @return Pet The validated pet
     */
    public function validatePetState(Pet $pet): Pet;
}
