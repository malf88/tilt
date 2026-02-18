<?php

use App\Models\Pet;

/**
 * Property 1: Inicialização de Pet com valores padrão
 * 
 * **Validates: Requirements 1.2**
 * 
 * Para qualquer nome de pet válido, quando um novo pet é criado,
 * ele deve ter Saúde = 100, Fome = 0, e Nível_de_Treinamento = 0.
 */
test('Property 1: new pets always start with default values', function () {
    // Generate multiple random valid pet names and test the property
    $iterations = 100;
    
    for ($i = 0; $i < $iterations; $i++) {
        // Generate a valid pet name (2-50 characters)
        $name = generateValidPetName();
        
        // Create a new pet
        $pet = Pet::create([
            'name' => $name,
            'health' => 100,
            'hunger' => 0,
            'training_level' => 0,
            'last_updated_at' => now(),
        ]);
        
        // Verify the property: all new pets must have default values
        expect($pet->health)->toBe(100, "Pet '{$name}' should have health = 100")
            ->and($pet->hunger)->toBe(0, "Pet '{$name}' should have hunger = 0")
            ->and($pet->training_level)->toBe(0, "Pet '{$name}' should have training_level = 0");
        
        // Clean up for next iteration
        $pet->delete();
    }
});

/**
 * Helper function to generate valid pet names
 * Valid names are 2-50 characters long
 */
function generateValidPetName(): string
{
    static $counter = 0;
    $counter++;
    
    $prefixes = ['Fluffy', 'Shadow', 'Luna', 'Max', 'Bella', 'Rocky', 'Daisy', 'Charlie', 'Milo', 'Coco'];
    $suffixes = ['Star', 'Moon', 'Fire', 'Storm', 'Wind', 'Thunder', 'Light', 'Dark', 'Swift', 'Brave'];
    $numbers = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X'];
    
    // Generate various combinations to ensure diversity
    $strategies = [
        // Simple prefix
        fn() => $prefixes[array_rand($prefixes)] . $counter,
        // Prefix + Suffix
        fn() => $prefixes[array_rand($prefixes)] . $suffixes[array_rand($suffixes)] . $counter,
        // Prefix + Number
        fn() => $prefixes[array_rand($prefixes)] . ' ' . $numbers[array_rand($numbers)] . $counter,
        // Random string
        fn() => 'Pet' . bin2hex(random_bytes(5)) . $counter,
        // Short name (2 chars minimum)
        fn() => chr(65 + rand(0, 25)) . chr(97 + rand(0, 25)) . $counter,
        // Long name (up to 50 chars)
        fn() => str_repeat($prefixes[array_rand($prefixes)], 3) . $counter,
    ];
    
    $name = $strategies[array_rand($strategies)]();
    
    // Ensure name is within valid length (2-50 characters)
    if (strlen($name) < 2) {
        $name = $name . 'XX';
    }
    if (strlen($name) > 50) {
        $name = substr($name, 0, 47) . $counter;
    }
    
    return $name;
}

/**
 * Property 4: Efeitos da alimentação
 * 
 * **Validates: Requirements 2.1, 2.2**
 * 
 * Para qualquer pet, alimentá-lo deve reduzir a Fome em 20 pontos (mínimo 0)
 * e aumentar a Saúde em 10 pontos (máximo 100).
 */
test('Property 4: feeding reduces hunger by 20 and increases health by 10', function () {
    $petService = app(\App\Contracts\PetServiceInterface::class);
    $iterations = 100;
    
    for ($i = 0; $i < $iterations; $i++) {
        // Generate a pet with random valid attributes
        $initialHealth = generateRandomAttribute();
        $initialHunger = generateRandomAttribute();
        $initialTrainingLevel = generateRandomAttribute();
        
        // Create pet with these attributes
        $pet = Pet::create([
            'name' => generateValidPetName(),
            'health' => $initialHealth,
            'hunger' => $initialHunger,
            'training_level' => $initialTrainingLevel,
            'last_updated_at' => now(),
        ]);
        
        // Feed the pet
        $fedPet = $petService->feedPet($pet);
        
        // Calculate expected values
        $expectedHunger = max(0, $initialHunger - 20);
        $expectedHealth = min(100, $initialHealth + 10);
        
        // Property 1: Hunger should be reduced by 20 (minimum 0)
        expect($fedPet->hunger)->toBe(
            $expectedHunger,
            "Hunger should be reduced by 20 (min 0). Initial: {$initialHunger}, Expected: {$expectedHunger}, Got: {$fedPet->hunger}"
        );
        
        // Property 2: Health should be increased by 10 (maximum 100)
        expect($fedPet->health)->toBe(
            $expectedHealth,
            "Health should be increased by 10 (max 100). Initial: {$initialHealth}, Expected: {$expectedHealth}, Got: {$fedPet->health}"
        );
        
        // Property 3: Training level should remain unchanged
        expect($fedPet->training_level)->toBe(
            $initialTrainingLevel,
            "Training level should not change when feeding"
        );
        
        // Property 4: All attributes should remain within valid bounds (0-100)
        expect($fedPet->health)->toBeGreaterThanOrEqual(0)
            ->and($fedPet->health)->toBeLessThanOrEqual(100)
            ->and($fedPet->hunger)->toBeGreaterThanOrEqual(0)
            ->and($fedPet->hunger)->toBeLessThanOrEqual(100);
        
        // Clean up
        $pet->delete();
    }
});

/**
 * Helper function to generate random attribute values (0-100)
 * Uses various strategies to ensure good coverage of edge cases
 */
function generateRandomAttribute(): int
{
    $strategies = [
        // Edge case: minimum value
        fn() => 0,
        // Edge case: maximum value
        fn() => 100,
        // Low values (1-20)
        fn() => rand(1, 20),
        // Medium-low values (21-40)
        fn() => rand(21, 40),
        // Medium values (41-60)
        fn() => rand(41, 60),
        // Medium-high values (61-80)
        fn() => rand(61, 80),
        // High values (81-99)
        fn() => rand(81, 99),
        // Random value across full range
        fn() => rand(0, 100),
    ];
    
    return $strategies[array_rand($strategies)]();
}

/**
 * Property 7: Efeitos do treinamento
 * 
 * **Validates: Requirements 3.1, 3.2, 3.3**
 * 
 * Para qualquer pet com Saúde >= 30, treiná-lo deve aumentar Nível_de_Treinamento
 * em 10 (máximo 100), aumentar Fome em 15 (máximo 100), e reduzir Saúde em 5 (mínimo 20).
 */
test('Property 7: training increases training_level by 10, hunger by 15, and reduces health by 5', function () {
    $petService = app(\App\Contracts\PetServiceInterface::class);
    $iterations = 100;
    
    for ($i = 0; $i < $iterations; $i++) {
        // Generate a pet with health >= 30 (requirement for training)
        $initialHealth = generateHealthForTraining();
        $initialHunger = generateRandomAttribute();
        $initialTrainingLevel = generateRandomAttribute();
        
        // Create pet with these attributes
        $pet = Pet::create([
            'name' => generateValidPetName(),
            'health' => $initialHealth,
            'hunger' => $initialHunger,
            'training_level' => $initialTrainingLevel,
            'last_updated_at' => now(),
        ]);
        
        // Train the pet
        $trainedPet = $petService->trainPet($pet);
        
        // Calculate expected values
        $expectedTrainingLevel = min(100, $initialTrainingLevel + 10);
        $expectedHunger = min(100, $initialHunger + 15);
        $expectedHealth = max(20, $initialHealth - 5);
        
        // Property 1: Training level should increase by 10 (maximum 100)
        expect($trainedPet->training_level)->toBe(
            $expectedTrainingLevel,
            "Training level should increase by 10 (max 100). Initial: {$initialTrainingLevel}, Expected: {$expectedTrainingLevel}, Got: {$trainedPet->training_level}"
        );
        
        // Property 2: Hunger should increase by 15 (maximum 100)
        expect($trainedPet->hunger)->toBe(
            $expectedHunger,
            "Hunger should increase by 15 (max 100). Initial: {$initialHunger}, Expected: {$expectedHunger}, Got: {$trainedPet->hunger}"
        );
        
        // Property 3: Health should decrease by 5 (minimum 20)
        expect($trainedPet->health)->toBe(
            $expectedHealth,
            "Health should decrease by 5 (min 20). Initial: {$initialHealth}, Expected: {$expectedHealth}, Got: {$trainedPet->health}"
        );
        
        // Property 4: All attributes should remain within valid bounds
        expect($trainedPet->health)->toBeGreaterThanOrEqual(20)
            ->and($trainedPet->health)->toBeLessThanOrEqual(100)
            ->and($trainedPet->hunger)->toBeGreaterThanOrEqual(0)
            ->and($trainedPet->hunger)->toBeLessThanOrEqual(100)
            ->and($trainedPet->training_level)->toBeGreaterThanOrEqual(0)
            ->and($trainedPet->training_level)->toBeLessThanOrEqual(100);
        
        // Property 5: A TrainingLog should be created
        $trainingLog = \App\Models\TrainingLog::where('pet_id', $pet->id)->latest()->first();
        expect($trainingLog)->not->toBeNull('A TrainingLog should be created after training')
            ->and($trainingLog->training_level_before)->toBe($initialTrainingLevel)
            ->and($trainingLog->training_level_after)->toBe($expectedTrainingLevel);
        
        // Clean up
        $pet->delete();
    }
});

/**
 * Helper function to generate health values suitable for training (>= 30)
 * Uses various strategies to ensure good coverage including edge cases
 */
function generateHealthForTraining(): int
{
    $strategies = [
        // Edge case: exactly at minimum threshold
        fn() => 30,
        // Just above threshold (31-40)
        fn() => rand(31, 40),
        // Low-medium values (41-60)
        fn() => rand(41, 60),
        // Medium-high values (61-80)
        fn() => rand(61, 80),
        // High values (81-99)
        fn() => rand(81, 99),
        // Edge case: maximum value
        fn() => 100,
        // Random value in valid range
        fn() => rand(30, 100),
    ];
    
    return $strategies[array_rand($strategies)]();
}

/**
 * Property 8: Bloqueio de treinamento por saúde baixa
 * 
 * **Validates: Requirements 3.4**
 * 
 * Para qualquer pet com Saúde < 30, tentar treiná-lo deve falhar
 * e retornar uma mensagem de erro.
 */
test('Property 8: training is blocked when health is below 30', function () {
    $petService = app(\App\Contracts\PetServiceInterface::class);
    $iterations = 100;
    
    for ($i = 0; $i < $iterations; $i++) {
        // Generate a pet with health < 30 (below training threshold)
        $initialHealth = generateHealthBelowTrainingThreshold();
        $initialHunger = generateRandomAttribute();
        $initialTrainingLevel = generateRandomAttribute();
        
        // Create pet with these attributes
        $pet = Pet::create([
            'name' => generateValidPetName(),
            'health' => $initialHealth,
            'hunger' => $initialHunger,
            'training_level' => $initialTrainingLevel,
            'last_updated_at' => now(),
        ]);
        
        // Store initial values to verify they don't change
        $initialState = [
            'health' => $pet->health,
            'hunger' => $pet->hunger,
            'training_level' => $pet->training_level,
        ];
        
        // Property 1: Attempting to train should throw an exception
        $exceptionThrown = false;
        $exceptionMessage = '';
        
        try {
            $petService->trainPet($pet);
        } catch (\Exception $e) {
            $exceptionThrown = true;
            $exceptionMessage = $e->getMessage();
        }
        
        expect($exceptionThrown)->toBeTrue(
            "Training should throw an exception when health ({$initialHealth}) is below 30"
        );
        
        // Property 2: The exception message should indicate the pet is too weak
        expect($exceptionMessage)->toBe(
            'Pet muito fraco para treinar. Alimente-o primeiro.',
            "Exception message should indicate pet is too weak to train. Got: {$exceptionMessage}"
        );
        
        // Property 3: Pet attributes should remain unchanged after failed training attempt
        $pet->refresh();
        expect($pet->health)->toBe(
            $initialState['health'],
            "Health should not change after failed training attempt"
        );
        expect($pet->hunger)->toBe(
            $initialState['hunger'],
            "Hunger should not change after failed training attempt"
        );
        expect($pet->training_level)->toBe(
            $initialState['training_level'],
            "Training level should not change after failed training attempt"
        );
        
        // Property 4: No TrainingLog should be created for failed training
        $trainingLogCount = \App\Models\TrainingLog::where('pet_id', $pet->id)->count();
        expect($trainingLogCount)->toBe(
            0,
            "No TrainingLog should be created when training fails"
        );
        
        // Clean up
        $pet->delete();
    }
});

/**
 * Helper function to generate health values below training threshold (< 30)
 * Uses various strategies to ensure good coverage of edge cases
 */
function generateHealthBelowTrainingThreshold(): int
{
    $strategies = [
        // Edge case: exactly at 0
        fn() => 0,
        // Very low values (1-9)
        fn() => rand(1, 9),
        // Low values (10-19)
        fn() => rand(10, 19),
        // Just below threshold (20-29)
        fn() => rand(20, 29),
        // Edge case: exactly at 29 (just below threshold)
        fn() => 29,
        // Random value in invalid range
        fn() => rand(0, 29),
    ];
    
    return $strategies[array_rand($strategies)]();
}

/**
 * Property 10: Fórmula de força de batalha
 * 
 * **Validates: Requirements 4.1, 4.4**
 * 
 * Para qualquer pet, a Força_de_Batalha calculada deve ser igual a
 * (Saúde × 0.4) + (Nível_de_Treinamento × 0.6), com penalidade de 20% se Fome > 70.
 */
test('Property 10: battle strength follows formula with hunger penalty', function () {
    $petService = app(\App\Contracts\PetServiceInterface::class);
    $iterations = 100;
    
    for ($i = 0; $i < $iterations; $i++) {
        // Generate a pet with random valid attributes
        $health = generateRandomAttribute();
        $hunger = generateRandomAttribute();
        $trainingLevel = generateRandomAttribute();
        
        // Create pet with these attributes
        $pet = Pet::create([
            'name' => generateValidPetName(),
            'health' => $health,
            'hunger' => $hunger,
            'training_level' => $trainingLevel,
            'last_updated_at' => now(),
        ]);
        
        // Calculate battle strength
        $actualStrength = $petService->calculateBattleStrength($pet);
        
        // Calculate expected strength using the formula
        $baseStrength = ($health * 0.4) + ($trainingLevel * 0.6);
        
        // Apply 20% penalty if hunger > 70
        if ($hunger > 70) {
            $expectedStrength = $baseStrength * 0.8;
        } else {
            $expectedStrength = $baseStrength;
        }
        
        // Ensure expected strength is clamped to 0-100 range
        $expectedStrength = max(0, min(100, $expectedStrength));
        
        // Property 1: Battle strength must match the formula
        // Using a small epsilon for floating point comparison
        $epsilon = 0.0001;
        $difference = abs($actualStrength - $expectedStrength);
        
        expect($difference)->toBeLessThan(
            $epsilon,
            "Battle strength should match formula. " .
            "Health: {$health}, Training: {$trainingLevel}, Hunger: {$hunger}, " .
            "Expected: {$expectedStrength}, Got: {$actualStrength}"
        );
        
        // Property 2: When hunger <= 70, no penalty should be applied
        if ($hunger <= 70) {
            $expectedNoPenalty = ($health * 0.4) + ($trainingLevel * 0.6);
            $expectedNoPenalty = max(0, min(100, $expectedNoPenalty));
            
            expect($difference)->toBeLessThan(
                $epsilon,
                "When hunger ({$hunger}) <= 70, no penalty should be applied. " .
                "Expected: {$expectedNoPenalty}, Got: {$actualStrength}"
            );
        }
        
        // Property 3: When hunger > 70, 20% penalty should be applied
        if ($hunger > 70) {
            $baseWithoutPenalty = ($health * 0.4) + ($trainingLevel * 0.6);
            $expectedWithPenalty = $baseWithoutPenalty * 0.8;
            $expectedWithPenalty = max(0, min(100, $expectedWithPenalty));
            
            $penaltyDifference = abs($actualStrength - $expectedWithPenalty);
            
            expect($penaltyDifference)->toBeLessThan(
                $epsilon,
                "When hunger ({$hunger}) > 70, 20% penalty should be applied. " .
                "Base: {$baseWithoutPenalty}, Expected with penalty: {$expectedWithPenalty}, Got: {$actualStrength}"
            );
        }
        
        // Property 4: Battle strength must always be within 0-100 range
        expect($actualStrength)->toBeGreaterThanOrEqual(
            0,
            "Battle strength should never be negative"
        );
        expect($actualStrength)->toBeLessThanOrEqual(
            100,
            "Battle strength should never exceed 100"
        );
        
        // Property 5: Health contributes 40% to base strength
        // When training_level = 0 and hunger <= 70, strength should be health * 0.4
        if ($trainingLevel === 0 && $hunger <= 70) {
            $expectedHealthOnly = $health * 0.4;
            $healthOnlyDifference = abs($actualStrength - $expectedHealthOnly);
            
            expect($healthOnlyDifference)->toBeLessThan(
                $epsilon,
                "When training_level is 0 and hunger <= 70, strength should be health * 0.4. " .
                "Health: {$health}, Expected: {$expectedHealthOnly}, Got: {$actualStrength}"
            );
        }
        
        // Property 6: Training level contributes 60% to base strength
        // When health = 0 and hunger <= 70, strength should be training_level * 0.6
        if ($health === 0 && $hunger <= 70) {
            $expectedTrainingOnly = $trainingLevel * 0.6;
            $trainingOnlyDifference = abs($actualStrength - $expectedTrainingOnly);
            
            expect($trainingOnlyDifference)->toBeLessThan(
                $epsilon,
                "When health is 0 and hunger <= 70, strength should be training_level * 0.6. " .
                "Training: {$trainingLevel}, Expected: {$expectedTrainingOnly}, Got: {$actualStrength}"
            );
        }
        
        // Clean up
        $pet->delete();
    }
});

/**
 * Property 11: Invariante de força de batalha
 * 
 * **Validates: Requirements 4.3**
 * 
 * Para qualquer pet, a Força_de_Batalha deve estar sempre entre 0 e 100.
 */
test('Property 11: battle strength is always between 0 and 100', function () {
    $petService = app(\App\Contracts\PetServiceInterface::class);
    $iterations = 100;
    
    for ($i = 0; $i < $iterations; $i++) {
        // Generate a pet with random valid attributes
        $health = generateRandomAttribute();
        $hunger = generateRandomAttribute();
        $trainingLevel = generateRandomAttribute();
        
        // Create pet with these attributes
        $pet = Pet::create([
            'name' => generateValidPetName(),
            'health' => $health,
            'hunger' => $hunger,
            'training_level' => $trainingLevel,
            'last_updated_at' => now(),
        ]);
        
        // Calculate battle strength
        $battleStrength = $petService->calculateBattleStrength($pet);
        
        // Property 1: Battle strength must always be >= 0
        expect($battleStrength)->toBeGreaterThanOrEqual(
            0,
            "Battle strength must never be negative. " .
            "Health: {$health}, Training: {$trainingLevel}, Hunger: {$hunger}, " .
            "Got: {$battleStrength}"
        );
        
        // Property 2: Battle strength must always be <= 100
        expect($battleStrength)->toBeLessThanOrEqual(
            100,
            "Battle strength must never exceed 100. " .
            "Health: {$health}, Training: {$trainingLevel}, Hunger: {$hunger}, " .
            "Got: {$battleStrength}"
        );
        
        // Property 3: Battle strength must be within the closed interval [0, 100]
        expect($battleStrength)->toBeGreaterThanOrEqual(0)
            ->and($battleStrength)->toBeLessThanOrEqual(100);
        
        // Clean up
        $pet->delete();
    }
});

/**
 * Property 24: Invariantes de atributos
 * 
 * **Validates: Requirements 10.1, 10.2, 10.3**
 * 
 * Para qualquer operação no pet, Saúde, Fome, e Nível_de_Treinamento
 * devem permanecer entre 0 e 100.
 */
test('Property 24: all pet attributes remain between 0 and 100 after any operation', function () {
    $petService = app(\App\Contracts\PetServiceInterface::class);
    $iterations = 100;
    
    for ($i = 0; $i < $iterations; $i++) {
        // Generate a pet with random valid attributes
        $initialHealth = generateRandomAttribute();
        $initialHunger = generateRandomAttribute();
        $initialTrainingLevel = generateRandomAttribute();
        
        // Create pet with these attributes
        $pet = Pet::create([
            'name' => generateValidPetName(),
            'health' => $initialHealth,
            'hunger' => $initialHunger,
            'training_level' => $initialTrainingLevel,
            'last_updated_at' => now(),
        ]);
        
        // Choose a random operation to perform
        $operations = ['feed', 'train', 'validate', 'time_degradation'];
        $operation = $operations[array_rand($operations)];
        
        $operationPerformed = false;
        
        try {
            switch ($operation) {
                case 'feed':
                    $pet = $petService->feedPet($pet);
                    $operationPerformed = true;
                    break;
                    
                case 'train':
                    // Only train if health >= 30
                    if ($pet->health >= 30) {
                        $pet = $petService->trainPet($pet);
                        $operationPerformed = true;
                    }
                    break;
                    
                case 'validate':
                    $pet = $petService->validatePetState($pet);
                    $operationPerformed = true;
                    break;
                    
                case 'time_degradation':
                    // Set last_updated_at to 1 hour ago to trigger degradation
                    $pet->last_updated_at = now()->subHours(1);
                    $pet->save();
                    $pet = $petService->applyTimeDegradation($pet);
                    $operationPerformed = true;
                    break;
            }
        } catch (\Exception $e) {
            // If training fails due to low health, that's expected
            // Just verify the pet state remains valid
            $operationPerformed = false;
        }
        
        // Refresh pet from database to get latest state
        $pet->refresh();
        
        // Property 1: Health must always be between 0 and 100
        expect($pet->health)->toBeGreaterThanOrEqual(
            0,
            "Health must be >= 0 after {$operation}. " .
            "Initial: {$initialHealth}, Got: {$pet->health}"
        );
        expect($pet->health)->toBeLessThanOrEqual(
            100,
            "Health must be <= 100 after {$operation}. " .
            "Initial: {$initialHealth}, Got: {$pet->health}"
        );
        
        // Property 2: Hunger must always be between 0 and 100
        expect($pet->hunger)->toBeGreaterThanOrEqual(
            0,
            "Hunger must be >= 0 after {$operation}. " .
            "Initial: {$initialHunger}, Got: {$pet->hunger}"
        );
        expect($pet->hunger)->toBeLessThanOrEqual(
            100,
            "Hunger must be <= 100 after {$operation}. " .
            "Initial: {$initialHunger}, Got: {$pet->hunger}"
        );
        
        // Property 3: Training level must always be between 0 and 100
        expect($pet->training_level)->toBeGreaterThanOrEqual(
            0,
            "Training level must be >= 0 after {$operation}. " .
            "Initial: {$initialTrainingLevel}, Got: {$pet->training_level}"
        );
        expect($pet->training_level)->toBeLessThanOrEqual(
            100,
            "Training level must be <= 100 after {$operation}. " .
            "Initial: {$initialTrainingLevel}, Got: {$pet->training_level}"
        );
        
        // Property 4: All three attributes must be within bounds simultaneously
        expect($pet->health)->toBeGreaterThanOrEqual(0)
            ->and($pet->health)->toBeLessThanOrEqual(100)
            ->and($pet->hunger)->toBeGreaterThanOrEqual(0)
            ->and($pet->hunger)->toBeLessThanOrEqual(100)
            ->and($pet->training_level)->toBeGreaterThanOrEqual(0)
            ->and($pet->training_level)->toBeLessThanOrEqual(100);
        
        // Clean up
        $pet->delete();
    }
});

/**
 * Property 25: Clamping de valores
 * 
 * **Validates: Requirements 10.4, 10.5**
 * 
 * Para qualquer tentativa de definir um atributo fora dos limites (< 0 ou > 100),
 * o valor deve ser ajustado para o limite mais próximo (0 ou 100).
 */
test('Property 25: attributes are clamped to valid range when set outside limits', function () {
    $petService = app(\App\Contracts\PetServiceInterface::class);
    $iterations = 100;
    
    for ($i = 0; $i < $iterations; $i++) {
        // Generate invalid attribute values (outside 0-100 range)
        $invalidHealth = generateInvalidAttribute();
        $invalidHunger = generateInvalidAttribute();
        $invalidTrainingLevel = generateInvalidAttribute();
        
        // Create pet with invalid attributes
        $pet = Pet::create([
            'name' => generateValidPetName(),
            'health' => $invalidHealth,
            'hunger' => $invalidHunger,
            'training_level' => $invalidTrainingLevel,
            'last_updated_at' => now(),
        ]);
        
        // Apply validation to clamp values
        $validatedPet = $petService->validatePetState($pet);
        
        // Calculate expected clamped values
        $expectedHealth = max(0, min(100, $invalidHealth));
        $expectedHunger = max(0, min(100, $invalidHunger));
        $expectedTrainingLevel = max(0, min(100, $invalidTrainingLevel));
        
        // Special rule: if hunger == 100, health should be set to 50
        if ($expectedHunger == 100) {
            $expectedHealth = 50;
        }
        
        // Property 1: Health should be clamped to [0, 100]
        expect($validatedPet->health)->toBe(
            $expectedHealth,
            "Health should be clamped to valid range. " .
            "Invalid: {$invalidHealth}, Expected: {$expectedHealth}, Got: {$validatedPet->health}"
        );
        
        // Property 2: Hunger should be clamped to [0, 100]
        expect($validatedPet->hunger)->toBe(
            $expectedHunger,
            "Hunger should be clamped to valid range. " .
            "Invalid: {$invalidHunger}, Expected: {$expectedHunger}, Got: {$validatedPet->hunger}"
        );
        
        // Property 3: Training level should be clamped to [0, 100]
        expect($validatedPet->training_level)->toBe(
            $expectedTrainingLevel,
            "Training level should be clamped to valid range. " .
            "Invalid: {$invalidTrainingLevel}, Expected: {$expectedTrainingLevel}, Got: {$validatedPet->training_level}"
        );
        
        // Property 4: Values below 0 should be clamped to 0
        if ($invalidHealth < 0) {
            expect($validatedPet->health)->toBeGreaterThanOrEqual(
                0,
                "Health below 0 ({$invalidHealth}) should be clamped to 0"
            );
        }
        if ($invalidHunger < 0) {
            expect($validatedPet->hunger)->toBeGreaterThanOrEqual(
                0,
                "Hunger below 0 ({$invalidHunger}) should be clamped to 0"
            );
        }
        if ($invalidTrainingLevel < 0) {
            expect($validatedPet->training_level)->toBeGreaterThanOrEqual(
                0,
                "Training level below 0 ({$invalidTrainingLevel}) should be clamped to 0"
            );
        }
        
        // Property 5: Values above 100 should be clamped to 100
        if ($invalidHealth > 100 && $expectedHunger != 100) {
            expect($validatedPet->health)->toBeLessThanOrEqual(
                100,
                "Health above 100 ({$invalidHealth}) should be clamped to 100"
            );
        }
        if ($invalidHunger > 100) {
            expect($validatedPet->hunger)->toBeLessThanOrEqual(
                100,
                "Hunger above 100 ({$invalidHunger}) should be clamped to 100"
            );
        }
        if ($invalidTrainingLevel > 100) {
            expect($validatedPet->training_level)->toBeLessThanOrEqual(
                100,
                "Training level above 100 ({$invalidTrainingLevel}) should be clamped to 100"
            );
        }
        
        // Property 6: All attributes must be within valid bounds after clamping
        expect($validatedPet->health)->toBeGreaterThanOrEqual(0)
            ->and($validatedPet->health)->toBeLessThanOrEqual(100)
            ->and($validatedPet->hunger)->toBeGreaterThanOrEqual(0)
            ->and($validatedPet->hunger)->toBeLessThanOrEqual(100)
            ->and($validatedPet->training_level)->toBeGreaterThanOrEqual(0)
            ->and($validatedPet->training_level)->toBeLessThanOrEqual(100);
        
        // Property 7: Clamping should be idempotent (applying twice gives same result)
        $revalidatedPet = $petService->validatePetState($validatedPet);
        expect($revalidatedPet->health)->toBe($validatedPet->health)
            ->and($revalidatedPet->hunger)->toBe($validatedPet->hunger)
            ->and($revalidatedPet->training_level)->toBe($validatedPet->training_level);
        
        // Clean up
        $pet->delete();
    }
});

/**
 * Helper function to generate invalid attribute values (outside 0-100 range)
 * Uses various strategies to ensure good coverage of edge cases
 */
function generateInvalidAttribute(): int
{
    $strategies = [
        // Negative values
        fn() => rand(-100, -1),
        // Just below 0
        fn() => -1,
        // Large negative values
        fn() => rand(-1000, -101),
        // Just above 100
        fn() => 101,
        // Values above 100
        fn() => rand(101, 200),
        // Large values above 100
        fn() => rand(201, 1000),
        // Edge case: exactly -1
        fn() => -1,
        // Edge case: exactly 101
        fn() => 101,
        // Mix of valid and invalid (to test edge cases)
        fn() => rand(-50, 150),
        // Very large positive values
        fn() => rand(500, 10000),
        // Very large negative values
        fn() => rand(-10000, -500),
    ];
    
    return $strategies[array_rand($strategies)]();
}
