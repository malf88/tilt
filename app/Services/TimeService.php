<?php

namespace App\Services;

use App\Contracts\TimeServiceInterface;
use DateTime;

class TimeService implements TimeServiceInterface
{
    /**
     * Calculate the number of elapsed intervals since the last update.
     *
     * @param DateTime $lastUpdate The timestamp of the last update
     * @param int $intervalMinutes The interval duration in minutes
     * @return int The number of complete intervals that have elapsed
     */
    public function calculateElapsedIntervals(DateTime $lastUpdate, int $intervalMinutes): int
    {
        $now = new DateTime();
        $elapsedSeconds = $now->getTimestamp() - $lastUpdate->getTimestamp();
        $elapsedMinutes = floor($elapsedSeconds / 60);
        
        return (int) floor($elapsedMinutes / $intervalMinutes);
    }

    /**
     * Apply a degradation cap to limit the number of intervals.
     *
     * @param int $intervals The number of intervals to cap
     * @param int $maxHours The maximum hours to simulate
     * @param int $intervalMinutes The interval duration in minutes
     * @return int The capped number of intervals
     */
    public function applyDegradationCap(int $intervals, int $maxHours, int $intervalMinutes): int
    {
        $maxIntervals = ($maxHours * 60) / $intervalMinutes;
        
        return min($intervals, (int) $maxIntervals);
    }
}
