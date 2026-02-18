<?php

use App\Contracts\BattleServiceInterface;
use App\Contracts\PetServiceInterface;
use App\Models\Battle;
use App\Models\Pet;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->battleService = app(BattleServiceInterface::class);
    $this->petService = app(PetServiceInterface::class);
});

test('executeBattle creates battle record', function () {
    $pet = Pet::factory()->create([
        'health' => 100,
        'training_level' => 100,
        'hunger' => 0,
    ]);
    
    $battle = $this->battleService->executeBattle($pet, 'easy');
    
    expect($battle)->toBeInstanceOf(Battle::class)
        ->and($battle->pet_id)->toBe($pet->id)
        ->and($battle->difficulty)->toBe('easy')
        ->and(Battle::where('pet_id', $pet->id)->exists())->toBeTrue();
});

test('executeBattle with medium difficulty generates appropriate opponent', function () {
    $pet = Pet::factory()->create([
        'health' => 80,
        'training_level' => 60,
    ]);
    
    $battle = $this->battleService->executeBattle($pet, 'medium');
    
    expect($battle->difficulty)->toBe('medium')
        ->and($battle->opponent_strength)->toBeGreaterThanOrEqual(40)
        ->and($battle->opponent_strength)->toBeLessThanOrEqual(70);
});

test('executeBattle with hard difficulty generates appropriate opponent', function () {
    $pet = Pet::factory()->create([
        'health' => 100,
        'training_level' => 100,
    ]);
    
    $battle = $this->battleService->executeBattle($pet, 'hard');
    
    expect($battle->difficulty)->toBe('hard')
        ->and($battle->opponent_strength)->toBeGreaterThanOrEqual(70)
        ->and($battle->opponent_strength)->toBeLessThanOrEqual(95);
});

test('executeBattle applies battle effects to pet', function () {
    $pet = Pet::factory()->create([
        'health' => 100,
        'hunger' => 0,
        'training_level' => 50,
    ]);
    
    $initialHealth = $pet->health;
    $initialHunger = $pet->hunger;
    
    $this->battleService->executeBattle($pet, 'easy');
    
    $pet->refresh();
    expect($pet->health)->toBeLessThan($initialHealth)
        ->and($pet->hunger)->toBeGreaterThan($initialHunger);
});

test('generateOpponent returns opponent with name and strength', function () {
    $opponent = $this->battleService->generateOpponent('easy');
    
    expect($opponent)->toHaveKey('name')
        ->and($opponent)->toHaveKey('strength')
        ->and($opponent['name'])->not->toBeEmpty()
        ->and($opponent['strength'])->toBeGreaterThanOrEqual(20)
        ->and($opponent['strength'])->toBeLessThanOrEqual(40);
});
