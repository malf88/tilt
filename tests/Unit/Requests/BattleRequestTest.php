<?php

use App\Http\Requests\BattleRequest;
use Illuminate\Support\Facades\Validator;

test('BattleRequest validates difficulty is required', function () {
    $request = new BattleRequest();
    $validator = Validator::make([], $request->rules());
    
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('difficulty'))->toBeTrue();
});

test('BattleRequest validates difficulty must be valid value', function () {
    $request = new BattleRequest();
    $validator = Validator::make(['difficulty' => 'invalid'], $request->rules());
    
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('difficulty'))->toBeTrue();
});

test('BattleRequest passes with easy difficulty', function () {
    $request = new BattleRequest();
    $validator = Validator::make(['difficulty' => 'easy'], $request->rules());
    
    expect($validator->passes())->toBeTrue();
});

test('BattleRequest passes with medium difficulty', function () {
    $request = new BattleRequest();
    $validator = Validator::make(['difficulty' => 'medium'], $request->rules());
    
    expect($validator->passes())->toBeTrue();
});

test('BattleRequest passes with hard difficulty', function () {
    $request = new BattleRequest();
    $validator = Validator::make(['difficulty' => 'hard'], $request->rules());
    
    expect($validator->passes())->toBeTrue();
});

test('BattleRequest has custom error messages', function () {
    $request = new BattleRequest();
    $messages = $request->messages();
    
    expect($messages)->toHaveKey('difficulty.required')
        ->and($messages)->toHaveKey('difficulty.in');
});
