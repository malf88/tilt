<?php

use App\Models\Battle;
use App\Models\Pet;
use App\Models\TrainingLog;

describe('PetFactory', function () {
    test('creates pet with default values', function () {
        $pet = Pet::factory()->create();

        expect($pet->health)->toBe(100)
            ->and($pet->hunger)->toBe(0)
            ->and($pet->training_level)->toBe(0)
            ->and($pet->name)->not->toBeEmpty();
    });

    test('creates pet with low health state', function () {
        $pet = Pet::factory()->lowHealth()->create();

        expect($pet->health)->toBeLessThanOrEqual(29)
            ->and($pet->health)->toBeGreaterThanOrEqual(0);
    });

    test('creates pet with high hunger state', function () {
        $pet = Pet::factory()->highHunger()->create();

        expect($pet->hunger)->toBeGreaterThanOrEqual(71)
            ->and($pet->hunger)->toBeLessThanOrEqual(100);
    });

    test('creates pet with trained state', function () {
        $pet = Pet::factory()->trained()->create();

        expect($pet->training_level)->toBeGreaterThanOrEqual(50)
            ->and($pet->training_level)->toBeLessThanOrEqual(100);
    });

    test('can combine multiple states', function () {
        $pet = Pet::factory()->lowHealth()->highHunger()->create();

        expect($pet->health)->toBeLessThanOrEqual(29)
            ->and($pet->hunger)->toBeGreaterThanOrEqual(71);
    });
});

describe('BattleFactory', function () {
    test('creates battle with default values', function () {
        $battle = Battle::factory()->create();

        expect($battle->opponent_name)->not->toBeEmpty()
            ->and($battle->opponent_strength)->toBeGreaterThanOrEqual(20)
            ->and($battle->opponent_strength)->toBeLessThanOrEqual(95)
            ->and($battle->pet_strength)->toBeGreaterThanOrEqual(0)
            ->and($battle->pet_strength)->toBeLessThanOrEqual(100)
            ->and($battle->result)->toBeIn(['win', 'loss', 'draw'])
            ->and($battle->difficulty)->toBeIn(['easy', 'medium', 'hard']);
    });

    test('creates battle with won state', function () {
        $battle = Battle::factory()->won()->create();

        expect($battle->result)->toBe('win')
            ->and($battle->pet_strength)->toBeGreaterThan($battle->opponent_strength);
    });

    test('creates battle with lost state', function () {
        $battle = Battle::factory()->lost()->create();

        expect($battle->result)->toBe('loss')
            ->and($battle->pet_strength)->toBeLessThan($battle->opponent_strength);
    });

    test('creates battle with draw state', function () {
        $battle = Battle::factory()->draw()->create();

        expect($battle->result)->toBe('draw')
            ->and($battle->pet_strength)->toBe($battle->opponent_strength);
    });

    test('creates battle with easy difficulty', function () {
        $battle = Battle::factory()->easy()->create();

        expect($battle->difficulty)->toBe('easy')
            ->and($battle->opponent_strength)->toBeGreaterThanOrEqual(20)
            ->and($battle->opponent_strength)->toBeLessThanOrEqual(40);
    });

    test('creates battle with medium difficulty', function () {
        $battle = Battle::factory()->medium()->create();

        expect($battle->difficulty)->toBe('medium')
            ->and($battle->opponent_strength)->toBeGreaterThanOrEqual(40)
            ->and($battle->opponent_strength)->toBeLessThanOrEqual(70);
    });

    test('creates battle with hard difficulty', function () {
        $battle = Battle::factory()->hard()->create();

        expect($battle->difficulty)->toBe('hard')
            ->and($battle->opponent_strength)->toBeGreaterThanOrEqual(70)
            ->and($battle->opponent_strength)->toBeLessThanOrEqual(95);
    });

    test('can combine result and difficulty states', function () {
        $battle = Battle::factory()->won()->hard()->create();

        expect($battle->result)->toBe('win')
            ->and($battle->difficulty)->toBe('hard');
    });
});

describe('TrainingLogFactory', function () {
    test('creates training log with default values', function () {
        $log = TrainingLog::factory()->create();

        expect($log->training_level_before)->toBeGreaterThanOrEqual(0)
            ->and($log->training_level_before)->toBeLessThanOrEqual(90)
            ->and($log->training_level_after)->toBe(min(100, $log->training_level_before + 10))
            ->and($log->training_level_after)->toBeGreaterThan($log->training_level_before);
    });

    test('creates training log with early training state', function () {
        $log = TrainingLog::factory()->earlyTraining()->create();

        expect($log->training_level_before)->toBeGreaterThanOrEqual(0)
            ->and($log->training_level_before)->toBeLessThanOrEqual(20)
            ->and($log->training_level_after)->toBe(min(100, $log->training_level_before + 10));
    });

    test('creates training log with mid training state', function () {
        $log = TrainingLog::factory()->midTraining()->create();

        expect($log->training_level_before)->toBeGreaterThanOrEqual(30)
            ->and($log->training_level_before)->toBeLessThanOrEqual(60)
            ->and($log->training_level_after)->toBe(min(100, $log->training_level_before + 10));
    });

    test('creates training log with late training state', function () {
        $log = TrainingLog::factory()->lateTraining()->create();

        expect($log->training_level_before)->toBeGreaterThanOrEqual(70)
            ->and($log->training_level_before)->toBeLessThanOrEqual(90)
            ->and($log->training_level_after)->toBe(min(100, $log->training_level_before + 10));
    });

    test('creates training log with max level state', function () {
        $log = TrainingLog::factory()->maxLevel()->create();

        expect($log->training_level_before)->toBe(90)
            ->and($log->training_level_after)->toBe(100);
    });

    test('maintains relationship with pet', function () {
        $pet = Pet::factory()->create();
        $log = TrainingLog::factory()->for($pet)->create();

        expect($log->pet_id)->toBe($pet->id)
            ->and($log->pet->id)->toBe($pet->id);
    });
});
