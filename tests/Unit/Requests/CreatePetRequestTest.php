<?php

use App\Http\Requests\CreatePetRequest;
use App\Models\Pet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;

uses(RefreshDatabase::class);

test('CreatePetRequest validates name is required', function () {
    $request = new CreatePetRequest();
    $validator = Validator::make([], $request->rules());
    
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('name'))->toBeTrue();
});

test('CreatePetRequest validates name minimum length', function () {
    $request = new CreatePetRequest();
    $validator = Validator::make(['name' => 'A'], $request->rules());
    
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('name'))->toBeTrue();
});

test('CreatePetRequest validates name maximum length', function () {
    $request = new CreatePetRequest();
    $longName = str_repeat('A', 51);
    $validator = Validator::make(['name' => $longName], $request->rules());
    
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('name'))->toBeTrue();
});

test('CreatePetRequest validates name uniqueness', function () {
    Pet::factory()->create(['name' => 'ExistingPet']);
    
    $request = new CreatePetRequest();
    $validator = Validator::make(['name' => 'ExistingPet'], $request->rules());
    
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('name'))->toBeTrue();
});

test('CreatePetRequest passes with valid name', function () {
    $request = new CreatePetRequest();
    $validator = Validator::make(['name' => 'ValidPet'], $request->rules());
    
    expect($validator->passes())->toBeTrue();
});

test('CreatePetRequest has custom error messages', function () {
    $request = new CreatePetRequest();
    $messages = $request->messages();
    
    expect($messages)->toHaveKey('name.required')
        ->and($messages)->toHaveKey('name.min')
        ->and($messages)->toHaveKey('name.max')
        ->and($messages)->toHaveKey('name.unique');
});
