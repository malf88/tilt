<?php

use App\Models\Pet;
use App\Services\PetService;

/**
 * Property 20: Persistência automática
 * 
 * **Validates: Requirements 8.1**
 * 
 * Para qualquer mudança em atributos do pet (saúde, fome, training_level),
 * os dados devem ser salvos no banco de dados.
 */
test('Property 20: pet attributes are automatically persisted to database after changes', function () {
    $petService = app(PetService::class);
    $iterations = 100;
    
    for ($i = 0; $i < $iterations; $i++) {
        // Create a pet with random initial attributes
        $initialHealth = rand(30, 100);
        $initialHunger = rand(0, 100);
        $initialTrainingLevel = rand(0, 100);
        
        $pet = Pet::create([
            'name' => 'PersistPet' . $i . uniqid(),
            'health' => $initialHealth,
            'hunger' => $initialHunger,
            'training_level' => $initialTrainingLevel,
            'last_updated_at' => now(),
        ]);
        
        $petId = $pet->id;
        
        // Choose a random operation that modifies pet attributes
        $operations = ['feed', 'train'];
        $operation = $operations[array_rand($operations)];
        
        // Perform the operation
        switch ($operation) {
            case 'feed':
                $modifiedPet = $petService->feedPet($pet);
                break;
                
            case 'train':
                // Only train if health >= 30
                if ($pet->health >= 30) {
                    $modifiedPet = $petService->trainPet($pet);
                } else {
                    // Skip this iteration if can't train
                    $pet->delete();
                    continue 2;
                }
                break;
        }
        
        // Property 1: The pet should be automatically saved to database
        // Verify by loading a fresh instance from database
        $freshPet = Pet::find($petId);
        
        expect($freshPet)->not->toBeNull(
            "Pet should exist in database after {$operation} operation"
        );
        
        // Property 2: The fresh instance should have the same attributes as modified pet
        expect($freshPet->health)->toBe(
            $modifiedPet->health,
            "Health should be persisted. Modified: {$modifiedPet->health}, Fresh: {$freshPet->health}"
        );
        
        expect($freshPet->hunger)->toBe(
            $modifiedPet->hunger,
            "Hunger should be persisted. Modified: {$modifiedPet->hunger}, Fresh: {$freshPet->hunger}"
        );
        
        expect($freshPet->training_level)->toBe(
            $modifiedPet->training_level,
            "Training level should be persisted. Modified: {$modifiedPet->training_level}, Fresh: {$freshPet->training_level}"
        );
        
        // Property 3: Attributes should have changed from initial values
        $attributesChanged = (
            $freshPet->health !== $initialHealth ||
            $freshPet->hunger !== $initialHunger ||
            $freshPet->training_level !== $initialTrainingLevel
        );
        
        expect($attributesChanged)->toBeTrue(
            "At least one attribute should have changed after {$operation} operation"
        );
        
        // Property 4: The persisted data should match exactly what was in memory
        expect($freshPet->toArray())->toMatchArray([
            'id' => $modifiedPet->id,
            'name' => $modifiedPet->name,
            'health' => $modifiedPet->health,
            'hunger' => $modifiedPet->hunger,
            'training_level' => $modifiedPet->training_level,
        ]);
        
        // Clean up
        $pet->delete();
    }
});

/**
 * Property 20 (Extended): Multiple operations persist correctly
 * 
 * **Validates: Requirements 8.1**
 * 
 * Verifies that multiple sequential operations all persist correctly.
 */
test('Property 20 Extended: multiple sequential operations persist correctly', function () {
    $petService = app(PetService::class);
    $iterations = 50;
    
    for ($i = 0; $i < $iterations; $i++) {
        // Create a pet with good starting attributes
        $pet = Pet::create([
            'name' => 'MultiOpPet' . $i . uniqid(),
            'health' => 80,
            'hunger' => 30,
            'training_level' => 40,
            'last_updated_at' => now(),
        ]);
        
        $petId = $pet->id;
        
        // Perform multiple operations
        $pet = $petService->feedPet($pet);
        $freshAfterFeed = Pet::find($petId);
        
        expect($freshAfterFeed->health)->toBe($pet->health)
            ->and($freshAfterFeed->hunger)->toBe($pet->hunger);
        
        // Train if possible
        if ($pet->health >= 30) {
            $pet = $petService->trainPet($pet);
            $freshAfterTrain = Pet::find($petId);
            
            expect($freshAfterTrain->training_level)->toBe($pet->training_level)
                ->and($freshAfterTrain->health)->toBe($pet->health)
                ->and($freshAfterTrain->hunger)->toBe($pet->hunger);
        }
        
        // Feed again
        $pet = $petService->feedPet($pet);
        $freshAfterSecondFeed = Pet::find($petId);
        
        expect($freshAfterSecondFeed->health)->toBe($pet->health)
            ->and($freshAfterSecondFeed->hunger)->toBe($pet->hunger)
            ->and($freshAfterSecondFeed->training_level)->toBe($pet->training_level);
        
        // Property: All changes should be persisted
        $finalFresh = Pet::find($petId);
        expect($finalFresh->toArray())->toMatchArray([
            'id' => $pet->id,
            'name' => $pet->name,
            'health' => $pet->health,
            'hunger' => $pet->hunger,
            'training_level' => $pet->training_level,
        ]);
        
        // Clean up
        $pet->delete();
    }
});

/**
 * Property 21: Timestamp de atualização
 * 
 * **Validates: Requirements 8.5**
 * 
 * Para qualquer salvamento de pet, o campo last_updated_at deve ser
 * atualizado com o timestamp atual.
 */
test('Property 21: last_updated_at is updated on every save', function () {
    $petService = app(PetService::class);
    $iterations = 100;
    
    for ($i = 0; $i < $iterations; $i++) {
        // Create a pet - observer will set last_updated_at to now()
        $pet = Pet::create([
            'name' => 'TimestampPet' . $i . uniqid(),
            'health' => rand(30, 100),
            'hunger' => rand(0, 100),
            'training_level' => rand(0, 100),
            'last_updated_at' => now(),
        ]);
        
        // Manually set to past time and save without triggering observer
        $pastTime = now()->subHours(rand(1, 24));
        \DB::table('pets')->where('id', $pet->id)->update(['last_updated_at' => $pastTime]);
        $pet->refresh();
        
        // Store the initial timestamp
        $initialTimestamp = $pet->last_updated_at;
        
        // Wait a tiny bit to ensure time difference
        usleep(1000); // 1ms
        
        // Choose a random operation
        $operations = ['feed', 'train', 'validate'];
        $operation = $operations[array_rand($operations)];
        
        // Perform the operation
        switch ($operation) {
            case 'feed':
                $pet = $petService->feedPet($pet);
                break;
                
            case 'train':
                if ($pet->health >= 30) {
                    $pet = $petService->trainPet($pet);
                } else {
                    // Just update an attribute manually
                    $pet->health = 50;
                    $pet->save();
                }
                break;
                
            case 'validate':
                $pet = $petService->validatePetState($pet);
                break;
        }
        
        // Refresh from database
        $pet->refresh();
        
        // Property 1: last_updated_at should be updated
        expect($pet->last_updated_at->isAfter($initialTimestamp))->toBeTrue(
            "last_updated_at should be updated after {$operation}. " .
            "Initial: {$initialTimestamp}, Current: {$pet->last_updated_at}"
        );
        
        // Property 2: last_updated_at should be recent (within last few seconds)
        $secondsAgo = now()->diffInSeconds($pet->last_updated_at);
        expect($secondsAgo)->toBeLessThan(
            5,
            "last_updated_at should be very recent (within 5 seconds). Was {$secondsAgo} seconds ago"
        );
        
        // Property 3: last_updated_at should not be in the future
        expect($pet->last_updated_at->isFuture())->toBeFalse(
            "last_updated_at should not be in the future"
        );
        
        // Clean up
        $pet->delete();
    }
});

/**
 * Property 2: Persistência round-trip
 * 
 * **Validates: Requirements 1.3, 8.4**
 * 
 * Para qualquer pet com atributos válidos, salvar o pet e depois carregá-lo
 * deve resultar em um pet com atributos idênticos.
 */
test('Property 2: pet attributes survive round-trip save and load', function () {
    $iterations = 100;
    
    for ($i = 0; $i < $iterations; $i++) {
        // Generate random valid attributes
        $originalName = 'RoundTripPet' . $i . uniqid();
        $originalHealth = rand(0, 100);
        $originalHunger = rand(0, 100);
        $originalTrainingLevel = rand(0, 100);
        $originalTimestamp = now()->subMinutes(rand(0, 1440)); // Random time in last 24h
        
        // Create and save pet - observer will set last_updated_at to now()
        $pet = Pet::create([
            'name' => $originalName,
            'health' => $originalHealth,
            'hunger' => $originalHunger,
            'training_level' => $originalTrainingLevel,
            'last_updated_at' => now(),
        ]);
        
        $petId = $pet->id;
        
        // Note: Observer updates last_updated_at, so we can't test exact timestamp preservation
        // Instead, we verify that the timestamp is reasonable (recent)
        $createdTimestamp = $pet->last_updated_at;
        
        // Clear the model from memory
        unset($pet);
        
        // Load pet from database
        $loadedPet = Pet::find($petId);
        
        // Property 1: Pet should be successfully loaded
        expect($loadedPet)->not->toBeNull(
            "Pet should be loadable from database"
        );
        
        // Property 2: All attributes should match exactly
        expect($loadedPet->name)->toBe(
            $originalName,
            "Name should match after round-trip"
        );
        
        expect($loadedPet->health)->toBe(
            $originalHealth,
            "Health should match after round-trip. Original: {$originalHealth}, Loaded: {$loadedPet->health}"
        );
        
        expect($loadedPet->hunger)->toBe(
            $originalHunger,
            "Hunger should match after round-trip. Original: {$originalHunger}, Loaded: {$loadedPet->hunger}"
        );
        
        expect($loadedPet->training_level)->toBe(
            $originalTrainingLevel,
            "Training level should match after round-trip. Original: {$originalTrainingLevel}, Loaded: {$loadedPet->training_level}"
        );
        
        // Property 3: Timestamp should be recent (within a few seconds of creation)
        $timestampDiff = abs($loadedPet->last_updated_at->diffInSeconds($createdTimestamp));
        expect($timestampDiff)->toBeLessThanOrEqual(
            2,
            "Timestamp should be preserved after round-trip. Difference: {$timestampDiff} seconds"
        );
        
        // Property 4: ID should be assigned and positive
        expect($loadedPet->id)->toBeGreaterThan(
            0,
            "Pet should have a valid positive ID"
        );
        
        // Property 5: Multiple round-trips should preserve data
        $loadedPet->health = rand(0, 100);
        $loadedPet->save();
        
        $secondLoad = Pet::find($petId);
        expect($secondLoad->health)->toBe(
            $loadedPet->health,
            "Attributes should survive multiple round-trips"
        );
        
        // Clean up
        $loadedPet->delete();
    }
});
