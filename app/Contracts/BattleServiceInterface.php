<?php

namespace App\Contracts;

use App\Models\Pet;
use App\Models\Battle;

interface BattleServiceInterface
{
    /**
     * Generate an opponent with random name and strength based on difficulty.
     *
     * @param string $difficulty The difficulty level ('easy', 'medium', 'hard')
     * @return array Array with 'name' and 'strength' keys
     */
    public function generateOpponent(string $difficulty): array;

    /**
     * Execute a battle between the pet and an opponent.
     *
     * @param Pet $pet The pet to battle
     * @param string $difficulty The difficulty level
     * @return Battle The battle record
     */
    public function executeBattle(Pet $pet, string $difficulty): Battle;

    /**
     * Determine the winner of a battle.
     *
     * @param float $petStrength The pet's battle strength
     * @param float $opponentStrength The opponent's battle strength
     * @return string The result ('win', 'loss', 'draw')
     */
    public function determineWinner(float $petStrength, float $opponentStrength): string;

    /**
     * Apply battle effects to the pet.
     *
     * @param Pet $pet The pet to apply effects to
     * @param string $result The battle result
     * @return Pet The updated pet
     */
    public function applyBattleEffects(Pet $pet, string $result): Pet;
}
