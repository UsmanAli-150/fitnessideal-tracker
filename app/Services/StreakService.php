<?php

namespace App\Services;

use App\Models\Task;
use Carbon\Carbon;

class StreakService
{
    /**
     * Recalculate and persist the streak for a task after it's marked Done for a given date.
     */
    public function recordCompletion(Task $task, Carbon $date): void
    {
        $streak = $task->streak ?? $task->streak()->create([
            'user_id' => $task->user_id,
            'current_streak' => 0,
            'longest_streak' => 0,
        ]);

        $last = $streak->last_completed_date;

        if ($last && $last->copy()->addDay()->isSameDay($date)) {
            // Consecutive day — extend the streak
            $streak->current_streak += 1;
        } elseif ($last && $last->isSameDay($date)) {
            // Already logged today, don't double count
            return;
        } else {
            // Streak broken or first ever completion
            $streak->current_streak = 1;
        }

        $streak->longest_streak = max($streak->longest_streak, $streak->current_streak);
        $streak->last_completed_date = $date;
        $streak->save();
    }

    /**
     * Reset the current streak to 0 when a task is skipped or missed.
     * Longest streak is preserved as the historical record.
     */
    public function breakStreak(Task $task): void
    {
        $streak = $task->streak;

        if ($streak) {
            $streak->current_streak = 0;
            $streak->save();
        }
    }
}