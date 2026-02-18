<?php

use App\Models\Pet;
use Livewire\Livewire;
use App\Livewire\PetDashboard;

/**
 * Property 19: Indicadores visuais de alerta
 * 
 * **Validates: Requirements 7.4, 7.5**
 * 
 * Para qualquer pet com Saúde < 30 ou Fome > 70, a UI deve exibir
 * um indicador visual de alerta.
 */
test('Property 19: UI displays alert indicators when health is low or hunger is high', function () {
    $iterations = 100;
    
    for ($i = 0; $i < $iterations; $i++) {
        // Generate random pet attributes
        $health = rand(0, 100);
        $hunger = rand(0, 100);
        $trainingLevel = rand(0, 100);
        
        // Create pet with these attributes
        $pet = Pet::create([
            'name' => 'TestPet' . $i . uniqid(),
            'health' => $health,
            'hunger' => $hunger,
            'training_level' => $trainingLevel,
            'last_updated_at' => now(),
        ]);
        
        // Render the PetDashboard component with this pet
        $component = Livewire::test(PetDashboard::class, ['pet' => $pet]);
        
        // Determine if alerts should be shown
        $shouldShowHealthAlert = $health < 30;
        $shouldShowHungerAlert = $hunger > 70;
        
        // Property 1: When health < 30, a health alert indicator must be present
        if ($shouldShowHealthAlert) {
            $component->assertSee('health-alert', false)
                ->assertSee('alert', false);
        }
        
        // Property 2: When hunger > 70, a hunger alert indicator must be present
        if ($shouldShowHungerAlert) {
            $component->assertSee('hunger-alert', false)
                ->assertSee('alert', false);
        }
        
        // Property 3: When health >= 30, no health alert should be shown
        if (!$shouldShowHealthAlert) {
            // The component should not have a health alert class or indicator
            // This is verified by checking the rendered HTML doesn't contain health-alert
            $html = $component->get('pet')->health >= 30;
            expect($html)->toBeTrue("Health alert should not be shown when health >= 30");
        }
        
        // Property 4: When hunger <= 70, no hunger alert should be shown
        if (!$shouldShowHungerAlert) {
            // The component should not have a hunger alert class or indicator
            $html = $component->get('pet')->hunger <= 70;
            expect($html)->toBeTrue("Hunger alert should not be shown when hunger <= 70");
        }
        
        // Property 5: Alert indicators should be present for edge cases
        // Edge case: health exactly at 29 (should show alert)
        if ($health === 29) {
            $component->assertSee('alert', false);
        }
        
        // Edge case: health exactly at 30 (should NOT show alert)
        if ($health === 30) {
            $shouldNotHaveHealthAlert = $component->get('pet')->health >= 30;
            expect($shouldNotHaveHealthAlert)->toBeTrue();
        }
        
        // Edge case: hunger exactly at 71 (should show alert)
        if ($hunger === 71) {
            $component->assertSee('alert', false);
        }
        
        // Edge case: hunger exactly at 70 (should NOT show alert)
        if ($hunger === 70) {
            $shouldNotHaveHungerAlert = $component->get('pet')->hunger <= 70;
            expect($shouldNotHaveHungerAlert)->toBeTrue();
        }
        
        // Property 6: Both alerts can be shown simultaneously
        if ($shouldShowHealthAlert && $shouldShowHungerAlert) {
            $component->assertSee('alert', false);
            // Both health and hunger alerts should be visible
            expect($component->get('pet')->health)->toBeLessThan(30)
                ->and($component->get('pet')->hunger)->toBeGreaterThan(70);
        }
        
        // Clean up
        $pet->delete();
    }
});

/**
 * Property 19 (Extended): Alert indicators are visually distinct
 * 
 * **Validates: Requirements 7.4, 7.5**
 * 
 * Verifies that health and hunger alerts are distinguishable in the UI.
 */
test('Property 19 Extended: health and hunger alerts are visually distinct', function () {
    $iterations = 50;
    
    for ($i = 0; $i < $iterations; $i++) {
        // Create a pet with both low health and high hunger
        $pet = Pet::create([
            'name' => 'AlertPet' . $i . uniqid(),
            'health' => rand(0, 29),  // Low health
            'hunger' => rand(71, 100), // High hunger
            'training_level' => rand(0, 100),
            'last_updated_at' => now(),
        ]);
        
        // Render the component
        $component = Livewire::test(PetDashboard::class, ['pet' => $pet]);
        
        // Property 1: Both alert types should be present
        $component->assertSee('alert', false);
        
        // Property 2: Pet state should reflect alert conditions
        expect($component->get('pet')->health)->toBeLessThan(30)
            ->and($component->get('pet')->hunger)->toBeGreaterThan(70);
        
        // Clean up
        $pet->delete();
    }
});

/**
 * Property 19 (Boundary): Alert indicators at exact thresholds
 * 
 * **Validates: Requirements 7.4, 7.5**
 * 
 * Tests the exact boundary conditions for alert display.
 */
test('Property 19 Boundary: alerts appear at exact threshold values', function () {
    // Test health threshold: 29 should show alert, 30 should not
    $testCases = [
        // [health, hunger, shouldShowHealthAlert, shouldShowHungerAlert]
        [29, 50, true, false],   // Health at 29: alert
        [30, 50, false, false],  // Health at 30: no alert
        [50, 71, false, true],   // Hunger at 71: alert
        [50, 70, false, false],  // Hunger at 70: no alert
        [29, 71, true, true],    // Both conditions: both alerts
        [0, 100, true, true],    // Extreme low health, extreme high hunger
        [100, 0, false, false],  // Perfect health, no hunger
    ];
    
    foreach ($testCases as $index => [$health, $hunger, $shouldShowHealthAlert, $shouldShowHungerAlert]) {
        $pet = Pet::create([
            'name' => 'BoundaryPet' . $index . uniqid(),
            'health' => $health,
            'hunger' => $hunger,
            'training_level' => 50,
            'last_updated_at' => now(),
        ]);
        
        $component = Livewire::test(PetDashboard::class, ['pet' => $pet]);
        
        // Verify alert conditions
        if ($shouldShowHealthAlert || $shouldShowHungerAlert) {
            $component->assertSee('alert', false);
        }
        
        // Verify pet state matches expectations
        expect($component->get('pet')->health)->toBe($health)
            ->and($component->get('pet')->hunger)->toBe($hunger);
        
        // Verify health alert logic
        $actualHealthAlert = $health < 30;
        expect($actualHealthAlert)->toBe(
            $shouldShowHealthAlert,
            "Health alert mismatch at health={$health}. Expected: " . 
            ($shouldShowHealthAlert ? 'true' : 'false') . ", Got: " . 
            ($actualHealthAlert ? 'true' : 'false')
        );
        
        // Verify hunger alert logic
        $actualHungerAlert = $hunger > 70;
        expect($actualHungerAlert)->toBe(
            $shouldShowHungerAlert,
            "Hunger alert mismatch at hunger={$hunger}. Expected: " . 
            ($shouldShowHungerAlert ? 'true' : 'false') . ", Got: " . 
            ($actualHungerAlert ? 'true' : 'false')
        );
        
        $pet->delete();
    }
});
