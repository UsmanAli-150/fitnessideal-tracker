<?php

namespace App\Http\Controllers;

use App\Models\DailyLog;
use App\Models\Streak;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AnalyticsController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $today = Carbon::today();

        // ---- Today's completion rate ----
        $todaysTasks = Task::where('user_id', $userId)
            ->where('is_active', true)
            ->get()
            ->filter(fn (Task $task) => $task->occursOn($today));

        $todaysLogs = DailyLog::where('user_id', $userId)
            ->whereDate('log_date', $today)
            ->get()
            ->keyBy('task_id');

        $todayTotal = $todaysTasks->count();
        $todayDone = $todaysTasks->filter(fn ($t) => optional($todaysLogs->get($t->id))->status === 'Done')->count();
        $todayRate = $todayTotal > 0 ? round(($todayDone / $todayTotal) * 100) : 0;

        // ---- Last 7 days completion chart ----
        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);

            $scheduledTasks = Task::where('user_id', $userId)
                ->where('is_active', true)
                ->get()
                ->filter(fn (Task $task) => $task->occursOn($date));

            $doneCount = DailyLog::where('user_id', $userId)
                ->whereDate('log_date', $date)
                ->where('status', 'Done')
                ->count();

            $total = $scheduledTasks->count();

            $last7Days->push([
                'label' => $date->format('D'),
                'rate' => $total > 0 ? round(($doneCount / $total) * 100) : 0,
            ]);
        }

        // ---- Streaks ----
        $streaks = Streak::where('user_id', $userId)
            ->with('task')
            ->orderByDesc('current_streak')
            ->get();

        $longestStreakOverall = $streaks->max('longest_streak') ?? 0;

        // ---- Productivity score (0-100) ----
        // Weighted blend: 50% today's rate, 30% 7-day average, 20% current streak momentum
        $sevenDayAvg = $last7Days->avg('rate');
        $streakMomentum = min(($streaks->max('current_streak') ?? 0) * 5, 100); // cap contribution at 100
        $productivityScore = round(($todayRate * 0.5) + ($sevenDayAvg * 0.3) + ($streakMomentum * 0.2));

        // ---- Category breakdown (all-time completed logs) ----
        $categoryBreakdown = DailyLog::where('daily_logs.user_id', $userId)
            ->where('status', 'Done')
            ->join('tasks', 'tasks.id', '=', 'daily_logs.task_id')
            ->selectRaw('tasks.category, count(*) as total')
            ->groupBy('tasks.category')
            ->pluck('total', 'category');

        // ---- Monthly calendar (current month, color-coded by completion rate) ----
        $monthStart = $today->copy()->startOfMonth();
        $monthEnd = $today->copy()->endOfMonth();
        $calendar = collect();

        for ($date = $monthStart->copy(); $date->lte($monthEnd); $date->addDay()) {
            $scheduled = Task::where('user_id', $userId)
                ->where('is_active', true)
                ->get()
                ->filter(fn (Task $task) => $task->occursOn($date));

            $done = DailyLog::where('user_id', $userId)
                ->whereDate('log_date', $date)
                ->where('status', 'Done')
                ->count();

            $total = $scheduled->count();
            $rate = $total > 0 ? round(($done / $total) * 100) : null; // null = no tasks scheduled that day

            $calendar->push([
                'day' => $date->day,
                'rate' => $rate,
                'isFuture' => $date->gt($today),
            ]);
        }

        return view('analytics.index', [
            'todayRate' => $todayRate,
            'last7Days' => $last7Days,
            'streaks' => $streaks,
            'longestStreakOverall' => $longestStreakOverall,
            'productivityScore' => $productivityScore,
            'categoryBreakdown' => $categoryBreakdown,
            'calendar' => $calendar,
            'monthName' => $today->format('F Y'),
        ]);
    }
}