<?php

use App\Contracts\PetServiceInterface;
use App\Models\Pet;
use App\Services\PetService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->petService = app(PetServiceInterface::class);
});

describe('createPet', function () {
    test('creates a pet with default values', function () {
        $pet = $this->petService->createPet('TestPet');

        expect($pet)->toBeInstanceOf(Pet::class)
            ->and($pet->name)->toBe('TestPet')
            ->and($pet->health)->toBe(100)
            ->and($pet->hunger)->toBe(0)
            ->and($pet->training_level)->toBe(0)
            ->and($pet->last_updated_at)->not->toBeNull();
    });

    test('persists the pet to the database', function () {
        $pet = $this->petService->createPet('TestPet');

        $this->assertDatabaseHas('pets', [
            'id' => $pet->id,
            'name' => 'TestPet',
            'health' => 100,
            'hunger' => 0,
            'training_level' => 0,
        ]);
    });

    test('sets last_updated_at to current timestamp', function () {
        $beforeCreate = now();
        $pet = $this->petService->createPet('TestPet');
        $afterCreate = now();

        expect($pet->last_updated_at->timestamp)
            ->toBeGreaterThanOrEqual($beforeCreate->timestamp)
            ->toBeLessThanOrEqual($afterCreate->timestamp);
    });
});

describe('feedPet', function () {
    test('reduces hunger by 20 points', function () {
        $pet = Pet::factory()->create(['hunger' => 50]);
        
        $updatedPet = $this->petService->feedPet($pet);
        
        expect($updatedPet->hunger)->toBe(30);
    });

    test('increases health by 10 points', function () {
        $pet = Pet::factory()->create(['health' => 50]);
        
        $updatedPet = $this->petService->feedPet($pet);
        
        expect($updatedPet->health)->toBe(60);
    });

    test('does not reduce hunger below 0', function () {
        $pet = Pet::factory()->create(['hunger' => 10]);
        
        $updatedPet = $this->petService->feedPet($pet);
        
        expect($updatedPet->hunger)->toBe(0);
    });

    test('does not increase health above 100', function () {
        $pet = Pet::factory()->create(['health' => 95]);
        
        $updatedPet = $this->petService->feedPet($pet);
        
        expect($updatedPet->health)->toBe(100);
    });

    test('updates last_updated_at timestamp', function () {
        $pet = Pet::factory()->create(['last_updated_at' => now()->subHour()]);
        $beforeFeed = now();
        
        $updatedPet = $this->petService->feedPet($pet);
        $afterFeed = now();
        
        expect($updatedPet->last_updated_at->timestamp)
            ->toBeGreaterThanOrEqual($beforeFeed->timestamp)
            ->toBeLessThanOrEqual($afterFeed->timestamp);
    });

    test('persists changes to the database', function () {
        $pet = Pet::factory()->create(['hunger' => 50, 'health' => 50]);
        
        $this->petService->feedPet($pet);
        
        $this->assertDatabaseHas('pets', [
            'id' => $pet->id,
            'hunger' => 30,
            'health' => 60,
        ]);
    });
});

describe('trainPet', function () {
    test('increases training_level by 10 points', function () {
        $pet = Pet::factory()->create(['health' => 50, 'training_level' => 30]);
        
        $updatedPet = $this->petService->trainPet($pet);
        
        expect($updatedPet->training_level)->toBe(40);
    });

    test('increases hunger by 15 points', function () {
        $pet = Pet::factory()->create(['health' => 50, 'hunger' => 20]);
        
        $updatedPet = $this->petService->trainPet($pet);
        
        expect($updatedPet->hunger)->toBe(35);
    });

    test('reduces health by 5 points', function () {
        $pet = Pet::factory()->create(['health' => 50]);
        
        $updatedPet = $this->petService->trainPet($pet);
        
        expect($updatedPet->health)->toBe(45);
    });

    test('does not increase training_level above 100', function () {
        $pet = Pet::factory()->create(['health' => 50, 'training_level' => 95]);
        
        $updatedPet = $this->petService->trainPet($pet);
        
        expect($updatedPet->training_level)->toBe(100);
    });

    test('does not increase hunger above 100', function () {
        $pet = Pet::factory()->create(['health' => 50, 'hunger' => 90]);
        
        $updatedPet = $this->petService->trainPet($pet);
        
        expect($updatedPet->hunger)->toBe(100);
    });

    test('reduces health by 5 but not below 20 minimum', function () {
        // With health at 30 (minimum to train), after reducing by 5, we get 25
        $pet = Pet::factory()->create(['health' => 30]);
        
        $updatedPet = $this->petService->trainPet($pet);
        
        // Should be 25, which is above the minimum of 20
        expect($updatedPet->health)->toBe(25);
    });

    test('throws exception when health is below 30', function () {
        $pet = Pet::factory()->create(['health' => 25]);
        
        expect(fn() => $this->petService->trainPet($pet))
            ->toThrow(\Exception::class, 'Pet muito fraco para treinar. Alimente-o primeiro.');
    });

    test('updates last_updated_at timestamp', function () {
        $pet = Pet::factory()->create(['health' => 50, 'last_updated_at' => now()->subHour()]);
        $beforeTrain = now();
        
        $updatedPet = $this->petService->trainPet($pet);
        $afterTrain = now();
        
        expect($updatedPet->last_updated_at->timestamp)
            ->toBeGreaterThanOrEqual($beforeTrain->timestamp)
            ->toBeLessThanOrEqual($afterTrain->timestamp);
    });

    test('creates a TrainingLog record', function () {
        $pet = Pet::factory()->create(['health' => 50, 'training_level' => 30]);
        
        $updatedPet = $this->petService->trainPet($pet);
        
        $this->assertDatabaseHas('training_logs', [
            'pet_id' => $pet->id,
            'training_level_before' => 30,
            'training_level_after' => 40,
        ]);
    });

    test('persists changes to the database', function () {
        $pet = Pet::factory()->create(['health' => 50, 'hunger' => 20, 'training_level' => 30]);
        
        $this->petService->trainPet($pet);
        
        $this->assertDatabaseHas('pets', [
            'id' => $pet->id,
            'health' => 45,
            'hunger' => 35,
            'training_level' => 40,
        ]);
    });

    test('does not create TrainingLog when validation fails', function () {
        $pet = Pet::factory()->create(['health' => 25]);
        
        try {
            $this->petService->trainPet($pet);
        } catch (\Exception $e) {
            // Expected exception
        }
        
        $this->assertDatabaseMissing('training_logs', [
            'pet_id' => $pet->id,
        ]);
    });
});

describe('calculateBattleStrength', function () {
    test('calculates battle strength using correct formula', function () {
        $pet = Pet::factory()->create(['health' => 80, 'training_level' => 60, 'hunger' => 50]);
        
        $strength = $this->petService->calculateBattleStrength($pet);
        
        // Formula: (80 × 0.4) + (60 × 0.6) = 32 + 36 = 68
        expect($strength)->toBe(68.0);
    });

    test('applies 20% penalty when hunger is above 70', function () {
        $pet = Pet::factory()->create(['health' => 80, 'training_level' => 60, 'hunger' => 75]);
        
        $strength = $this->petService->calculateBattleStrength($pet);
        
        // Base: (80 × 0.4) + (60 × 0.6) = 68
        // With penalty: 68 × 0.8 = 54.4
        expect(abs($strength - 54.4))->toBeLessThan(0.01);
    });

    test('does not apply penalty when hunger is exactly 70', function () {
        $pet = Pet::factory()->create(['health' => 80, 'training_level' => 60, 'hunger' => 70]);
        
        $strength = $this->petService->calculateBattleStrength($pet);
        
        // No penalty at exactly 70
        expect($strength)->toBe(68.0);
    });

    test('ensures result is not below 0', function () {
        $pet = Pet::factory()->create(['health' => 0, 'training_level' => 0, 'hunger' => 0]);
        
        $strength = $this->petService->calculateBattleStrength($pet);
        
        expect($strength)->toBe(0.0);
    });

    test('ensures result is not above 100', function () {
        $pet = Pet::factory()->create(['health' => 100, 'training_level' => 100, 'hunger' => 0]);
        
        $strength = $this->petService->calculateBattleStrength($pet);
        
        // Formula: (100 × 0.4) + (100 × 0.6) = 40 + 60 = 100
        expect($strength)->toBe(100.0);
    });

    test('matches Pet model computed property', function () {
        $pet = Pet::factory()->create(['health' => 75, 'training_level' => 50, 'hunger' => 80]);
        
        $serviceStrength = $this->petService->calculateBattleStrength($pet);
        $modelStrength = $pet->battle_strength;
        
        expect($serviceStrength)->toBe($modelStrength);
    });
});

describe('applyTimeDegradation', function () {
    test('increases hunger by 5 points per 30-minute interval', function () {
        $pet = Pet::factory()->create([
            'hunger' => 20,
            'health' => 100,
            'last_updated_at' => now()->subMinutes(60) // 2 intervals of 30 minutes
        ]);
        
        $updatedPet = $this->petService->applyTimeDegradation($pet);
        
        // 20 + (2 intervals × 5 points) = 30
        expect($updatedPet->hunger)->toBe(30);
    });

    test('reduces health by 2 points per interval when hunger > 50', function () {
        $pet = Pet::factory()->create([
            'hunger' => 60,
            'health' => 100,
            'last_updated_at' => now()->subMinutes(60) // 2 intervals
        ]);
        
        $updatedPet = $this->petService->applyTimeDegradation($pet);
        
        // Hunger: 60 + (2 × 5) = 70
        // Health: 100 - (2 × 2) = 96 (because hunger > 50)
        expect($updatedPet->hunger)->toBe(70)
            ->and($updatedPet->health)->toBe(96);
    });

    test('reduces health by 5 points per hour when hunger > 80', function () {
        $pet = Pet::factory()->create([
            'hunger' => 85,
            'health' => 100,
            'last_updated_at' => now()->subHours(2) // 2 hours
        ]);
        
        $updatedPet = $this->petService->applyTimeDegradation($pet);
        
        // Hunger: 85 + (4 intervals × 5) = 105 → capped at 100
        // Health degradation from hunger > 50: 100 - (4 intervals × 2) = 92
        // Additional degradation from hunger > 80: 92 - (2 hours × 5) = 82
        expect($updatedPet->hunger)->toBe(100)
            ->and($updatedPet->health)->toBe(82);
    });

    test('does not reduce health when hunger is 50 or below', function () {
        $pet = Pet::factory()->create([
            'hunger' => 30,
            'health' => 100,
            'last_updated_at' => now()->subMinutes(60) // 2 intervals
        ]);
        
        $updatedPet = $this->petService->applyTimeDegradation($pet);
        
        // Hunger: 30 + (2 × 5) = 40
        // Health: 100 (no reduction because hunger <= 50)
        expect($updatedPet->hunger)->toBe(40)
            ->and($updatedPet->health)->toBe(100);
    });

    test('applies 8-hour cap to degradation', function () {
        $pet = Pet::factory()->create([
            'hunger' => 0,
            'health' => 100,
            'last_updated_at' => now()->subHours(24) // 24 hours, but capped at 8
        ]);
        
        $updatedPet = $this->petService->applyTimeDegradation($pet);
        
        // 8 hours = 16 intervals of 30 minutes
        // Hunger: 0 + (16 × 5) = 80
        // Health: 100 - (16 × 2) = 68 (because hunger > 50 after some intervals)
        expect($updatedPet->hunger)->toBe(80);
    });

    test('does not increase hunger above 100', function () {
        $pet = Pet::factory()->create([
            'hunger' => 90,
            'health' => 100,
            'last_updated_at' => now()->subHours(4)
        ]);
        
        $updatedPet = $this->petService->applyTimeDegradation($pet);
        
        // Hunger would be 90 + (8 × 5) = 130, but capped at 100
        expect($updatedPet->hunger)->toBe(100);
    });

    test('does not reduce health below 0', function () {
        $pet = Pet::factory()->create([
            'hunger' => 85,
            'health' => 10,
            'last_updated_at' => now()->subHours(8)
        ]);
        
        $updatedPet = $this->petService->applyTimeDegradation($pet);
        
        // Health would go negative, but capped at 0
        expect($updatedPet->health)->toBeGreaterThanOrEqual(0);
    });

    test('updates last_updated_at timestamp', function () {
        $pet = Pet::factory()->create([
            'last_updated_at' => now()->subHours(2)
        ]);
        $beforeDegradation = now();
        
        $updatedPet = $this->petService->applyTimeDegradation($pet);
        $afterDegradation = now();
        
        expect($updatedPet->last_updated_at->timestamp)
            ->toBeGreaterThanOrEqual($beforeDegradation->timestamp)
            ->toBeLessThanOrEqual($afterDegradation->timestamp);
    });

    test('persists changes to the database', function () {
        $pet = Pet::factory()->create([
            'hunger' => 20,
            'health' => 100,
            'last_updated_at' => now()->subMinutes(60)
        ]);
        
        $this->petService->applyTimeDegradation($pet);
        
        $this->assertDatabaseHas('pets', [
            'id' => $pet->id,
            'hunger' => 30,
        ]);
    });

    test('handles zero elapsed time', function () {
        $pet = Pet::factory()->create([
            'hunger' => 20,
            'health' => 100,
            'last_updated_at' => now()
        ]);
        
        $updatedPet = $this->petService->applyTimeDegradation($pet);
        
        // No time elapsed, no changes
        expect($updatedPet->hunger)->toBe(20)
            ->and($updatedPet->health)->toBe(100);
    });
});

describe('validatePetState', function () {
    test('clamps health to maximum of 100', function () {
        $pet = Pet::factory()->make(['health' => 150]);
        
        $validatedPet = $this->petService->validatePetState($pet);
        
        expect($validatedPet->health)->toBe(100);
    });

    test('clamps health to minimum of 0', function () {
        $pet = Pet::factory()->make(['health' => -20]);
        
        $validatedPet = $this->petService->validatePetState($pet);
        
        expect($validatedPet->health)->toBe(0);
    });

    test('clamps hunger to maximum of 100', function () {
        $pet = Pet::factory()->make(['hunger' => 120]);
        
        $validatedPet = $this->petService->validatePetState($pet);
        
        expect($validatedPet->hunger)->toBe(100);
    });

    test('clamps hunger to minimum of 0', function () {
        $pet = Pet::factory()->make(['hunger' => -10]);
        
        $validatedPet = $this->petService->validatePetState($pet);
        
        expect($validatedPet->hunger)->toBe(0);
    });

    test('clamps training_level to maximum of 100', function () {
        $pet = Pet::factory()->make(['training_level' => 200]);
        
        $validatedPet = $this->petService->validatePetState($pet);
        
        expect($validatedPet->training_level)->toBe(100);
    });

    test('clamps training_level to minimum of 0', function () {
        $pet = Pet::factory()->make(['training_level' => -50]);
        
        $validatedPet = $this->petService->validatePetState($pet);
        
        expect($validatedPet->training_level)->toBe(0);
    });

    test('sets health to 50 when hunger is exactly 100', function () {
        $pet = Pet::factory()->make(['hunger' => 100, 'health' => 80]);
        
        $validatedPet = $this->petService->validatePetState($pet);
        
        expect($validatedPet->health)->toBe(50)
            ->and($validatedPet->hunger)->toBe(100);
    });

    test('does not change health when hunger is 99', function () {
        $pet = Pet::factory()->make(['hunger' => 99, 'health' => 80]);
        
        $validatedPet = $this->petService->validatePetState($pet);
        
        expect($validatedPet->health)->toBe(80)
            ->and($validatedPet->hunger)->toBe(99);
    });

    test('applies special rule after clamping hunger to 100', function () {
        $pet = Pet::factory()->make(['hunger' => 150, 'health' => 80]);
        
        $validatedPet = $this->petService->validatePetState($pet);
        
        // Hunger clamped to 100, then special rule applies
        expect($validatedPet->hunger)->toBe(100)
            ->and($validatedPet->health)->toBe(50);
    });

    test('leaves valid attributes unchanged', function () {
        $pet = Pet::factory()->make(['health' => 75, 'hunger' => 50, 'training_level' => 60]);
        
        $validatedPet = $this->petService->validatePetState($pet);
        
        expect($validatedPet->health)->toBe(75)
            ->and($validatedPet->hunger)->toBe(50)
            ->and($validatedPet->training_level)->toBe(60);
    });

    test('handles multiple out-of-bounds attributes simultaneously', function () {
        $pet = Pet::factory()->make(['health' => 150, 'hunger' => -10, 'training_level' => 200]);
        
        $validatedPet = $this->petService->validatePetState($pet);
        
        expect($validatedPet->health)->toBe(100)
            ->and($validatedPet->hunger)->toBe(0)
            ->and($validatedPet->training_level)->toBe(100);
    });
});
