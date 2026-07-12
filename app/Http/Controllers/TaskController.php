<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::where('user_id', Auth::id());

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->status === 'active') {
            $query->where('is_active', true);
        } elseif ($request->status === 'inactive') {
            $query->where('is_active', false);
        }

        $tasks = $query->orderBy('scheduled_time')->paginate(15)->withQueryString();

        return view('tasks.index', compact('tasks'));
    }

    public function create()
    {
        return view('tasks.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateTask($request);
        $validated['user_id'] = Auth::id();

        Task::create($validated);

        return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
    }

    public function edit(Task $task)
    {
        $this->authorizeTask($task);

        return view('tasks.edit', compact('task'));
    }

    public function update(Request $request, Task $task)
    {
        $this->authorizeTask($task);

        $validated = $this->validateTask($request);
        $task->update($validated);

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        $this->authorizeTask($task);
        $task->delete(); // soft delete

        return back()->with('success', 'Task moved to trash.');
    }

    public function trash()
    {
        $tasks = Task::onlyTrashed()->where('user_id', Auth::id())->orderBy('deleted_at', 'desc')->get();

        return view('tasks.trash', compact('tasks'));
    }

    public function restore($id)
    {
        $task = Task::onlyTrashed()->where('user_id', Auth::id())->findOrFail($id);
        $task->restore();

        return back()->with('success', 'Task restored.');
    }

    public function forceDelete($id)
    {
        $task = Task::onlyTrashed()->where('user_id', Auth::id())->findOrFail($id);
        $task->forceDelete();

        return back()->with('success', 'Task permanently deleted.');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);

        Task::where('user_id', Auth::id())->whereIn('id', $ids)->delete();

        return back()->with('success', count($ids) . ' task(s) moved to trash.');
    }

    public function toggleActive(Task $task)
    {
        $this->authorizeTask($task);
        $task->update(['is_active' => ! $task->is_active]);

        return back()->with('success', 'Task status updated.');
    }

    private function validateTask(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::in(['Health', 'Work', 'Learning', 'Chores', 'Other'])],
            'scheduled_time' => ['required', 'date_format:H:i'],
            'priority' => ['required', Rule::in(['High', 'Medium', 'Low'])],
            'recurrence' => ['required', Rule::in(['Daily', 'Weekdays', 'Weekends', 'Custom'])],
            'custom_days' => ['nullable', 'array'],
            'custom_days.*' => ['string', Rule::in(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])],
            'estimated_duration' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    private function authorizeTask(Task $task): void
    {
        abort_if($task->user_id !== Auth::id(), 403);
    }
}