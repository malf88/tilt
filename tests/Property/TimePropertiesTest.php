<?php

use App\Services\TimeService;

/**
 * Property 18: Limite de degradação
 * 
 * **Validates: Requirements 6.4**
 * 
 * Para qualquer período de ausência, a degradação aplicada não deve exceder
 * o equivalente a 8 horas de simulação.
 */
test('Property 18: time degradation is capped at 8 hours maximum', function () {
    $timeService = new TimeService();
    $iterations = 100;
    
    for ($i = 0; $i < $iterations; $i++) {
        // Generate random test parameters
        $intervalMinutes = generateRandomIntervalMinutes();
        $maxHours = 8; // As per requirement 6.4
        $elapsedIntervals = generateRandomElapsedIntervals();
        
        // Apply degradation cap
        $cappedIntervals = $timeService->applyDegradationCap(
            $elapsedIntervals,
            $maxHours,
            $intervalMinutes
        );
        
        // Calculate the maximum allowed intervals for 8 hours
        $maxAllowedIntervals = ($maxHours * 60) / $intervalMinutes;
        
        // Property: The capped intervals must never exceed the 8-hour equivalent
        expect($cappedIntervals)->toBeLessThanOrEqual(
            $maxAllowedIntervals,
            "Capped intervals ({$cappedIntervals}) should not exceed max allowed ({$maxAllowedIntervals}) " .
            "for {$intervalMinutes}-minute intervals over {$maxHours} hours"
        );
        
        // Property: If elapsed intervals are below the cap, they should remain unchanged
        if ($elapsedIntervals <= $maxAllowedIntervals) {
            expect($cappedIntervals)->toBe(
                $elapsedIntervals,
                "When elapsed intervals ({$elapsedIntervals}) are below cap ({$maxAllowedIntervals}), " .
                "they should remain unchanged"
            );
        }
        
        // Property: If elapsed intervals exceed the cap, result should equal the cap
        if ($elapsedIntervals > $maxAllowedIntervals) {
            expect($cappedIntervals)->toBe(
                (int) $maxAllowedIntervals,
                "When elapsed intervals ({$elapsedIntervals}) exceed cap ({$maxAllowedIntervals}), " .
                "result should equal the cap"
            );
        }
        
        // Property: Capped intervals must always be non-negative
        expect($cappedIntervals)->toBeGreaterThanOrEqual(
            0,
            "Capped intervals should never be negative"
        );
    }
});

/**
 * Helper function to generate random interval durations in minutes
 * Common intervals: 15, 30, 60 minutes
 */
function generateRandomIntervalMinutes(): int
{
    $commonIntervals = [15, 30, 60];
    $randomIntervals = range(5, 120, 5); // 5, 10, 15, ..., 120
    
    // 70% chance of common interval, 30% chance of random
    if (rand(1, 100) <= 70) {
        return $commonIntervals[array_rand($commonIntervals)];
    }
    
    return $randomIntervals[array_rand($randomIntervals)];
}

/**
 * Helper function to generate random elapsed intervals
 * Generates a wide range including values below, at, and above typical caps
 */
function generateRandomElapsedIntervals(): int
{
    $strategies = [
        // Small values (0-10 intervals)
        fn() => rand(0, 10),
        // Medium values (10-50 intervals)
        fn() => rand(10, 50),
        // Large values that would exceed 8 hours (50-200 intervals)
        fn() => rand(50, 200),
        // Very large values (simulating days of absence)
        fn() => rand(200, 1000),
        // Edge case: exactly at common cap boundaries
        fn() => [16, 32, 48, 96][array_rand([16, 32, 48, 96])],
    ];
    
    return $strategies[array_rand($strategies)]();
}

/**
 * Property 16: Degradação temporal
 * 
 * **Validates: Requirements 6.1, 6.2**
 * 
 * Para qualquer pet, simular 30 minutos de tempo deve aumentar Fome em 5 pontos;
 * se Fome > 50, também deve reduzir Saúde em 2 pontos.
 */
test('Property 16: time degradation increases hunger and reduces health appropriately', function () {
    $petService = app(\App\Contracts\PetServiceInterface::class);
    $iterations = 100;
    
    for ($i = 0; $i < $iterations; $i++) {
        // Generate a pet with random valid attributes
        $initialHealth = generateRandomAttribute();
        $initialHunger = generateRandomAttribute();
        $initialTrainingLevel = generateRandomAttribute();
        
        // Create pet with these attributes
        $pet = \App\Models\Pet::create([
            'name' => generateValidPetName(),
            'health' => $initialHealth,
            'hunger' => $initialHunger,
            'training_level' => $initialTrainingLevel,
            'last_updated_at' => now()->subMinutes(30), // Simulate 30 minutes elapsed
        ]);
        
        // Apply time degradation
        $degradedPet = $petService->applyTimeDegradation($pet);
        
        // Calculate expected values
        // Property 1: Hunger should increase by 5 points per 30-minute interval (1 interval = 5 points)
        $expectedHunger = min(100, $initialHunger + 5);
        
        // Property 2: If initial hunger > 50, health should decrease by 2 points per interval
        $expectedHealth = $initialHealth;
        if ($initialHunger > 50) {
            $expectedHealth = max(0, $initialHealth - 2);
        }
        
        // Additional degradation if hunger > 80 (5 points per hour)
        // For 30 minutes, there are 0 complete hourly intervals, so no additional degradation
        // This only applies when elapsed time >= 60 minutes
        
        // Verify Property 1: Hunger increases by 5 points per 30-minute interval
        expect($degradedPet->hunger)->toBe(
            $expectedHunger,
            "Hunger should increase by 5 points per 30-minute interval. " .
            "Initial: {$initialHunger}, Expected: {$expectedHunger}, Got: {$degradedPet->hunger}"
        );
        
        // Verify Property 2: Health degradation depends on hunger level
        expect($degradedPet->health)->toBe(
            $expectedHealth,
            "Health degradation should depend on hunger level. " .
            "Initial hunger: {$initialHunger}, Initial health: {$initialHealth}, " .
            "Expected health: {$expectedHealth}, Got: {$degradedPet->health}"
        );
        
        // Property 3: When hunger <= 50, health should not decrease from the 30-min interval degradation
        if ($initialHunger <= 50 && $initialHunger <= 80) {
            expect($degradedPet->health)->toBe(
                $initialHealth,
                "When hunger ({$initialHunger}) <= 50 and <= 80, health should not decrease. " .
                "Initial: {$initialHealth}, Got: {$degradedPet->health}"
            );
        }
        
        // Property 4: When hunger > 50, health should decrease by 2 points per interval
        if ($initialHunger > 50 && $initialHunger <= 80) {
            $expectedHealthDecrease = max(0, $initialHealth - 2);
            expect($degradedPet->health)->toBe(
                $expectedHealthDecrease,
                "When hunger ({$initialHunger}) > 50 and <= 80, health should decrease by 2 points. " .
                "Initial: {$initialHealth}, Expected: {$expectedHealthDecrease}, Got: {$degradedPet->health}"
            );
        }
        
        // Property 5: All attributes should remain within valid bounds (0-100)
        expect($degradedPet->health)->toBeGreaterThanOrEqual(0)
            ->and($degradedPet->health)->toBeLessThanOrEqual(100)
            ->and($degradedPet->hunger)->toBeGreaterThanOrEqual(0)
            ->and($degradedPet->hunger)->toBeLessThanOrEqual(100);
        
        // Property 6: Training level should remain unchanged
        expect($degradedPet->training_level)->toBe(
            $initialTrainingLevel,
            "Training level should not change during time degradation"
        );
        
        // Clean up
        $pet->delete();
    }
});

