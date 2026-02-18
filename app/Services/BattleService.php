<?php

namespace App\Services;

use App\Contracts\BattleServiceInterface;
use App\Contracts\PetServiceInterface;
use App\Models\Pet;
use App\Models\Battle;
use DateTime;

class BattleService implements BattleServiceInterface
{
    /**
     * List of possible opponent names.
     */
    private const OPPONENT_NAMES = [
        'Shadow', 'Thunder', 'Blaze', 'Storm', 'Frost',
        'Luna', 'Nova', 'Spike', 'Rex', 'Max',
        'Bella', 'Rocky', 'Zeus', 'Apollo', 'Atlas',
        'Titan', 'Phoenix', 'Dragon', 'Viper', 'Cobra',
        'Tiger', 'Lion', 'Bear', 'Wolf', 'Eagle',
        'Hawk', 'Falcon', 'Raven', 'Onyx', 'Ruby'
    ];

    public function __construct(
        private PetServiceInterface $petService
    ) {}

    /**
     * Generate an opponent with random name and strength based on difficulty.
     *
     * @param string $difficulty The difficulty level ('easy', 'medium', 'hard')
     * @return array Array with 'name' and 'strength' keys
     */
    public function generateOpponent(string $difficulty): array
    {
        // Generate random name
        $name = self::OPPONENT_NAMES[array_rand(self::OPPONENT_NAMES)];
        
        // Define strength based on difficulty
        $strength = match ($difficulty) {
            'easy' => rand(20, 40),
            'medium' => rand(40, 70),
            'hard' => rand(70, 95),
            default => rand(20, 40), // Default to easy if invalid difficulty
        };
        
        return [
            'name' => $name,
            'strength' => (float) $strength,
        ];
    }

    /**
     * Execute a battle between the pet and an opponent.
     *
     * @param Pet $pet The pet to battle
     * @param string $difficulty The difficulty level
     * @return Battle The battle record
     */
    public function executeBattle(Pet $pet, string $difficulty): Battle
    {
        // Generate opponent
        $opponent = $this->generateOpponent($difficulty);
        
        // Calculate pet's battle strength
        $petStrength = $this->petService->calculateBattleStrength($pet);
        
        // Determine winner
        $result = $this->determineWinner($petStrength, $opponent['strength']);
        
        // Apply battle effects to pet
        $this->applyBattleEffects($pet, $result);
        
        // Create and save Battle record
        $battle = Battle::create([
            'pet_id' => $pet->id,
            'opponent_name' => $opponent['name'],
            'opponent_strength' => $opponent['strength'],
            'pet_strength' => $petStrength,
            'result' => $result,
            'difficulty' => $difficulty,
            'fought_at' => new DateTime(),
        ]);
        
        return $battle;
    }

    /**
     * Determine the winner of a battle.
     *
     * @param float $petStrength The pet's battle strength
     * @param float $opponentStrength The opponent's battle strength
     * @return string The result ('win', 'loss', 'draw')
     */
    public function determineWinner(float $petStrength, float $opponentStrength): string
    {
        if ($petStrength > $opponentStrength) {
            return 'win';
        } elseif ($petStrength < $opponentStrength) {
            return 'loss';
        } else {
            return 'draw';
        }
    }

    /**
     * Apply battle effects to the pet.
     *
     * @param Pet $pet The pet to apply effects to
     * @param string $result The battle result
     * @return Pet The updated pet
     */
    public function applyBattleEffects(Pet $pet, string $result): Pet
    {
        // Increase hunger by 10
        $pet->hunger = min(100, $pet->hunger + 10);
        
        // Reduce health by 10
        $pet->health = max(0, $pet->health - 10);
        
        // If win: increase training_level by 5 (maximum 100)
        if ($result === 'win') {
            $pet->training_level = min(100, $pet->training_level + 5);
        }
        
        // Update last_updated_at timestamp
        $pet->last_updated_at = new DateTime();
        
        // Validate pet state
        $pet = $this->petService->validatePetState($pet);
        
        // Save the pet
        $pet->save();
        
        return $pet;
    }
}
