<?php

namespace App\Http\Controllers;

use App\Models\DailyLog;
use App\Models\Task;
use App\Services\StreakService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    public function __construct(private StreakService $streakService)
    {
    }

    public function index()
    {
        $today = Carbon::today();

        $tasks = Task::where('user_id', Auth::id())
            ->where('is_active', true)
            ->get()
            ->filter(fn (Task $task) => $task->occursOn($today))
            ->sortBy('scheduled_time')
            ->values();

        // Preload today's logs so we don't run a query per task in the view
        $logsByTaskId = DailyLog::where('user_id', Auth::id())
            ->whereDate('log_date', $today)
            ->get()
            ->keyBy('task_id');

        $total = $tasks->count();
        $done = $tasks->filter(fn ($task) => optional($logsByTaskId->get($task->id))->status === 'Done')->count();
        $completionRate = $total > 0 ? round(($done / $total) * 100) : 0;

        return view('dashboard', [
            'tasks' => $tasks,
            'logs' => $logsByTaskId,
            'completionRate' => $completionRate,
            'done' => $done,
            'total' => $total,
        ]);
    }

    public function markDone(Request $request, Task $task)
    {
        abort_if($task->user_id !== Auth::id(), 403);

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
            'actual_duration' => ['nullable', 'integer', 'min:1'],
        ]);

        $today = Carbon::today();

        $log = DailyLog::updateOrCreate(
            ['task_id' => $task->id, 'log_date' => $today],
            [
                'user_id' => Auth::id(),
                'status' => 'Done',
                'notes' => $validated['notes'] ?? null,
                'actual_duration' => $validated['actual_duration'] ?? null,
                'completed_at' => now(),
                'skip_reason' => null,
            ]
        );

        $this->streakService->recordCompletion($task, $today);

        return back()->with('success', "\"{$task->name}\" marked as done.");
    }

    public function markSkipped(Request $request, Task $task)
    {
        abort_if($task->user_id !== Auth::id(), 403);

        $validated = $request->validate([
            'skip_reason' => ['required', Rule::in(['sick', 'holiday', 'overtime', 'other'])],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $today = Carbon::today();

        DailyLog::updateOrCreate(
            ['task_id' => $task->id, 'log_date' => $today],
            [
                'user_id' => Auth::id(),
                'status' => 'Skipped',
                'skip_reason' => $validated['skip_reason'],
                'notes' => $validated['notes'] ?? null,
                'completed_at' => null,
            ]
        );

        $this->streakService->breakStreak($task);

        return back()->with('success', "\"{$task->name}\" marked as skipped.");
    }
}