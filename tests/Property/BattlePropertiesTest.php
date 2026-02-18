<?php

use App\Contracts\BattleServiceInterface;

/**
 * Property 22: Geração de oponentes por dificuldade
 * 
 * **Validates: Requirements 9.2, 9.3, 9.4**
 * 
 * Para qualquer nível de dificuldade ('easy', 'medium', 'hard'), gerar um oponente
 * deve resultar em força dentro do range apropriado:
 * - easy: 20-40
 * - medium: 40-70
 * - hard: 70-95
 */
test('Property 22: opponent generation produces strength within correct range for difficulty', function () {
    $battleService = app(BattleServiceInterface::class);
    $iterations = 100;
    
    // Test each difficulty level
    $difficulties = [
        'easy' => ['min' => 20, 'max' => 40],
        'medium' => ['min' => 40, 'max' => 70],
        'hard' => ['min' => 70, 'max' => 95],
    ];
    
    foreach ($difficulties as $difficulty => $range) {
        for ($i = 0; $i < $iterations; $i++) {
            // Generate opponent with the specified difficulty
            $opponent = $battleService->generateOpponent($difficulty);
            
            // Property 1: Opponent must have 'name' and 'strength' keys
            expect($opponent)->toHaveKeys(['name', 'strength'],
                "Opponent for difficulty '{$difficulty}' must have 'name' and 'strength' keys"
            );
            
            // Property 2: Opponent name must be a non-empty string
            expect($opponent['name'])->toBeString()
                ->and($opponent['name'])->not->toBeEmpty(
                    "Opponent name must be a non-empty string for difficulty '{$difficulty}'"
                );
            
            // Property 3: Opponent strength must be a float
            expect($opponent['strength'])->toBeFloat(
                "Opponent strength must be a float for difficulty '{$difficulty}'"
            );
            
            // Property 4: Opponent strength must be within the correct range for the difficulty
            expect($opponent['strength'])->toBeGreaterThanOrEqual(
                $range['min'],
                "Opponent strength for difficulty '{$difficulty}' must be >= {$range['min']}. Got: {$opponent['strength']}"
            );
            expect($opponent['strength'])->toBeLessThanOrEqual(
                $range['max'],
                "Opponent strength for difficulty '{$difficulty}' must be <= {$range['max']}. Got: {$opponent['strength']}"
            );
            
            // Property 5: Opponent strength must be within the closed interval [min, max]
            expect($opponent['strength'])->toBeGreaterThanOrEqual($range['min'])
                ->and($opponent['strength'])->toBeLessThanOrEqual($range['max']);
        }
    }
});

/**
 * Property 22 (Edge Cases): Test boundary values for opponent generation
 * 
 * **Validates: Requirements 9.2, 9.3, 9.4**
 * 
 * Verify that the boundaries of each difficulty range are respected.
 */
test('Property 22 (Edge Cases): opponent generation respects exact boundaries', function () {
    $battleService = app(BattleServiceInterface::class);
    
    // Generate many opponents to ensure we hit boundary cases
    $iterations = 200;
    
    $difficulties = [
        'easy' => ['min' => 20, 'max' => 40],
        'medium' => ['min' => 40, 'max' => 70],
        'hard' => ['min' => 70, 'max' => 95],
    ];
    
    foreach ($difficulties as $difficulty => $range) {
        $strengths = [];
        
        for ($i = 0; $i < $iterations; $i++) {
            $opponent = $battleService->generateOpponent($difficulty);
            $strengths[] = $opponent['strength'];
        }
        
        // Property 1: All strengths must be within range
        foreach ($strengths as $strength) {
            expect($strength)->toBeGreaterThanOrEqual($range['min'])
                ->and($strength)->toBeLessThanOrEqual($range['max']);
        }
        
        // Property 2: Minimum value should be achievable (at least once in many iterations)
        $minStrength = min($strengths);
        expect($minStrength)->toBeLessThanOrEqual(
            $range['min'] + 5,
            "Minimum strength for '{$difficulty}' should be close to {$range['min']}. Got: {$minStrength}"
        );
        
        // Property 3: Maximum value should be achievable (at least once in many iterations)
        $maxStrength = max($strengths);
        expect($maxStrength)->toBeGreaterThanOrEqual(
            $range['max'] - 5,
            "Maximum strength for '{$difficulty}' should be close to {$range['max']}. Got: {$maxStrength}"
        );
        
        // Property 4: There should be variation in generated strengths
        $uniqueStrengths = count(array_unique($strengths));
        expect($uniqueStrengths)->toBeGreaterThan(
            10,
            "There should be significant variation in generated strengths for '{$difficulty}'. Got {$uniqueStrengths} unique values"
        );
    }
});

/**
 * Property 22 (Invariant): Opponent generation is deterministic per call
 * 
 * **Validates: Requirements 9.2, 9.3, 9.4**
 * 
 * Each call to generateOpponent should produce a valid opponent,
 * regardless of how many times it's called.
 */
test('Property 22 (Invariant): opponent generation is consistent and reliable', function () {
    $battleService = app(BattleServiceInterface::class);
    $iterations = 100;
    
    for ($i = 0; $i < $iterations; $i++) {
        // Randomly select a difficulty
        $difficulty = ['easy', 'medium', 'hard'][array_rand(['easy', 'medium', 'hard'])];
        
        // Generate opponent
        $opponent = $battleService->generateOpponent($difficulty);
        
        // Property 1: Every call must return a valid opponent structure
        expect($opponent)->toBeArray()
            ->and($opponent)->toHaveKeys(['name', 'strength']);
        
        // Property 2: Name must always be valid
        expect($opponent['name'])->toBeString()
            ->and($opponent['name'])->not->toBeEmpty();
        
        // Property 3: Strength must always be a positive number
        expect($opponent['strength'])->toBeFloat()
            ->and($opponent['strength'])->toBeGreaterThan(0);
        
        // Property 4: Strength must be within the global valid range (20-95)
        expect($opponent['strength'])->toBeGreaterThanOrEqual(20)
            ->and($opponent['strength'])->toBeLessThanOrEqual(95);
    }
});

/**
 * Property 22 (Non-Overlapping Ranges): Verify difficulty ranges are distinct
 * 
 * **Validates: Requirements 9.2, 9.3, 9.4**
 * 
 * Easy opponents should generally be weaker than medium, and medium weaker than hard.
 */
test('Property 22 (Non-Overlapping): difficulty levels produce appropriately scaled opponents', function () {
    $battleService = app(BattleServiceInterface::class);
    $iterations = 50;
    
    $easyStrengths = [];
    $mediumStrengths = [];
    $hardStrengths = [];
    
    // Generate multiple opponents for each difficulty
    for ($i = 0; $i < $iterations; $i++) {
        $easyStrengths[] = $battleService->generateOpponent('easy')['strength'];
        $mediumStrengths[] = $battleService->generateOpponent('medium')['strength'];
        $hardStrengths[] = $battleService->generateOpponent('hard')['strength'];
    }
    
    // Calculate averages
    $avgEasy = array_sum($easyStrengths) / count($easyStrengths);
    $avgMedium = array_sum($mediumStrengths) / count($mediumStrengths);
    $avgHard = array_sum($hardStrengths) / count($hardStrengths);
    
    // Property 1: Average strength should increase with difficulty
    expect($avgEasy)->toBeLessThan(
        $avgMedium,
        "Average easy strength ({$avgEasy}) should be less than average medium strength ({$avgMedium})"
    );
    expect($avgMedium)->toBeLessThan(
        $avgHard,
        "Average medium strength ({$avgMedium}) should be less than average hard strength ({$avgHard})"
    );
    
    // Property 2: Easy opponents should never be as strong as the hardest possible opponent
    $maxEasy = max($easyStrengths);
    expect($maxEasy)->toBeLessThanOrEqual(
        40,
        "Maximum easy strength should not exceed 40. Got: {$maxEasy}"
    );
    
    // Property 3: Hard opponents should never be as weak as the weakest possible opponent
    $minHard = min($hardStrengths);
    expect($minHard)->toBeGreaterThanOrEqual(
        70,
        "Minimum hard strength should not be below 70. Got: {$minHard}"
    );
    
    // Property 4: Medium difficulty should bridge easy and hard
    $minMedium = min($mediumStrengths);
    $maxMedium = max($mediumStrengths);
    
    expect($minMedium)->toBeGreaterThanOrEqual(
        40,
        "Minimum medium strength should be at least 40. Got: {$minMedium}"
    );
    expect($maxMedium)->toBeLessThanOrEqual(
        70,
        "Maximum medium strength should not exceed 70. Got: {$maxMedium}"
    );
});

/**
 * Property 23: Oponente com nome
 * 
 * **Validates: Requirements 9.6**
 * 
 * Para qualquer oponente gerado, ele deve ter um nome não-vazio.
 */
test('Property 23: generated opponents always have non-empty names', function () {
    $battleService = app(BattleServiceInterface::class);
    $iterations = 100;
    
    $difficulties = ['easy', 'medium', 'hard'];
    
    foreach ($difficulties as $difficulty) {
        for ($i = 0; $i < $iterations; $i++) {
            // Generate opponent
            $opponent = $battleService->generateOpponent($difficulty);
            
            // Property 1: Opponent must have a 'name' key
            expect($opponent)->toHaveKeys(['name', 'strength']);
            
            // Property 2: Name must be a string
            expect($opponent['name'])->toBeString();
            
            // Property 3: Name must not be empty
            expect($opponent['name'])->not->toBeEmpty();
            
            // Property 4: Name must have at least one character
            expect(strlen($opponent['name']))->toBeGreaterThan(0);
            
            // Property 5: Name should not be just whitespace
            expect(trim($opponent['name']))->not->toBeEmpty();
        }
    }
});

/**
 * Property 23 (Variation): Opponent names should vary
 * 
 * **Validates: Requirements 9.6**
 * 
 * Over multiple generations, different opponent names should be produced.
 */
test('Property 23 (Variation): opponent names show variation across generations', function () {
    $battleService = app(BattleServiceInterface::class);
    $iterations = 50;
    
    $names = [];
    
    // Generate multiple opponents
    for ($i = 0; $i < $iterations; $i++) {
        $difficulty = ['easy', 'medium', 'hard'][array_rand(['easy', 'medium', 'hard'])];
        $opponent = $battleService->generateOpponent($difficulty);
        $names[] = $opponent['name'];
    }
    
    // Property 1: There should be multiple unique names
    $uniqueNames = array_unique($names);
    expect(count($uniqueNames))->toBeGreaterThan(
        1,
        "Multiple unique opponent names should be generated. Got " . count($uniqueNames) . " unique names"
    );
    
    // Property 2: Each name should still be valid
    foreach ($uniqueNames as $name) {
        expect($name)->toBeString()
            ->and($name)->not->toBeEmpty()
            ->and(trim($name))->not->toBeEmpty();
    }
});

/**
 * Property 12: Determinação de vencedor
 * 
 * **Validates: Requirements 5.2**
 * 
 * Para qualquer par de forças de batalha onde força_pet > força_oponente,
 * o resultado deve ser 'win'; onde força_pet < força_oponente, deve ser 'loss'.
 */
test('Property 12: winner determination is correct based on strength comparison', function () {
    $battleService = app(BattleServiceInterface::class);
    $iterations = 100;
    
    for ($i = 0; $i < $iterations; $i++) {
        // Generate random strengths between 0 and 100
        $petStrength = (float) rand(0, 100);
        $opponentStrength = (float) rand(0, 100);
        
        // Determine winner
        $result = $battleService->determineWinner($petStrength, $opponentStrength);
        
        // Property 1: Result must be one of the valid values
        expect($result)->toBeIn(['win', 'loss', 'draw']);
        
        // Property 2: If pet strength > opponent strength, result must be 'win'
        if ($petStrength > $opponentStrength) {
            expect($result)->toBe('win',
                "Pet with strength {$petStrength} should win against opponent with strength {$opponentStrength}"
            );
        }
        
        // Property 3: If pet strength < opponent strength, result must be 'loss'
        if ($petStrength < $opponentStrength) {
            expect($result)->toBe('loss',
                "Pet with strength {$petStrength} should lose against opponent with strength {$opponentStrength}"
            );
        }
        
        // Property 4: If strengths are equal, result must be 'draw'
        if ($petStrength === $opponentStrength) {
            expect($result)->toBe('draw',
                "Pet with strength {$petStrength} should draw against opponent with strength {$opponentStrength}"
            );
        }
    }
});

/**
 * Property 12 (Edge Cases): Winner determination at boundaries
 * 
 * **Validates: Requirements 5.2**
 * 
 * Test specific edge cases for winner determination.
 */
test('Property 12 (Edge Cases): winner determination handles boundary values correctly', function () {
    $battleService = app(BattleServiceInterface::class);
    
    // Test case 1: Both strengths at 0
    expect($battleService->determineWinner(0.0, 0.0))->toBe('draw');
    
    // Test case 2: Both strengths at 100
    expect($battleService->determineWinner(100.0, 100.0))->toBe('draw');
    
    // Test case 3: Pet at 0, opponent at 100
    expect($battleService->determineWinner(0.0, 100.0))->toBe('loss');
    
    // Test case 4: Pet at 100, opponent at 0
    expect($battleService->determineWinner(100.0, 0.0))->toBe('win');
    
    // Test case 5: Very small difference (pet wins)
    expect($battleService->determineWinner(50.1, 50.0))->toBe('win');
    
    // Test case 6: Very small difference (pet loses)
    expect($battleService->determineWinner(50.0, 50.1))->toBe('loss');
    
    // Test case 7: Equal strengths at various points
    expect($battleService->determineWinner(25.0, 25.0))->toBe('draw');
    expect($battleService->determineWinner(50.0, 50.0))->toBe('draw');
    expect($battleService->determineWinner(75.0, 75.0))->toBe('draw');
});

/**
 * Property 12 (Transitivity): Winner determination is transitive
 * 
 * **Validates: Requirements 5.2**
 * 
 * If A beats B and B beats C, then A should beat C.
 */
test('Property 12 (Transitivity): winner determination follows transitive property', function () {
    $battleService = app(BattleServiceInterface::class);
    $iterations = 50;
    
    for ($i = 0; $i < $iterations; $i++) {
        // Generate three different strengths in ascending order
        $strengthA = (float) rand(0, 30);
        $strengthB = (float) rand(31, 60);
        $strengthC = (float) rand(61, 100);
        
        // A < B < C, so:
        // C should beat B
        expect($battleService->determineWinner($strengthC, $strengthB))->toBe('win');
        
        // B should beat A
        expect($battleService->determineWinner($strengthB, $strengthA))->toBe('win');
        
        // C should beat A (transitivity)
        expect($battleService->determineWinner($strengthC, $strengthA))->toBe('win');
    }
});

/**
 * Property 12 (Symmetry): Winner determination is symmetric
 * 
 * **Validates: Requirements 5.2**
 * 
 * If pet A beats pet B, then pet B should lose to pet A.
 */
test('Property 12 (Symmetry): winner determination is symmetric', function () {
    $battleService = app(BattleServiceInterface::class);
    $iterations = 100;
    
    for ($i = 0; $i < $iterations; $i++) {
        // Generate two different strengths
        $strengthA = (float) rand(0, 100);
        $strengthB = (float) rand(0, 100);
        
        // Skip if equal (draw case)
        if ($strengthA === $strengthB) {
            continue;
        }
        
        // Get results from both perspectives
        $resultAvsB = $battleService->determineWinner($strengthA, $strengthB);
        $resultBvsA = $battleService->determineWinner($strengthB, $strengthA);
        
        // Property: Results should be opposite
        if ($resultAvsB === 'win') {
            expect($resultBvsA)->toBe('loss',
                "If A (strength {$strengthA}) wins against B (strength {$strengthB}), then B should lose to A"
            );
        } elseif ($resultAvsB === 'loss') {
            expect($resultBvsA)->toBe('win',
                "If A (strength {$strengthA}) loses to B (strength {$strengthB}), then B should win against A"
            );
        }
    }
});

/**
 * Property 13: Efeitos de batalha no pet
 * 
 * **Validates: Requirements 5.4, 5.5**
 * 
 * Para qualquer batalha completada, o pet deve ter Fome aumentada em 10 pontos
 * e Saúde reduzida em 10 pontos.
 */
test('Property 13: battle effects always increase hunger and decrease health', function () {
    $battleService = app(BattleServiceInterface::class);
    $iterations = 100;
    
    $results = ['win', 'loss', 'draw'];
    
    for ($i = 0; $i < $iterations; $i++) {
        // Create a pet with random valid attributes
        // Keep hunger below 90 to avoid the hunger=100 special rule
        $initialHealth = rand(20, 100);
        $initialHunger = rand(0, 89);
        $initialTraining = rand(0, 100);
        
        $pet = \App\Models\Pet::factory()->create([
            'health' => $initialHealth,
            'hunger' => $initialHunger,
            'training_level' => $initialTraining,
        ]);
        
        // Apply battle effects with random result
        $result = $results[array_rand($results)];
        $battleService->applyBattleEffects($pet, $result);
        
        // Refresh pet from database
        $pet->refresh();
        
        // Property 1: Hunger should increase by 10 (capped at 100)
        $expectedHunger = min(100, $initialHunger + 10);
        expect($pet->hunger)->toBe($expectedHunger,
            "Hunger should increase by 10 from {$initialHunger} to {$expectedHunger}, got {$pet->hunger}"
        );
        
        // Property 2: Health should decrease by 10 (minimum 0)
        // Note: If hunger reaches 100, health is set to 50 (Requirement 2.5)
        if ($expectedHunger == 100) {
            expect($pet->health)->toBe(50,
                "When hunger reaches 100, health should be set to 50, got {$pet->health}"
            );
        } else {
            $expectedHealth = max(0, $initialHealth - 10);
            expect($pet->health)->toBe($expectedHealth,
                "Health should decrease by 10 from {$initialHealth} to {$expectedHealth}, got {$pet->health}"
            );
        }
    }
});

/**
 * Property 13 (Edge Cases): Battle effects at boundaries
 * 
 * **Validates: Requirements 5.4, 5.5**
 * 
 * Test battle effects when pet attributes are at boundary values.
 */
test('Property 13 (Edge Cases): battle effects respect attribute boundaries', function () {
    $battleService = app(BattleServiceInterface::class);
    
    // Test case 1: Pet with hunger at 95 (should cap at 100, and health becomes 50 due to hunger=100 rule)
    $pet1 = \App\Models\Pet::factory()->create([
        'health' => 60,
        'hunger' => 95,
        'training_level' => 50,
    ]);
    $battleService->applyBattleEffects($pet1, 'loss');
    $pet1->refresh();
    expect($pet1->hunger)->toBe(100);
    // When hunger = 100, validatePetState sets health to 50 (Requirement 2.5)
    expect($pet1->health)->toBe(50);
    
    // Test case 2: Pet with health at 5 (should not go below 0)
    $pet2 = \App\Models\Pet::factory()->create([
        'health' => 5,
        'hunger' => 50,
        'training_level' => 50,
    ]);
    $battleService->applyBattleEffects($pet2, 'loss');
    $pet2->refresh();
    expect($pet2->health)->toBe(0);
    expect($pet2->hunger)->toBe(60);
    
    // Test case 3: Pet with hunger at 100 (should stay at 100, health becomes 50)
    $pet3 = \App\Models\Pet::factory()->create([
        'health' => 60,
        'hunger' => 100,
        'training_level' => 50,
    ]);
    $battleService->applyBattleEffects($pet3, 'loss');
    $pet3->refresh();
    expect($pet3->hunger)->toBe(100);
    expect($pet3->health)->toBe(50);
    
    // Test case 4: Pet with health at 10 (should go to 0)
    $pet4 = \App\Models\Pet::factory()->create([
        'health' => 10,
        'hunger' => 50,
        'training_level' => 50,
    ]);
    $battleService->applyBattleEffects($pet4, 'loss');
    $pet4->refresh();
    expect($pet4->health)->toBe(0);
    expect($pet4->hunger)->toBe(60);
});

/**
 * Property 13 (Consistency): Battle effects are consistent across results
 * 
 * **Validates: Requirements 5.4, 5.5**
 * 
 * Hunger and health changes should be the same regardless of battle result
 * (win/loss/draw). Only training_level should differ.
 */
test('Property 13 (Consistency): hunger and health changes are consistent across all battle results', function () {
    $battleService = app(BattleServiceInterface::class);
    $iterations = 30;
    
    $results = ['win', 'loss', 'draw'];
    
    for ($i = 0; $i < $iterations; $i++) {
        $initialHealth = rand(20, 100);
        // Keep hunger below 90 to avoid the hunger=100 special rule
        $initialHunger = rand(0, 89);
        
        $pets = [];
        foreach ($results as $result) {
            $pet = \App\Models\Pet::factory()->create([
                'health' => $initialHealth,
                'hunger' => $initialHunger,
                'training_level' => 50,
            ]);
            $battleService->applyBattleEffects($pet, $result);
            $pet->refresh();
            $pets[$result] = $pet;
        }
        
        // Property: All pets should have the same hunger and health changes
        $expectedHunger = min(100, $initialHunger + 10);
        $expectedHealth = max(0, $initialHealth - 10);
        
        foreach ($results as $result) {
            expect($pets[$result]->hunger)->toBe($expectedHunger,
                "Hunger should be {$expectedHunger} for result '{$result}', got {$pets[$result]->hunger}"
            );
            expect($pets[$result]->health)->toBe($expectedHealth,
                "Health should be {$expectedHealth} for result '{$result}', got {$pets[$result]->health}"
            );
        }
    }
});

/**
 * Property 14: Bônus por vitória
 * 
 * **Validates: Requirements 5.6**
 * 
 * Para qualquer batalha vencida, o Nível_de_Treinamento do pet deve aumentar
 * em 5 pontos (máximo 100).
 */
test('Property 14: winning battles increases training level by 5', function () {
    $battleService = app(BattleServiceInterface::class);
    $iterations = 100;
    
    for ($i = 0; $i < $iterations; $i++) {
        // Create a pet with random training level (below 95 to test the increase)
        $initialTraining = rand(0, 95);
        
        $pet = \App\Models\Pet::factory()->create([
            'health' => 50,
            'hunger' => 50,
            'training_level' => $initialTraining,
        ]);
        
        // Apply battle effects with 'win' result
        $battleService->applyBattleEffects($pet, 'win');
        
        // Refresh pet from database
        $pet->refresh();
        
        // Property: Training level should increase by 5 (capped at 100)
        $expectedTraining = min(100, $initialTraining + 5);
        expect($pet->training_level)->toBe($expectedTraining,
            "Training level should increase by 5 from {$initialTraining} to {$expectedTraining}, got {$pet->training_level}"
        );
    }
});

/**
 * Property 14 (No Bonus): Losing or drawing does not increase training level
 * 
 * **Validates: Requirements 5.6**
 * 
 * Only wins should increase training level. Losses and draws should not.
 */
test('Property 14 (No Bonus): losing or drawing does not increase training level', function () {
    $battleService = app(BattleServiceInterface::class);
    $iterations = 50;
    
    $nonWinResults = ['loss', 'draw'];
    
    foreach ($nonWinResults as $result) {
        for ($i = 0; $i < $iterations; $i++) {
            $initialTraining = rand(0, 100);
            
            $pet = \App\Models\Pet::factory()->create([
                'health' => 50,
                'hunger' => 50,
                'training_level' => $initialTraining,
            ]);
            
            // Apply battle effects with non-win result
            $battleService->applyBattleEffects($pet, $result);
            
            // Refresh pet from database
            $pet->refresh();
            
            // Property: Training level should remain unchanged
            expect($pet->training_level)->toBe($initialTraining,
                "Training level should not change for result '{$result}'. Was {$initialTraining}, got {$pet->training_level}"
            );
        }
    }
});

/**
 * Property 14 (Cap): Training level caps at 100
 * 
 * **Validates: Requirements 5.6**
 * 
 * Training level should never exceed 100, even after winning.
 */
test('Property 14 (Cap): training level never exceeds 100 after victory', function () {
    $battleService = app(BattleServiceInterface::class);
    
    // Test various training levels near the cap
    $testCases = [95, 96, 97, 98, 99, 100];
    
    foreach ($testCases as $initialTraining) {
        $pet = \App\Models\Pet::factory()->create([
            'health' => 50,
            'hunger' => 50,
            'training_level' => $initialTraining,
        ]);
        
        // Apply battle effects with 'win' result
        $battleService->applyBattleEffects($pet, 'win');
        
        // Refresh pet from database
        $pet->refresh();
        
        // Property 1: Training level should not exceed 100
        expect($pet->training_level)->toBeLessThanOrEqual(100,
            "Training level should not exceed 100. Started at {$initialTraining}, got {$pet->training_level}"
        );
        
        // Property 2: Training level should be exactly 100 for all these cases
        expect($pet->training_level)->toBe(100,
            "Training level should be capped at 100. Started at {$initialTraining}, got {$pet->training_level}"
        );
    }
});

/**
 * Property 14 (Consistency): Victory bonus is consistent
 * 
 * **Validates: Requirements 5.6**
 * 
 * The training level increase should always be exactly 5 for wins (when not capped).
 */
test('Property 14 (Consistency): victory bonus is always exactly 5 points', function () {
    $battleService = app(BattleServiceInterface::class);
    $iterations = 50;
    
    for ($i = 0; $i < $iterations; $i++) {
        // Use training levels that won't hit the cap
        $initialTraining = rand(0, 90);
        
        $pet = \App\Models\Pet::factory()->create([
            'health' => 50,
            'hunger' => 50,
            'training_level' => $initialTraining,
        ]);
        
        // Apply battle effects with 'win' result
        $battleService->applyBattleEffects($pet, 'win');
        
        // Refresh pet from database
        $pet->refresh();
        
        // Property: The increase should be exactly 5
        $actualIncrease = $pet->training_level - $initialTraining;
        expect($actualIncrease)->toBe(5,
            "Training level increase should be exactly 5. Started at {$initialTraining}, ended at {$pet->training_level}, increase was {$actualIncrease}"
        );
    }
});

/**
 * Property 15: Registro de batalha
 * 
 * **Validates: Requirements 5.7**
 * 
 * Para qualquer batalha executada, deve existir um Battle record com resultado,
 * forças, e timestamp.
 */
test('Property 15: executing battle creates complete battle record', function () {
    $battleService = app(BattleServiceInterface::class);
    $iterations = 100;
    
    $difficulties = ['easy', 'medium', 'hard'];
    
    for ($i = 0; $i < $iterations; $i++) {
        // Create a pet with random attributes
        $pet = \App\Models\Pet::factory()->create([
            'health' => rand(30, 100),
            'hunger' => rand(0, 70),
            'training_level' => rand(0, 100),
        ]);
        
        // Select random difficulty
        $difficulty = $difficulties[array_rand($difficulties)];
        
        // Get initial battle count
        $initialBattleCount = \App\Models\Battle::count();
        
        // Execute battle
        $battle = $battleService->executeBattle($pet, $difficulty);
        
        // Property 1: A new Battle record should be created
        expect(\App\Models\Battle::count())->toBe($initialBattleCount + 1,
            "A new Battle record should be created after executing battle"
        );
        
        // Property 2: Battle should be an instance of Battle model
        expect($battle)->toBeInstanceOf(\App\Models\Battle::class);
        
        // Property 3: Battle should have pet_id set
        expect($battle->pet_id)->toBe($pet->id,
            "Battle record should have correct pet_id"
        );
        
        // Property 4: Battle should have opponent_name (non-empty string)
        expect($battle->opponent_name)->toBeString()
            ->and($battle->opponent_name)->not->toBeEmpty();
        
        // Property 5: Battle should have opponent_strength (valid float)
        expect($battle->opponent_strength)->toBeFloat()
            ->and($battle->opponent_strength)->toBeGreaterThan(0);
        
        // Property 6: Battle should have pet_strength (valid float)
        expect($battle->pet_strength)->toBeFloat()
            ->and($battle->pet_strength)->toBeGreaterThanOrEqual(0);
        
        // Property 7: Battle should have result (one of valid values)
        expect($battle->result)->toBeIn(['win', 'loss', 'draw']);
        
        // Property 8: Battle should have difficulty set correctly
        expect($battle->difficulty)->toBe($difficulty);
        
        // Property 9: Battle should have fought_at timestamp
        expect($battle->fought_at)->toBeInstanceOf(\DateTime::class);
        
        // Property 10: fought_at should be recent (within last minute)
        $now = new \DateTime();
        $timeDiff = $now->getTimestamp() - $battle->fought_at->getTimestamp();
        expect($timeDiff)->toBeLessThan(60,
            "Battle fought_at timestamp should be recent"
        );
    }
});

/**
 * Property 15 (Correctness): Battle record data matches battle outcome
 * 
 * **Validates: Requirements 5.7**
 * 
 * The recorded result should match the actual comparison of strengths.
 */
test('Property 15 (Correctness): battle record result matches strength comparison', function () {
    $battleService = app(BattleServiceInterface::class);
    $iterations = 50;
    
    $difficulties = ['easy', 'medium', 'hard'];
    
    for ($i = 0; $i < $iterations; $i++) {
        $pet = \App\Models\Pet::factory()->create([
            'health' => rand(30, 100),
            'hunger' => rand(0, 70),
            'training_level' => rand(0, 100),
        ]);
        
        $difficulty = $difficulties[array_rand($difficulties)];
        
        // Execute battle
        $battle = $battleService->executeBattle($pet, $difficulty);
        
        // Property: Result should match the strength comparison
        if ($battle->pet_strength > $battle->opponent_strength) {
            expect($battle->result)->toBe('win',
                "Result should be 'win' when pet_strength ({$battle->pet_strength}) > opponent_strength ({$battle->opponent_strength})"
            );
        } elseif ($battle->pet_strength < $battle->opponent_strength) {
            expect($battle->result)->toBe('loss',
                "Result should be 'loss' when pet_strength ({$battle->pet_strength}) < opponent_strength ({$battle->opponent_strength})"
            );
        } else {
            expect($battle->result)->toBe('draw',
                "Result should be 'draw' when pet_strength ({$battle->pet_strength}) == opponent_strength ({$battle->opponent_strength})"
            );
        }
    }
});

/**
 * Property 15 (Persistence): Battle records are persisted to database
 * 
 * **Validates: Requirements 5.7**
 * 
 * Battle records should be retrievable from the database after creation.
 */
test('Property 15 (Persistence): battle records are persisted and retrievable', function () {
    $battleService = app(BattleServiceInterface::class);
    $iterations = 30;
    
    for ($i = 0; $i < $iterations; $i++) {
        $pet = \App\Models\Pet::factory()->create([
            'health' => rand(30, 100),
            'hunger' => rand(0, 70),
            'training_level' => rand(0, 100),
        ]);
        
        $difficulty = ['easy', 'medium', 'hard'][array_rand(['easy', 'medium', 'hard'])];
        
        // Execute battle
        $battle = $battleService->executeBattle($pet, $difficulty);
        
        // Property 1: Battle should have an ID (was saved)
        expect($battle->id)->not->toBeNull();
        
        // Property 2: Battle should be retrievable from database
        $retrievedBattle = \App\Models\Battle::find($battle->id);
        expect($retrievedBattle)->not->toBeNull();
        
        // Property 3: Retrieved battle should have same attributes
        expect($retrievedBattle->pet_id)->toBe($battle->pet_id);
        expect($retrievedBattle->opponent_name)->toBe($battle->opponent_name);
        expect($retrievedBattle->opponent_strength)->toBe($battle->opponent_strength);
        // Use approximate comparison for floats
        expect(abs($retrievedBattle->pet_strength - $battle->pet_strength))->toBeLessThan(0.01);
        expect($retrievedBattle->result)->toBe($battle->result);
        expect($retrievedBattle->difficulty)->toBe($battle->difficulty);
    }
});

/**
 * Property 15 (Relationship): Battle records are associated with pet
 * 
 * **Validates: Requirements 5.7**
 * 
 * Battle records should be accessible through the pet's battles relationship.
 */
test('Property 15 (Relationship): battle records are accessible through pet relationship', function () {
    $battleService = app(BattleServiceInterface::class);
    
    // Create a pet
    $pet = \App\Models\Pet::factory()->create([
        'health' => 80,
        'hunger' => 30,
        'training_level' => 50,
    ]);
    
    // Execute multiple battles
    $battleCount = rand(3, 7);
    $battleIds = [];
    
    for ($i = 0; $i < $battleCount; $i++) {
        // Refresh pet and reset to healthy state for each battle
        $pet = \App\Models\Pet::find($pet->id);
        $pet->health = 80;
        $pet->hunger = 30;
        $pet->training_level = 50;
        $pet->save();
        
        $difficulty = ['easy', 'medium', 'hard'][array_rand(['easy', 'medium', 'hard'])];
        $battle = $battleService->executeBattle($pet, $difficulty);
        $battleIds[] = $battle->id;
    }
    
    // Property 1: Pet should have the correct number of battles
    $pet = \App\Models\Pet::find($pet->id);
    expect($pet->battles()->count())->toBe($battleCount,
        "Pet should have {$battleCount} battles"
    );
    
    // Property 2: All created battles should be in the pet's battles
    $petBattleIds = $pet->battles()->pluck('id')->toArray();
    foreach ($battleIds as $battleId) {
        expect(in_array($battleId, $petBattleIds))->toBeTrue(
            "Pet's battles should include battle ID {$battleId}"
        );
    }
});
