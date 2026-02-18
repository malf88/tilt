<?php

use App\Contracts\PetServiceInterface;
use App\Models\Pet;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->petService = app(PetServiceInterface::class);
});

test('feed successfully feeds pet', function () {
    $pet = Pet::factory()->create(['hunger' => 50, 'health' => 80]);
    
    $initialHunger = $pet->hunger;
    $initialHealth = $pet->health;
    
    $this->petService->feedPet($pet);
    
    $pet->refresh();
    expect($pet->hunger)->toBe($initialHunger - 20)
        ->and($pet->health)->toBe(min(100, $initialHealth + 10));
});

test('train successfully trains pet with sufficient health', function () {
    $pet = Pet::factory()->create(['health' => 50, 'training_level' => 20]);
    
    $initialTrainingLevel = $pet->training_level;
    
    $this->petService->trainPet($pet);
    
    $pet->refresh();
    expect($pet->training_level)->toBe($initialTrainingLevel + 10);
});

test('train fails when pet health is below 30', function () {
    $pet = Pet::factory()->create(['health' => 25]);
    
    expect(fn() => $this->petService->trainPet($pet))
        ->toThrow(\Exception::class, 'muito fraco');
});

test('createPet creates pet with correct initial values', function () {
    $pet = $this->petService->createPet('TestPet');
    
    expect($pet->name)->toBe('TestPet')
        ->and($pet->health)->toBe(100)
        ->and($pet->hunger)->toBe(0)
        ->and($pet->training_level)->toBe(0);
});
