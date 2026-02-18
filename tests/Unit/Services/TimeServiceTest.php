<?php

use App\Services\TimeService;

describe('TimeService', function () {
    describe('calculateElapsedIntervals', function () {
        it('calculates zero intervals when no time has passed', function () {
            $timeService = new TimeService();
            $now = new DateTime();
            $intervals = $timeService->calculateElapsedIntervals($now, 30);
            
            expect($intervals)->toBe(0);
        });

        it('calculates one interval after 30 minutes', function () {
            $timeService = new TimeService();
            $lastUpdate = new DateTime('-30 minutes');
            $intervals = $timeService->calculateElapsedIntervals($lastUpdate, 30);
            
            expect($intervals)->toBe(1);
        });

        it('calculates multiple intervals correctly', function () {
            $timeService = new TimeService();
            $lastUpdate = new DateTime('-90 minutes');
            $intervals = $timeService->calculateElapsedIntervals($lastUpdate, 30);
            
            expect($intervals)->toBe(3);
        });

        it('ignores partial intervals', function () {
            $timeService = new TimeService();
            $lastUpdate = new DateTime('-45 minutes');
            $intervals = $timeService->calculateElapsedIntervals($lastUpdate, 30);
            
            expect($intervals)->toBe(1);
        });

        it('calculates intervals for different interval durations', function () {
            $timeService = new TimeService();
            $lastUpdate = new DateTime('-120 minutes');
            $intervals = $timeService->calculateElapsedIntervals($lastUpdate, 60);
            
            expect($intervals)->toBe(2);
        });
    });

    describe('applyDegradationCap', function () {
        it('returns intervals unchanged when below cap', function () {
            $timeService = new TimeService();
            $capped = $timeService->applyDegradationCap(5, 8, 30);
            
            expect($capped)->toBe(5);
        });

        it('caps intervals to 8 hours maximum with 30-minute intervals', function () {
            $timeService = new TimeService();
            $maxIntervals = (8 * 60) / 30; // 16 intervals
            $capped = $timeService->applyDegradationCap(20, 8, 30);
            
            expect($capped)->toBe(16);
        });

        it('caps intervals to 8 hours maximum with 60-minute intervals', function () {
            $timeService = new TimeService();
            $maxIntervals = (8 * 60) / 60; // 8 intervals
            $capped = $timeService->applyDegradationCap(15, 8, 60);
            
            expect($capped)->toBe(8);
        });

        it('handles exact cap value', function () {
            $timeService = new TimeService();
            $maxIntervals = (8 * 60) / 30; // 16 intervals
            $capped = $timeService->applyDegradationCap(16, 8, 30);
            
            expect($capped)->toBe(16);
        });

        it('works with different max hours', function () {
            $timeService = new TimeService();
            $maxIntervals = (4 * 60) / 30; // 8 intervals
            $capped = $timeService->applyDegradationCap(10, 4, 30);
            
            expect($capped)->toBe(8);
        });
    });
});
