<?php

use App\Contracts\BattleServiceInterface;
use App\Services\BattleService;

beforeEach(function () {
    $this->battleService = app(BattleServiceInterface::class);
});

describe('generateOpponent', function () {
    test('generates opponent with easy difficulty in correct strength range', function () {
        $opponent = $this->battleService->generateOpponent('easy');
        
        expect($opponent)->toHaveKeys(['name', 'strength'])
            ->and($opponent['name'])->toBeString()->not->toBeEmpty()
            ->and($opponent['strength'])->toBeFloat()
            ->and($opponent['strength'])->toBeGreaterThanOrEqual(20)
            ->and($opponent['strength'])->toBeLessThanOrEqual(40);
    });

    test('generates opponent with medium difficulty in correct strength range', function () {
        $opponent = $this->battleService->generateOpponent('medium');
        
        expect($opponent)->toHaveKeys(['name', 'strength'])
            ->and($opponent['name'])->toBeString()->not->toBeEmpty()
            ->and($opponent['strength'])->toBeFloat()
            ->and($opponent['strength'])->toBeGreaterThanOrEqual(40)
            ->and($opponent['strength'])->toBeLessThanOrEqual(70);
    });

    test('generates opponent with hard difficulty in correct strength range', function () {
        $opponent = $this->battleService->generateOpponent('hard');
        
        expect($opponent)->toHaveKeys(['name', 'strength'])
            ->and($opponent['name'])->toBeString()->not->toBeEmpty()
            ->and($opponent['strength'])->toBeFloat()
            ->and($opponent['strength'])->toBeGreaterThanOrEqual(70)
            ->and($opponent['strength'])->toBeLessThanOrEqual(95);
    });

    test('generates different opponents on multiple calls', function () {
        $opponents = [];
        for ($i = 0; $i < 10; $i++) {
            $opponents[] = $this->battleService->generateOpponent('medium');
        }
        
        // Check that we get some variation in names or strengths
        $names = array_column($opponents, 'name');
        $strengths = array_column($opponents, 'strength');
        
        // At least some variation should exist
        $uniqueNames = count(array_unique($names));
        $uniqueStrengths = count(array_unique($strengths));
        
        expect($uniqueNames + $uniqueStrengths)->toBeGreaterThan(2);
    });

    test('defaults to easy difficulty for invalid difficulty', function () {
        $opponent = $this->battleService->generateOpponent('invalid');
        
        expect($opponent['strength'])->toBeGreaterThanOrEqual(20)
            ->and($opponent['strength'])->toBeLessThanOrEqual(40);
    });
});
